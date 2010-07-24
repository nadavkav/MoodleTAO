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
 * assign users to other users (eg PT to MT, MT to ST)
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$userto      = required_param('user', PARAM_INT);
$roleid      = required_param('assignrole', PARAM_INT);
$reciproleid = required_param('reciprole', PARAM_INT);
$cap         = required_param('cap', PARAM_ALPHA);
$unassign    = optional_param('unassign', false, PARAM_INT);

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
$usercontext = get_context_instance(CONTEXT_USER, $userto);

$returnurl = $CFG->wwwroot . '/user/view.php?id=' . $userto;

if (!$assignrole = get_record('role', 'id', $roleid)) {
    print_error('unknownrole', 'error', $returnurl, $roleid);
}

if (!$reciprole = get_record('role', 'id', $reciproleid)) {
    print_error('unknownrole', 'error', $returnurl, $reciproleid);
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error', $returnurl);
}

$exists       = user_has_role_assignment($USER->id, $assignrole->id, $usercontext->id);
$canassign    = has_capability('moodle/local:canassign' . $cap, $sitecontext);
$isassignable = has_capability('moodle/local:isassignable' . $cap, $sitecontext, $userto, false);

$assignedstr        = get_string('roleassigned', 'local', $reciprole->name);
$assignedshortstr   = get_string('roleassignedshort', 'local');
$unassignedstr      = get_string('roleunassigned', 'local', $reciprole->name);
$unassignedshortstr = get_string('roleunassignedshort', 'local');

if ($exists) {
    if (!$unassign) {
        print_error('alreadyassigned', 'local', $returnurl, $reciprole->name);
    }
    // fine , we've been asked to unassign it.
    // don't bother checking the capabilities since we're removing it.
    if (!role_unassign($assignrole->id, $USER->id, 0, $usercontext->id)) {
        print_error('couldnotunassignrole', 'local', $returnurl);
    }
    $returnurl = $CFG->wwwroot;
    print_header($unassignedstr, $unassignedstr, build_navigation($unassignedshortstr));
    redirect($returnurl, $unassignedstr, 3);
} else { // no role exists yet
    if ($unassign) {
        print_error('roleassignmentdidnotexist', 'local', $returnurl, $reciprole->name);
    }
    if (!$canassign && $isassignable) {
        print_error('notassignable', 'local', $returnurl, $reciprole->name);
    }
    if (!role_assign($assignrole->id, $USER->id, 0, $usercontext->id)) {
        print_error('couldnotassignrole', 'error', $returnurl);
    }
    print_header($assignedstr, $assignedstr, build_navigation($assignedshortstr));
    redirect($returnurl, $assignedstr, 3);
}


?>
