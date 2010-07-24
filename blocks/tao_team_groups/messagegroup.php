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
 * sends messages to group members
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/forms.php');
$strheading = get_string('messagegroup', 'block_tao_team_groups');

$courseid = required_param('id', PARAM_INT);
$groupid = required_param('groupid', PARAM_INT);

if (! ($COURSE = get_record('course', 'id', $courseid)) ) {
    error('Invalid course idnumber');
}
if (!empty($groupid) && !($group = get_record('groups', 'id', $groupid, 'courseid', $courseid))) {
    error('Invalid group id');
}

require_login();

print_header_simple($strheading, $strheading, build_navigation($strheading));

if(groups_is_member($groupid)) {
    $grpmembers = groups_get_members($groupid);
    if (count($grpmembers) > 1) {
            require_once($CFG->dirroot . '/local/forms.php');
            require_once($CFG->dirroot . '/message/lib.php');
            $messageform = new tao_group_message_send_form('', array('course' => $COURSE, 'group' => $group, 'count' => count($grpmembers)-1));
            if ($data = $messageform->get_data()) {
                foreach($grpmembers as $touser) {
                    if ($touser->id <> $USER->id) { //don't send a message to yourself.
                        message_post_message($USER, $touser, $data->body, $data->format, 'direct');
                    }
                }
                notify(get_string('groupmessagesent','block_tao_team_groups'),'notifysuccess');
                print_continue($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);
            } else if (!$messageform->is_cancelled()) {
                $messageform->display();
                return;
            }
        
    } else {
        notify(get_string('messagenorecipients', 'block_tao_team_groups'));
    }

} else {
    error("you are not a member of this group");
}
    
print_footer();

?>