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
 * Displays collaborative features for the current user
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('collaborationpagelib.php');
require_once($CFG->dirroot.'/tag/lib.php');

require_login();

$strheading = get_string('mycollaboration', 'local');

if (isguest()) {
    $wwwroot = $CFG->wwwroot.'/login/index.php';
    if (!empty($CFG->loginhttps)) {
        $wwwroot = str_replace('http:','https:', $wwwroot);
    }

    $a->page = $strheading;

    print_header($strheading);
    notice_yesno(get_string('noguest', 'local', $a).'<br /><br />'.get_string('liketologin'), $wwwroot, $CFG->wwwroot);
    print_footer();
    die();
}

$PAGE = page_create_object('my-collaboration', $USER->id);

$pageblocks = blocks_setup($PAGE,BLOCKS_PINNED_BOTH);

$blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

$PAGE->print_header($strheading);

echo '<table id="layout-table">';
echo '<tr valign="top">';

$lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
foreach ($lt as $column) {
    switch ($column) {
    case 'left':

    if(blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing()) {
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        print_container_end();
        echo '</td>';
    } else {
        echo '<td id="left-column"></td>';
    }

    break;
    case 'middle':    
        echo '<td valign="top" id="middle-column">';

        echo '<h2>' . get_string('mygroups', 'local') . '</h2>';

        /***  Start Groups Listing ***/

        $sql = "SELECT g.id, g.groupleader, g.name, g.courseid, c.id " . sql_as() . " courseid, c.fullname " . sql_as() . " coursename
                  FROM {$CFG->prefix}groups g, {$CFG->prefix}groups_members m, {$CFG->prefix}course c
                  WHERE m.userid = {$USER->id}
                    AND c.id = g.courseid
                    AND m.groupid = g.id";


        if ($groups = get_records_sql($sql)) {
        //print_object($groups);

            foreach ($groups as $group) {

                 $uri = $CFG->wwwroot . '/course/view.php?id=' . $group->courseid;

                 $members = groups_get_members($group->id);
                 if (isset($group->groupleader)) {
                     $leader = $members[$group->groupleader];
                 }

                 echo '<div class="my_collaboration_group_box">';

                 // define links
                 $messagelink = '<a href="' . $CFG->wwwroot. '/blocks/tao_team_groups/messagegroup.php?id=' . $group->courseid . '&groupid=' . $group->id .'">&raquo; '.strtolower(get_string('messageall', 'local')).'</a> ';
                 $invitelink = '<a href="' . $CFG->wwwroot. '/blocks/tao_team_groups/managegroup.php?id=' . $group->courseid . '&groupid=' . $group->id .'">&raquo; '.get_string('invite', 'local').'</a> ';
                 $viewlink = '<a href="' . $CFG->wwwroot. '/course/view.php?id=' . $group->courseid.'">&raquo; ' . strtolower(get_string('view')) . '</a> ';

                 // print quick function links
                 echo '<span class="my_collaboration_group_tools">';
                 if ($USER->id == $group->groupleader) {
                     echo $invitelink . $messagelink . $viewlink;
                 } else {
                     echo $messagelink . $viewlink;
                 }
                 echo '</span>';

                 echo '<h3>' . $group->name . ' in <a href="' . $uri . '">' . $group->coursename . '</a></h3>';

                 if (isset($leader)) {
                     echo '<p><b>' . get_string('leader', 'block_tao_team_groups') . ': </b><a href="' . $CFG->wwwroot . '/user/view.php?id=' . $leader->id . '">' . $leader->firstname . ' ' . $leader->lastname . '</a>';
                 }
                 echo '<br/><b>' . get_string('members', 'local') . ': </b>';

                 foreach ($members as $member) {
                     echo '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $member->id . '">' . $member->firstname . ' ' . $member->lastname . '</a> ';
                 }

                 echo '</p>';
                 echo '</div>';

            }

        } else {

            echo get_string('notinagroup', 'local');

        }


        /***  End Groups Listing ***/

        /***  Start Groups Invite ***/
        if ($invites = get_records('group_invites', 'userid', $USER->id)) {
            echo '<h3>'.get_string('groupinvites', 'block_tao_team_groups').'</h3>';
            echo '<ul>';
            foreach ($invites as $invite) {
                $grpinv = get_record('groups', 'id', $invite->groupid);
                if (empty($grpinv)) { //if empty, then this group doesn't exist so delete the invite!
                    delete_records('group_invites', 'groupid', $invite->groupid);
                } else {
                    $course = get_record('course', 'id', $invite->courseid);
                    echo "<li><a href='$CFG->wwwroot/course/view.php?id=$invite->courseid'>".$course->fullname.'</a> - '. get_string('group').':'.$grpinv->name."</li>";
                }
            }
            echo '</ul>';
        }

        /***  End Groups Invite ***/

        /***  Start Friends ***/

        echo '<div class="my_collaboration_friends_box">';

        $sql = "SELECT friendid as id, approved FROM {$CFG->prefix}user_friend WHERE userid = {$USER->id}";

        $friends = get_records_sql($sql);

        if (!empty($friends)) {
            $needsapproval = array();


            echo '<h2>'.get_string('myfriends', 'local').'</h2>';

            foreach ($friends as $friend) {
                if ($friend->approved) {
                    // not efficient but want to ensure getting all user fields. consider changing.
                    $user = get_record('user', 'id', $friend->id);
                    tao_print_friend_box($user);
                } else {
                    $needsapproval[] = $friend->id;
                }
            }

            if (!empty($needsapproval)) {
                echo '<h2 style="clear: left">'.get_string('mypendingfriends', 'local').'</h2>';

                foreach ($needsapproval as $friendid) {
                        // not efficient but want to ensure getting all user fields. consider changing.
                        $user = get_record('user', 'id', $friendid);
                        tao_print_friend_box($user, 'pending');
                }
            }

        }

        $friendrequests = get_records_select('user_friend', "friendid='$USER->id' AND approved='0'");
        if (!empty($friendrequests)) {
            echo '<h2 style="clear: left">'.get_string('friendrequests','local').'</h2>';
            foreach($friendrequests as $fr) {
                $user = get_record('user', 'id', $fr->userid);
                tao_print_friend_box($user, 'request');
            }
        }

        echo '</div>';
        
        /***  End Friends ***/

    break;
    case 'right':
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="right-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        print_container_end();
        echo '</td>';


    break;
    }
}

    /// Finish the page
    echo '</tr></table>';

print_footer();

?>
