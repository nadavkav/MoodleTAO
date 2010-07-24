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
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * go find users to assign to yourself.
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/forms.php');

require_login();

$strtitle = get_string('finduser', 'local');
$strdesc = get_string('finduserdesc', 'local');

print_header($strtitle, $strtitle, build_navigation($strtitle));

$userroles = tao_get_assignable_userroles();
$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
$canassign = array();

foreach ($userroles as $key => $roledata) {
    $roledata = (object)$roledata;
    if (has_capability('moodle/local:' . $roledata->canassigncap, $sitecontext)) {
        $canassign[$key] = $roledata;
    }
}

if (count($canassign) == 0) {
    print_error('nopermissions', 'error', null, get_string('finduser', 'local'));
}

$mform = new tao_finduser_form();
if ($fromform = $mform->get_data()) {
    if ($user = get_record('user', 'idnumber', $fromform->idnumber, 'email', $fromform->email)) {
        $count = 0;
        foreach ($canassign as $key => $roledata) {
            $roledata = (object)$roledata;
            if (has_capability('moodle/local:' . $roledata->isassignablecap, $sitecontext, $user->id, false)) {
                if ($count == 0) {
                    print_heading(fullname($user));
                }
                $role = get_record('role', 'shortname', $roledata->recipientrole);
                $assignrole = get_record('role', 'shortname', $roledata->assignerrole);
                $url = $CFG->wwwroot . '/local/user/assign.php';
                $buttonstring = get_string('assignrole', 'local', $role->name);
                $options = array(
                    'sesskey'    => sesskey(),
                    'user'       => $user->id,
                    'assignrole' => $assignrole->id,
                    'reciprole'  => $role->id,
                    'cap'        => $key,
                );

                $usercontext = get_context_instance(CONTEXT_USER, $user->id);


                if (user_has_role_assignment($USER->id, $assignrole->id, $usercontext->id)) {
                    $options['unassign'] = 1;
                    $buttonstring = get_string('unassignrole', 'local', $role->name);
                }

                print_single_button($url, $options, $buttonstring);
                $count++;
            }
        }
        if ($count == 0) {
            notify(get_string('nosuchuser', 'local'));
        } else {
            print_footer();
            exit;
        }
    } else {
        notify(get_string('nosuchuser', 'local'));
    }
}
$mform->display();


print_footer();
?>
