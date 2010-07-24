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
 * this file should be used for all tao-specific methods
 * and will be included automatically in local/lib.php along
 * with other core libraries.
 *
 * functions should all start with the tao_ prefix.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

// course status definitions - for learning path implementation
define('COURSE_STATUS_NOTSUBMITTED', 1);
define('COURSE_STATUS_SUBMITTED', 2);
define('COURSE_STATUS_NEEDSCHANGE', 3);
define('COURSE_STATUS_RESUBMITTED', 4);
define('COURSE_STATUS_APPROVED', 5);
define('COURSE_STATUS_PUBLISHED', 6);
define('COURSE_STATUS_SUSPENDEDDATE', 7);
define('COURSE_STATUS_SUSPENDEDAUTHOR', 8);

// learning path mode
define('LEARNING_PATH_MODE_STANDARD', 1);
define('LEARNING_PATH_MODE_RAFL', 2);

// tao specific role shortnames
define('ROLE_HEADTEACHER', 'headteacher');
define('ROLE_PT', 'participatingteacher');
define('ROLE_MT', 'masterteacher');
define('ROLE_ST', 'seniorteacher');
define('ROLE_TEMPLATEEDITOR', 'templateeditor');
define('ROLE_LPCREATOR', 'lpcreator');
define('ROLE_LPEDITOR', 'lpeditor');
define('ROLE_LPAUTHOR', 'lpauthor');
define('ROLE_LPCONTRIBUTOR', 'lpcontributor');
define('ROLE_HEADEDITOR', 'headeditor');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPERADMIN', 'superadmin');
define('ROLE_USER', 'user'); // authenticated user role - assign caps to this for everyone
define('ROLE_CERTIFIEDPT', 'certifiedpt');

/**
 *
 * takes a role shortname and assigns it to the given user and context.
 *   note: you should use a constant when passing in a shortname,  so if you
 *         need to use this function define a constant for the role
 *         you're assigning.
 * 
 *   usage example: tao_role_assign_by_shortname(ROLE_LPEDITOR, $USER->id, $context->id);
 *
 * @param string $shortname  
 * @param integer $userid  
 * @param integer $contextid  
 *
 * @return bool 
*/
function tao_role_assign_by_shortname ($shortname, $userid, $contextid) {
    // look up roleid based on shortname
    $roleid = get_field('role', 'id', 'shortname', $shortname);

    if (!empty($roleid)) {
        if(role_assign($roleid, $userid, 0, $contextid)) {
            return true;
        }
    }
}

/**
 *
 * takes a role shortname and unassigns it to the given user and context.
 * 
 *   usage example: tao_role_unassign_by_shortname(ROLE_LPEDITOR, $USER->id, $context->id);
 *
 * @param string $shortname  
 * @param integer $userid  
 * @param integer $contextid  
 *
 * @return bool 
*/
function tao_role_unassign_by_shortname ($shortname, $userid, $contextid) {
    // look up roleid based on shortname
    $roleid = get_field('role', 'id', 'shortname', $shortname);

    if (!empty($roleid)) {
        if(role_unassign($roleid, $userid, 0, $contextid)) {
            return true;
        }
    }
}

/**
 *
 * returns an array of users with the learning path author role for the given context. 
 * 
 * usage example: $authors = tao_get_lpauthors($context);  
 *
 * @param object $context  
 *
 * @return array 
*/

function tao_get_lpauthors($context) {
    $roleid = get_field('role', 'id', 'shortname', ROLE_LPAUTHOR);
    return get_role_users($roleid, $context);
}

/**

 * helper function to get head editors for the given context
 * 
 * usage example: $editors = tao_get_headeditors($context);  
 *
 * @param object $context  
 *
 * @return array 
*/

function tao_get_headeditors($context) {
    $roleid = get_field('role', 'id', 'shortname', ROLE_HEADEDITOR);
    return get_role_users($roleid, $context);
}

/**

 * helper function to get template editors for the given context
 * 
 * usage example: $editors = tao_get_templateeditors($context);  
 *
 * @param object $context  
 *
 * @return array 
*/

function tao_get_templateeditors($context) {
    $roleid = get_field('role', 'id', 'shortname', ROLE_TEMPLATEEDITOR);
    return get_role_users($roleid, $context);
}

/**

 * helper function to get the learning path contributors for the given context
 * 
 * usage example: $editors = tao_get_lpcontributors($context);  
 *
 * @param object $context  
 *
 * @return array 
*/

function tao_get_lpcontributors($context) {
    $roleid = get_field('role', 'id', 'shortname', ROLE_LPCONTRIBUTOR);
    return get_role_users($roleid, $context);
}

/**
 * Event handling for when a learning path is submitted for approval.
 * Currently just handles notifications to the appropriate editors.
 */

function tao_handle_learning_path_submission_event($eventdata) {
    global $CFG;

    require_once($CFG->dirroot . '/local/lib/messagelib.php');
    require_once($CFG->dirroot . '/message/lib.php');

    // load our user
    $user = get_record('user', 'id', $eventdata['userid']);

    if (empty($user)) {
        mtrace("Invalid user");
        return;
    }
    
    // load our course
    $course = get_record('course', 'id', $eventdata['courseid']);

    if (empty($course)) {
        mtrace("Invalid course");
        return;
    }

    $format = FORMAT_HTML; 

    // compose message body
    $body  = get_string('lpsubmitted', 'local') . '.<br />';
    $body .= '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '" target="_blank">' . $course->fullname . '</a> ';
    $body .= get_string('submittedby', 'local') . ' ' . $user->firstname . ' ' . $user->lastname . '.<br />';
    $body .= '"' . $eventdata['reason'] . '"';

    // look for any head editors for this course that might already exist
    $editors = tao_get_headeditors(get_context_instance(CONTEXT_COURSE, $course->id));  

    if (!empty($editors)) {
        // Notify existing editors of the learning path that it is ready for review

        foreach($editors as $editor) {
            if ($editor->id == $user->id) {
                continue;
	    }
            message_post_message($user, $editor, $body, $format, 'direct');
        }

    } else {
        // Notify ALL editors that the learning path needs an editor 

        $site = get_record('course', 'id', SITEID);

        // use the targetted messaging api for tao
        $targetobject = (object)tao_message_target_get(TM_ALL_HEADEDITORS, $site);

        if ($count = tao_message_count_recipients_by_target($targetobject, $site)) {
            $targetobject->key = TM_ALL_HEADEDITORS; 

            $eventdata = array(
                'body'   => $body,
                'from'   => $user->id,
                'format' => $format,
                'course' => $site,
                'target' => $targetobject,
            );
            events_trigger('tao_message_role', $eventdata); 
            echo get_string('messagequeued', 'local');

        }

    }

    return true;

}

/**
 * event handler for messaging by role
 */
function tao_handle_message_role_event($eventdata) {
    global $CFG;
    require_once($CFG->dirroot . '/local/lib/messagelib.php');
    require_once($CFG->dirroot . '/message/lib.php');
    if (!$tomessage = tao_message_get_recipients_by_target($eventdata['target'], $eventdata['course'], $eventdata['from'])) {
        return true; // done!
    }
    $a = (object)array(
        'target' => get_string('messagetarget' . $eventdata['target']->stringkey, 'local'),
        'course' => $eventdata['course']->fullname
    );
    if ($eventdata['course']->id == SITEID) {
        $footer = get_string('messagelistfooter', 'local', $a);
    }
    else {
        $footer = get_string('messagelistfootercourse', 'local', $a);
    }
    $eventdata['body'] .= '<br /><br />' . $footer;
    $fromuser = get_record('user', 'id', $eventdata['from']);
    foreach ($tomessage as $user) {
        if ($fromuser->id == $user->id) {
            continue;
        }
        message_post_message($fromuser, $user, $eventdata['body'], $eventdata['format'], 'direct');
    }
    return true;
}


/**
* event handler for when a user is certified
*/
function tao_handle_certification_event($eventdata) {
    // event data contains userid and some role information
    foreach (array('userid', 'course', 'certification') as $required) {
        if (empty($eventdata->{$required})) {
            mtrace("Invalid certification event thrown, missing required field $required");
            return false;
        }
    }
    // go figure out which role we're switched from
    $c = explode('_', $eventdata->certification);
    if (!@defined('ROLE_' . strtoupper($c[1]))) {
        mtrace("Invalid certification event thrown, couldn't figure out the role from $eventdata->certification");
        return false;
    }

    $fromrole = get_field('role', 'id', 'shortname', constant('ROLE_' . strtoupper($c[1])));
    $torole   = get_field('role', 'id', 'shortname', constant('ROLE_CERTIFIED' . strtoupper($c[1])));

    if (empty($fromrole) || empty($torole)) {
        mtrace("Invalid certification event thrown, couldn't find fromrole or torole");
        return false;
    }

    //db_begin(); //function in Mahara - not in Moodle :-(
    $lpcontext = get_context_instance(CONTEXT_COURSE, $eventdata->course);
    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

    role_unassign($fromrole, $eventdata->userid, 0, $sitecontext->id);
    role_unassign($fromrole, $eventdata->userid, 0, $lpcontext->id);

    role_assign($torole, $eventdata->userid, 0, $sitecontext->id);
    role_assign($torole, $eventdata->userid, 0, $lpcontext->id);

    //return db_commit(); //function in Mahara - not in Moodle :-(
    return true;
}



/**
* from a return of tao_get_users_on_exact_roles
* which has a nested array with keys = roleids
* just return a unique array of all users
*/
function tao_get_unique_role_users($roleusers) {
    $unique = array();
    foreach ($roleusers as $role => $users) {
        foreach ($users as $user) {
            $unique[$user->id] = $user;
        }
    }
    return $unique;
}

/**
 * return the list of users who have an assignment in the given context
 *
 * @param array   $roles     arrays or roles to match - can contain id, shortname or stdclass object.
 * @param mixed $contextids   context id(s) to find assignment on, optional, defaults to SYSCONTEXTID, can be array or int.
 *
 * @return array - keyed on roleid, value is array of user objects
*/
function tao_get_users_on_exact_roles($roles, $contextids=0) {
    global $CFG;

    if (empty($contextids)) {
        $contextids = SYSCONTEXTID;
    }

    if (is_numeric($contextids)) {
        $contextids = array($contextids);
    }

    if (!$roleids = tao_tidyup_rolearray($roles)) {
        return;
    }

    $sql = "SELECT DISTINCT ra.roleid, " . tao_needed_userfields . "
            FROM {$CFG->prefix}user u
               JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
             WHERE ra.roleid " . ((count($roleids) == 1) ? " = " . $roleids[0]  : " IN ( " . implode(', ', $roleids) . " ) ") . "
             AND ra.contextid " . ((count($contextids) == 1) ? " = " . $contextids[0] : " IN ( " . implode(', ', $contextids) . " ) ");

    $processed = array_fill_keys($roleids, array());
    if (!$raw = get_records_sql($sql)) {
        return $processed;
    }

    foreach ($raw as $r) {
        if (!array_key_exists($r->roleid, $processed)) {
            $processed[$r->roleid] = array();
        }
        $processed[$r->roleid][] = $r;
    }

    return $processed;
}

/**
 * return the count of users who have an assignment in the given context
 *
 * @param array   $roles     arrays or roles to match - can contain id, shortname or stdclass object.
 * @param mixed $contextids   context id(s) to find assignment on, optional, defaults to SYSCONTEXTID, can be array or int.
 *
 * @return array - keyed on roleid, value is count
*/
function tao_count_users_on_exact_roles($roles, $contextids=0) {
    global $CFG;

    if (empty($contextids)) {
        $contextids = SYSCONTEXTID;
    }

    if (is_numeric($contextids)) {
        $contextids = array($contextids);
    }

    if (!$roleids = tao_tidyup_rolearray($roles)) {
        return;
    }

    $sql = "SELECT ra.roleid, COUNT(u.id) AS count
               FROM {$CFG->prefix}user u
               JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
             WHERE ra.roleid " . ((count($roleids) == 1) ? " = " . $roleids[0]  : " IN ( " . implode(', ', $roleids) . " ) ") . "
             AND ra.contextid " . ((count($contextids) == 1) ? " = " . $contextids[0] : " IN ( " . implode(', ', $contextids) . " ) " ) . "
             GROUP BY ra.roleid";

    $processed = array_fill_keys($roleids, 0);
    if (!$raw = get_records_sql($sql)) {
        return $processed;
    }

    foreach ($raw as $r) {
        $processed[$r->roleid] = $r->count;
    }

    return $processed;
}

/**
* helper function to turn an array of messyrole mixed arguments
* into an array of roleids.
* used in tao_{get,count}_users_on_exact_roles
*/
function tao_tidyup_rolearray($roles) {
    $roleids = array();
    if (empty($roles) || !is_array($roles)) {
        return;
    }
    foreach ($roles as $role) {
        $roleid = 0;
        if (is_object($role)) {
            $roleid = $role->id;
        } else if (is_numeric($role)) {
            $roleid = $role;
        } else if (!$roleid = get_field('role', 'id', 'shortname', $role)) {
            debugging('something invalid passed to tao_tidyup_rolearray: ' . $role);
        }
        $roleids[] = $roleid;
    }
    return $roleids;
}

/**
* helper function to return all the filters for learning path classifications
*
* @param boolean $count whether to get the count for each classification or not
* @param int $courseid if given, will just return the values for a given course.
* @param int $status if given, will just return the values courses at the given status.
* @param int $category if given, will just return the values courses in the given category.
*
* @return mixed. if !$count, just return the array of results.
*               if count, will be a standard class with allvalues, filtercounts and secondcounts variables.
*/
function tao_get_classifications($count=true, $courseid=null, $status=null, $category=null) {
    global $CFG;

    $return = new StdClass;

    $sql = '
        SELECT cv.id, ct.id AS typeid, ct.type, ct.name, cv.value
        FROM ' . $CFG->prefix . 'classification_type ct
        JOIN ' . $CFG->prefix . 'classification_value cv ON cv.type = ct.id
    ' . ((!$courseid) ? 'LEFT' : '') . ' JOIN ' . $CFG->prefix . 'course_classification cc ON cc.value = cv.id
    ' . (($courseid) ? ' WHERE cc.course = ' . $courseid : '') . '
        ORDER BY ct.type, cv.value
    ';

    $return->allvalues = get_records_sql($sql);
    if (empty($count)) {
        return $return->allvalues;
    }

    $countsql = '
        SELECT cc.value, COUNT(cc.id)
        FROM ' . $CFG->prefix . 'course_classification cc
        JOIN ' . $CFG->prefix . 'classification_value cv ON cv.id = cc.value
        JOIN ' . $CFG->prefix . 'classification_type ct ON cv.type = ct.id
        JOIN ' . $CFG->prefix . 'course c ON c.id = cc.course
        WHERE ct.type = \'filter\'
    ' . (($courseid) ? ' AND cc.course = ' . $courseid : '') . '
    ' . (($category) ? ' AND c.category = ' . $category : '') . '
    ' . (($status) ? ' AND c.approval_status_id = ' . $status : '') . '
        GROUP BY cc.value
    ';

    if (!$return->filtercounts = get_records_sql($countsql)) {
        $return->filtercounts = array();
    }

    $concat = sql_concat('cc1.value', "'|'", 'cc2.value');

    $countsql = '
        SELECT ' . $concat . ' AS id , COUNT(cc2.id) AS count
        FROM ' . $CFG->prefix . 'course_classification cc1
        JOIN ' . $CFG->prefix . 'course_classification cc2 ON cc1.course = cc2.course
        JOIN ' . $CFG->prefix . 'classification_value cv1 ON cv1.id = cc1.value
        JOIN ' . $CFG->prefix . 'classification_type ct1 ON cv1.type = ct1.id
        JOIN ' . $CFG->prefix . 'classification_value cv2 ON cv2.id = cc2.value
        JOIN ' . $CFG->prefix . 'classification_type ct2 ON cv2.type = ct2.id
        JOIN ' . $CFG->prefix . 'course c ON c.id = cc2.course
        WHERE ct1.type = \'topcategory\' AND ct2.type = \'secondcategory\'
    ' . (($courseid) ? ' AND cc.course = ' . $courseid : '') . '
    ' . (($category) ? ' AND c.category = ' . $category : '') . '
    ' . (($status) ? ' AND c.approval_status_id = ' . $status : '') . '
        GROUP BY ' . $concat;

    if (!$return->secondcounts = get_records_sql($countsql)) {
        $return->secondcounts = array();
    }

    return $return;
}

/**
* return all course ids that match a given classification
* @param int $id id of the value trying to match
* @param int $secondid optional second value id
* @param int $status optional status of the course
* @param int $category optional course category to match on
*
* @return array array of ints (courseids)
*/
function tao_get_courseids_with_classification($id, $secondid=0, $status=null, $category=null) {
    global $CFG;
    $sql = 'SELECT cc.course
        FROM ' . $CFG->prefix . 'course_classification cc  
        JOIN ' . $CFG->prefix . 'course c ON c.id = cc.course '
        . (($secondid) ? ' JOIN ' . $CFG->prefix . 'course_classification cc2 ON cc.course = cc2.course' : '')
        . ' WHERE cc.value = ' . $id
        . (($secondid) ? ' AND cc2.value = ' . $secondid : '') 
        . (($category) ? ' AND c.category = ' . $category : '')    
        . (($status) ? ' AND c.approval_status_id = ' . $status : '');   

    return get_records_sql($sql);
}

/**
* returns all courses the given user has 'authored', which is determined by the LP Author role given at course context
* 
* @param int $user
* 
* @return array course records
*/
function tao_get_authored_learning_paths($user) {
    return tao_get_learning_paths_by_role_of_user($user, ROLE_LPAUTHOR);
}

/**
* returns all courses the given user is 'editing', which is determined by the Head Editor role given at course context
* 
* @param int $user
* 
* @return array course records
*/
function tao_get_editing_learning_paths($user) {
    return tao_get_learning_paths_by_role_of_user($user, ROLE_HEADEDITOR);
}

/**
* returns all courses where the given user is assigned the given role
*
* @param int $user
* @param text $roleshortname
*
* @return array course records
*/
function tao_get_learning_paths_by_role_of_user($user, $roleshortname) {
    global $CFG;

    $sql = "SELECT c.id, c.shortname, c.fullname, c.format, s.displayname " . sql_as() . " status
             FROM {$CFG->prefix}context x 
             JOIN {$CFG->prefix}role_assignments a ON x.id = a.contextid
             JOIN {$CFG->prefix}role r ON a.roleid = r.id
             JOIN {$CFG->prefix}course c ON c.id = x.instanceid
             JOIN {$CFG->prefix}course_approval_status s ON s.id = c.approval_status_id
            WHERE x.contextlevel = ".CONTEXT_COURSE."
              AND a.userid = {$user->id}
              AND r.shortname = '".$roleshortname."'
            ";

    return get_records_sql($sql);

}

function tao_get_learning_path_templates() {
    global $CFG;

    // get a list of courses that are learning path templates
    //    note: currently this is defined by which courses exist in the locally configured "template" category
    return get_records('course', 'category',$CFG->lptemplatescategory);
}

/**
* helper used by the restore functions to track keys
*/
function tao_localrestore_create_key(&$parser) {
    $newkey = (count($parser->info->localcoursedata) + 1);
    $parser->info->localcoursedata[$newkey] = new StdClass;
    $parser->info->localcourserestorekey = $newkey;
}

/**
* helper function to return the per user assignable roles
*
* @return keyed array capcomponent => role data
*/
function tao_get_assignable_userroles() {
    static $userroles = array(
        'pt' => array(
            'recipientrole'   => ROLE_PT,
            'assignerrole'    => ROLE_MT,
            'canassigncap'    => 'canassignpt',
            'isassignablecap' => 'isassignablept',
        ),
        'mt' => array(
            'recipientrole'   => ROLE_MT,
            'assignerrole'    => ROLE_ST,
            'canassigncap'    => 'canassignmt',
            'isassignablecap' => 'isassignablemt',
        ),
    );
    return $userroles;
}


/**
* helper function for the theme to figure out what header graphic to use
*/
function tao_header_image() {
    global $CFG, $COURSE, $db;
    // first figure out the url mapping
    $me = me();
    $pathinfo = strstr(substr(strstr($CFG->wwwroot, '//'),2), '/'); //strip out http://mywebsite.com and https://mywebsite.com
    $me = str_replace($pathinfo, '', $me); //remove any prepended directories
    $me = strip_querystring($me); //remove any params!
    // this is dangerous, so use prepared statements.
    if (!empty($COURSE->id)) {
        $coursehdrs = get_records('header_image', 'courseid', $COURSE->id,'sortorder');
        if (!empty($coursehdrs)) {
            foreach ($coursehdrs as $ch) {
                if(empty($ch->url)) { //if url is empty then all pages with this id must use this image. 
                    return tao_header_image_location($ch->image);
                } elseif(strpos($me, $ch->url) !== false) {
                    return tao_header_image_location($ch->image);
                }
            }
        }
    }
    $sth = $db->prepare("SELECT * FROM " . $CFG->prefix . "header_image WHERE url like ? || '%' ORDER BY sortorder LIMIT 1");
    if (!$resultset = $db->execute($sth, array($me))) {
        if (isset($CFG->defaultcustomheader)) {
            return $CFG->defaultcustomheader;
        }
        return;
    }

    if ($resultset->recordCount() == 1) {
        $image = $resultset->fields['image'];
    } else {
        $image = $CFG->defaultcustomheader;
    }
    return tao_header_image_location($image);
}

function tao_header_image_location($image) {
    global $CFG;

    $dirbase = $CFG->dirroot . '/theme/' . current_theme() . '/pix/headers/';
    $urlbase = $CFG->wwwroot . '/theme/' . current_theme() . '/pix/headers/';

    $datarootbase = $CFG->dataroot . '/' . SITEID . '/pix/headers/';
    $datarootfilebase = SITEID . '/pix/headers/';

    $lang = current_language();

    // check dataroot first...
    if (file_exists($datarootbase . $lang . '/'. $image)) {
        require_once($CFG->libdir . '/filelib.php');
        return get_file_url($datarootfilebase . $lang . '/' . $image);
    }
    if (file_exists($dirbase . $lang . '/' . $image)) {
        return $urlbase . $lang . '/' . $image;
    }
    // go walking language tree to find one
    while (true) {
        $parent = false;
        $langconfig = $CFG->dataroot . '/lang/' . $lang . '/langconfig.php';
        if (!file_exists($langconfig)) {
            $langconfig = $CFG->dirroot . '/lang/' . $lang . '/langconfig.php';
            if (!file_exists($langfile)) {
                return false; // nothing we can do
            }
        }
        get_string_from_file('parentlanguage', $langconfig, "\$parent");
        if ($parent) {
            // check dataroot first...
            if (file_exists($datarootbase . $parent . '/'. $image)) {
                require_once($CFG->libdir . '/filelib.php');
                return get_file_url($datarootfilebase . $parent . '/' . $images);
            }
            if (file_exists($dirbase . $parent . '/' . $image)) {
                return $urlbase . $parent . '/' . $image;
            } else {
                // loop through again and look for another parent
            }
        } else {
            return $urlbase . 'en_utf8/' . $image;
        }
    }
}

function tao_needed_userfields($tableprefix='u.') {
    $fields = array('firstname', 'lastname', 'email', 'emailstop', 'mailformat', 'lastaccess', 'mnethostid', 'id', 'picture', 'idnumber', 'imagealt');
    return $tableprefix . implode(', ' . $tableprefix, $fields);
}

/**
* return an array of users that the given user has roles assigned on.
*
* @param object $user stdclass object of user to fetch roles for
* @param object $role stdclass object direct role that recipient users must have a direct assignment at course context
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param int $start where to start from
* @param int $limit how many results to fetch
*
* @return array array of user stdclass objects.
*/
function tao_get_mentees_by_courserole($user, $role, $course=null, $start=0, $limit=0) {
    global $CFG;

    $user = tao_user_parameter($user);
    $courseid = (!empty($course) ? $course->id : SITEID);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $sql = "SELECT c.instanceid, c.instanceid, r.name AS rolename, " . tao_needed_userfields() . "
         FROM {$CFG->prefix}role_assignments ra
         JOIN {$CFG->prefix}role r ON ra.roleid = r.id
         JOIN {$CFG->prefix}context c ON ra.contextid = c.id
         JOIN {$CFG->prefix}user u ON c.instanceid = u.id
         WHERE ra.userid = $user->id
         AND EXISTS (
            SELECT *
            FROM {$CFG->prefix}role_assignments innerra
            JOIN {$CFG->prefix}role innerr ON innerra.roleid = innerr.id
            WHERE innerr.id = {$role->id}
            AND innerra.contextid = {$coursecontext->id}
            AND innerra.userid = c.instanceid
         )
         AND c.contextlevel = " . CONTEXT_USER . "
         ORDER BY u.lastname, u.firstname";
    return get_records_sql($sql, $start, $limit);
}
/**
* return an array of users that the given user has roles assigned on that are pending certification.
*
* @param object $user stdclass object of user to fetch roles for
* @param object $role stdclass object direct role that recipient users must have a direct assignment at course context
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param int $start where to start from
* @param int $limit how many results to fetch
*
* @return array array of user stdclass objects.
*/
function tao_get_mentees_by_courserole_pending_certification($user, $role, $course=null, $start=0, $limit=0) {
    global $CFG;

    $user = tao_user_parameter($user);
    $courseid = (!empty($course) ? $course->id : SITEID);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $sql = "SELECT c.instanceid, c.instanceid, r.name AS rolename, cs.courseid, cs.id AS reqid, cs.timechanged, " . tao_needed_userfields() . "
         FROM {$CFG->prefix}role_assignments ra
         JOIN {$CFG->prefix}role r ON ra.roleid = r.id
         JOIN {$CFG->prefix}context c ON ra.contextid = c.id
         JOIN {$CFG->prefix}user u ON c.instanceid = u.id
         JOIN {$CFG->prefix}tao_user_certification_status cs ON u.id = cs.userid
         WHERE ra.userid = $user->id AND cs.status ='submitted'
         AND EXISTS (
            SELECT *
            FROM {$CFG->prefix}role_assignments innerra
            JOIN {$CFG->prefix}role innerr ON innerra.roleid = innerr.id
            WHERE innerr.id = {$role->id}
            AND innerra.contextid = {$coursecontext->id}
            AND innerra.userid = c.instanceid
         )
         AND c.contextlevel = " . CONTEXT_USER . "
         ORDER BY u.lastname, u.firstname";
    return get_records_sql($sql, $start, $limit);
}
/**
* return all uncertified PTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param mixed $course optional stdclass course object, defaults to site
* @param int $start optional, for paging
* @param int $limit optional, for paging
*
* @return array array of user stdclass objects
*/
function tao_get_uncertified_pts($user=null, $course=null, $start=0, $limit=0) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_PT);
    return tao_get_mentees_by_courserole($user, $role, $course, $start, $limit);
}
/**
* return all uncertified PTS assigned to the given user that have requested certification
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param mixed $course optional stdclass course object, defaults to site
* @param int $start optional, for paging
* @param int $limit optional, for paging
*
* @return array array of user stdclass objects
*/
function tao_get_uncertified_pts_pending_certification($user=null, $course=null, $start=0, $limit=0) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_PT);
    return tao_get_mentees_by_courserole_pending_certification($user, $role, $course, $start, $limit);
}
/**
* count all uncertified PTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param mixed $course optional stdclass course object, defaults to site
*
* @return int count
*/
function tao_count_uncertified_pts($user=null, $course=null) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_PT);
    return tao_count_mentees_by_courserole($user, $role, $course);
}

/**
* return all certified PTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param mixed $course optional stdclass course object, defaults to site
* @param int $start optional, for paging
* @param int $limit optional, for paging
*
* @return array array of user stdclass objects
*/
function tao_get_certified_pts($user, $course=null, $start=0, $limit=0) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_CERTIFIEDPT);
    return tao_get_mentees_by_courserole($user, $role, $course, $start, $limit);
}

/**
* count all certified PTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param mixed $course optional stdclass course object, defaults to site
*
* @return int count
*/
function tao_count_certified_pts($user=null, $course=null) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_CERTIFIEDPT);
    return tao_count_mentees_by_courserole($user, $role, $course);
}

/**
* return all MTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param null $course not used, just for consistent function contract with the other tao_get/count functions
* @param int $start optional, for paging
* @param int $limit optional, for paging
*
* @return array array of user stdclass objects
*/
function tao_get_mts($user, $course=null, $start=0, $limit=0) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_MT);
    return tao_get_mentees_by_courserole($user, $role, null, $start, $limit);
}

/**
* count all MTS assigned to the given user
*
* @param mixed $user optional user  - can be object, int, null (logged in user)
* @param null $course not used, just for consistent function contract with the other tao_get/count functions
*
* @return int count
*/
function tao_count_mts($user=null, $course=null) {
    $user = tao_user_parameter($user);
    $role = get_record('role', 'shortname', ROLE_MT);
    return tao_count_mentees_by_courserole($user, $role, null);
}

/**
* return an array of grand children - certified pts assigned to mts of the given user (alumni)
*
* @param mixed $user optional, defaults to null, can be int or object. if not supplied, currently logged in user is used.
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param array $children limit to grandchildren who have a parent in this array. optional, defaults to none (all grandchildren)
*
* @return array array of user stdclass objects
*/
function tao_get_certified_pts_of_mts($user=null, $course=null, $children=null) {
    $user = tao_user_parameter($user);
    if (empty($children)) {
        $childrole = get_record('role', 'shortname', ROLE_MT);
    } else {
        $childrole = false; // not needed if we're filtering by children
    }
    $grandchildrole = get_record('role', 'shortname', ROLE_CERTIFIEDPT);
    return tao_get_grandchildren_by_courserole($user, $grandchildrole, $course, $childrole, $children);
}

/**
* count all the certified pts assigned to mts, assigned to the given user (alumni)
*
* @param mixed $user optional, defaults to null, can be int or object. if not supplied, currently logged in user is used.
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
*
* @return int count
*/
function tao_count_certified_pts_of_mts($user=null, $course=null) {
    $user = tao_user_parameter($user);
    $childrole = get_record('role', 'shortname', ROLE_MT);
    $grandchildrole = get_record('role', 'shortname', ROLE_CERTIFIEDPT);
    return tao_count_grandchildren_by_courserole($user, $grandchildrole, $course, $childrole, null);
}

/**
* return an array of grand children - uncertified pts assigned to mts of the given user
*
* @param mixed $user optional, defaults to null, can be int or object. if not supplied, currently logged in user is used.
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param array $children limit to grandchildren who have a parent in this array. optional, defaults to none (all grandchildren)
*
* @return array array of user stdclass objects
*/
function tao_get_uncertified_pts_of_mts($user=null, $course=null, $children=null) {
    $user = tao_user_parameter($user);
    if (empty($children)) {
        $childrole = get_record('role', 'shortname', ROLE_MT);
    } else {
        $childrole = false; // not needed if we're filtering by children
    }
    $grandchildrole = get_record('role', 'shortname', ROLE_PT);
    return tao_get_grandchildren_by_courserole($user, $grandchildrole, $course, $childrole, $children);
}

/**
* count all the uncertified pts assigned to mts, assigned to the given user
*
* @param mixed $user optional, defaults to null, can be int or object. if not supplied, currently logged in user is used.
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
*
* @return int count
*/
function tao_count_uncertified_pts_of_mts($user=null, $course=null) {
    $user = tao_user_parameter($user);
    $childrole = get_record('role', 'shortname', ROLE_MT);
    $grandchildrole = get_record('role', 'shortname', ROLE_PT);
    return tao_count_grandchildren_by_courserole($user, $grandchildrole, $course, $childrole, null);
}

/**
* helper function to generate the FROM .. part of the query used to calculate grandchildren
*
* @access private
*
* @param mixed $user stdclass user object
* @param stdclass $grandchildrole role that grandchildren must have a direct assignment to at site context. (eg PT)
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param stdclass $childrrole (optional) role that children must have a direct assignment to at site context (eg MT)
* @param array $children limit to grandchildren who have a parent in this array. optional, defaults to none (all grandchildren)
*
* @return string SQL snippet
*/
function _tao_grandchild_query($user, $grandchildrole, $course=null, $childrole=null, $children=null) {
    global $CFG;
    $courseid = (!empty($course) ? $course->id : SITEID);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
    return "
         FROM {$CFG->prefix}role_assignments ra
         JOIN {$CFG->prefix}role r ON ra.roleid = r.id
         JOIN {$CFG->prefix}context c ON ra.contextid = c.id
         JOIN {$CFG->prefix}user u ON c.instanceid = u.id
         WHERE " . (is_array($children)
         ? " ra.userid IN ( " . implode(',', $children) . ') '
         : " ra.userid IN (
                 SELECT c2.instanceid
                 FROM {$CFG->prefix}context c2
                 JOIN {$CFG->prefix}role_assignments ra2 ON ra2.contextid = c2.id
                 WHERE ra2.userid = $user->id AND c2.contextlevel = " . CONTEXT_USER . " " . (!empty($childrole)
                 ?  " AND EXISTS (
                         SELECT *
                         FROM {$CFG->prefix}role_assignments innerra
                         JOIN {$CFG->prefix}role innerr ON innerra.roleid = innerr.id
                         WHERE innerr.id = {$childrole->id}
                         AND innerra.contextid = {$coursecontext->id}
                         AND innerra.userid = c2.instanceid
                 ) " : " ") . "
                 AND c2.instanceid != c.instanceid
            ) "
         ) . "
         AND c.contextlevel = " . CONTEXT_USER . "
         AND EXISTS (
             SELECT *
             FROM {$CFG->prefix}role_assignments innerra
             JOIN {$CFG->prefix}role innerr ON innerra.roleid = innerr.id
             WHERE innerr.id = {$grandchildrole->id}
             AND innerra.contextid = {$coursecontext->id}
             AND innerra.userid = c.instanceid
         ) ";
}

/**
* return a count of all grandchildren of the given user
*
* @param object $user stdclass user object
* @param stdclass $grandchildrole role that grandchildren must have a direct assignment to at given course context. (eg PT)
* @param stdclass $childrrole (optional) role that children must have a direct assignment to at given course context (eg MT)
* @param array $children limit to grandchildren who have a parent in this array. optional, defaults to none (all grandchildren)
*
* @return int count
*/
function tao_count_grandchildren_by_courserole($user, $grandchildrole, $course=null, $childrole=null, $children=null) {
    $sql = "SELECT COUNT(c.instanceid)
         " . _tao_grandchild_query($user, $grandchildrole, $course, $childrole, $children);
    return count_records_sql($sql);

}

/**
* return an array of grand children
*
* @param object $user stdclass user object
* @param stdclass $grandchildrole role that grandchildren must have a direct assignment to at given course context. (eg PT)
* @param stdclass $childrrole (optional) role that children must have a direct assignment to at given course context (eg MT)
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
* @param array $children limit to grandchildren who have a parent in this array. optional, defaults to none (all grandchildren)
*
* @return array array of user stdclass objects
*/
function tao_get_grandchildren_by_courserole($user, $grandchildrole, $course=null, $childrole=null, $children=null) {
    global $CFG;


    $sql = "SELECT c.instanceid, r.name AS rolename, ra.userid AS parentid, " . tao_needed_userfields() . "
         " . _tao_grandchild_query($user, $grandchildrole, $course, $childrole, $children) . "
         ORDER BY u.lastname, u.firstname";

    $toreturn = array();

    if (!$grandchildren = get_records_sql($sql)) {
        return $toreturn;
    }

    // resort them by parent
    foreach ($grandchildren as $grandchild) {
        if (!array_key_exists($grandchild->parentid, $toreturn)) {
            $toreturn[$grandchild->parentid] = array();
        }
        $toreturn[$grandchild->parentid][$grandchild->instanceid] = $grandchild;
    }
    return $toreturn;
}

/**
* returns the count of the mentees for the given (or logged in user)
* this one and {@see tao_get_mentees} are used for paging.
*
* @param mixed $user can be int or object or null
* @param object $role stdclass object direct role that recipient users must have a direct assignment at course context
* @param object $course stdclass object of course to fetch role assignment on (optional, defaults to SITE)
*
* @return int total count
*/
function tao_count_mentees_by_courserole($user, $role, $course) {
    global $CFG;
    $courseid = (!empty($course) ? $course->id : SITEID);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $sql = "SELECT COUNT(c.instanceid)
         FROM {$CFG->prefix}role_assignments ra
         JOIN {$CFG->prefix}context c ON ra.contextid = c.id
         WHERE ra.userid = $user->id
         AND EXISTS (
            SELECT *
            FROM {$CFG->prefix}role_assignments innerra
            JOIN {$CFG->prefix}role innerr ON innerra.roleid = innerr.id
            WHERE innerr.id = {$role->id}
            AND innerra.contextid = {$coursecontext->id}
            AND innerra.userid = c.instanceid
         )
         AND c.contextlevel = " . CONTEXT_USER;

    return count_records_sql($sql);
}

/**
* returns a user object from the given arguments
* or the currently logged in user, if none is given.
*
* @param mixed $user can be id or object or null
*
* @return stdclass user object.
*/
function tao_user_parameter($user=null) {
    global $USER;
    static $usercache = array();

    if (empty($user)) {
        return $USER;
    } else if (is_object($user)) {
        return $user;
    } else if (is_numeric($user)) {
        if (array_key_exists($user, $usercache)) {
            return $usercache[$user];
        }
        if ($tmp = get_record('user', 'id', $user)) {
            $usercache[$user] = $tmp;
            return $tmp;
        }
    }
    if (!empty($USER)) {
        return $USER;
    }
    return false;
}

/**
* hook function from inside the theme.
* in this case, detect lack of $PAGE and do a horrible hack
* to get a consistent page format.
*/
function tao_local_header_hook() {
    global $CFG;
    require_once($CFG->libdir . '/blocklib.php');
    require_once($CFG->libdir . '/pagelib.php');

    global $PAGE;
    if (!empty($PAGE)) {
        return true;
    }
    if (defined('ADMIN_STICKYBLOCKS')) {
        return true;
    }

    if (optional_param('inpopup')) {
        return true;
    }
    
    $lmin = (empty($THEME->block_l_min_width)) ? 100 : $THEME->block_l_min_width;
    $lmax = (empty($THEME->block_l_max_width)) ? 210 : $THEME->block_l_max_width;
    $rmin = (empty($THEME->block_r_min_width)) ? 100 : $THEME->block_r_min_width;
    $rmax = (empty($THEME->block_r_max_width)) ? 210 : $THEME->block_r_max_width;

    (!defined('BLOCK_L_MIN_WIDTH')) && define('BLOCK_L_MIN_WIDTH', $lmin);
    (!defined('BLOCK_L_MAX_WIDTH')) && define('BLOCK_L_MAX_WIDTH', $lmax);
    (!defined('BLOCK_R_MIN_WIDTH')) && define('BLOCK_R_MIN_WIDTH', $rmin);
    (!defined('BLOCK_R_MAX_WIDTH')) && define('BLOCK_R_MAX_WIDTH', $rmax);

    $PAGE = new tao_page_class_hack();
    $pageblocks = blocks_setup($PAGE, true);
    // we could replace this with a stickyblocks implementation, this is a proof of concept.
    $preferred_width_left  = bounded_number(
        BLOCK_L_MIN_WIDTH,
        blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
        BLOCK_L_MAX_WIDTH
    );

    echo '<table id="layout-table" summary="layout">
      <tr>
    ';
    echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
    ob_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    $blockscontent = ob_get_clean();
    if (!$blockscontent) {
        $rec = (object)array(
            'id' => 0,
            'blockid' => 0,
            'pageid' => 0,
            'pagetype' => tao_page_class_hack::get_type(),
            'position' => BLOCK_POS_LEFT,
            'visible' => true,
            'configdata' => '',
            'weight' => 0,
        );
        $pageblocks = array(BLOCK_POS_LEFT => array(0 => $rec));
        $pageblocks[BLOCK_POS_LEFT][0]->rec = $rec;
        $pageblocks[BLOCK_POS_LEFT][0]->obj = new tao_dummy_block($rec);
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    } else {
        echo $blockscontent;
    }
    echo '</td>';
    echo '<td id="middle-column">';
    define('TAO_HEADER_OVERRIDDEN', 1);
}

require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
/**
* dummy block to print out a tiny amount of space
* so that the left column prints
*/
class tao_dummy_block extends block_base {

    function __construct($rec) {
        $this->instance = $rec;
        $this->content = (object)array(
            'text'   => '',
            'footer' => '',
        );
    }

    function is_empty() {
        return false;
    }

}

/**
* hacked page lib that gets used on any page that doesn't have one.
* really just exists to fulfil requirements and allow stickyblocks
*/
class tao_page_class_hack extends page_base {
    function get_type() {
        return 'tao';
    }

    function user_is_editing() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return true;
        }
        return false;
    }

    function blocks_default_position() {
        return BLOCK_POS_LEFT; // avoid getting the admin block
    }

    function get_id() {
        return 0;
    }

    function user_allowed_editing() {
        return $this->user_is_editing();
    }
    function url_get_path() {
        global $CFG;
        if (defined('ADMIN_STICKYBLOCKS')) {
            return $CFG->wwwroot . '/admin/stickyblocks.php';
        }
        return '';
    }

    function url_get_parameters() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return array('pt' => 'tao');
        }
    }

    function print_header($title, $morenavlinks=NULL) {
        $nav = build_navigation($morenavlinks);
        print_header($title, $title, $nav);
    }
}
page_map_class('tao', 'tao_page_class_hack');

/**
* local footer hook. nothing yet but this could print right blocks
*/
function tao_local_footer_hook() {
    if (!defined('TAO_HEADER_OVERRIDDEN')) {
        return;
    }
    echo '</td></tr></table>';
}

/**
* print out the TAO nav section
*/
function tao_print_static_nav($return=false) {
    global $CFG, $USER;

    $myteachinglink = '/local/mahara/taoviewtaoresources.php';

    $mytoolslink = '/local/mahara/taoviewtaotools.php';

    $returnstr = '
     <ul id="tao-nav">
       <li><a href="' . $CFG->wwwroot . '">' . get_string('home') . '</a></li>
       <li><a href="' . $CFG->wwwroot . $myteachinglink.'">' . get_string('myteaching', 'local') . '</a></li>
       <li><a href="' . $CFG->wwwroot . '/local/my/learning.php">' . get_string('mylearning', 'local') . '</a></li>
       <li><a href="' . $CFG->wwwroot . '/local/my/collaboration.php">' . get_string('mycollaboration', 'local') . '</a></li>
       <li><a href="' . $CFG->wwwroot . $mytoolslink.'">' . get_string('mytools', 'local') . '</a></li>
    ';

    if (has_capability('moodle/local:managemytasks', get_context_instance(CONTEXT_COURSE, SITEID))) {
        $returnstr .= '
             <li><a href="' . $CFG->wwwroot . '/local/my/work.php">' . get_string('mywork', 'local') . '</a></li>
         ';
    }

    if (!isloggedin()) {
        $returnstr .= '
       <li><a href="' . $CFG->wwwroot . '/login/index.php">' . get_string('login') . '</a></li>
        ';
    } else {
        if (!isguestuser()){
            $maharaid = get_field('mnet_host', 'id', 'name', 'localmahara');
            if (!empty($maharaid)) {
                $returnstr .=  '<li><a href="'.$CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$maharaid.'">'.get_string('myportfolio', 'local').'</a></li>';
            }
           $returnstr .= '<li><a href="' . $CFG->wwwroot . '/user/view.php?id=' . $USER->id . '">' . get_string('myprofile', 'local') . '</a></li>';
        }
        $returnstr .= '
       <li><a href="' . $CFG->wwwroot . '/login/logout.php">' . get_string('logout') . '</a></li>
        ';
    }
    $returnstr .= '
     </ul>
    ';


    if ($return) {
        return $returnstr;
    }
    echo $returnstr;
}

/**
* applies custom capabilities to roles. 
*/

function tao_reassign_capabilities($path='local') {
    global $CFG, $_SESSION;

    if (!get_site()) { // not finished installing, skip
        return true;
     }
    // look for a matrix of role/capability definitions and make sure we have them all
    $cappath = $CFG->dirroot .  '/' . $path . '/capabilities.php';
    if (!file_exists($cappath)) {
        debugging("Local caps reassignment called with invalid path $path");
        return false;
    }
    require_once($cappath);
    $caps = get_custom_capabilities();
    if (!isset($caps)) {
        debugging('no caps');
        return true; // nothing to do.
    }
    foreach ($caps as $role => $caparray) {
        if (!$roleid = get_field('role', 'id', 'shortname', $role)) {
            debugging("Local caps made use of an invalid role, $role");
            continue;
        }

        foreach ($caparray as $c => $info) {
            $info = (object)$info;
            if (!isset($info->permission) || !isset($info->contextid)) {
                debugging("Local caps must define permission and contextid for assignments");
                continue;
            }
            assign_capability($c, $info->permission, $roleid, $info->contextid, true);
        }
    }
    return true;
}

/**
* same as tao_reassign_capabilities except removes existing custom capabilities.  
*
* note: there are some safety checks to ensure only custom roles are affected. 
*/

function tao_reset_capabilities($path='local') {
    global $CFG;

    if (!get_site()) { // not finished installing, skip
        return true;
    }

    // look for a matrix of role/capability definitions and make sure we have them all
    $cappath = $CFG->dirroot .  '/' . $path . '/capabilities.php';
    if (!file_exists($cappath)) {
        debugging("Local caps reassignment called with invalid path $path");
        return false;
    }
    require_once($cappath);
    $caps = get_custom_capabilities();
    if (!isset($caps)) {
        return true; // nothing to do.
    }

    $adminroles = get_admin_roles();

    foreach ($caps as $shortname => $caparray) {
        // get the roleid
        $role = get_record('role', 'shortname', $shortname);

        // don't mess with non-custom roles!
        if (!$role->custom == 1) {
            continue;
        }        

        // extra safety: whatever we do - don't mess with the admin role!!! 
        if (isset($adminroles[$role->id]) || $shortname == 'admin') {
            continue;
        }

        // reset capabilities on this role - this will reset to legacy role settings
        reset_role_capabilities($role->id);
    }

    // now reapply the custom capabilities
    return tao_reassign_capabilities($path);
  
}

/**
* When called resets all custom roles as per definition set down in /local/roles.php 
*
* Note that this uses the non-core role.custom field to isolate roles to remove. 
*
* Utilise the $path parameter to allow for localisation (i.e. different roles defintion than core).
*
* Sort order is reset based on the order listed in the defintion.
*
* WARNING: as long as you retain the same shortname existing user role assigments will
*             be retained.  if you change the shortname they will be lost.
*
* KNOWN ISSUE: we rely on shortname being unique, but this is not enforced by the db.  
*                       this is more a problem with moodle.
*
* @param text $path  
*
*/

function tao_reset_custom_roles($path='local') {
    global $CFG;

    if (!get_site()) { // not finished installing, skip
        return true;
    }

    // get latest role definition from roles file
    $rolespath = $CFG->dirroot .  '/' . $path . '/roles.php';
    if (!file_exists($rolespath)) {
        debugging("Local caps reassignment called with invalid path $path");
        return false;
    }
    require_once($rolespath);
    if (!isset($customroles)) {
        return true; // nothing to do.
    }

    $undeletableroles = array();
    $undeletableroles[$CFG->notloggedinroleid] = 1;
    $undeletableroles[$CFG->guestroleid] = 1;
    $undeletableroles[$CFG->defaultuserroleid] = 1;
    $undeletableroles[$CFG->defaultcourseroleid] = 1;
    // If there is only one admin role, add that to $undeletableroles too.
    $adminroles = get_admin_roles();
    if (count($adminroles) == 1) {
        $undeletableroles[reset($adminroles)->id] = 1;
    }

    // get recordset of existing custom roles
    $sql = "SELECT id, name, shortname, description, sortorder, custom 
              FROM {$CFG->prefix}role
              WHERE custom IS NOT NULL";

    $roles = get_records_sql($sql);

    // remove custom roles that are not in the latest definition
    foreach ($roles as $role) { 

        // check whether this role is in the latest definition
        if (array_key_exists($role->shortname, $customroles)) {
            continue;
        }

        // extra safety: check undeletable roles
        if (isset($undeletableroles[$role->id])) {
            continue;
        }

        delete_role($role->id);
    }


    // hack to avoid sortorder unique constraint
    execute_sql("UPDATE {$CFG->prefix}role SET sortorder = (sortorder+1000) WHERE custom IS NOT NULL");

    // set sortorder to current highest value
    $sortorder = get_field_sql("SELECT " . sql_max('sortorder') . " FROM {$CFG->prefix}role WHERE custom IS NULL");

    // now loop through the new settings
    foreach ($customroles as $shortname => $role) {
        $sortorder++;

        // get the roleid
        $roleid = get_field('role', 'id', 'shortname', $shortname);

        // if exists then make updates
        if (!empty($roleid)) {

            // only update fields that have been set
            if (isset($role['name'])) {
                set_field('role', 'name', $role['name'], 'shortname', $shortname);
            }
            if (isset($role['description'])) {
                set_field('role', 'description', $role['description'], 'shortname', $shortname);
            }
            // reset sortorder
            set_field('role', 'sortorder', $sortorder, 'shortname', $shortname);

        // else create record
        } else {

            $newrole = new stdclass();
            $newrole->name = $role['name'];
            $newrole->shortname = $shortname;
            $newrole->description = $role['description'];
            $newrole->sortorder = $sortorder;
            $newrole->custom = 1;

            $roleid = insert_record('role', $newrole);
 
        }

        // remove any previously set legacy roles
        $legacyroles = get_legacy_roles();
        foreach ($legacyroles as $ltype=>$lcap) {
            unassign_capability($lcap, $roleid); 
        }

        // reset legacy role
        if (isset($role['legacy'])) {
            $legacycap = $legacyroles[$role['legacy']];
            $context = get_context_instance(CONTEXT_SYSTEM);
            assign_capability($legacycap, CAP_ALLOW, $roleid, $context->id);
        }

        // update the context settings
        set_role_contextlevels($roleid, $role['context']); //  e.g. array(CONTEXT_SYSTEM, CONTEXT_COURSECAT)

        // set allow assigns
        if (is_array($role['canassign'])) {
            // delete existing
            delete_records('role_allow_assign', 'allowassign', $roleid);
            foreach ($role['canassign'] as $canassign) {
                $canassignid = get_field('role', 'id', 'shortname', $canassign);
                allow_assign($canassignid, $roleid);
            }
        }
    }

    // reset custom capabilities to keep up with changes 
    return tao_reset_capabilities();
}

/**
 * resets the customised front page blocks.  designed to be called from local_postinst 
 * 
 * @return bool
 */
function tao_reset_frontpage_blocks() {
    global $CFG;

    // first delete pre-set ones
    execute_sql('DELETE FROM ' . $CFG->prefix . 'block_instance
        WHERE pageid = ' . SITEID . "
        AND pagetype = 'course-view'"
    );

    // build new block array
    $blocks = array(
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'tao_nav'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'l',
            'weight'   => 0,
            'visible'  => 1,
            'configdata' => '',
        ),
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'admin_tree'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'l',
            'weight'   => 1,
            'visible'  => 1,
            'configdata' => '',
        ),
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'news_items'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'r',
            'weight'   => 0,
            'visible'  => 1,
            'configdata' => '',
        ),
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'messages'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'r',
            'weight'   => 1,
            'visible'  => 1,
            'configdata' => '',
        ),
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'online_users'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'r',
            'weight'   => 2,
            'visible'  => 1,
            'configdata' => '',
        ),
        (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'calendar_month'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'r',
            'weight'   => 3,
            'visible'  => 1,
            'configdata' => '',
        ),
    );

    // insert blocks
    foreach ($blocks as $b) {
        insert_record('block_instance', $b);
    }

    return 1;

}

/**
 * resets the customised sticky blocks settings.  designed to be called from /local/db/upgrade.php 
 * 
 * @param bool   $remove   dictates whether to remove existing sticky blocks 
 * @param string $path     path to the stickyblocks definition file
 *
 * @return bool
 */
function tao_reset_stickyblocks($remove=false, $path='local') {
    global $CFG;

    if ($remove) {
        // remove existing.  we only remove from the custom pagetypes format_learning and my-collaboration
        delete_records('block_pinned', 'pagetype', 'format_learning');
        delete_records('block_pinned', 'pagetype', 'my-collaboration');
    }

    // get the sticky block object
    $filepath = $CFG->dirroot .  '/' . $path . '/stickyblocks.php';
    if (!file_exists($filepath)) {
        debugging("Local caps reassignment called with invalid path $path");
        return false;
    }
    require_once($filepath);
    $blocks = get_custom_stickyblocks();
    if (!isset($blocks)) {
        return true; // nothing to do.
    }

    foreach($blocks as $block) {

        // check for existing record
        $id = get_field('block_pinned', 'id', 'blockid', $block->blockid, 'pagetype', $block->pagetype);

        if (empty($id)) {
            // if not there then insert a new record
            insert_record('block_pinned', $block);
        } else {
            // if there then just update the relevant settings
            $block->id = $id;
            update_record('block_pinned', $block);
        }
    }

    return true;

}

/**
 * returns the 'station' pages for the passed learning path 
 *
 * @param int $courseid  
 *
 * @return array
 */
function tao_get_learning_path_stations ($courseid) {

    $toppage = page_get_default_page($courseid);

    return page_filter_child_pages($toppage->id, page_get_all_pages($courseid, 'flat'));
}

/**
 * returns the 'station' pages for the passed learning path that the passed user has marked as viewed 
 *
 * note: just returns the page_ids in an array
 *
 * @param int $courseid  
 * @param int $userid  
 *
 * @return array
 */
function tao_get_viewed_learning_path_stations($courseid, $userid) {
    global $CFG;

    $sql = "SELECT format_page_id  
              FROM {$CFG->prefix}format_page_user_view v
              JOIN {$CFG->prefix}format_page p on p.id = v.format_page_id
             WHERE p.courseid = $courseid
               AND v.userid = $userid";

    $records = get_records_sql($sql);
    if (empty($records)) {
        return array();
    } else {
        return array_keys($records);
    }
}
/**
 * checks whether the user has marked the passed learning path as 'theirs' 
 *
 *    // this version just checking standard local:ispt capability at course context.  
 *    // hook here is intended for expansion though.
 *
 * @param int $courseid  
 *
 * @return bool
 */

function tao_is_my_learning_path($courseid) {
    global $USER;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    if (!has_capability('moodle/local:ispt', $context)) {
        return false;
    }

    return true;

}

/**
 * Update a course status return true or false
 *
 * put in its own function because there are 2 tables to update and 2 ensure consistant usage
 *
 * @param integer status  
 * @param text $reason   
 * @param object $course
 * @param object $context
 */

function tao_update_course_status($status, $reason, $course) {
    global $USER, $CFG;

    // update status value
    $course->approval_status_id = $status;

    if (!set_field('course', 'approval_status_id', $status, 'id', $course->id)) {
        print_error('statusupdateerror', 'local');
        return false;
    }

    // not ENTIRELY sure this needs to be seperated to an event - vague argument is so message notification can be handled independently
    if ($status == COURSE_STATUS_SUBMITTED || $status == COURSE_STATUS_RESUBMITTED ) {
        $eventdata = array(
            'status'   => $status,
            'reason'   => $reason,
            'userid'   => $USER->id,
            'courseid' => $course->id,
        );
        events_trigger('learning_path_submitted', $eventdata);
    }

    // custom course format hook
    $file=$CFG->dirroot."/course/format/$course->format/lib.php";

    if(file_exists($file)) {
        require_once($file);
        $function = $course->format . '_update_course_status';
        if(function_exists($function)) {
            if(!$function($status, $reason, $course)) {
                print_error('statuscustomhookerror', 'local');
                return false;
            }	
        } else {
            // move on
        }
    }

    // update status history
    $history = new object();
    $history->courseid = $course->id;
    $history->approval_status_id = $status;
    $history->reason = $reason;
    $history->timestamp = time();
    $history->userid = $USER->id;
 
    if (!insert_record('course_status_history', $history)) {
        print_error('statushistoryupdateerror', 'local');
        return false;
    }

    return true;
}

/**
 * Update a course status return true or false
 *
 * returns text description of the given course status
 *
 * @param object $course  
 */

function tao_get_course_status_desc($course) {

    if (!$course->approval_status_id) {
        return get_string('nostatusset', 'local'); 
    } else {
        // lookup status of course
        return get_field('course_approval_status', 'description', 'id', $course->approval_status_id);
    }
}

/**
 * returns an array of status's options valid to switch to for the given course 
 *
 * @param object $course  
 */

function tao_get_course_status_options($course) {
    global $CFG, $USER;

    // todo move this to tao.php

    $currentstatus = $course->approval_status_id;

    (empty($currentstatus) ? $currentstatus = 0 : '');

    // get this users course context roles
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $roles = get_user_roles($context, $USER->id, true); 

    $allowstatus = array($currentstatus); // allowed status 

    // grant status options by role
    // additional conditions based on current status
    //   developer note:  this works but is a bit clunky and may revisit if have time.  although the main idea
    //                    is that this should be easy to see what's being applied if you ever have to change 
    //                    the rules - not some impenetrable one liner.
    foreach ($roles as $role) {

        switch($role->shortname) {

            case ROLE_ADMIN:
            case ROLE_SUPERADMIN:
                // no restrictions on admins
                array_push($allowstatus, COURSE_STATUS_NOTSUBMITTED);
                array_push($allowstatus, COURSE_STATUS_SUBMITTED);
                array_push($allowstatus, COURSE_STATUS_NEEDSCHANGE);
                array_push($allowstatus, COURSE_STATUS_RESUBMITTED);
                array_push($allowstatus, COURSE_STATUS_APPROVED);
                array_push($allowstatus, COURSE_STATUS_PUBLISHED);
                array_push($allowstatus, COURSE_STATUS_SUSPENDEDDATE);
                array_push($allowstatus, COURSE_STATUS_SUSPENDEDAUTHOR);

            case ROLE_HEADEDITOR:
                // head editors have more limited options but can apply at any time
                array_push($allowstatus, COURSE_STATUS_NEEDSCHANGE);
                array_push($allowstatus, COURSE_STATUS_APPROVED);
                array_push($allowstatus, COURSE_STATUS_PUBLISHED);
                break;

            case ROLE_LPEDITOR:
                // lp editors can only submit for approval at the appropriate juncture
                if ( $currentstatus ==  COURSE_STATUS_NOTSUBMITTED ) {
                     array_push($allowstatus, COURSE_STATUS_SUBMITTED);
                }
                if ( $currentstatus ==  COURSE_STATUS_NEEDSCHANGE ) {
                     array_push($allowstatus, COURSE_STATUS_RESUBMITTED);
                }
                break;

            default:
                // if anyone else should somehow get here (they shouldn't) they won't be given any options
                break; 
        }

    }


    if (!empty($allowstatus)){
        $statusstr = implode(',', $allowstatus);

        $sql = "SELECT id, shortname, displayname, description
                  FROM {$CFG->prefix}course_approval_status 
                WHERE id IN ( {$statusstr} ) 
              ORDER BY id";
 
        if ($status_array = get_records_sql($sql)) {
            return $status_array;
        }

    }

    return;

}

/**
* prints the assign me this user stuff (18.4/18.5)
*
* @param object $user user being viewed
* @param object $course course being viewed (often SITE)
*/
function tao_can_assign_user($user, $course) {
    global $USER, $CFG;

    if ($USER == $user) {
        return;
    }

    // able to be overridden by valery if necessary
    $userroles = tao_get_assignable_userroles();

    $canassign = array();

    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

    foreach ($userroles as $cappart => $roledata) {
        if (has_capability('moodle/local:canassign' . $cappart, $sitecontext)
            && has_capability('moodle/local:isassignable' . $cappart, $sitecontext, $user->id, false)) {
            $canassign[$cappart] = $roledata;
        }
    }

    if (count($canassign) == 0) {
        return;
    }

    if (count($canassign) != 1) { // something weird has happened
        debugging('something weird happened in the TAO local_user_view hook - more than one role to assign: ' . implode(',', $canassign));
        return;
    }

    $cappart = array_shift(array_keys($canassign));
    $roledata = (object)array_shift($canassign);

    if (!$reciprole = get_record('role', 'shortname', $roledata->recipientrole)) {
        debugging('something weird happened in the TAO local_user_view_hook - found a role to assign by capability, but not in db: ' . $roledata->recipientrole);
        return;
    }

    if (!$assignrole = get_record('role', 'shortname', $roledata->assignerrole)) {
        debugging('something weird happened in the TAO local_user_view_hook - found a role to assign by capability, but not in db: ' . $roledata->assignerrole);
        return;
    }

    $url = $CFG->wwwroot . '/local/user/assign.php';
    $buttonstring = get_string('assignrole', 'local', $reciprole->name);
    $options = array(
        'sesskey' => sesskey(),
        'user'    => $user->id,
        'cap'     => $cappart,
        'course'  => $course->id,
        'assignrole' => $assignrole->id,
        'reciprole'  => $reciprole->id,
    );

    $usercontext = get_context_instance(CONTEXT_USER, $user->id);


    if (user_has_role_assignment($USER->id, $assignrole->id, $usercontext->id)) {
        $options['unassign'] = 1;
        $buttonstring = get_string('unassignrole', 'local', $reciprole->name);
    }

    echo '<div class="buttons">';
    print_single_button($url, $options, $buttonstring);
    echo '</div>';

}

/**
* looks for users with similar interests to you (based on tagging) 
*
* @param object $user user being viewed
*/
function tao_get_similar_users($user, $limit=10) {
    global $USER, $CFG;

    // look for other users with my tags
    //  note the weighting system - higher placed tags in the other users list will have more weight
    //  a couple of subqueries here, hopefully this scales...
    $sql = "SELECT u.id, count(u.id) as matches, sum((select max(ordering) from {$CFG->prefix}tag_instance where itemid = {$user->id})-t.ordering) as weight
              FROM {$CFG->prefix}user u, {$CFG->prefix}tag_instance t
              WHERE t.tagid in ( select tagid from {$CFG->prefix}tag_instance where itemid = {$user->id} and (itemtype = 'user' or itemtype='userclassify') )
                AND t.itemid = u.id
                AND (t.itemtype = 'user' or t.itemtype = 'userclassify')
                AND not itemid = {$user->id}
                AND NOT u.id IN ( select friendid from {$CFG->prefix}user_friend where userid = {$USER->id} )
             GROUP BY u.id
             ORDER BY weight DESC";

    return get_records_sql($sql, 0, $limit);

}
/**
* looks for users with similar interests to this course (based on tagging) 
*
* @param object $courseid course being viewed
*/
function tao_print_similar_users_course($courseid,$groupid) {
    global $USER, $CFG;

    // look for other users with tags like this LP, but not already in a group in this LP
    //  note the weighting system - higher placed tags in the other users list will have more weight
    //  a couple of subqueries here, hopefully this scales...
    $sql = "SELECT u.id, count(u.id) as matches, sum((SELECT max(ordering) FROM {$CFG->prefix}tag_instance WHERE itemid = {$courseid})-t.ordering) AS weight
              FROM {$CFG->prefix}user u, {$CFG->prefix}tag_instance t
              WHERE t.tagid in ( SELECT tagid FROM {$CFG->prefix}tag_instance WHERE itemid = {$courseid} AND (itemtype = 'course' or itemtype='courseclassification') )
                AND t.itemid = u.id
                AND (t.itemtype = 'user' or t.itemtype='userclassify')
                AND not t.itemid = {$USER->id} 
                AND NOT u.id IN ( SELECT userid 
                                  FROM {$CFG->prefix}groups_members gm, {$CFG->prefix}groups g 
                                  WHERE g.courseid={$courseid} AND g.id=gm.groupid)
                AND NOT u.id IN ( SELECT userid
                                  FROM {$CFG->prefix}group_invites
                                  WHERE courseid={$courseid} AND groupid={$groupid} )
             GROUP BY u.id
             ORDER BY weight desc 
             LIMIT 10";
    if ($users = get_records_sql($sql)) {
        return $users;
    } else {
        return false;
    }

}
/**
* gets a list of courses and their linked activities from the Certificate modules inside a course. 
*
* @param object $courseid courseid to show - if not set, return all courses for this user.
*/
function tao_certificate_get_certification_tasks($userid, $courseid=null, $showrequest=true) {
    global $CFG, $USER;
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

    $linkedactivities = array();
    $taskswarningdisplayed = false; //so that tasks warning is only displayed once.
    if (!empty($courseid)) {
        //convert $courseid to single item array for use in for loop
        $courses[$courseid] = get_record('course','id',$courseid);
    } else {
        $courses = get_my_courses($userid, 'visible DESC,sortorder ASC', '*', false, 21);
    }
    $ptrole = get_record('role', 'shortname', ROLE_PT);
    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $ispt = user_has_role_assignment($userid, $ptrole->id, $sitecontext->id);
    foreach($courses as $course) {
        $taskscomplete = true;
        $linkedactivities[$course->id] = '';

        //now check to see if PT user has a mentor:
        if ($ispt && !$ismentor) {
            $ptcontext = get_context_instance(CONTEXT_USER, $userid);
            $mtuser = get_users_by_capability($ptcontext,'moodle/local:ismt','', '', '', '', '', '', false);
            if (empty($mtuser) ) { //this user doesn't have a mentor.
                $linkedactivities[$course->id] .= '<div class="cert-nomentor">'.get_string('nomentor','block_tao_certification_path').'</div>';
                $taskscomplete = false;
            }
        }
        //first check if PT user is a member of a group.
        if ($course->groupmode=='1') { //if groupmode for this course is set to seperate.
            if ($ispt) { //check if logged in user is a PT
                //get user group.
                $groups = groups_get_all_groups($course->id, $userid);
                $certstatus = 'complete';
                if (empty($groups)) {
                    $certstatus = 'incomplete';
                }
                $linkedactivities[$course->id] .=  '<div class="cert-task-'.$certstatus.'">';
                if (!$ismentor) {
                    $linkedactivities[$course->id] .= '<a href="'.$CFG->wwwroot.'/blocks/tao_team_groups/managegroup.php?id='.$course->id.'&action=joingroup">';
                }
                $linkedactivities[$course->id] .= "<img src='$CFG->wwwroot/pix/i/group.gif' alt=''/> ".get_string('joingroup','block_tao_team_groups');
                if (!$ismentor) {
                    $linkedactivities[$course->id] .= '</a>';
                }
                if (empty($groups)) { //if user isn't in a Group - throw an error.
                    $linkedactivities[$course->id] .=  '<span class="cert_block_cross"><img src="'.$CFG->wwwroot.'/pix/i/cross_red_small.gif'.'" alt=""/></span></div>';
                    $taskscomplete = false;
                } else {
                    $linkedactivities[$course->id] .=  '<span class="cert_block_tick"><img src="'.$CFG->wwwroot.'/pix/i/tick_green_small.gif'.'" alt=""/></span></div>';
                }
            }
        }
        
        $certificates = get_records('certificate', 'course', $course->id);
        if (!empty($certificates)) {
           require_once($CFG->dirroot.'/mod/certificate/lib.php');
            foreach ($certificates as $cert) {
                //now get linked modules
                $linkedmods = get_records('certificate_linked_modules', 'certificate_id', $cert->id);
                if (!empty($linkedmods)) {
                    foreach($linkedmods as $linkedmod) {
                        if ($linkedmod->linkid > 0) {
                            $mod = get_record_sql("SELECT cm.*, md.name as modname
                                   FROM {$CFG->prefix}course_modules cm,
                                        {$CFG->prefix}modules md
                                    WHERE cm.course = '".intval($course->id)."' AND
                                          cm.id = '".intval($linkedmod->linkid)."' AND
                                          md.id = cm.module");
                            $modr = get_coursemodule_from_instance($mod->modname, $mod->instance);
                            $certstatus = 'incomplete';
                            if (certificate_activity_completed($linkedmod, $mod, $userid)) {
                                $certstatus = 'complete';
                            }
                            $linkedactivities[$course->id] .= "<div class='cert-task-$certstatus'><a href='$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id'><img src='$CFG->wwwroot/mod/$mod->modname/icon.gif' alt='' />".$modr->name.'</a>';
                            if ($certstatus =='complete') {
                                $linkedactivities[$course->id] .= '<span class="cert_block_tick"><img src="'.$CFG->wwwroot.'/pix/i/tick_green_small.gif'.'" alt="" /></span></div>';
                            } else {
                                $linkedactivities[$course->id] .= '<span class="cert_block_cross"><img src="'.$CFG->wwwroot.'/pix/i/cross_red_small.gif'.'" alt="" /></span></div>';
                                $taskscomplete = false;
                            }
                            if ($ismentor) {
                                $linkedactivities[$course->id] .= tao_user_complete($mod, $course, $userid);
                            }
                        }
                    }
                }
                if (!empty($linkedactivities[$course->id])) {
                    $linkedactivities[$course->id] = '<strong>'.$cert->name.'</strong><br/>'.$linkedactivities[$course->id];
                }
                if ($showrequest && $taskscomplete && $ispt) {
                     //check to see if there are any existing requests that have not been actioned yet.
                     if (record_exists('tao_user_certification_status', 'userid', $userid, 'status', 'approved', 'courseid', $course->id)) {
                         //$linkedactivities[$course->id] .= "<p>".get_string('requestcertificationhasbeenachieved','block_tao_certification_path')."</p>";
                     } elseif (record_exists('tao_user_certification_status', 'userid', $userid, 'status', 'submitted', 'courseid', $course->id)) {
                         $linkedactivities[$course->id] .= "<p>".get_string('requestcertificationhasbeensubmitted','block_tao_certification_path')."</p>";
                     } else {
                         if (!$ismentor) {
                             $linkedactivities[$course->id] .= "<p><a href='$CFG->wwwroot/blocks/tao_certification_path/request.php?id=$course->id'>".get_string('requestcertification','block_tao_certification_path')."</a></p>";
                         } else {
                             //todo: need to add a link to approve/decline a users request here?
                         }
                     }
                }
                if (!$taskscomplete && !$taskswarningdisplayed  && !$ismentor) {
                    $linkedactivities[$course->id] = get_string('mustcompletetasks', 'block_tao_certification_path') . '<br/><br/>'.$linkedactivities[$course->id];
                    $taskswarningdisplayed = true; //only display this warning once at the top of the block - not for each course.
                }
            }
        }
    }
    return $linkedactivities;
}
/**
* gets a list of group invites to display
*
* @param int $userid userid of user
* @param int $courseid courseid for course
*/
function tao_show_user_invites($userid, $courseid) {
    global $CFG;
//check for invites.
    $returntext = '<br/><strong>'.get_string('groupinvites', 'block_tao_team_groups') .':&nbsp;</strong><br/>';
    $invites = get_records_select('group_invites', "userid='$userid' AND courseid='$courseid'");
    if (!empty($invites)) {
        $returntext .= get_string('groupinvitesdesc', 'block_tao_team_groups').":";
        foreach($invites as $inv) {
            $grpinv = get_record('groups', 'id', $inv->groupid);
            if (empty($grpinv)) { //if empty, then this group doesn't exist so delete the invite!
                delete_records('group_invites', 'groupid', $inv->groupid);
            } else {
                $returntext .= "<br/>".$grpinv->name." ".
                               "<a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$inv->groupid&action=accept'>".get_string('accept','block_tao_team_groups')."</a> | ".
                               "<a href='$CFG->wwwroot/blocks/tao_team_groups/managegroup.php?id=$courseid&groupid=$inv->groupid&action=decline'>".get_string('decline','block_tao_team_groups')."</a>";
            }
        }
    } else {
        $returntext .= get_string('noinvites', 'block_tao_team_groups');
    }
    return $returntext;
}
/**
* displays form to create group
*
* @param int $courseid courseid for course
*/
function tao_new_group_form($courseid) {
    global $CFG;
    return '<form action="'.$CFG->wwwroot.'/blocks/tao_team_groups/managegroup.php" method="post">'.
                   '<input type="hidden" name="id" value="'.$courseid.'"/>'.
                   '<br/><strong>'.get_string('startmygroup', 'block_tao_team_groups') .':&nbsp;</strong>'.
                   print_textfield('groupname', '', '',15, 0, true).
                   '<input type="submit" value="'.get_string('createnewgroup', 'block_tao_team_groups').'"/></form>'.
                   get_string('createnewgroupdesc', 'block_tao_team_groups');
}
/**
* checks user enrollment and enrols the user as a participating teacher in the course if required.
* must be called before assigning a user to a group.
*
* @param int $userid userid of user
* @param int $courseid courseid for course
**/
function tao_check_enrol($userid, $courseid) {
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $roles = get_user_roles($context, $userid);
    if (empty($roles)) { //user has no roles in this course.
        $roleid = get_field('role', 'id', 'shortname', ROLE_PT);
        role_assign($roleid, $userid, 0, $context->id);
    }
}

function tao_user_complete($cm, $course, $userid) {
    global $CFG;
    $return = '';
    $user = get_record('user', 'id', $userid);
    $instance = get_record("$cm->modname", "id", "$cm->instance");

    $libfile = "$CFG->dirroot/mod/$cm->modname/lib.php";
    $cm->modfullname = get_string('modulename', $cm->modname);
    if (file_exists($libfile)) {
        require_once($libfile);
        $user_complete = $cm->modname."_user_complete";
        if (function_exists($user_complete)) {
            ob_start();
            $user_complete($course, $user, $cm, $instance);
            $output = ob_get_contents();
            ob_end_clean();
            if (!empty($output)) {
                $return .= '<div class="taousercomplete"><div class="taousercontent">'.$output."</div></div>";
            }
       }
    }
    return $return;
}

/** prints friend box for the given user
*
* @param obj $user
* @param text $type
**/

function tao_print_friend_box($user, $type='display') {
    global $CFG;

    $profilelink = $CFG->wwwroot .'/user/view.php?id='. $user->id;
    $fullname = fullname($user);
    $alt = $fullname;
    $messagepath = $CFG->wwwroot . '/message/discussion.php?id=' . $user->id;
    $messagejs = "return openpopup('$messagepath', 'message_18', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0); return true;";
    $removelink = $CFG->wwwroot . '/local/user/friend.php?userid=' . $user->id . '&action=unfriend';

    $html = '';
    $html .= '<div class="tao_friend_box">';

    $html .= '<a href="' . $profilelink  . '">';
    if ($user->picture) {
        $html .= '<img alt="'. $alt .'" class="friend-image" src="'. $CFG->wwwroot .'/user/pix.php/'. $user->id .'/f1.jpg" />';
    } else {
        $html .= '<img alt="'. $alt .'" class="friend-image" src="'. $CFG->wwwroot .'/pix/u/f1.png" />';
    }
    $html .= '</a>';

    $html .= '<div class="tao_friend_name"><a href="' . $profilelink . '">' . $fullname . '</a></div><br/>';

    switch ($type) {  
        case 'display':
            $html .= '<img src="/pix/t/message.gif" alt="" /> <a href="' . $messagepath . '" onclick="' . $messagejs . '">Send Message</a><br/>';
            $html .= '<img src="/pix/t/delete.gif" alt="" /> <a href="' . $removelink . '">Remove</a>';
            break;
        case 'request':
            $html .= '<a href="' . $CFG->wwwroot . '/local/user/friend.php?userid=' . $user->id . '&action=accept"> <img src="/pix/t/clear.gif"> '.get_string('accept','local').'</a><br/>';
            $html .= '<a href="' . $CFG->wwwroot . '/local/user/friend.php?userid=' . $user->id . '&action=decline"> <img src="/pix/t/delete.gif"> '.get_string('decline','local').'</a>';
            break;
        case 'pending':
            $html .= '<img src="/pix/t/message.gif" alt=""/> <a href="' . $messagepath . '" onclick="' . $messagejs . '">Send Message</a><br/>';
            $html .= '<img src="/pix/t/delete.gif" alt=""/> <a href="' . $removelink . '">' . get_string('cancel') . '</a>';
            break;
    }

    $html .= '</div>';
    echo $html;

}

/** prints neighbours box for the given user
*
* @param obj $user
**/

function tao_print_neighbour_box($user, $return=false) {
    global $CFG;

    $profilelink = $CFG->wwwroot .'/user/view.php?id='. $user->id;
    $fullname = fullname($user);
    $alt = $fullname;
    $messagepath = $CFG->wwwroot . '/message/discussion.php?id=' . $user->id;
    $messagejs = "return openpopup('$messagepath', 'message_18', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0); return true;";
    $removelink = $CFG->wwwroot . '/local/user/friend.php?userid=' . $user->id . '&action=unfriend';

    $html = '';

    $html .= '<div class="tao_neighbour_box">';

    $html .= '<a href="' . $profilelink  . '">';
    if ($user->picture) {
        $html .= '<img alt="'. $alt .'" class="neighbour-image" src="'. $CFG->wwwroot .'/user/pix.php/'. $user->id .'/f1.jpg" />';
    } else {
        $html .= '<img alt="'. $alt .'" class="neighbour-image" src="'. $CFG->wwwroot .'/pix/u/f1.png" />';
    }
    $html .= '</a>';

    $html .= "<br/>$fullname";

    $html .= '</div>';


    if ($return) {
        return $html;
    } else {
        echo $html;
    }

}

/** checks whether rafl learning path authoring mode is enabled at config 
*
* @return bool 
**/

function tao_rafl_mode_enabled() {
    global $CFG;

    if(isset($CFG->raflmodeenabled)){
	if($CFG->raflmodeenabled==1){
	    return true;
	}
    }

    return false;
}

/** sets the the user_rafl mode based on certain conditions.
*     note that this only sets if not already set, so if called multiple times on
*       a page will only be executed once.
*
* @param object $course
*/
function tao_set_user_rafl_mode($course) {
    global $USER;
    $rafl   = optional_param('rafl', '', PARAM_ALPHA);  // RAFL mode switch
    if ($rafl) {
        $USER->raflmode = $rafl;
    }
	if ($course->learning_path_mode == LEARNING_PATH_MODE_RAFL) {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        if (has_capability('moodle/local:canviewraflmod', $coursecontext, NULL, false) or
            has_capability('moodle/local:canassignselftorafl', get_context_instance(CONTEXT_COURSE, SITEID))) {
            if (!isset($USER->raflmode)) {
                $USER->raflmode = 1;
            }
        } else {
            $USER->raflmode = 0;
        }
    } else {
        $USER->raflmode = 0;
    }
}


//functions used by taoview.php
function tao_print_rating_menu($artefactid, $userid, $scale) {
/// Print the menu of ratings as part of a larger form.
/// If the entry has already been - set that value.
/// Scale is an array of ratings
    static $strrate;

    if (!$rating = get_record("taoview_ratings", "userid", $userid, "artefactid", $artefactid)) {
        $rating->rating = -999;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "local");
    }

    choose_from_menu($scale, $artefactid, $rating->rating, "$strrate...", '', -999, false, false, 0, '', false, false, 'taoviewratingmenu');
}
function tao_get_ratings_mean($artefactid, $scale, $ratings=NULL) {
/// Return the mean rating of a entry given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("taoview_ratings", "artefactid", $artefactid)) {
            foreach ($rates as $rate) {
                $ratings[] = $rate->rating;
            }
        }
    }

    $count = count($ratings);

    if ($count == 0) {
        return "";

    } else if ($count == 1) {
        return '<span class="taoviewratingstars">'.$scale[$ratings[0]].'</span>';

    } else {
        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating;
        }
        $mean = round( ((float)$total/(float)$count) + 0.001);  // Little fudge factor so that 0.5 goes UP

        if (isset($scale[$mean])) {
            return '<span class="taoviewratingstars">'.$scale[$mean]."</span> ($count)";
        } else {
            return '<span class="taoviewratingstars">'."$mean</span> ($count)";    // Should never happen, hopefully
        }
    }
}
function tao_print_ratings($artefactid, $possiblevalues, $return=false) {
    $str =  get_string('rating','local').': '.tao_get_ratings_mean($artefactid, $possiblevalues);
    if ($return) {
        return $str;
    }
    echo $str;
}

function tao_create_lp($data, $author, $creatorroleid, $createtemplate=0, $preferences=array()) {
    global $CFG;

    if (empty($data)) {
        error("invalid call to tao_create_lp");
    }
        // get course template
        if (! ($course = get_record('course', 'id', $data->course_template)) ) {
            error('Invalid course id');
        }

        // get a handle on the most recent backup for the selected course
        $wdir = "/backupdata";

        $fullpath = $CFG->dataroot."/".$course->id.$wdir;
        $dirlist = array();
        $filelist = array();

        if (!is_dir($fullpath)) {
            error("No templates for selected course");
        }

        $directory = opendir($fullpath);             // Find all files

        while (false !== ($file = readdir($directory))) {
            if ($file == "." || $file == "..") {
                continue;
            }
            if (strchr($file,".") != ".zip") {
                continue;
            }
            if (is_dir($fullpath."/".$file)) {
                $dirlist[] = $file;
            } else {
                $filelist[] = $file;
            }
        }
        closedir($directory);
        asort($filelist);

        // get the last file
        $file = array_pop($filelist);
        $fullpathtofile = "$fullpath/$file";

        if ( !$file ) {
            error("No templates for selected course");
        }

        // attempt to create the new course
        if (!$newcourse = create_course($data)) {
            print_error('coursenotcreated');
        }

        $context = get_context_instance(CONTEXT_COURSE, $newcourse->id);
        $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
        // assign our initial user - note this means automatic assigning by the backup should be skipped, which is based
        //   the on the manage:activities role being present
        role_assign($creatorroleid, $author->id, 0, $context->id);

        if ($data->learning_path_mode == LEARNING_PATH_MODE_RAFL) {
            //now set role override for PT users to prevent them from being able to add blocks and activities.
            $ptroleid = get_field('role', 'id', 'shortname', ROLE_PT);
            $ispt = user_has_role_assignment($author->id, $ptroleid, $sitecontext->id);
            if ($ispt) {
                //prevent from being able to change the structure of the pages.
                assign_capability('format/learning:manageactivities', CAP_PREVENT, $creatorroleid, $context->id);
            }
        }
        // create default the TAO Course (LP) Forum
        /// ** load this in template instead? ** local_create_forum($newcourse->id, SEPARATEGROUPS, get_string('defaultforumname', 'local'), get_string('defaultforumintro', 'local'));

        // create default the TAO Course (LP) Wiki
        /// ** load this in template instead? ** local_create_wiki($newcourse->id, SEPARATEGROUPS, get_string('defaultwikiname', 'local'), get_string('defaultwikisummary', 'local'));

        if (!$createtemplate) {
            // set course status
            if (!tao_update_course_status(COURSE_STATUS_NOTSUBMITTED, "Created new learning path from template", $newcourse)) {
                error('could not update status');
            }
        }

        //set up preferences for pasign to backup_file_silently
        $preferences['course_format'] = 1;

        if ($data->learning_path_mode == LEARNING_PATH_MODE_STANDARD) {
            // load the backup data into course //TODO some way of validating this
            import_backup_file_silently($fullpathtofile, $newcourse->id, false, false, $preferences ,RESTORETO_CURRENT_DELETING);

            // if valid
            // set course status
            if (!tao_update_course_status(COURSE_STATUS_NOTSUBMITTED, "Created new learning path from template", $newcourse)) {
                error('could not update status');
            }

            // ensure we can use the course right after creating it
            // this means trigger a reload of accessinfo...
            mark_context_dirty($context->path);

            $author->raflmode = 0;

            // Redirect to course page
            return $newcourse->id;

        } elseif ($data->learning_path_mode == LEARNING_PATH_MODE_RAFL) {
            //set pref to not restore pages for all RAFL imports:
            $preferences['nopages'] = 1;
            // load the template, but leave out actual content pages - they are created by rafl structure.
            //     note: we must do this before adding the new pages otherwise this process will remove them
            import_backup_file_silently($fullpathtofile, $newcourse->id, false, false, $preferences);

	    // todo do a non-fatal check for rafl module, and give a friendly message if not found
            require_once($CFG->dirroot . '/mod/rafl/lib.php');
            require_once($CFG->dirroot . '/mod/rafl/locallib.php');
            require_once($CFG->dirroot . '/course/format/learning/lib.php');
            require_once($CFG->dirroot . '/course/format/learning/pagelib.php');
            $rafl = new localLibRafl();

            $pageid = $newcourse->id; // pageid is actually courseid in the blockinstance context.  i know!
            $pagetype = 'course-view';

            // first create the summary page
            $page = new stdClass;
            $page->nameone         = get_string('lpsummarypagetitle', 'local');
            $page->nametwo         = get_string('lpsummarypagetitle', 'local');
            $page->courseid        = $newcourse->id;
            $page->display         = 1;
            $page->showbuttons     = 3;

            $summarypageid = insert_record('format_page', $page);

            // add the standard blocks for a summary page
            $instanceid = tao_add_learningpath_block('tao_learning_path_summary', $pageid, $pagetype, "Learning Stations");
            if (!empty($instanceid)) {
                tao_add_learningpath_pageitem($summarypageid, $instanceid);
            }

            // now the station pages
            $items = $rafl->get_lp_item_structure(0);  // todo don't hardcode this parameter

            foreach ($items as $item) {
                 // check for existing station/pie, if not there insert as a format_page
                 $sql = "SELECT id FROM {$CFG->prefix}format_page WHERE courseid = {$newcourse->id} AND rafl_item = {$item->question_item_id}";
                 $exists = get_records_sql($sql);

                 if ( empty($exists) ) {
                     // add the format page
                     $page = new stdClass;
                     $page->nameone         = $item->title;
                     $page->nametwo         = $item->title;
                     $page->courseid        = $newcourse->id;
                     $page->display         = 1;
                     $page->showbuttons     = 3;
                     $page->parent          = $summarypageid;
                     $page->rafl_item       = $item->question_item_id;
                     $formatpageid = insert_record('format_page', $page);

                     // add the title block
                     $instanceid = tao_add_learningpath_block('html', $pageid, $pagetype, '', '<h1>' . $item->title . '</h1>');
                     if (!empty($instanceid)) {
                         tao_add_learningpath_pageitem($formatpageid, $instanceid);
                     }
                 }
            }

            // now add the question blocks
            $country_item_id = $rafl->get_rafl_item_id_by_country('uk');
            $items = $rafl->get_lp_item_structure($country_item_id); // todo find a better way to pass this parameter

            foreach ($items as $item) {

                 // db integrity - at least check for existing station/pie
                 $sql = "SELECT id FROM {$CFG->prefix}format_page WHERE courseid = {$newcourse->id} AND rafl_item = {$item->pie_item_id}";
                 $formatpage = get_record_sql($sql);

                 if ( !empty($formatpage) ) {
                     // insert question as a block and format_page item on this page
                     $instanceid = tao_add_learningpath_block('tao_lp_qa', $pageid, $pagetype, $item->title);

                     // create a new page item that links to the instance
                     if (!empty($instanceid)) {
                         tao_add_learningpath_pageitem($formatpage->id, $instanceid, $item->question_item_id);
                     }

                 } else {
                      debugging("pie {$item->pie_item_id} is not in the database");
                 }

            }

            // add the rafl module to the course
            $mod = new object();
            $mod->course = $newcourse->id;
            $mod->name = 'RAFL Authoring Module';
            $mod->intro = 'RAFL Authoring Module';

            $instanceid = rafl_add_instance($mod);

            if (! $module = get_record("modules", "name", 'rafl')) {
                error("This module type doesn't exist");
            }

            if (! $cs = get_record("course_sections", "section", 0, "course", $newcourse->id)) {
                error("This course section doesn't exist");
            }

            $cm->section          = 0; //$cs->id;
            $cm->course           = $newcourse->id;
            $cm->module           = $module->id;
            $cm->modulename       = $module->name;
            $cm->instance         = $instanceid;

            // connect to course
            if (! $cm->coursemodule = add_course_module($cm) ) {
                error("Could not add a new course module");
            }

            $sectionid = add_mod_to_section($cm);
            set_field("course_modules", "section", $sectionid, "id", $cm->coursemodule);

            // create rafl_share
            $rafl->create_share($newcourse->id, $author->id);

            // if valid
            // set course status
            if (!tao_update_course_status(COURSE_STATUS_NOTSUBMITTED, "Created new learning path from RAFL module", $newcourse)) {
                error('could not update status');
            }

            // ensure we can use the course right after creating it
            // this means trigger a reload of accessinfo...
            mark_context_dirty($context->path);

            $author->raflmode = 1;

            rebuild_course_cache($newcourse->id);

            // redirect to participants page
            return $newcourse->id;

        } else {
            error("invalid authoring mode");
        }

}

//LP Functions
function tao_add_learningpath_block($type = 'html', $pageid, $pagetype, $title, $text = '') {
    global $CFG;

    $block   = get_record('block', 'name', $type);

    // get the next weight value
    $weight = get_record_sql('SELECT 1, MAX(weight) + 1 '.sql_as().' nextfree
                            FROM '. $CFG->prefix .'block_instance
                           WHERE pageid = '. $pageid .'
                             AND pagetype = \''. $pagetype .'\'
                             AND position = \''. BLOCK_POS_LEFT .'\'');

    if (empty($weight->nextfree)) {
        $weight->nextfree = 0;
    }

    // insert the block instance record
    $newinstance = new stdClass;
    $newinstance->blockid    = $block->id;
    $newinstance->pageid     = $pageid;
    $newinstance->pagetype   = $pagetype;
    $newinstance->position   = BLOCK_POS_LEFT; // Make sure we keep them all in same column
    $newinstance->weight     = $weight->nextfree;
    $newinstance->visible    = 1;
    $newinstance->configdata = '';

    $instanceid = $newinstance->id = insert_record('block_instance', $newinstance);

    if ($newinstance and ($obj = block_instance($block->name, $newinstance))) {
        // Return value ignored
        $obj->instance_create();

        // update the configdata for the block (i.e. content)
        $blockdata->title   = $title;
        $blockdata->text    = $text;
        if(!$obj->instance_config_save($blockdata)) {
            error('Error saving block configuration');
        }

    }

    return $instanceid;
}

function tao_add_learningpath_pageitem($formatpageid, $instanceid, $raflitemid = null) {

    // Create a new page item that links to the instance
    $pageitem                = new stdClass;
    $pageitem->pageid        = $formatpageid;
    $pageitem->cmid          = 0;
    $pageitem->blockinstance = $instanceid;
    $pageitem->position      = BLOCK_POS_CENTER;
    $pageitem->sortorder     = page_get_next_weight($pageitem->pageid, $pageitem->position);
    $pageitem->visible       = 1;
    $pageitem->rafl_item     = $raflitemid;

    if (!insert_record('format_page_items', $pageitem) ) {
        error('could not insert format_page_item');
    }

}
//function to remove tags for lps that aren't published
function tao_filter_tags($tags) {
    $lpstatus = array();
    foreach ($tags as $tag) {
        if (isset($tag->itemtype) && $tag->itemtype=='courseclassification') {
            //check if this tag's LP has been published.
            if(!isset($lpstatus[$tag->itemid])) {
                //this lp hasn't been checked yet - pull it from the db
                $lpstatus[$tag->itemid] = get_field('course','approval_status_id', 'id', $tag->itemid);
            }
            if ($lpstatus[$tag->itemid] <> COURSE_STATUS_PUBLISHED) {
                //if this tag is from a course that hasn't been published, remove it!
                unset($tags[$tag->id]);
            }
        }
    }
    return $tags;
}
//function to replace the standard cloud call - only includes tags for lps that are published.
function tao_get_tags_for_cloud($nr_of_tags) {
    global $CFG;
    $tagsincloud = get_records_sql('SELECT tg.rawname, tg.id, tg.name, tg.tagtype, COUNT(ti.id) AS count, tg.flag '.
        'FROM '. $CFG->prefix .'tag_instance ti INNER JOIN '. $CFG->prefix .'tag tg ON tg.id = ti.tagid '.
        'LEFT JOIN '. $CFG->prefix .'course c ON c.id=ti.itemid AND ti.itemtype=\'courseclassification\' AND c.approval_status_id='.COURSE_STATUS_PUBLISHED.
        ' WHERE ti.itemtype != \'tag\' AND ((ti.itemtype != \'courseclassification\') or (ti.itemtype =\'courseclassification\' AND c.id is not null)) '.
        'GROUP BY tg.id, tg.rawname, tg.name, tg.flag, tg.tagtype '.
        'ORDER BY count DESC, tg.name ASC', 0, $nr_of_tags);
    if (empty($tagsincloud)) {
         return array();
    }
    return $tagsincloud;
}
function tao_display_certifications($userid) {
    global $CFG;
    $returntext = '';
    $showrequestlink = false;
    $certifications = get_records_select('tao_user_certification_status',"userid='$userid' AND status='approved'");
    if (!empty($certifications)) {
        $certmodid = get_field('modules', 'id','name','certificate');
        $returntext .= '<h3>'.get_string('currentcertifications', 'block_tao_certification_path').':</h3>';
        foreach($certifications as $cert) {
            //get id of certificate
            $certid = get_field('certificate', 'id', 'course', $cert->courseid, 'setcertification', $cert->certtype);
            //get cm for this cert
            $cmid = get_field('course_modules','id', 'course', $cert->courseid, 'module', $certmodid, 'instance', $certid);
            $returntext .= "<img src='$CFG->wwwroot/mod/certificate/icon.gif'>".
                           "<a href='$CFG->wwwroot/mod/certificate/view.php?id=$cmid'>".
                           get_string($cert->certtype, 'block_tao_certification_path').
                           "</a><br/>";
            if ($cert->certtype =='certified_pt') { //only show request link if user isn't certified_pt
               $showrequestlink = false;
            }
        }
    }
    $returnclass = new stdclass();
    $returnclass->text = $returntext;
    $returnclass->notpt = $showrequestlink;
    return $returnclass;
}

?>