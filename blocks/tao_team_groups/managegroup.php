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
 * @package    blocks-tao-team_groups
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * manages Team Groups
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/tao.php');
require_once($CFG->dirroot . '/tag/locallib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot . '/local/lib/messagelib.php');
require_once($CFG->dirroot . '/message/lib.php');

$strheading = get_string('manageteamgroup', 'block_tao_team_groups');

$courseid = required_param('id', PARAM_INT);
$groupname = optional_param('groupname', '', PARAM_ALPHANUM);
$groupid = optional_param('groupid', '', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$inviteuserid = optional_param('userid', '', PARAM_INT);

//used by search form
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page

if (! ($COURSE = get_record('course', 'id', $courseid)) ) {
    error('Invalid course idnumber');
}
if (!empty($groupid) && !($group = get_record('groups', 'id', $groupid, 'courseid', $courseid))) {
    error('Invalid group id');
}



require_login();

$ptrole = get_record('role', 'shortname', ROLE_PT); 
$mtrole = get_record('role', 'shortname', ROLE_MT);
$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
$ispt = user_has_role_assignment($USER->id, $ptrole->id, $sitecontext->id);
$ismt = user_has_role_assignment($USER->id, $mtrole->id, $sitecontext->id);

print_header_simple($strheading, $strheading, build_navigation($strheading));
if ($action=='joingroup') {
    if ($COURSE->groupmode=='1') { //if groupmode for this course is set to seperate.
        $groups = groups_get_all_groups($COURSE->id, $USER->id);
        if (empty($groups)) { //if user isn't in a Group - display invites and add group stuff.
            echo tao_show_user_invites($USER->id, $COURSE->id);
            echo tao_new_group_form($COURSE->id);
            print_footer();
            exit;   
        } else {
            notify(get_string('alreadyinagroup', 'block_tao_team_groups'));
            print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
            print_footer();
            exit;   
        }
    }
} elseif (!empty($groupname) && ($ispt or $ismt)) {
    $groups = groups_get_all_groups($courseid, $USER->id);
    if (!empty($groups) && !$ismt) { //PTS can only be a member of one group.
        notify(get_string('alreadyinagroup', 'block_tao_team_groups'));
        print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
        print_footer();
        exit;   
    } else {
        if (record_exists('groups', 'name', $groupname)) {
            notify(get_string('groupexists', 'block_tao_team_groups'));
            print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
            print_footer();
            exit;
        } else {
            //create new group.
            $newgroup = new stdClass;
            $newgroup->name = $groupname;
            $newgroup->picture = 0;
            $newgroup->hidepicture = 0;
            $newgroup->timecreated = time();
            $newgroup->timemodified = time();
            $newgroup->courseid = $courseid;
            $newgroup->groupleader = $USER->id;
            if (!$groupid = insert_record('groups', $newgroup)) {
                error('could not insert record into groups table');
            }

            tao_check_enrol($USER->id, $courseid);  //check to see if user is enrolled in the course - if not then enrol them!

            //now assign $USER as a member of the group.
            $newgroupmember = new stdClass;
            $newgroupmember->groupid = $groupid;
            $newgroupmember->userid = $USER->id;
            $newgroupmember->timeadded = time();
            if (!$groupid = insert_record('groups_members', $newgroupmember)) {
                error('could not insert record into groups members table');
            }

            $group = get_record('groups', 'id', $groupid);
            notify(get_string('groupcreated', 'block_tao_team_groups'), 'notifysuccess');
            print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
        }
    }
}
if (!empty($groupid)) {
    if (!empty($action)) {
       if ($action=='delete' or $action=='deleteconfirm' or $action=='deleteinv' or $action=='deleteinvconfirm') { //show confirmation page
            $deleteuser = required_param('userid', PARAM_INT);
            //allow users to delete their own assignment as long as they aren't the team leader, and allow team leaders to delete other assignments
            if (($USER->id == $deleteuser && $group->groupleader <> $deleteuser) || ($group->groupleader == $USER->id && $deleteuser <> $USER->id)) {
                if($action=='delete' or $action=='deleteinv') {
                    $deluser = get_record('user', 'id', $deleteuser);
                    $a->name = fullname($deluser);
                    $a->group = $group->name;
                    notice_yesno(get_string('removefromgroup','block_tao_team_groups', $a), $CFG->wwwroot."/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&userid=$deleteuser&action=$action"."confirm", $CFG->wwwroot."/course/view.php?id=$courseid");
                } elseif($action=='deleteconfirm') {
                    delete_records('groups_members','groupid', $group->id, 'userid', $deleteuser);
                    //now e-mail group leader and group member regarding deletion
                    group_send_email($group->groupleader, $USER->id, $group, $action);  //email group leader.
                    group_send_email($deleteuser, $USER->id, $group, $action);          //email group member.
                    notify(get_string('memberdeleted', 'block_tao_team_groups'), 'notifysuccess');
                    print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
                } elseif($action=='deleteinvconfirm') {
                    delete_records('group_invites','groupid', $group->id, 'userid', $deleteuser);
                    //now e-mail group leader and group member regarding deletion.
                    group_send_email($group->groupleader, $USER->id, $group, $action);  //email group leader.
                    group_send_email($deleteuser, $USER->id, $group, $action);          //email group member.

                    notify(get_string('invitedeleted', 'block_tao_team_groups'), 'notifysuccess');
                    print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
                }
            } else {
                error("you cannot delete this user");
            }
       } elseif ($action=="accept" or $action=="decline") { //show confirmation page.
           notice_yesno(get_string($action.'invite','block_tao_team_groups'), $CFG->wwwroot."/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&action=confirm$action", $CFG->wwwroot."/course/view.php?id=$courseid");
       } elseif($action=="confirmaccept" or $action=="confirmdecline") {        
           //check if this is a valid invite.
           $invite = get_record('group_invites', 'userid', $USER->id, 'courseid', $courseid, 'groupid', $groupid);
           if (empty($invite)) {
               error("invalid invite");
           }
           if($action=="confirmdecline") { //delete invite
               delete_records('group_invites', 'id', $invite->id);
               notify(get_string('invitedeclined', 'block_tao_team_groups'), 'notifysuccess');
           } else { //add this user to the group.
               tao_check_enrol($USER->id, $courseid);  //check to see if user is enrolled in the course - if not then enrol them!
               $newgroupmember = new stdClass;
               $newgroupmember->groupid = $groupid;
               $newgroupmember->userid = $USER->id;
               $newgroupmember->timeadded = time();
               if (!insert_record('groups_members', $newgroupmember)) {
                   error('could not insert record into groups members table');
               }
               delete_records('group_invites', 'id', $invite->id); //delete this invite.

               //now decline all other invites for this course!
               $invites = get_records_select('group_invites', "userid='$USER->id' AND courseid='$courseid'");
               if (!empty($invites)) {
                   foreach($invites as $invd) {
                       group_send_email($invd->fromuserid, $USER->id, $group, $action);
                       group_send_email($group->groupleader, $invd->fromuserid, $group, $action);
                   }
                   delete_records('group_invites', 'userid', $USER->id, 'courseid', $courseid);
               }
               notify(get_string('inviteaccepted', 'block_tao_team_groups'), 'notifysuccess');
           }
           //send e-mails.
           group_send_email($invite->fromuserid, $invite->fromuserid, $group, $action);
           group_send_email($group->groupleader, $invite->fromuserid, $group, $action);
           print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
       } elseif($action=='removegroup' or $action=='removegroupconfirm')  {
           //first check to see if this group can be removed.
           $groupcount = count_records('groups_members','groupid', $groupid);
           if ($groupcount == 1 && groups_is_member($groupid, $USER->id)) {
               if ($action=='removegroup') {
                   $a->group = $group->name;
                   notice_yesno(get_string('removegroup','block_tao_team_groups', $a), $CFG->wwwroot."/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&action=$action"."confirm", $CFG->wwwroot."/course/view.php?id=$courseid");
               } elseif ($action=='removegroupconfirm') {
                   //remove this user from the group and delete the group.
                   require_once($CFG->dirroot.'/group/lib.php');
                   groups_delete_group($groupid);
                   notify(get_string('groupdeleted', 'block_tao_team_groups'),'notifysuccess');
                   print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
               }
           } else {
               error('you cannot delete this group');
           }
       } elseif ($action=='transfer' or $action=='transferuser' or $action=='transferconfirm') {
           if ($group->groupleader == $USER->id) {
               if ($action=='transfer') {
                   print_heading(get_string('transferleadership','block_tao_team_groups'));
                   print_string('selecttransferuser','block_tao_team_groups');
                   echo "<br/>";
                   //TODO display list of users that leadership can be transferred to.
                   $grpmembers = groups_get_members($group->id);
                   $i = 0;
                   foreach ($grpmembers as $gm) {
                       if ($i > 0) {
                           echo "<br/>";
                       }
                       if ($gm->id <> $USER->id) {
                           echo "<a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=transferuser&userid=$gm->id'>".fullname($gm)."</a>";
                           $i++;
                       }
                   }
               } elseif ($action=='transferuser') {
                   $userid = required_param('userid', PARAM_INT);
                   $a->group = $group->name;
                   $a->user = fullname(get_record('user', 'id', $userid));
                   notice_yesno(get_string('transferuser','block_tao_team_groups', $a), $CFG->wwwroot."/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&action=transferconfirm&userid=$userid", $CFG->wwwroot."/course/view.php?id=$courseid");
               } elseif ($action=='transferconfirm') {
                   $userid = required_param('userid', PARAM_INT);
                   $group->groupleader = $userid;
                   update_record('groups', $group);
                   //now e-mail new group leader regarding transfer
                   group_send_email($group->groupleader, $USER->id, $group, $action);  //email group leader.
                   
                   notify(get_string('transferconfirmed', 'block_tao_team_groups'),'notifysuccess');
                   print_continue($CFG->wwwroot."/course/view.php?id=$COURSE->id");
               }
           } else {
               error("you are not the leader of this group");
           }
       } elseif ($action=='inviteuser' && !empty($inviteuserid) && isset($group->groupleader) && $group->groupleader == $USER->id) {
           if ($user = get_record('user', 'id', $inviteuserid)) {
                //check this users group.
                $usergroups = groups_get_all_groups($courseid, $user->id); 
                if (!empty($usergroups)) {
                    notify(get_string('useralreadyingroup', 'block_tao_team_groups'));
                } else {
                    //send invite to user.
                    group_send_invite($user->id, $USER->id, $group, $courseid);
                }
                print_continue($CFG->wwwroot."/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid");
           }
       } else {
           error("Invalid Action");
       }
    }
    if (empty($action) && isset($group->id)) {
        print_heading(get_string('groupmembers','block_tao_team_groups'));
        $grpmembers = groups_get_members($group->id);
        $i = 0;
        foreach ($grpmembers as $gm) {
            if ($i > 0) {
                echo "<br/>";
            }
            echo "<a href='$CFG->wwwroot/user/view.php?id=$gm->id&course=$COURSE->id'>".fullname($gm)."</a>";
            $i++;
        }
    }
    if (isset($group->id) && empty($action) && $group->groupleader == $USER->id) { //don't show invites or the ability to invite people as this is an accept/decline request.
        $invites = get_records('group_invites', 'groupid', $group->id);
        $invitecount = 0;
        if (!empty($invites)) {
            print_heading(get_string('groupinvites','block_tao_team_groups'));
            foreach ($invites as $inv) {
                $inuser = get_record('user', 'id', $inv->userid);
                if ($invitecount > 0) {
                    echo "<br/>";
                }
                echo "<a href='$CFG->wwwroot/user/view.php?id=$gm->id&course=$COURSE->id'>".fullname($inuser)."</a>";
                $invitecount++;
            }
        }

        print_heading(get_string('inviteauser','block_tao_team_groups'), '','3');
        $interestedusers = tao_print_similar_users_course($courseid, $groupid);
        if (!empty($interestedusers)) { 
            print_heading(get_string('similarusers', 'block_tao_team_groups'));
            foreach ($interestedusers as $u) {
                // not efficient but want to ensure getting all user fields. consider changing.
                $useri = get_record('user', 'id', $u->id);
                echo "<a href='$CFG->wwwroot/user/view.php?id=$groupid&course=$courseid'>".fullname($useri)."</a>
                      <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&action=inviteuser&userid=$u->id'>".get_string('invitethisuser','block_tao_team_groups')."</a><br/>";
            }
        }
        print_heading(get_string('searchforusers', 'block_tao_team_groups'));
        if ($CFG->groupmax > ($i+$invitecount)) {     //check if max number of group members has not been exceeded and print invite link.
            echo "<p>".get_string('searchforusersdesc','block_tao_team_groups')."</p>";
//print search form
    $site = get_site();

    // create the user filter form
    $ufiltering = new user_filtering(array('realname'=>0, 'lastname'=>1, 'firstname'=>1, 'email'=>0, 'city'=>1, 'country'=>1,
                                'profile'=>1, 'mnethostid'=>1), null, array('id'=>$courseid, 'groupid'=>$groupid,'perpage'=>$perpage, 'page'=>$page,'sort'=>$sort,'dir'=>$dir));
    $extrasql = $ufiltering->get_sql_filter();
    if (!empty($extrasql)) { //don't bother to do any of the following unless a filter is already set!
        //exclude users already in a group inside this course.
        $extrasql .= "AND id NOT IN (SELECT userid 
                                  FROM {$CFG->prefix}groups_members gm, {$CFG->prefix}groups g 
                                  WHERE g.courseid={$courseid} AND g.id=gm.groupid) ";
        //exclude users already invited.
        $extrasql .= "AND id NOT IN (SELECT userid
                                  FROM {$CFG->prefix}group_invites
                                  WHERE courseid={$courseid} AND groupid={$groupid} ) ";
                                  
        $columns = array("firstname", "lastname", "city", "country", "lastaccess");

        foreach ($columns as $column) {
            $string[$column] = get_string("$column");
            if ($sort != $column) {
                $columnicon = "";
                if ($column == "lastaccess") {
                    $columndir = "DESC";
                } else {
                    $columndir = "ASC";
                }
            } else {
                $columndir = $dir == "ASC" ? "DESC":"ASC";
                if ($column == "lastaccess") {
                    $columnicon = $dir == "ASC" ? "up":"down";
                } else {
                    $columnicon = $dir == "ASC" ? "down":"up";
                }
                $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" alt=\"\" />";

            }
            $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
        }

        if ($sort == "name") {
            $sort = "firstname";
        }

        $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '', $extrasql);
        $usersearchcount = get_users(false, '', true, "", "", '', '', '', '', '*', $extrasql);

        $alphabet = explode(',', get_string('alphabet'));
        $strall = get_string('all');

        print_paging_bar($usersearchcount, $page, $perpage,
                "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");
    
        flush();

        if (!$users) {
            $match = array();
            $table = NULL;
            print_heading(get_string('nousersfound'));
        } else {
            $countries = get_list_of_countries();
            if (empty($mnethosts)) {
                $mnethosts = get_records('mnet_host', '', '', 'id', 'id,wwwroot,name');
            }

            foreach ($users as $key => $user) {
                if (!empty($user->country)) {
                    $users[$key]->country = $countries[$user->country];
                }
            }
            if ($sort == "country") {  // Need to resort by full country name, not code
                foreach ($users as $user) {
                    $susers[$user->id] = $user->country;
                }
                asort($susers);
                foreach ($susers as $key => $value) {
                    $nusers[] = $users[$key];
                }
                $users = $nusers;
            }

            $mainadmin = get_admin();

            $override = new object();
            $override->firstname = 'firstname';
            $override->lastname = 'lastname';
            $fullnamelanguage = get_string('fullnamedisplay', '', $override);
            if (($CFG->fullnamedisplay == 'firstname lastname') or
                ($CFG->fullnamedisplay == 'firstname') or
                ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
                $fullnamedisplay = "$firstname / $lastname";
            } else { // ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'lastname firstname') 
                $fullnamedisplay = "$lastname / $firstname";
            }
            $table->head = array ($fullnamedisplay, $city, $country, $lastaccess, "");
            $table->align = array ("left", "left", "left", "left", "center", "center", "center");
            $table->width = "95%";
            foreach ($users as $user) {
                if ($user->username == 'guest') {
                    continue; // do not dispaly dummy new user and guest here
                }

                if ($user->lastaccess) {
                    $strlastaccess = format_time(time() - $user->lastaccess);
                } else {
                    $strlastaccess = get_string('never');
                }
                $fullname = fullname($user, true);

                $table->data[] = array ("<a href=\"../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>",
                                    "$user->city",
                                    "$user->country",
                                    $strlastaccess,
                                    "<a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$groupid&action=inviteuser&userid=$user->id'>".get_string('invitethisuser','block_tao_team_groups')."</a><br/>");
            }
        }
    }
    // add filters
    $ufiltering->display_add();
    $ufiltering->display_active();

    if (!empty($table)) {
        print_table($table);
        print_paging_bar($usersearchcount, $page, $perpage,
                         "user.php?sort=$sort&amp;dir=$dir&amp;perpage=$perpage&amp;");
    }


//end of search form printing



        } else {
            print_string('groupfull','block_tao_team_groups');
        }
    }
} else {
    notify('invalid request');
}
     
print_footer();
function group_send_invite($userid, $fromuserid, $group, $courseid) {
    global $CFG,$COURSE;
    
    if (record_exists('group_invites', 'courseid', $courseid, 'userid', $userid, 'groupid', $group->id)) {
        notify(get_string('invitealready','block_tao_team_groups'));
    } else {
        $invite = new stdclass();
        $invite->courseid = $courseid;
        $invite->userid = $userid;
        $invite->fromuserid = $fromuserid;
        $invite->groupid = $group->id;
        $invite->timemodified = time();
        insert_record('group_invites', $invite);
        //now send e-mail
        $sendto = get_record('user', 'id', $userid);
        $sendfrom = get_record('user', 'id', $fromuserid);
        $a = new stdclass();
        $a->firstname = $sendto->firstname;
        $a->group = $group->name;
        $a->course = $COURSE->fullname;
        $a->link = '<a href="'.$CFG->wwwroot."/course/view.php?id=$courseid".'">'.$CFG->wwwroot."/course/view.php?id=$courseid</a>";
        message_post_message($sendfrom, $sendto, addslashes(get_string('inviteemailbody','block_tao_team_groups',$a)), FORMAT_HTML, 'direct');

        notify(get_string('invitesent','block_tao_team_groups'),'notifysuccess');
    }
}

function group_send_email($touserid, $fromuserid, $group, $action) {
           global $COURSE;
           $sendto = get_record('user', 'id', $touserid);
           $sendfrom = get_record('user','id', $fromuserid);
           $a = new stdclass();
           $a->firstname = $sendto->firstname;
           $a->user = fullname($sendfrom);
           $a->course = $COURSE->fullname;
           $a->group = $group->name;
           email_to_user($sendto, $sendfrom, get_string($action.'emailsubject','block_tao_team_groups'), get_string($action.'emailbody','block_tao_team_groups', $a));
}
?>