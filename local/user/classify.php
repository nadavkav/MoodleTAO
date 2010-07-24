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
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * adds classification tags for users.
 *
 */
    require_once('../../config.php');
    require_once($CFG->dirroot . '/local/forms.php');
    require_once($CFG->dirroot . '/tag/lib.php');

    $id      = optional_param('id',     0,      PARAM_INT);   // user id
    $course  = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }
    if (! $user = get_record("user", "id", $id) ) {
        error("No such user in this course");
    }
    
    if (! $course = get_record("course", "id", $course) ) {
        error("No such course id");
    }
    
    if ($course->id == SITEID) {
        $coursecontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
    } else {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
    }
    $systemcontext   = get_context_instance(CONTEXT_SYSTEM);
    $personalcontext = get_context_instance(CONTEXT_USER, $user->id);

    // check access control
    if ($user->id == $USER->id) {
        //editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
        if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
            error('Can not edit own profile, sorry.');
        }

    } else {
        // teachers, parents, etc.
        require_capability('moodle/user:editprofile', $personalcontext);
    }

    $strclassify = get_string('classifylp', 'local');

    $streditmyclassifications = get_string('editmyclassifications','local');
    $strparticipants  = get_string('participants');
    $userfullname     = fullname($user, true);
    
    $navlinks = array();
    if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
        $navlinks[] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$course->id", 'type' => 'misc');
    }
    $navlinks[] = array('name' => $userfullname,
                        'link' => "$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => $streditmyclassifications, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header("$course->shortname: $streditmyclassifications", $course->fullname, $navigation, "");

    $currenttab = 'taotopicsinterest';
    $showroles = 1;
    if (!$user->deleted) {
        include('../../user/tabs.php');
    }

$mform = new tao_classify_user_form($CFG->wwwroot.'/local/user/classify.php', array('user' => $user));
if ($formdata = $mform->get_data()) {
    begin_sql();
    $values = array();
    foreach ($formdata as $key => $value) {
        if (preg_match('/checkboxes(\d+)/', $key, $matches) && !empty($value)) {
            foreach($value as $valname => $val) {
                $values[] = $valname;
            }            
        }
    }
    tag_set('userclassify', $user->id, $values);

    commit_sql();
    events_trigger('user_classification', $user->id);
    notify(get_string('changessaved'), 'notifysuccess');
    print_continue($CFG->wwwroot . '/local/user/classify.php?id=' . $user->id);
} else {
    $mform->display();
    
    print_footer($course);
}
?>