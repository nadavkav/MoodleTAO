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
 * show a list of users who are assigned to you
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();

$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$userid    = optional_param('user', 0, PARAM_INT);
$user = tao_user_parameter($userid);

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
if ($user->id == $USER->id) {
    $strheading = get_string('responsiblefor', 'local');
    require_capability('moodle/local:viewresponsibleusers', $sitecontext);
} else {
    $strheading = get_string('responsibleforbehalfof', 'local', fullname($user));
    $usercontext = get_context_instance(CONTEXT_USER, $user->id);
    require_capability('moodle/local:viewresponsibleusersbehalfof', $usercontext);
}

print_header($strheading, $strheading, build_navigation($strheading));

$users    = array();
$children = array(); // for STS only

if (has_capability('moodle/local:isst', $sitecontext, $user->id)) {
    $count = tao_count_mts($user);
    if ($users = tao_get_mts($user, null, $page, $perpage)) {
        $children = tao_get_uncertified_pts_of_mts($user, null, array_keys($users));
    }
} else if (has_capability('moodle/local:ismt', $sitecontext, $user->id)) {
    $count = tao_count_uncertified_pts($user);
    $users = tao_get_uncertified_pts($user, null, $page, $perpage);
}

if (!$users) {
    print_error('nousers', 'local', $CFG->wwwroot . '/local/user/find.php');
}

print_heading($strheading);
print_paging_bar($count, $page, $perpage, $CFG->wwwroot . '/local/user/responsible.php?');

$table = new StdClass;
$table->data = array();
$table->head = array('','',get_string('roletype', 'local'), get_string('certification', 'local'));

if (count($children) > 0) {
    $table->head[] = get_string('grandchildren', 'local');
}
foreach ($users as $user) {
    //get certification for this user
    $certification_status = get_records_select('tao_user_certification_status', "userid='$user->id' AND (status='submitted' or status='approved')");
    $certstring = '';
    if (empty($certification_status)) {
        $certstring = get_string('pendingcertification','block_tao_certification_path');
    } else {
        foreach ($certification_status as $cert) {
            if($cert->status=='approved') {
                if (!empty($certstring)) {
                    $certstring .= "<br/>";
                }
                $certstring .= get_string($cert->certtype, 'local');
            } else {
                $certstring .= "<a href='$CFG->wwwroot/blocks/tao_certification_path/approverequest.php?id=$cert->id'>".get_string('viewcertificationrequest','block_tao_certification_path')."</a> | ";
            }
        }
    }
    $certstring .= " <a href='$CFG->wwwroot/local/lp/certification.php?user=$user->id'>".get_string('reviewstatus','block_tao_certification_path')."</a>";
    $tmp = array(
        print_user_picture($user, SITEID, null, 0, true),
        '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&amp;course=' . SITEID . '">' . fullname($user) . '</a>',
        $user->rolename, $certstring
    );
    if (count($children) > 0) {
        $grandchildren = array();
        if (array_key_exists($user->id, $children)) {
            $grandchildren = $children[$user->id];
        }
        $g = '';
        foreach ($grandchildren as $grandchild) {
            $g .= '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $grandchild->id . '&amp;course=' . SITEID . '">' . fullname($grandchild) . '</a><br />';
        }
        $tmp[] = $g;
    }
    $table->data[] = $tmp;
}
print_table($table);

print_footer();

?>
