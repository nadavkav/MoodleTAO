<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   blocks-tao-team_groups
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class block_tao_team_groups extends block_base {

    function init() {
        $this->title = get_string('teamgroups', 'block_tao_team_groups');
        $this->version = 2008111202;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $USER,$CFG,$COURSE;

        $this->content = new stdClass;
        $this->content->text = '';

        if($COURSE->approval_status_id != COURSE_STATUS_PUBLISHED) {
            $this->content = NULL;
            return $this->content;
        }

        //check team groups information
        if ($COURSE->groupmode=='1') { //if groupmode for this course is set to seperate.
            $ptrole = get_record('role', 'shortname', ROLE_PT);
            $mtrole = get_record('role', 'shortname', ROLE_MT);
            $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
            $ispt = user_has_role_assignment($USER->id, $ptrole->id, $sitecontext->id);
            $ismt = user_has_role_assignment($USER->id, $mtrole->id, $sitecontext->id);
            if ($ispt or $ismt) { //check if logged in user is a PT or an MT
                //get user group.
                $groups = groups_get_all_groups($COURSE->id, $USER->id);
                if (empty($groups)) { //if user isn't in a Group - throw an error.
                    if ($ispt) {
                        $this->content->text .= get_string('nogroupset', 'block_tao_team_groups') . '<br/>';
                    }
                    $this->content->text .= tao_show_user_invites($USER->id, $COURSE->id);
                } else {
                    //now display list of groups and their members
                    foreach($groups as $group) {
                        $this->content->text .= "<strong>".get_string('group').":</strong> ".$group->name."<br/>";
                        //  get all members of this group
                        $grpmembers = groups_get_members($group->id);
                        $i = 0;
                        foreach ($grpmembers as $gm) {
                            $i++;
                            $this->content->text .= "<a href='$CFG->wwwroot/user/view.php?id=$gm->id&course=$COURSE->id'>".fullname($gm)."</a>";
                            if ($group->groupleader == $gm->id) {
                            $this->content->text .= '('.get_string('leader','block_tao_team_groups').')';
                            }
                            if ($group->groupleader == $USER->id && $gm->id <> $USER->id) {
                                //show delete link
                                $this->content->text .= "<a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=delete&userid=$gm->id'><img src='$CFG->wwwroot/pix/t/delete.gif'/></a>";
                            }
                            $this->content->text .='<br/>';
                        }
                        $invites = get_records('group_invites', 'groupid', $group->id);
                        $invitecount = 0;
                        if (!empty($invites)) {
                            foreach ($invites as $inv) {
                                $invitecount++;
                                $inuser = get_record('user', 'id', $inv->userid);
                                $this->content->text .= "<a href='$CFG->wwwroot/user/view.php?id=$inv->userid&course=$COURSE->id'>".fullname($inuser)."</a> (".get_string('invited', 'block_tao_team_groups').")";
                                //show delete link
                                if ($group->groupleader == $USER->id) {
                                    $this->content->text .= " <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=deleteinv&userid=$inv->userid'><img src='$CFG->wwwroot/pix/t/delete.gif'/></a>";
                                }
                                $this->content->text .='<br/>';
                            }
                        }
                        $this->content->text .='<br/>';
                        //get max number of group members
                        //check if groupleader and if max number of group members has not been exceeded and print invite link.
                        if ($group->groupleader == $USER->id && $CFG->groupmax > ($i+$invitecount)) {
                            $this->content->text .= "&rsaquo; <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id'>".get_string('invitegroupmembers', 'block_tao_team_groups')."</a><br/>";
                        }

                        if ($i > 1) { //if the group members is higher than 1 allow messaging.
                            $this->content->text .= "&rsaquo; <a href='$CFG->wwwroot/blocks/tao_team_groups/messagegroup.php?id=$COURSE->id&groupid=$group->id'>".get_string('messagegroup', 'block_tao_team_groups')."</a><br/>";
                        }
                        
                        //check if this is the only member left and display a remove membership and delete group option.
                        if (($i+$invitecount) == 1) {
                            $this->content->text .= "&rsaquo; <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=removegroup'>".get_string('deletegroup', 'block_tao_team_groups')."</a><br/>";
                        } elseif ($group->groupleader <> $USER->id) {
                            $this->content->text .= "&rsaquo; <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=delete&userid=$USER->id'>".get_string('removemefromgroup', 'block_tao_team_groups')."</a><br/>";
                        } elseif ($group->groupleader == $USER->id && $i > 1) {
                            $this->content->text .= "&rsaquo; <a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$COURSE->id&groupid=$group->id&action=transfer'>".get_string('transferleadership', 'block_tao_team_groups')."</a><br/>";
                        }
                    }
                }
                if (empty($groups) or $ismt) {
                    //print form
                    $this->content->text .= tao_new_group_form($COURSE->id);
                }
            }
        }
        if (empty($this->content->text)) {
            $this->content->text = get_string('nothingtodisplay');
        }
        $this->content->footer = '';

        return $this->content;
        }
    }
?>
