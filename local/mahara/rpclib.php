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
 * @subpackage mod-taoresource
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */
//this file defines the mnet functions that we can call!

function mahara_mnet_publishes() {
    return array(array(
        'name'       => 'local_mahara',
        'apiversion' => 1,
        'methods'    => array(
            'get_artefacts_by_viewtype',
            'get_view_details',
            'get_user_ids',
            'get_friends',
            'add_friend',
            'remove_friend',
        ),
    ));
}

function get_artefacts_by_viewtype($viewtype, $remoteusername=null, $tag=null) {
    return new StdClass;
}

function get_view_details($id, $checkviewid) {
    return array();
}
function get_user_ids($users) {
    return array();
}

// Get all friends and open requests for a user
function get_friends($username) {
    global $CFG;
    $uname = addslashes($username);
    if ($data = get_records_sql("
        SELECT
            uf.userid || ':' || uf.friendid AS id,
            uf.userid, u.username, uf.friendid, f.username AS friendusername, uf.approved
        FROM
            {$CFG->prefix}user_friend uf
            JOIN {$CFG->prefix}user u ON uf.userid = u.id
            JOIN {$CFG->prefix}user f ON uf.friendid = f.id
        WHERE
            u.username = '$uname'
            OR (f.username = '$uname' AND uf.approved = 0)")) {
        return $data;
    }
    return array();
}

// Add request or confirm friendship
function add_friend($username, $friendusername) {
    if (!$userids = get_records_select(
        'user',
        "username = '" . addslashes($username) . "' OR username = '" . addslashes($friendusername) . "'",
        '',
        'username,id')) {
        return;
    }
    $userid = $userids[$username]->id;
    $friendid = $userids[$friendusername]->id;

    if (!$record = get_record('user_friend', 'userid', $friendid, 'friendid', $userid)) {
        if (!get_record('user_friend', 'userid', $userid, 'friendid', $friendid)) {
            // No open request, add one
            insert_record('user_friend', (object) array('userid' => $userid, 'friendid' => $friendid, 'approved' => 0));
        }
    } else if (!$record->approved) {
        // Approve existing request
        $record->approved = 1;
        update_record('user_friend', $record);
        insert_record('user_friend', (object) array('userid' => $userid, 'friendid' => $friendid, 'approved' => 1));
    }
}

// Remove friend or decline request
function remove_friend($username, $friendusername) {
    if (!$userids = get_records_select(
        'user',
        "username = '" . addslashes($username) . "' OR username = '" . addslashes($friendusername) . "'",
        '',
        'username,id')) {
        return;
    }
    $userid = $userids[$username]->id;
    $friendid = $userids[$friendusername]->id;
    delete_records_select('user_friend', "(userid = $userid AND friendid = $friendid) OR (userid = $friendid AND friendid = $userid)");
}

?>
