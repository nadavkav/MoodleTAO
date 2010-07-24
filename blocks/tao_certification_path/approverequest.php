<?PHP

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
 * @package   blocks-tao-certification_path
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
    require_once('../../config.php');
    require_once($CFG->dirroot.'/lib/formslib.php');
    require_once($CFG->dirroot . '/local/lib/messagelib.php');
    require_once($CFG->dirroot . '/message/lib.php');

    $id = required_param('id', PARAM_INT);    // Course Module ID
    $action = optional_param('action', '', PARAM_ALPHA);
    $description = optional_param('description', '', PARAM_TEXT);

    if (! $certrequest = get_record('tao_user_certification_status', 'id', $id)) {
        error('Invalid Certification');
    }
    if (! $course = get_record('course','id', $certrequest->courseid)) {
        error('Invalid Course');
    }
    if ($certrequest->status <> 'submitted') {
        error('Invalid Certification');
    }
    require_login($course->id);
    $strtitle = get_string('approvecertification', 'block_tao_certification_path');
    
    //check this user is an MT for this user.
    $ptcontext = get_context_instance(CONTEXT_USER, $certrequest->userid);
    require_capability('moodle/local:ismt',$ptcontext);
    
    $navlinks = array();
    $navlinks[] = array('name' => get_string('certification', 'block_tao_certification_path'), 'link' => "$CFG->wwwroot/local/lp/certification.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strtitle, 'link' => "", 'type' => 'misc');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple($strtitle, $strtitle, $navigation);
 
    if ($action=='approved') {
        $certrequest->status = 'approved';
        $certrequest->changeuserid = $USER->id;
        $certrequest->timechanged = time();
        $certrequest->description = $description;
        if (!update_record('tao_user_certification_status', $certrequest)) {
            notify("Error updating certification status");
            print_footer(NULL, $course);
            die;
        };
        //now save event
        $eventdata = new object();
        $eventdata->userid = $certrequest->userid;
        $eventdata->course = $course->id;
        $eventdata->certification = $certrequest->certtype;
        $eventdata->certificationid = $certrequest->id;
        events_trigger('certification_updated', $eventdata);
        
        $mtuser = get_record('user', 'id', $certrequest->userid);
        $emailtext = get_string('certemailapprovetext', 'block_tao_certification_path')."<br><br>".$description;
        message_post_message($USER, $mtuser, addslashes($emailtext), FORMAT_HTML, 'direct');
        notify(get_string('certificationapproved','block_tao_certification_path'),'notifysuccess');
    } elseif ($action=='declined') {
        $certrequest->status = 'declined';
        $certrequest->changeuserid = $USER->id;
        $certrequest->timechanged = time();
        $certrequest->description = $description;
        if (!update_record('tao_user_certification_status', $certrequest)) {
            notify("Error updating certification status");
            print_footer(NULL, $course);
            die;
        };
        $mtuser = get_record('user', 'id', $certrequest->userid);
        $emailtext = get_string('certemaildeclinetext', 'block_tao_certification_path')."<br><br>".$description;
        message_post_message($USER, $mtuser, addslashes($emailtext), FORMAT_HTML, 'direct');
        notify(get_string('certemaildeclinesubject','block_tao_certification_path'),'notifysuccess');
    }
    
    if(empty($action)) {
        $ptuser = get_record('user', 'id', $certrequest->userid);
        echo '<div class="approvecertform">';
        echo '<div class="label">'.get_string('name') . ":</div><div class=\"content\"><a href='$CFG->wwwroot/user/view.php?id=$certrequest->userid&course=$certrequest->courseid'>".fullname($ptuser)."</a></div>";
        echo '<div class="label">'.get_string('course').":</div><div class=\"content\"><a href='$CFG->wwwroot/course/view.php?id=$certrequest->courseid'>$course->shortname</a></div>";
        echo "<div class=\"reviewstatus\"><a href='$CFG->wwwroot/local/lp/certification.php?user=$certrequest->userid'>".get_string('reviewstatus','block_tao_certification_path').'</a></div>';
        echo "<div class=\"sendmessage\"><form onclick=\"this.target='message$certrequest->userid'\" action=\"../message/discussion.php\" method=\"get\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$certrequest->userid\" />";
        echo "<input type=\"submit\" value=\"".get_string("sendmessage", "message")."\" onclick=\"return openpopup('/message/discussion.php?id=$certrequest->userid', 'message_$certrequest->userid', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);\" />";
        echo "</form>";
        echo "</div></div>";
        
        //print previous Certification requests
        $sql = "userid='$certrequest->userid' AND courseid='$certrequest->courseid' AND status='declined'";
        $prevrequests = get_records_select('tao_user_certification_status',$sql);
        if (!empty($prevrequests)) {
            print_heading(get_string('previousrequests', 'block_tao_certification_path'));
            $table = new stdclass();
            $table->head = array(get_string('time'), get_string('changedby','block_tao_certification_path'), get_string('status'), get_string('description'));
            foreach ($prevrequests as $prev) {
                if ($USER->id == $prev->changeuserid) { 
                    $changeuser = $USER;
                } else {
                    $changeuser = get_record('user', 'id', $prev->changeuserid);
                }
                $table->data[] = array(userdate($prev->timechanged), fullname($changeuser), $prev->status, $prev->description);
            }
             print_table($table);
        }
        print_heading($strtitle);
        
        require_once('approve_form.php');
        $userform = new approve_form();
        $userform->display();

    }

    print_footer(NULL, $course);

function print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}
?>