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
require_once($CFG->dirroot . '/local/lib/messagelib.php');
require_once($CFG->dirroot . '/message/lib.php');

    $id = required_param('id', PARAM_INT);    // Course Module ID
    $action = optional_param('action', '', PARAM_ALPHA);
 

    if (! $course = get_record('course', 'id', $id)) {
        error('course is misconfigured');
    }

    require_login($course->id);
    $strtitle = get_string('requestcertification', 'block_tao_certification_path');
    
    $navlins = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "", 'type' => 'misc');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple($strtitle, $strtitle, $navigation);
     
    if (get_record('tao_user_certification_status', 'userid', $USER->id, 'status', 'submitted')) {
        notify(get_string('certrequestexists', 'block_tao_certification_path'));
        print_footer(NULL, $course);
        die;
    }
    
    if ($action=='requestcert') {
        $newrequest = new stdclass();
        $newrequest->userid = $USER->id;
        $newrequest->courseid = $id;
        $newrequest->status = 'submitted';
        $newrequest->changeuserid = $USER->id;
        $newrequest->timechanged = time();
        $newrequest->certtype = 'certified_pt';
        if (!insert_record('tao_user_certification_status', $newrequest)) {
            notify("Error updating certification status");
            print_footer(NULL, $course);
            die;
        };
        $ptcontext = get_context_instance(CONTEXT_USER, $USER->id);
        $mtuser = get_users_by_capability($ptcontext,'moodle/local:ismt');
        $a = new stdclass();
        $a->user = fullname($USER);
        $emailtext = get_string('certemailrequesttext', 'block_tao_certification_path', $a)."<br><br><a href='$CFG->wwwroot/local/lp/certification.php?user=$USER->id'>$CFG->wwwroot/local/lp/certification.php?user=$USER->id</a>";
        foreach ($mtuser as $mt) {
            message_post_message($USER, $mt, addslashes($emailtext), FORMAT_HTML, 'direct');

        }
        notify(get_string('requestedcertification','block_tao_certification_path'),'notifysuccess');
    }
    
    //check current certification.
    if (record_exists('tao_user_certification_status', 'userid', $USER->id, 'certtype', 'certified_pt', 'status', 'approved')) { //error - this user already has certification - this page shouldn't have been loaded.
        notify("this user already has PT certification");
        print_footer(NULL, $course);
        die;
    }

    if(empty($action)) {
        $strreqcert = get_string('requestcertification', 'block_tao_certification_path');
        print_heading($strreqcert);
        print_box(get_string('requestcertificationdesc', 'block_tao_certification_path'));
        $options = new stdclass();
        $options->action = 'requestcert';
        $options->id = $id;
        
        echo '<div align="center">';
        print_single_button('request.php',$options, $strreqcert);
        echo '</div>';
    }
    

    print_footer(NULL, $course);
?>