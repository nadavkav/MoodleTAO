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
 * This file is used for functions related to the "My Work" section 
 * of the TAO website.
 * 
 * included from /local/my/work.php
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}	

function tao_print_no_work() {

    echo '<h2>' . get_string('nowork', 'local') . '</h2>';
    echo '<p>' . get_string('noworktext', 'local') . '</p>';

}

function tao_print_need_editing() {
    global $CFG;

    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

    echo '<h2>' . get_string('learningpathsneededit', 'local') . '</h2>';

    // look for unallocated learning paths
    $pf = $CFG->prefix;
    $as = sql_as();

    // select courses ready for review but without an existing head editor - not sure this sql is super-scalable so might need to be revised
    $sql = "SELECT c.id, c.fullname, c.shortname, s.displayname $as status
              FROM {$pf}course c, {$pf}course_approval_status s
             WHERE c.approval_status_id in ( ".COURSE_STATUS_SUBMITTED.", ".COURSE_STATUS_RESUBMITTED." )
               AND c.approval_status_id = s.id
               AND c.id NOT IN ( SELECT x.instanceid
                                  FROM mdl_context x, mdl_role_assignments a, mdl_role r
                                 WHERE x.id = a.contextid
                                   AND a.roleid = r.id
                                   AND x.contextlevel = ".CONTEXT_COURSE." 
                                   AND r.shortname = '".ROLE_HEADEDITOR."' )";

    $courses = get_records_sql($sql);

    // todo: add an 'author(s)' column

    if (!empty($courses)) {

        $html  = '<table id="unedited_learning_paths" width="100%" border="0" cellspacing="0" cellpadding="0">';
        $html .= '<tr><th align="left">' . get_string('course') . '</th><th align="left">' . get_string('status') . '</th><th></th>';

        foreach($courses as $course) {
            $html .= '<tr>';
            $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
            $html .= '<td>'.$course->status.'</td>';
            $html .= '<td align="right">';
            if (has_capability('moodle/local:canselfassignheadeditor', $sitecontext)) {
                $html .= '<a href="' . $CFG->wwwroot . '/local/lp/selfassignedit.php?id='.$course->id.'">' . get_string('editlearningpath', 'local') . '</a>';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

    } else {
        $html = '<p>' . get_string('nolearningpaths', 'local') . '</p>';
    }


    echo $html;

}

/*
Prints a list of Learning Paths that have been approved but not published.
*/

function tao_print_need_publishing () {
    global $CFG;

    echo '<h2>' . get_string('learningpathsneedpublish', 'local') . '</h2>';

    $pf = $CFG->prefix;
    $as = sql_as();

    $sql = "SELECT c.id, c.fullname, c.shortname, s.displayname $as status
              FROM {$pf}course c, {$pf}course_approval_status s
             WHERE c.approval_status_id = ".COURSE_STATUS_APPROVED."
               AND c.approval_status_id = s.id";


    $courses = get_records_sql($sql);

    if (!empty($courses)) {

        $html  = '<table id="unpublished_learning_paths" width="100%" border="0" cellspacing="0" cellpadding="0">';
        $html .= '<tr><th align="left">' . get_string('course') . '</th><th align="left">' . get_string('status') . '</th><th></th>';

        foreach($courses as $course) {
            $html .= '<tr>';
            $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
            $html .= '<td>'.$course->status.'</td>';
            $html .= '<td align="right">&nbsp;</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

    } else {
        $html = '<p>' . get_string('nolearningpaths', 'local') . '</p>';
    }


    echo $html;

}

/* 
Prints a list of Learning Paths the use has authored
*/

function tao_print_my_authoring () {
    global $CFG, $USER;

    $courses = tao_get_authored_learning_paths($USER);

    echo '<h2>' . get_string('authoredlearningpaths', 'local') . '</h2>';

    if (!empty($courses)) {

        $html  = '<table id="unpublished_learning_paths" width="100%" border="0" cellspacing="0" cellpadding="0">';
        $html .= '<tr><th align="left">' . get_string('course') . '</th><th align="left">' . get_string('status') . '</th><th></th>';

        foreach($courses as $course) {
            $html .= '<tr>';
            $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
            $html .= '<td>'.$course->status.'</td>';
            $html .= '<td align="right">&nbsp;</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

    } else {
        $html = '<p>' . get_string('nolearningpaths', 'local') . '</p>';
    }


    echo $html;

    echo '<p><a href="'.$CFG->wwwroot.'/course/addlearningpath.php?category=' . $CFG->lpdefaultcategory . '">' . get_string('addnewlearningpath', 'local') . '...</a></p>';

}

/**
* Prints the learning paths the user is editing
*/

function tao_print_my_editing() {
    global $CFG, $USER;

    $courses = tao_get_editing_learning_paths($USER);

    echo '<h2>' . get_string('myediting', 'local') . '</h2>';

    // todo: add author column

    if (!empty($courses)) {

        $html  = '<table id="unpublished_learning_paths" width="100%" border="0" cellspacing="0" cellpadding="0">';
        $html .= '<tr><th align="left">' . get_string('course') . '</th><th align="left">' . get_string('status') . '</th><th></th>';

        foreach($courses as $course) {
            $html .= '<tr>';
            $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a></td>';
            $html .= '<td>'.$course->status.'</td>';
            $html .= '<td align="right">&nbsp;</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

    } else {
        $html = '<p>' . get_string('nolearningpaths', 'local') . '</p>';
    }


    echo $html;



}

/**
Prints an entry point to message by role screen
*/

function tao_print_message_by_role_link() {
    global $CFG;

    echo '<h2>' . get_string('messaging', 'local') . '</h2>';

    $link = $CFG->wwwroot.'/message/index.php?tab=byrole" onclick="this.target=\'message\'; return openpopup(\'/message/index.php?tab=byrole\', \'message\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);';

    echo '<p><a href="' . $link . '">' . get_string('messagebyrolelink', 'local') . '...</a></p>';
}

/**
Prints a link to the users I am responsible for page 
(final version should perhaps display this directly on the my work screen).
*/

function tao_print_my_participants() {
    global $CFG;

    echo '<h2>' . get_string('myparticipants', 'local') . '</h2>';
    echo '<p><a href="' . $CFG->wwwroot.'/local/user/responsible.php' . '">' . get_string('responsiblefor', 'local') . '...</a></p>';
    echo '<p><a href="' . $CFG->wwwroot.'/local/user/find.php' . '">' . get_string('finduser', 'local') . '...</a></p>';
    echo '<p><a href="' . $CFG->wwwroot.'/local/login/signup.php' . '">' . get_string('local:invitenewuser', 'local') . '...</a></p>';

}

/**
Prints the learning path templates on the system.
Learning path 'templates' are simply courses in the templates directory.
This print out:
  1. gives easy access to these without digging through the category navigation.
  2. avoids us having to give rights in the admin block to template editors. 
**/

function tao_print_templates() {
    global $CFG, $USER;

    // get learning paths in the template directory
    $courses = tao_get_learning_path_templates();

    echo '<h2>' . get_string('lptemplates', 'local') . '</h2>';

    if (!empty($courses)) {

        $html  = '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
        $html .= '<tr><th align="left">' . get_string('lptemplates', 'local') . '</th><th align="left">' . get_string('assignedtotemplate', 'local') . '</th><th></th>';

        foreach($courses as $course) {

            // get a list of current editors on the course
            $editors = tao_get_templateeditors(get_context_instance(CONTEXT_COURSE, $course->id));

            $editorlist = '';

            if (!empty($editors)) {
                foreach($editors as $editor) {
                    if (!empty($editorlist)) {
                        $editorlist .= ', ';
                    }
                    $editorlist .= $editor->firstname . ' ' . $editor->lastname;
                }
            }

            $areeditor = 0;
            if (isset($editors[$USER->id])) {
                $areeditor = 1;
            }

            $html .= '<tr>';
            $html .= '<td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">' .$course->fullname.'</a></td>';
            $html .= '<td><font size="1">' . $editorlist . '</font></td>';
            $html .= '<td align="right">';
            if (!$areeditor && has_capability('moodle/local:canselfassigntemplateeditor', get_context_instance(CONTEXT_COURSE, SITEID))) { 
                $html .= '<a href="' . $CFG->wwwroot . '/local/lp/selfassigntemplate.php?id='.$course->id.'">' . get_string('editlearningpath', 'local') . '</a>';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
 
        $html .= '</table>';

    } else {
        $html = '<p>' . get_string('nolearningpaths', 'local') . '</p>';
    }

//$CFG->lptemplatescategory
//print_object($CFG);

    $html .= '<p><a href="'.$CFG->wwwroot.'/course/addlearningpath.php?category=' . $CFG->lptemplatescategory . '&ct=1">' . get_string('createnewtemplate', 'local') . '...</a></p>';

    echo $html;
}

?>
