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
 * this file should be used for all message-specific methods
 * and is included on-demand.
 *
 * functions should all start with the tao_ prefix.
 */


define('TM_OTHER_PTS_BY_LP', 1);             // PT message other PTs enrolled in a given LP
define('TM_OWN_PTS_BY_LP', 2);               // MT message their PTs enrolled in a given LP
define('TM_OWN_PTS', 3);                     // MT message all PTs (not specific to LP)
define('TM_OTHER_MTS', 4);                   // MT message all other MTs
define('TM_OWN_MTS', 5);                     // ST message all their MTs
define('TM_PTS_OF_OWN_MTS', 6);              // ST message PTs assigned to their MTs
define('TM_OTHER_STS', 7);                   // ST message other STs
define('TM_HTS', 8);                         // STs and HTs message STs
define('TM_OWN_ALUMNI', 9);                  // STs and MTs to message their PTs that have been certified
define('TM_ALUMNI_OF_OWN_MTS', 10);          // STs to message the alumni of their MTs
define('TM_ALL_ALUMNI', 11);                 // Admins to message all certified PTs
define('TM_ALUMNI_BY_LP', 12);               // Admins to message all certified PTs by the LP they were certified on
define('TM_OWN_UNCERTIFIED_PTS', 13);        // MTs to message all their uncertified PTs
define('TM_UNCERTIFIED_PTS_OF_OWN_MTS', 14); // STs to message all the uncertified PTs assigned to their MTs
define('TM_ALL_UNCERTIFIED_PTS', 15);        // Admins to message all uncertified PTs
define('TM_PTS_BY_LP', 16);                  // Admins to message all PTs enrolled in a given LP
define('TM_ANY_SINGLE_USER', 17);            // Any user sending a message to any other user
define('TM_OTHER_ADMINS', 18);               // (super +) Admins to message all other (super +) admins
define('TM_ALL_HEADEDITORS', 19);            // Learning path status process to notify "editorial board" (non-ui) 


function tao_message_target_get($target, $course) {
    $all = tao_message_targets();
    $targetobj = '';
    if ($course->id == SITEID) {
        $targetobj = $all['site'][$target];
    } else {
        $targetobj = $all['lp'][$target];
    }
    $targetobj = (object)$targetobj;
    $targetobj->key = $target;
    return $targetobj;
}

function tao_message_targets() {
    global $CFG;
    static $targets;
    if (!empty($targets)) {
        return $targets;
    }
    $targets = array(
        'site' => array(
            TM_OWN_PTS => array(
                'capability'        => 'canmessageownpts',
                'stringkey'         => 'allownpts',
                'recipientfunction' => array('count' => 'tao_count_uncertified_pts', 'users' => 'tao_get_uncertified_pts'),
            ),
            TM_OTHER_MTS => array(
                'capability'        => 'canmessagefellowmts',
                'stringkey'         => 'othermts',
                'recipientrole'     => ROLE_MT,
            ),
            TM_OWN_MTS => array(
                'capability'        => 'canmessageownmts',
                'stringkey'         => 'ownmts',
                'recipientfunction' => array('count' => 'tao_count_mts', 'users' => 'tao_get_mts'),
            ),
            TM_PTS_OF_OWN_MTS => array(
                'capability'        => 'canmessagemtspts',
                'stringkey'         => 'ptsofmts',
                'recipientfunction' => array('count' => 'tao_count_uncertified_pts_of_mts', 'users' => 'tao_get_uncertified_pts_of_mts'),
                'recipienttransform' => 'nested',
            ),
            TM_OTHER_STS => array(
                'capability'        => 'canmessagefellowsts',
                'stringkey'         => 'othersts',
                'recipientrole'     => ROLE_ST,
            ),
            TM_HTS => array(
                'capability'        => 'canmessagehts',
                'stringkey'         => 'hts',
                'recipientrole'     => ROLE_HEADTEACHER,
            ),
            TM_OWN_ALUMNI => array(
                'capability'        => 'canmessageownalumni',
                'stringkey'         => 'ownalumni',
                'recipientfunction' => array('count' => 'tao_count_certified_pts', 'users' => 'tao_get_certified_pts'),
            ),
            TM_ALUMNI_OF_OWN_MTS => array(
                'capability'        => 'canmessagemtsalumni',
                'stringkey'         => 'mtsalumni',
                'recipientfunction' => array('count' => 'tao_count_certified_pts_of_mts', 'users' => 'tao_get_certified_pts_of_mts'),
                'recipienttransform' => 'nested',
            ),
            TM_ALL_ALUMNI => array(
                'capability'        => 'canmessageallalumni',
                'stringkey'         => 'allalumni',
                'recipientrole'     => ROLE_CERTIFIEDPT,
            ),
            TM_OWN_UNCERTIFIED_PTS => array(
                'capability'        => 'canmessageownpts',
                'stringkey'         => 'ownuncertifiedpts',
                'recipientfunction' => array('count' => 'tao_count_uncertified_pts', 'users' => 'tao_get_uncertified_pts'),
            ),
            TM_UNCERTIFIED_PTS_OF_OWN_MTS => array(
                'capability'        => 'canmessagemtspts',
                'stringkey'         => 'uncertifiedptsofmts',
                'recipientfunction' => array('count' => 'tao_count_uncertified_pts_of_mts', 'users' => 'tao_get_uncertified_pts_of_mts'),
                'recipienttransform' => 'nested',
            ),
            TM_ALL_UNCERTIFIED_PTS => array(
                'capability'        => 'canmessageallpts',
                'stringkey'         => 'alluncertifiedpts',
                'recipientrole'     => ROLE_PT,
            ),
            TM_ANY_SINGLE_USER => array(
                'capability' => 'canmessageanyuser',
                'stringkey'  => 'anyotheruser',
                'otherurl'   => $CFG->wwwroot . '/message/index.php?tab=search',
            ),
            TM_OTHER_ADMINS => array(
                'capability'       => 'canmessagefellowadmins',
                'stringkey'        => 'otheradmins',
                'recipientroles'   => array(ROLE_ADMIN, ROLE_SUPERADMIN),
                'recipientcontext' => SYSCONTEXTID,
            ),
            TM_ALL_HEADEDITORS => array(
                'stringkey'        => 'headeditors',
                'recipientrole'    => ROLE_HEADEDITOR,
                'capability'       => '',
                'donotprint'       => true,
            ),
        ),
        'lp' => array(
            TM_OTHER_PTS_BY_LP => array(
                'capability'    => 'canmessagefellowpts',
                'stringkey'     => 'otherptsonlp',
                'recipientrole' => ROLE_PT,
            ),
            TM_OWN_PTS_BY_LP => array(
                'capability'        => 'canmessageownpts',
                'stringkey'         => 'ownptsonlp',
                'recipientfunction' => array('count' => 'tao_count_uncertified_pts', 'users' => 'tao_get_uncertified_pts'),
            ),
            TM_ALUMNI_BY_LP => array(
                'capability' => 'canmessagealumnibylp',
                'stringkey'  => 'allalumnionlp',
                'recipientrole' => ROLE_CERTIFIEDPT,
            ),
            TM_PTS_BY_LP => array(
                'capability'    => 'canmessageallpts',
                'stringkey'     => 'ptsonlp',
                'recipientrole' => ROLE_PT,
            ),
        ),
    );

    return $targets;
}


function tao_print_target($target, $course=null, $sitecheck=false) {
    global $CFG;

    if (!empty($target->donotprint)) {
        return;
    }

    if (empty($course)) {
        $course = get_site();
    }
    if ($sitecheck) {
        $context = get_context_instance(CONTEXT_COURSE, SITEID);
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
    }
    if (!has_capability('moodle/local:' . $target->capability, $context, null, false)) {
        return;
    }
    $url = $CFG->wwwroot . '/message/index.php?tab=byrole&target=' . $target->key . '&amp;lp=' . $course->id;
    if (!empty($target->otherurl)) {
        $url = $target->otherurl;
    }

    return '<a href="' . $url . '">' .
        get_string('messagetarget' . $target->stringkey, 'local') . '</a><br />';

}


function tao_message_count_recipients_by_target($target, $course, $user=null) {
    static $cache = array();
    $key = $target->key . ' | ' . $course->id;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $user = tao_user_parameter($user);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (!empty($target->recipientrole) || !empty($target->recipientroles)) {
        $roleid = false;
        $rolesql = '';
        if (!empty($target->recipientrole)) {
            $roleid = get_field('role', 'id', 'shortname', $target->recipientrole);
            $rolesql = " = $roleid";
        } else if (!empty($target->recipientroles)) {
            $rolesql = " IN ( '" . implode("','", $target->recipientroles) . "' ) ";
            $roleid = array_keys(get_records_select('role', 'shortname ' . $rolesql, '', 'id, id'));
            $rolesql = " IN ( '" . implode("','", $roleid) . "' ) ";
        }
        if (!empty($target->recipientcontext)) {
            $context = get_context_instance_by_id($target->recipientcontext);
        }
        $firstcount = count_role_users($roleid, $context);
        if ($assign = count_records_select('role_assignments', "userid = $user->id AND roleid $rolesql AND contextid = $context->id")) {
            $firstcount = $firstcount - $assign;
        }
        $count = $firstcount;
    } else if (is_array($target->recipientfunction)) {
        // recipientfunction
        $function = $target->recipientfunction['count'];
        $count = $function($user, $course);
    }
    $cache[$key] = $count;
    return $count;
}

function tao_message_get_recipients_by_target($target, $course, $user=null) {
    $user = tao_user_parameter($user);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (!empty($target->recipientrole) || !empty($target->recipientroles)) {
        if (!empty($target->recipientcontext)) {
            $context = get_context_instance_by_id($target->recipientcontext);
        }
        if (!empty($target->recipientrole)) {
            $roleid = get_field('role', 'id', 'shortname', $target->recipientrole);
        } else {
            $rolesql = " IN ( '" . implode("','", $target->recipientroles) . "' ) ";
            $roleid = array_keys(get_records_select('role', 'shortname ' . $rolesql, '', 'id, id'));
        }
        return get_role_users($roleid, $context);
    } else if (is_array($target->recipientfunction)) {
        // recipientfunction
        $function = $target->recipientfunction['users'];
        $users = $function($user, $course);
        if (empty($target->recipienttransform)) {
            return $users;
        }
        switch ($target->recipienttransform) {
            case 'nested':
                // these are the grandchild ones
                $newu = array();
                foreach ($users as $key => $children) {
                    $newu = array_merge($newu, $children);
                }
                return $newu;
        }
    }
}

?>
