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
 * Displays a series of the workflow management controls dependent on your user role(s)
 *
 */

// Note: first cut of this is assuming that you have the correct 'type' roles assigned at site level. 

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot.'/local/lib/work.php');
require_login();

$strheading = get_string('mywork', 'local');

print_header($strheading, $strheading, build_navigation($strheading));

// get this users site level roles
$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
$roles = get_user_roles($sitecontext, $USER->id, true);  // note set $checkparentcontexts true to inherit system level roles 
                                                             //     - not sure about this though

// add page sections by role 
//  note:  in the case of a user being a member of multiple roles duplicate sections are not shown
//         and ordering is determined by the role order as defined in the switch below. i.e. the higher
//         rated roles sections will take priority 
//  note2: the main idea here is to make it clear to see what belongs to each role and easy to alter later,
//          so if you think of a 'cleverer' way to do this, make sure it adheres to those principles

$sections = array(); // sections to display on page

echo '<h2>' . get_string('myroles', 'local') . '</h2>';

// all users
array_push($sections, "message_by_role_link");

foreach ($roles as $role) {

    echo "<h4>$role->name</h4>";

    switch($role->shortname) {

        case ROLE_ADMIN:
            array_push($sections, "templates");
            array_push($sections, "my_editing");
            array_push($sections, "my_authoring");
            array_push($sections, "need_editing");
            array_push($sections, "need_publishing");
            array_push($sections, "my_participants");
            break;

        case ROLE_TEMPLATEEDITOR:
            array_push($sections, "templates");
            break;

        case ROLE_HEADEDITOR:
            array_push($sections, "my_editing");
            array_push($sections, "need_editing");
            array_push($sections, "need_publishing");
            break;

        case ROLE_LPCREATOR:
        case ROLE_LPAUTHOR:
            array_push($sections, "my_authoring");
            break;

        case ROLE_ST:
            array_push($sections, "my_participants");
            break;

        case ROLE_MT:
            array_push($sections, "my_participants");
            break;

        case ROLE_PT:
            // nothing yet
            break;

        default:

    }
}

echo '<p><i>' . get_string('myrolestext', 'local') . '.</i></p>';


/*
  Sections needed:
    * PARTICIPANT: Complete certification on my learning paths
    * MENTOR: Status of your learning paths (i.e. progess of your participants)
*/

// shuffle to ensure uniqueness // TODO: MUST be a better way to do this in php
$usections = array();
foreach ($sections as $section) {
    if (!in_array($section, $usections)) {
        array_push($usections, $section);
    }
}

if (empty($usections)) {
    array_push($usections, "no_work");
}

// now try call the print functions for each section
foreach($usections as $section) {
    $fname = "tao_print_$section";
    if (function_exists($fname)) {
        $fname();
    }
}

print_footer();

?>

