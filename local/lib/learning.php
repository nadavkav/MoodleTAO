<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage local
 * @author     David Drummond <david@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 * This file is used for functions related to the "My Learning" section 
 * of the TAO website.
 * 
 * included from /local/my/learning.php
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}	

function tao_print_browse_learning_paths() {
    global $CFG;

    echo '<h2>' . get_string('browselearningpaths', 'local') . '</h2>';
    echo get_string('browselearningpathsdescription', 'local');
    echo '<p><a href="' . $CFG->wwwroot . '/local/lp/list.php">&gt;&gt; ' . get_string('browselearningpaths', 'local') . '</a></p>';
}

function tao_print_related_learning_paths() {
    global $USER, $CFG;
    if (isguest()) { //don't print anything if this is a guest user.
        return;
    }
    echo '<h2>' . get_string('recommendedlearningpaths', 'local') . '</h2>';
    $submitted = get_field('course_approval_status', 'id', 'shortname', 'published');

    $sql = "SELECT c.id, c.fullname, c.summary, count(c.id) ".sql_as()." matches, 
                     sum((select max(ordering) from mdl_tag_instance where itemid = {$USER->id})-t.ordering) as weight
             FROM mdl_course c, mdl_tag_instance t
             WHERE t.tagid in ( select tagid from mdl_tag_instance where itemid = {$USER->id} and (itemtype = 'user' or itemtype = 'userclassify') )
               AND t.itemid = c.id
               AND ( t.itemtype = 'course' or t.itemtype = 'courseclassification' )
               AND c.format = 'learning'
               AND c.approval_status_id = {$submitted}
             GROUP BY c.id, c.fullname, c.summary
           ORDER BY weight desc";


    if ($courses = get_records_sql($sql)) {

        $count = 0;

        //print_simple_box_start('center', '100%', '', 5, "coursebox");
        echo '<table id="my-learning-paths-list" width="100%" border=0>';
        foreach ($courses as $course) {


            // only display those that you don't have a relationship with already
            if (has_capability('moodle/local:hasdirectlprelationship', get_context_instance(CONTEXT_COURSE, $course->id))) {
                continue;
            }

            // only display up to 3 learning paths
            $count++;
            if ($count>3){
                break;
            } 

            echo '<tr>';
            echo '<td>';
            echo '<a title="'. format_string($course->fullname).'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'"> '. format_string($course->fullname).'</a>';

            $summary = $course->summary;

            if (strlen($summary) > 150) {
                $summary = substr($summary, 0, 150) . '...';
            } 

            echo '<br/>' . $summary;
            echo '</td>';
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';

        }
        echo '</table>';
        //print_simple_box_end();
        echo '<p>' . get_string('recommendedlearningpathsdescription', 'local') . '</p>';

    } else {
        echo '<p>' . get_string('nomatchinglearningpaths', 'local') . '</p>';
    }

    echo '<p><a href="' . $CFG->wwwroot . '/local/user/classify.php?id=' . $USER->id . '">&gt;&gt; ' . get_string('updateyourinterests', 'local') . '</a></p>';
}

function tao_print_my_learning_paths() {
    global $USER, $CFG;
    if (isguest()) { //don't print anything if this is a guest user.
        return;
    }

    // list learning paths i am enrolled in
    echo '<h2>' . get_string('mylearningpaths', 'local') . '</h2>';
    echo '<p>' . get_string('mylearningpathsdescription', 'local') . '</p>';

    // limits the number of courses showing up
    $courses = get_my_courses($USER->id, 'visible DESC,sortorder ASC', '*', false, 21);
    tao_print_mylearningpath_list($courses);

}

function tao_print_my_learning_paths_raflmode() {
    global $USER, $CFG;
    if (isguest()) { //don't print anything if this is a guest user.
        return;
    }
    echo '<h2>' . get_string('mylearningpaths', 'local') . '</h2>';
    echo '<p>' . get_string('mylearningpathsdescription', 'local') . '</p>';

    echo '<table id="my_learning_paths_raflmode" border=0><tr>';

    echo '<td>';

        echo '<h4>' . get_string('myownlearningpaths', 'local') . '</h4>';

        // print learning paths i created
        $courses = tao_get_authored_learning_paths($USER);
        tao_print_mylearningpath_list($courses);

        echo '<p><a href="'.$CFG->wwwroot.'/course/addlearningpath.php?category=' . $CFG->lpdefaultcategory . '">' . get_string('addnewlearningpath', 'local') . '...</a></p>';

    echo '</td>';
    echo '<td>';

        // print learning paths I am contributing to 
        echo '<h4>' . get_string('mylearningpathcontributions', 'local') . '</h4>';

        $courses = tao_get_learning_paths_by_role_of_user($USER, ROLE_LPCONTRIBUTOR);
        tao_print_mylearningpath_list($courses);

    echo '</td>';
    echo '<td>';

        // print the learning paths i am enrolled in 
        echo '<h4>' . get_string('mylearningpathbookmarks', 'local') . '</h4>';
        $courses = get_my_courses($USER->id, 'visible DESC,sortorder ASC', '*', false, 21);
        tao_print_mylearningpath_list($courses);

    echo '</td>';
    echo '</tr></table>';

}



function tao_print_certification() {
    global $CFG;
    if (isguest()) { //don't print anything if this is a guest user.
        return;
    }
    echo '<h2>' . get_string('mycertification', 'local') . '</h2>';
    echo '<p>' . get_string('mycertificationdescription', 'local') . '</p>';

    echo '<p><a href="' . $CFG->wwwroot . '/local/lp/certification.php">' . get_string('reviewcertification', 'local') . '>></a></p>';

}

function tao_print_mylearningpath_list($courses) {
    global $CFG, $USER;

    if (isguest()) { //don't print anything if this is a guest user.
        return;
    }
    if (empty($courses)) {
        echo '<p>' . get_string('nolearningpaths', 'local') . '.</p>';
        return;
    }

    echo '<table id="my_learning_paths_list">';
    foreach ($courses as $course) {
        echo '<tr>';
        $linkcss = '';
        if (empty($course->visible)) {
            $linkcss = 'class="dimmed"';
        }
        echo '<td><a title="'. format_string($course->fullname).'" '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a></td>';
        echo '<td align="right"><a href="'.$CFG->wwwroot.'/course/unenrol.php?id='.$course->id.'&amp;return=&#47;local&#47;my&#47;learning.php">&lt; remove &gt;<td>';
    }
    echo '</table>';
}

?>
