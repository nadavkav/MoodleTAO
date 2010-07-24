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
 * add the user as a friend
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();

$friendid    = required_param('userid', PARAM_INT);
$action      = optional_param('action', '', PARAM_ALPHA);

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

$returnurl = $CFG->wwwroot . '/user/view.php?id=' . $friendid;

$unfriendedstr      = get_string('unfriended', 'local');
$friendedstr        = get_string('friended', 'local');

$exists = 0;
if (get_record('user_friend', 'userid', $USER->id, 'friendid', $friendid)) {
    $exists = 1;
}
if ($action=='accept') {
    $friendrequest = get_record('user_friend', 'userid', $friendid , 'friendid', $USER->id);
    if (empty($friendrequest)) {
        error('invalid friendid');
    }
    //first approve request.
    $friendrequest->approved = '1';
    update_record('user_friend', $friendrequest);
    
    //now add the user as a friend.
    //first check if already a friend record.
    $existingfriend = get_record('user_friend', 'userid', $USER->id, 'friendid', $friendid);
    if (!empty($existingfriend)) {
        //set approved if not already.
        if (!$existingfriend->approved) {
            $existingfriend->approved = 1;
            update_record('user_friend', $existingfriend);
        }
    } else {
        //add new friend record.
        $newfriend = new stdclass();
        $newfriend->userid = $USER->id;
        $newfriend->friendid = $friendid;
        $newfriend->approved = 1;
        insert_record('user_friend', $newfriend);
    }
    friend_send_email($friendid,$USER->id, $action);
    local_mahara_mnet_call('local/mahara/rpclib.php/add_friend', $USER->username, get_field('user', 'username', 'id', $friendid));
    print_header($friendedstr, $friendedstr, build_navigation($friendedstr));
    redirect($returnurl, $friendedstr, 3);

} elseif ($action=='decline') {
    $friendrequest = get_record('user_friend', 'userid', $friendid , 'friendid', $USER->id);
    if (empty($friendrequest)) {
        error('invalid friendid');
    }
    friend_send_email($friendid,$USER->id, $action);
    delete_records('user_friend', 'id', $friendrequest->id);
    local_mahara_mnet_call('local/mahara/rpclib.php/remove_friend', $USER->username, get_field('user', 'username', 'id', $friendid));
    print_header($unfriendedstr, $unfriendedstr, build_navigation($unfriendedstr));
    redirect($returnurl, $unfriendedstr, 3);

} elseif ($action=='unfriend') {
    if (!$exists) {
        print_error('notfriends', 'local', $returnurl);
    }
    if (!delete_records('user_friend', 'friendid', $friendid)) {
        print_error('couldnotremoveasfriend', 'local', $returnurl);
    }
    local_mahara_mnet_call('local/mahara/rpclib.php/remove_friend', $USER->username, get_field('user', 'username', 'id', $friendid));
    print_header($unfriendedstr, $unfriendedstr, build_navigation($unfriendedstr));
    redirect($returnurl, $unfriendedstr, 3);
} else { 

    if ($exists) {
        print_error('arealreadyfriend', 'local', $returnurl);
    }
    $friend = new object();
    $friend->userid = $USER->id;
    $friend->friendid = $friendid;
    $friend->approved = 0;
    if (!insert_record('user_friend', $friend)) {
        print_error('couldnotaddasfriend', 'local', $returnurl);
    }
    friend_send_email($friendid,$USER->id, 'request');
    local_mahara_mnet_call('local/mahara/rpclib.php/add_friend', $USER->username, get_field('user', 'username', 'id', $friendid));
    $reqfriend = get_string('userfriendrequest', 'local');
    print_header($reqfriend, $reqfriend, build_navigation($reqfriend));
    redirect($returnurl, $reqfriend, 3);
}

function friend_send_email($touserid, $fromuserid, $action) {
           global $CFG;
           $sendto = get_record('user', 'id', $touserid);
           $sendfrom = get_record('user','id', $fromuserid);
           $a = new stdclass();
           $a->firstname = $sendto->firstname;
           $a->user = fullname($sendfrom);
           $a->url = $CFG->wwwroot.'/local/my/collaboration.php'; 
           email_to_user($sendto, $sendfrom, get_string($action.'friendemailsubject','local'), get_string($action.'friendemailbody','local', $a));
}
?>