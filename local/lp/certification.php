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
 * @package   local
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_js(array('yui_yahoo', 'yui_animation', 'yui_dom', 'yui_event'));

require_login();
$userid = optional_param('user', $USER->id, PARAM_INT);

if (! ($user = get_record('user', 'id', $userid)) ) {
    error('Invalid userid');
}

$ismentor = false;
//check if user has rights to see this.
if ($userid <> $USER->id) {
   //has to be either an st or MT to be able to see other users cert path
   $usercontext = get_context_instance(CONTEXT_USER, $userid);
   if (!has_capability('moodle/local:isst', $usercontext) &&
      (!has_capability('moodle/local:ismt', $usercontext))) {
          error('you are not an MT or ST for this user!');
   }
   $ismentor = true;
}

$strheading = get_string('certification', 'local');

print_header($strheading, $strheading, build_navigation($strheading));
require_once($CFG->dirroot . '/local/lp/certificationjs.php');
    $certasks = tao_certificate_get_certification_tasks($userid);
    if (empty($certasks)) {
        if ($USER->id ==$userid) {
            notify(get_string('youarenotinlp','block_tao_certification_path'));
        } else {
            notify(get_string('thisptnotinlp','block_tao_certification_path'));
        }
    }
    if (!$ismentor) {
       echo "<h2>".get_string('learningpathstatus', 'local').":</h2>";
    } else {
       echo "<h2>".get_string('reviewstatus','block_tao_certification_path').": ".fullname($user)."</h2>";
       //check certification requests and print link if one exists.
       $certification_status = get_records_select('tao_user_certification_status', "userid='$userid' AND status='submitted'");
       if (!empty($certification_status)) {
           foreach ($certification_status as $cert) {
               $a = new stdClass;
               $courseshortname = get_field('course', 'shortname', 'id', $cert->courseid);
               $a->courselink = "<a href='$CFG->wwwroot/course/view.php?id=$cert->courseid'>$courseshortname</a>";
               $a->user = "<a href='$CFG->wwwroot/user/view.php?id=$user->id&course=$cert->courseid'>".fullname($user)."</a>";
               echo "<p>".get_string('userhasrequestedcertification','block_tao_certification_path', $a).": <a href='$CFG->wwwroot/blocks/tao_certification_path/approverequest.php?id=$cert->id'>".get_string('viewcertificationrequest','block_tao_certification_path')."</a></p>";

               //now print the tasks for this LP
               if (!empty($certasks[$cert->courseid])) {
                   $coursename = get_field('course', 'fullname', 'id', $cert->courseid);
                   echo "<h3><a href='$CFG->wwwroot/course/view.php?id=$cert->courseid'>".$coursename."</a></h3>";
                   echo $certasks[$cert->courseid];
                   unset($certasks[$cert->courseid]);
               }

           }
       }
    }

    // print activies required for certification
    if (!empty($certasks)) {
        print_certasks ($certasks);
    }
    $hascerts =  tao_display_certifications($userid);
    echo "<p>".$hascerts->text."<p>";
print_footer();

function print_certasks ($certasks) {
    global $CFG;
    foreach($certasks as $cid => $lp) {
        $coursename = get_field('course', 'fullname', 'id', $cid);
        echo "<h3><a href='$CFG->wwwroot/course/view.php?id=$cid'>".$coursename."</a></h3>";
        echo $lp;
    }
}
?>