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
 * and will be included automatically in setup.php along
 * with other core libraries.
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/local/tao.php');
require_once($CFG->dirroot.'/local/my/collaborationpagelib.php');


/**
 * hook for the messagebyroletab.
 * not correctly namespaced because of limitations in messaging.
 *
 * unfortunately we don't seem to be able to pass
 * parameters from the request here...
*/
function message_print_byrole() {
    global $CFG, $USER;

    require_once($CFG->dirroot . '/local/lib/messagelib.php');

    $target  = optional_param('target', 0, PARAM_INT);
    $course  = optional_param('lp', 0, PARAM_INT);
    $page    = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', 10, PARAM_INT);

    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $cansearch = has_capability('moodle/local:cansearchforlptomessage', $sitecontext);
    $searchform = null;
    $messageform = null;

    $courses = array();
    $totalcount = 0;

    if (!empty($course) || !empty($target)) {
        $c = get_record('course', 'id', $course);
        $targetobject = (object)tao_message_target_get($target, $c);
        if ($count = tao_message_count_recipients_by_target($targetobject, $c)) {
            $targetobject->key = $target;
            require_capability('moodle/local:' . $targetobject->capability, get_context_instance(CONTEXT_COURSE, $c->id));
            // give the message send form
            require_once($CFG->dirroot . '/local/forms.php');
            $messageform = new tao_message_send_form('', array('course' => $c, 'target' => $targetobject));
            if ($data = $messageform->get_data()) {
                // send message
                $eventdata = array(
                    'body'   => $data->body,
                    'from'   => $USER->id,
                    'format' => $data->format,
                    'course' => $c,
                    'target' => $targetobject,
                );
                events_trigger('tao_message_role', $eventdata);
                echo get_string('messagequeued', 'local');
                print_continue($CFG->wwwroot . '/message/index.php?tab=byrole');
                return;
            } else if (!$messageform->is_cancelled()) {
                $messageform->display();
                return;
            }
        } else {
            notify(get_string('messagenorecipients', 'local'));
        }
    }

   if ($cansearch) { // set up the search form object and process any search requests
        require_capability('moodle/local:cansearchforlptomessage', $sitecontext);
        require_once($CFG->dirroot . '/local/forms.php');
        $searchform = new tao_message_lpsearch_form('', array(), 'get');
        if ($data = $searchform->get_data()) {
            $search = trim(strip_tags($data->search)); // trim & clean raw searched string
            if ($search) {
                $searchterms = explode(" ", $search);    // Search for words independently
                foreach ($searchterms as $key => $searchterm) {
                    if (strlen($searchterm) < 1) {
                        unset($searchterms[$key]);
                    }
                }
                $search = trim(implode(" ", $searchterms));
            }
            if (count($searchterms) > 0) {
                $courses = get_courses_search($searchterms, "fullname ASC", $page, $perpage, $totalcount);
            }
            if (empty($courses)) {
                $nosearchresults = true;
            }
        }
    }

    // print the main part of the page
    $targets = (object)tao_message_targets();

    // SITE wide message groups first
    $sitecontent = '';
    foreach ($targets->site as $key => $target) {
        $target = (object)$target;
        $target->key = $key;
        $sitecontent .= tao_print_target($target);
    }

    $lpcontent = '';
    if (empty($courses)) { // if we haven't come from a search, get all courses they have a direct relationship with
        if (has_capability('moodle/local:hasdirectlprelationship', $sitecontext)) {
            // Non-cached - get accessinfo
            if (isset($USER->access)) {
                $accessinfo = $USER->access;
            } else {
                $accessinfo = get_user_access_sitewide($USER->id);
            }
            $courses = get_user_courses_bycap($USER->id, 'moodle/local:hasdirectlprelationship', $accessinfo, false, 'c.fullname', array('fullname'));
        }
    }

    if ($courses) { // either from a search, or from the 'direct' relationships
        foreach ($courses as $course) { // print the targets for each course
            $coursecontent = '';
            foreach ($targets->lp as $key => $target) {
                $target = (object)$target;
                $target->key = $key;
                $coursecontent .= tao_print_target($target, $course, !empty($search));
            }
            if (!empty($coursecontent)) {
                $lpcontent .= '<b>' . $course->fullname  . '</b><br />' . $coursecontent . '<br />';
            }
        }
        if (!empty($searchform) && !empty($search)) {
            $url = $searchform->get_fake_url($CFG->wwwroot . '/message/index.php', array('search' => urlencode(stripslashes($search)), 'perpage' => $perpage));
            $lpcontent .= print_paging_bar($totalcount, $page, $perpage, $url, 'page', ($perpage == 99999), true);
        }
    }

    if (empty($sitecontent) && empty($lpcontent) && empty($cansearch)) {
        print_error('nomessagetargets', 'local');
    }
    if (!empty($sitecontent)) {
        print_heading(get_string('sitelists', 'local'));
        echo $sitecontent . '<br /><br />';
    }
    if (!empty($lpcontent)) {
        print_heading(get_string('lplists', 'local'));
        echo $lpcontent;
        $lpprinted = true;
    }
    if (!empty($searchform)) {
        if (empty($lpprinted)) {
            print_heading(get_string('lplists', 'local'));
        } else {
            echo '<br /><br />';
        }
        if (!empty($nosearchresults)) {
            notify(get_string('noresults'));
        }
        $searchform->display();
    }
}


/**
* implement the local hook to run some custom code at course backup time.
* see lib/locallib.php
*
* @param filehandle $bf open file handle for writing to
* @param stdclass object $course course we're backing up
* @param integer $startlevel level we're currently using
*
* @return boolean
*/
function local_course_backup($bf, $course, $startlevel) {
    // in the case of TAO we want to primarily make sure we back up course classification data.
    if (!$values = tao_get_classifications(false, $course->id)) {
        return true;
    }
    $status = fwrite($bf, start_tag('TAO_CLASSIFICATIONS', $startlevel, true));
    foreach ($values as $value) {
        $status = $status && fwrite($bf, start_tag('TAO_CLASSIFICATION', $startlevel+1, true));

        $status = $status && fwrite($bf, full_tag('TAO_TYPE', $startlevel+2, false, $value->type));
        $status = $status && fwrite($bf, full_tag('TAO_NAME', $startlevel+2, false, $value->name));
        $status = $status && fwrite($bf, full_tag('TAO_VALUE', $startlevel+2, false, $value->value));

        $status = $status && fwrite($bf, end_tag('TAO_CLASSIFICATION', $startlevel+1, true));
    }
    $status = $status && fwrite($bf, end_tag('TAO_CLASSIFICATIONS', $startlevel, true));

    // backup the course.approval_status_id field
    $status = $status && fwrite($bf, full_tag('TAO_COURSE_APPROVAL_STATUS', $startlevel, false, $course->approval_status_id));

    return $status;
}

/**
* implement the local hook to run some custom code at course restore time.
* see lib/locallib.php
*
* @param object $parser      moodleparser object, passed by reference
* @param string $tagname     current tag being read
* @param int    $level       current depth of XML where LOCAL tag is found +1
* @param string $errorstring string to fill with errors
*
* @return boolean
*/
function local_course_restore_getdata(&$parser, $tagname, $level, &$errorstr) {
    if (empty($parser->info->localcoursedata)) {
        $parser->info->localcoursedata = array();
    }

    if ($tagname == 'TAO_COURSE_APPROVAL_STATUS') {
        $parser->info->localcourseapprovalstatus = $parser->getContents();
        return true;
    }

    if ($parser->tree[$level] != 'TAO_CLASSIFICATIONS') {
        return true; // nothing to do
    }

    if ($tagname == 'TAO_CLASSIFICATIONS') {
        return true;
    }

    $mapping = array(
        'TAO_TYPE'  => 'type',
        'TAO_NAME'  => 'name',
        'TAO_VALUE' => 'value',
    );

    if ($tagname == 'TAO_CLASSIFICATION') {
        tao_localrestore_create_key($parser);
        return true; // just set ourselves up, nothing else to do until we go one step deeper
    }

    $err = '';
    if (!array_key_exists($tagname, $mapping)) {
        $err = "Error reading local course data - weird XML structure, couldn't understand $tagname";
    }
    if (empty($parser->info->localcourserestorekey)) {
        if ($tagname == 'TAO_TYPE' && count($parser->info->localcoursedata) == 0) {
            // work around very strange oddity of the xml parser in moodle
            tao_localrestore_create_key($parser);
        } else {
            $err = "Error reading local course data - weird XML structure, got tag $tagname but no container for it";
        }
    }

    if (empty($err)) {
        // actually store the data into the local cache
        $parser->info->localcoursedata[$parser->info->localcourserestorekey]->{$mapping[$tagname]} = $parser->getContents();
        return true;
    }

    if (!defined('RESTORE_SILENTLY')) {
        notify($err);
    } else if (!empty($err)) {
        $errorstr = $err;
    }

    return false;
}

/**
* implement the local hook to run some custom code at course restore time.
* see lib/locallib.php
*
* @param int $courseid the id of the course to restore into
* @param stdclass $data the data the parser has created
*
* @return boolean
*/
function local_course_restore_createdata($courseid, $data) {
    if (empty($data->localcoursedata) || !is_array($data->localcoursedata)) {
        return true;
    }
    $seentypes = array();
    $seenvalues = array();

    if (!empty($data->localcourseapprovalstatus)) {
        $course = new object();
        $course->id = $courseid;
        $course->approval_status_id = $data->localcourseapprovalstatus;
        update_record('course', $course);
    }

    foreach ($data->localcoursedata as $c) {
        if (empty($c) || count(array_keys((array)$c)) == 0) {
            continue;
        }
        if (!array_key_exists($c->name, $seentypes)) {
            if (!$typeid = get_field('classification_type', 'id', 'type', $c->type, 'name', $c->name)) {
                $typeid = insert_record('classification_type', $c);
            }
            $seentypes[$c->name] = $typeid;
        }
        $valuekey = $seentypes[$c->name] . '|' . $c->value;
        if (!array_key_exists($valuekey, $seenvalues)) {
            if (!$valueid = get_field('classification_value', 'id', 'type', $typeid, 'value', $c->value)) {
                $valueid = insert_record('classification_value', (object)array('type' => $seentypes[$c->name], 'value' => $c->value));
            }
            $seenvalues[$valuekey] = $valueid;
        }
        insert_record('course_classification', (object)array('course' => $courseid, 'value' => $seenvalues[$valuekey]));
    }
    return true;
}


/**
* hook to print extra stuff at the bottom of the user view page
*
* @param object $user user being viewed
* @param object $course course being viewed (often SITE)
*/
function local_user_view($user, $course) {
    global $CFG, $USER, $MNET;

    if ($CFG->mnet_dispatcher_mode =='strict') {
    require_once($CFG->dirroot . '/mnet/xmlrpc/client.php'); //mnet client library
    /// Setup MNET environment
    if (empty($MNET)) {
        $MNET = new mnet_environment();
        $MNET->init();
    }

/// Setup the server
    $host = get_record('mnet_host','name', 'localmahara'); //we retrieve the server(host) from the 'mnet_host' table
    if (!empty($host)) {
        $users[] = $user->username;
        $mnet_peer = new mnet_peer();                          //we create a new mnet_peer (server/host)
        $mnet_peer->set_wwwroot($host->wwwroot);               //we set this mnet_peer with the host http address

        //now call the method and get data
        $client = new mnet_xmlrpc_client();        //create a new client
        $client->set_method('local/mahara/rpclib.php/get_user_ids'); //tell it which method we're going to call
        $client->add_param($users);
        $client->send($mnet_peer);                 //Call the server
                     //Receive the server response
        if (!empty($client->response[$user->username])) {
            $mahurl = $CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$host->id.'&wantsurl=user/view.php?id='.$client->response[$user->username];
            echo '<p align=center><a href="'.$mahurl.'">' . get_string('viewmaharaprofile', 'local') . '</a></p>';
        }
    }
    }

    if ($USER->id <> $user->id) {
        // include the add as friend button
        print_spacer(10);
        echo '<table width="80%" class="userinfobox" summary="">';
        echo '<tr>';
        echo '<td class="content">';

        $url = $CFG->wwwroot . '/local/user/friend.php';

        $options['userid'] = $user->id;

        $a->user = $user->firstname;

        if ($userfriend = get_record('user_friend', 'userid', $USER->id, 'friendid', $user->id)) {
            if ($userfriend->approved) {
                // already friend
                echo '<p align=center>' . get_string('arefriend', 'local', $a) . '</p>';
                $options['action'] = 'unfriend';
                $buttonstr = get_string('removefriend', 'local');
            } else {
                //waiting for approval.
                echo '<p align=center>' . get_string('friendapprovalneeded', 'local', $a) . '</p>';
                $options['action'] = 'unfriend';
                $buttonstr = get_string('removefriendrequest', 'local');
            }
        } else {
            $buttonstr = get_string('requestfriend', 'local');
        }

        echo '<div class="buttons">';
        print_single_button($url, $options, $buttonstr);
        echo '</div>';

        echo '</td>';
        echo '</tr>';
        echo '</table>';
    

        // include the assign user button
        print_spacer(10);
        echo '<table width="80%" class="userinfobox" summary="">';
        echo '<tr>';
        echo '<td class="content">';
        tao_can_assign_user($user, $course);
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }

    // include similar users
/** HIDE THIS
    print_spacer(10);
    echo '<table width="80%" class="userinfobox" summary="">';
    echo '<tr>';
    echo '<td class="content">';
    echo $buttonstr = get_string('similarusers', 'local') . ':';
    tao_print_similar_users($user);
    echo '</td>';
    echo '</tr>';
    echo '</table>';
**/

}

/**
* checks a users access to a learning path.
*
* @param object $course
*
*/

function local_require_login($course) {
    global $CFG;

    $allowed = true;

    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    global $USER;

    // do a normal require_login, but override the default redirect to enrol
    if (require_login($course, null, null, null, false)) { // this returns true if access is DENIED
        print_error('notpermittedtoviewcourse', 'local', $CFG->wwwroot . '/local/lp/list.php');
    }

    // check whether the learning path is viewable to this user at its current status
    //      note: only perform this check on learning path courses
    if ($course->format == 'learning') {
        switch ($course->approval_status_id) {
            case COURSE_STATUS_PUBLISHED:
                // allow in all users who have got this far (must have course:view capability as checked by require_login above)
                break;
            default:
                if (!has_capability('moodle/local:viewunpublishedlearningpath', $context) && !has_capability('moodle/local:viewunpublishedlearningpath', $sitecontext) ) {
                    print_error('courseisunpublished', 'local', $CFG->wwwroot . '/local/lp/list.php');
                }
        };
    }

    // user is allowed to see the course
    return;

}

/**
* hook to add extra sticky-able page types.
*/
function local_get_sticky_pagetypes() {
    return array(
    // not using a constant here because we're doing funky overrides to PAGE_COURSE_VIEW in the learning path format
    // and it clobbers the page mapping having them both defined at the same time
        'format_learning' => array(
            'id' => 'format_learning',
            'lib' => '/course/format/learning/pagelib.php',
            'name' => get_string('learningpath', 'local')
        ),
        'tao'             => array(
            'id' => 'tao',
            'lib' => '/local/lib.php',
            'name' => 'TAO'
        ),
        PAGE_MY_COLLABORATION    => array(
            'id' => PAGE_MY_COLLABORATION,
            'lib' => '/local/my/collaborationpagelib.php',
            'name' => get_string('mycollaboration', 'local')
        ),
    );
}

/***
* hook to add local buttons to the course index screen(s)
*/

function local_print_course_index_buttons($category = null) {

    // no longer print the button here - following code left for reference
    return;

    $options = array();
    $options = array('category' => get_field('course_categories', 'id', 'parent', '0'));

    if (!empty($category->id)) {
        $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
    } else {
        $context = get_context_instance(CONTEXT_SYSTEM);
    }
    if (has_capability('moodle/course:create', $context)) {
        print_single_button('addlearningpath.php', $options, get_string('addnewlearningpath', 'local'), 'get');
    }
}

function local_postinst() {
    global $db, $CFG;
    $olddebug = $db->debug;
    $db->debug = $CFG->debug;

    set_config('theme', 'intel');

    $db->debug = $olddebug;

    // set frontpage blocks
    tao_reset_frontpage_blocks();

    // set sticky blocks
    tao_reset_stickyblocks();

    // create the TAO Site Forum
    $forum = local_create_forum(SITEID, NOGROUPS, get_string('siteforumname', 'local'), get_string('siteforumintro', 'local'));

    // remove guest access to the site level forum
    $required = array(
                    "mod/forum:addnews",
                    "mod/forum:createattachment",
                    "mod/forum:deleteanypost",
                    "mod/forum:deleteownpost",
                    "mod/forum:editanypost",
                    "mod/forum:initialsubscriptions",
                    "mod/forum:managesubscriptions",
                    "mod/forum:movediscussions",
                    "mod/forum:rate",
                    "mod/forum:replynews",
                    "mod/forum:replypost",
                    "mod/forum:splitdiscussions",
                    "mod/forum:startdiscussion",
                    "mod/forum:throttlingapplies",
                    "mod/forum:viewanyrating",
                    "mod/forum:viewdiscussion",
                    "mod/forum:viewhiddentimedposts",
                    "mod/forum:viewqandawithoutposting",
                    "mod/forum:viewrating",
                    "mod/forum:viewsubscribers");
    if (!$cm = get_coursemodule_from_id("forum",$forum->coursemodule, SITEID)) {
         error("Could not determine which course module this belonged to!");
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // find the exisiting list of capabilities
    $capabilities = fetch_context_capabilities($context);

    if ($roles = get_records_select('role', 'shortname IN (\'guest\')')) {
        foreach ($roles as $role) {
            foreach ($capabilities as $capability) {
                // check against the list of capabilities that we want to block
                if (!in_array($capability->name, $required)) {
                    continue;
                }
                assign_capability($capability->name, -1000, $role->id, $context->id);
            }
        }
        mark_context_dirty($context->path);
    }
    // give everyone else access to the site level forum
    $required = array(
                    "mod/forum:addnews",
                    "mod/forum:createattachment",
                    "mod/forum:deleteownpost",
                    "mod/forum:initialsubscriptions",
                    "mod/forum:rate",
                    "mod/forum:replynews",
                    "mod/forum:replypost",
                    "mod/forum:startdiscussion",
                    "mod/forum:viewanyrating",
                    "mod/forum:viewdiscussion",
                    "mod/forum:viewrating");

    if ($roles = get_records_select('role', 'shortname NOT IN (\'guest\', \'user\')')) {
        foreach ($roles as $role) {
            foreach ($capabilities as $capability) {
                // check against the list of capabilities that we want to block
                if (!in_array($capability->name, $required)) {
                    continue;
                }
                assign_capability($capability->name, 1, $role->id, $context->id);
            }
        }
        mark_context_dirty($context->path);
    }

    // add TAO site wide FAQ
    $glossary = local_create_glossary(SITEID, get_string('defaultglossaryname', 'local'), get_string('defaultglossarydescription', 'local'));

    // set of capabilities that we want to orverride
    $required = array('mod/glossary:approve',
                      'mod/glossary:comment',
                      'mod/glossary:import',
                      'mod/glossary:managecategories',
                      'mod/glossary:managecomments',
                      'mod/glossary:manageentries',
                      'mod/glossary:rate',
                      'mod/glossary:write');

    if (!$cm = get_coursemodule_from_id("glossary",$glossary->coursemodule, SITEID)) {
         error("Could not determine which course module this belonged to!");
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // find the exisiting list of capabilities
    $capabilities = fetch_context_capabilities($context);

    if ($roles = get_records_select('role', 'shortname NOT IN (\'admin\', \'superadmin\')')) {
        foreach ($roles as $role) {
            foreach ($capabilities as $capability) {
                // check against the list of capabilities that we want to block
                if (!in_array($capability->name, $required)) {
                    continue;
                }
                assign_capability($capability->name, -1000, $role->id, $context->id);
            }
        }
        mark_context_dirty($context->path);
    }

    //set up default Course Categories
    $course_cat = new stdclass();
    $course_cat->name = get_string('taocatlp', 'local');
    $course_cat->description ='';
    $course_cat->parent = 0;
    $course_cat->sortorder = 2;
    $course_cat->coursecount = 0;
    $course_cat->visible = 1;
    $course_cat->timemodified = time();
    $course_cat->depth = 1;
    $course_cat->path = "''";
    $course_cat->theme = '';
    $lpid = insert_record('course_categories', $course_cat); //insert learning path category and keep id for use later.
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $lpid);
    mark_context_dirty($catcontext->path);
 
    $course_cat->name = get_string('taocatlptemplates', 'local');
    $course_cat->sortorder = 3;
    $course_cat->visible = 0;
    $lptempid = insert_record('course_categories', $course_cat); //insert my sections category keep id for use later.
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $lptempid);
    mark_context_dirty($catcontext->path);

    $course_cat->name = get_string('taotrainingcourses','local');
    $course_cat->sortorder = 5;
    $course_cat->visible = 1;
    $taotrainingid = insert_record('course_categories', $course_cat); //insert my sections category keep id for use later.
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $taotrainingid);
    mark_context_dirty($catcontext->path);
   
    $course_cat->name = get_string('taocatworkshop','local');
    $course_cat->parent =$lpid;
    $course_cat->sortorder = 2;
    $course_cat->visible = 0;
    $catworkid = insert_record('course_categories', $course_cat); //insert published category
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $catworkid);
    mark_context_dirty($catcontext->path);

    $course_cat->name = get_string('taocatsuspended','local');
    $course_cat->parent =$lpid;
    $course_cat->sortorder = 3;
    $course_cat->visible = 0;
    $suscatid = insert_record('course_categories', $course_cat); //insert published category
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $suscatid);
    mark_context_dirty($catcontext->path);

    fix_course_sortorder(); //set paths correctly.

    //now set default categories for learning path stuff.
    set_config('lptemplatescategory', $lptempid); //set template category
    set_config('lpdefaultcategory', $catworkid);
    set_config('lppublishedcategory', $lpid);
    set_config('lpsuspendedcategory', $suscatid);

    //now prevent the admin user from having the switchrole cap in learning path category
    $catcontext = get_context_instance(CONTEXT_COURSECAT, $lpid);
    foreach (get_admin_roles() as $adminrole) {
        assign_capability('moodle/role:switchroles', CAP_PREVENT, $adminrole->id, $catcontext->id);
    }

    //now we need to do some silent restores for:
    // Learning Path Templates
    //silent restore of LP Template into $lptempid
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/backup/lib.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');
    if (file_exists($CFG->dirroot.'/local/initialcoursetemplates/lpt.zip')) {
        $course = new StdClass;
        $course->category = $lptempid;
        $course->fullname  = get_string('learningpathtemplate','local');
        $course->shortname = get_string('learningpathtemplateshortname','local');
        $course->format = 'learning'; //hardcoded for this course format - for some reason silent restore doesn't like restoring this format if not already set
        if ($newcourse = create_course($course)) {
            import_backup_file_silently($CFG->dirroot.'/local/initialcoursetemplates/lpt.zip', $newcourse->id, true, false);
        }
        //now copy the backup file into place to use as template file.
        $fullpath = $CFG->dataroot."/".$newcourse->id."/backupdata";
        if (!is_dir($fullpath)) {
            mkdir($fullpath,$CFG->directorypermissions,true);
        }
        copy($CFG->dirroot.'/local/initialcoursetemplates/lpt.zip', $fullpath.'/lpt.zip');
    }
    //now restore Training courses:
    if (file_exists($CFG->dirroot.'/local/initialcoursetemplates/taodocs1.zip')) {
        import_backup_file_silently($CFG->dirroot.'/local/initialcoursetemplates/taodocs1.zip', SITEID, true, false, array(), RESTORETO_NEW_COURSE);
    }
    if (file_exists($CFG->dirroot.'/local/initialcoursetemplates/taodocs2.1.zip')) {
        import_backup_file_silently($CFG->dirroot.'/local/initialcoursetemplates/taodocs2.1.zip', SITEID, true, false, array(), RESTORETO_NEW_COURSE);
    }
    if (file_exists($CFG->dirroot.'/local/initialcoursetemplates/taodocs2.2.zip')) {
        import_backup_file_silently($CFG->dirroot.'/local/initialcoursetemplates/taodocs2.2.zip', SITEID, true, false, array(), RESTORETO_NEW_COURSE);
    }

    //site frontpage stuff - default settings and some initial content.
    $sitecourse = get_record('course', 'id', SITEID);
    $sitecourse->numsections = 1;
    update_record('course', $sitecourse);
    
    $a = new stdclass();
    $a->myteachinglink    = $CFG->wwwroot.'/local/mahara/taoviewtaoresources.php';
    $a->mytoolslink        = $CFG->wwwroot.'/local/mahara/taoviewtaotools.php';
    $a->mylearninglink    = $CFG->wwwroot.'/local/my/learning.php';
    $a->myworklink        = $CFG->wwwroot.'/local/my/work.php';
    $a->mycollablink      = $CFG->wwwroot.'/local/my/collaboration.php';
    $a->myteachingimg = $CFG->wwwroot.'/theme/intel/pix/path/teaching.jpg';
    $a->mylearningimg = $CFG->wwwroot.'/theme/intel/pix/path/learning.jpg';
    $a->myworkimg     = $CFG->wwwroot.'/theme/intel/pix/path/work.jpg';
    $a->mytoolsimg    = $CFG->wwwroot.'/theme/intel/pix/path/tools.jpg';
    $a->mycollabimg   = $CFG->wwwroot.'/theme/intel/pix/path/collaboration.jpg';

    $sitesection = new stdclass();
    $sitesection->course = SITEID;
    $sitesection->section = 1;
    $sitesection->summary = addslashes(get_string('initialfrontpagecontent', 'local', $a));
    $sitesection->sequence = '';
    $sitesection->visible = 1;
    $sitesectionid = insert_record('course_sections', $sitesection);


    //add a link to the training courses on the frontpage: - there's probably a cleaner way to do this!!!
    $resource = new stdclass();
    $resource->course = SITEID;
    $resource->name = get_string('taotrainingcourses','local');
    $resource->type = 'file';
    $resource->reference = $CFG->wwwroot.'/course/category.php?id='.$taotrainingid;
    $resource->summary = '';
    $resource->timemodified = time();

    $trainresid = insert_record("resource", $resource);

//add record to course_modules for the resource
    $resm = new stdclass();
    $resm->course   = SITEID;
    $resm->module = get_field('modules', 'id', 'name', 'resource');
    $resm->instance = $trainresid;
    $resm->section = $sitesectionid;
    $resm->added = time();
    $resm->score = 0;
    $resm->indent = 0;
    $resm->visible = 1;
    $resm->visibleold = 1;
    $resm->groupmode = 0;
    $resm->groupingid = 0;
    $resm->groupmembersonly = 0;
    $resm->trackprogress = 0;
    $resmid = insert_record('course_modules', $resm);

    //now update the sequence for the section just created.
    $sitesection->sequence = $resmid;
    $sitesection->id = $sitesectionid;
    update_record('course_sections', $sitesection);

    //set no other stuff to display in main content section of homepage
    set_config('frontpage', '');
    set_config('frontpageloggedin', '');
    set_config('allowvisiblecoursesinhiddencategories', '1');

    // ensure custom roles are set correctly 
    tao_reset_custom_roles();
    rebuild_course_cache(SITEID);

    return true;
}


/**
* basic encapsulation of creating a forum instance - as would be done in modedit.php
* with forum_add_instance();
*
* @param int $courseid the id of the course to add forum to
* @param int $groupmode the groupmode to use eg. separate groups
* @param string $forum_name  name of the forum - title
* @param string $forum_intro intro of the forum - breif description
*/

function local_create_forum($courseid, $groupmode, $forum_name, $forum_intro) {
    global $CFG;
    require_once($CFG->dirroot."/mod/forum/lib.php");
    require_once($CFG->dirroot."/lib/grouplib.php");

    $forum = new object();

    $forum->course = $courseid;
    $forum->cmidnumber = 1;
    $forum->name = $forum_name;
    $forum->intro = $forum_intro;
    $forum->assessed = 0;
    $forum->type = 'general';
    $forum->forcesubscribe = false;
    $forum->trackingtype = FORUM_TRACKING_OPTIONAL;
    if ($CFG->enablerssfeeds && isset($CFG->forum_enablerssfeeds) && $CFG->forum_enablerssfeeds) {
      $forum->rsstype = 2;
      $forum->rssarticles = 10;
    }
    $forum->groupmode = $groupmode;
    $forum->visible = '1';
    $forum->module = get_field('modules', 'id', 'name', 'forum');
    $forum->id = forum_add_instance($forum);
    $forum->instance = $forum->id;
    $forum->section = 0; // default to first level section
    $forum->coursemodule = add_course_module($forum);
    $sectionid = add_mod_to_section($forum);
    set_field("course_modules", "section", $sectionid, "id", $forum->coursemodule);
    set_coursemodule_visible($forum->coursemodule, $forum->visible);
    set_coursemodule_idnumber($forum->coursemodule, $forum->cmidnumber);
    rebuild_course_cache($forum->course);
    return $forum;
}

/**
* basic encapsulation of creating a wiki instance - as would be done in modedit.php
* with wiki_add_instance();
*
* @param int $courseid the id of the course to add forum to
* @param int $groupmode the groupmode to use eg. separate groups
* @param string $wiki_name  name of the wiki - title
* @param string $summary  summary descrition of the wiki
* */

function local_create_wiki($courseid, $groupmode, $wiki_name, $summary) {
    global $CFG;
    require_once($CFG->dirroot."/mod/wiki/lib.php");
    require_once($CFG->dirroot."/lib/grouplib.php");
    if (! $course = get_record("course", "id", $courseid)) {
        error("This course doesn't exist");
    }

    $wiki = new object();
    $wiki->course = $courseid;
    $wiki->cmidnumber = 1;
    $wiki->name = $wiki_name;
    $wiki->summary = $summary;
    $wiki->htmlmode = 2;
    $wiki->wtype = 'group';
    $wiki->ewikiacceptbinary = 0;
    $wiki->ewikiprinttitle = 1;
    $wiki->disablecamelcase = 0;
    $wiki->setpageflags = 0;
    $wiki->strippages = 0;
    $wiki->removepages = 0;
    $wiki->revertchanges = 0;
    $wiki->pagename = '';
    $wiki->initialcontent = '';
    $wiki->groupingid = $course->defaultgroupingid;
    $wiki->groupmembersonly = 0;
//    gradecat    8   - uncategorised
    $wiki->groupmode = $groupmode;
    $wiki->visible = '0';
    $wiki->module = get_field('modules', 'id', 'name', 'wiki');
    $wiki->id = wiki_add_instance($wiki);
    $wiki->modulename = 'wiki';
    $wiki->instance = $wiki->id;
    $wiki->section = 0; // default to first level section
    $wiki->coursemodule = add_course_module($wiki);
    $sectionid = add_mod_to_section($wiki);
    set_field("course_modules", "section", $sectionid, "id", $wiki->coursemodule);
    set_coursemodule_visible($wiki->coursemodule, $wiki->visible);
    set_coursemodule_idnumber($wiki->coursemodule, $wiki->cmidnumber);
    rebuild_course_cache($wiki->course);
}

/**
* basic encapsulation of creating a glossary instance - as would be done in modedit.php
* with glossary_add_instance();
*
* @param int $courseid the id of the course to add forum to
* @param string $glossary_name  name of the glossary
* @param string $description descrition of the glossary
* @return $glossary object
* */

function local_create_glossary($courseid, $glossary_name, $description) {
    global $CFG;
    require_once($CFG->dirroot."/mod/glossary/lib.php");
    require_once($CFG->dirroot."/lib/grouplib.php");
    if (! $course = get_record("course", "id", $courseid)) {
        error("This course doesn't exist");
    }

    $glossary = new object();
    $glossary->course = $courseid;
    $glossary->cmidnumber = 1;
    $glossary->name = $glossary_name;
    $glossary->intro = $description;
    $glossary->entbypage = 10;
    $glossary->mainglossary = 1;
    $glossary->globalglossary = false;
    $glossary->userrating = false;
    $glossary->ratingtime = false;
    if ($CFG->enablerssfeeds && isset($CFG->glossary_enablerssfeeds) && $CFG->glossary_enablerssfeeds) {
      $glossary->rsstype = 2;
      $glossary->rssarticles = 10;
    }
    $glossary->allowduplicatedentries = 0;
    $glossary->allowcomments = 1;
    $glossary->allowprintview = 1;
    $glossary->usedynalink = 1;
    $glossary->defaultapproval = 1;
    $glossary->displayformat = 'faq';
    $glossary->showspecial = 1;
    $glossary->showalphabet = 1;
    $glossary->showall = 1;
    $glossary->editalways = 0;
    $glossary->assesstimestart = time();
    $glossary->assesstimefinish = time();
    $glossary->showall = 1;
    $glossary->showall = 1;
    $glossary->showall = 1;
    $glossary->showall = 1;
    $glossary->groupingid = $course->defaultgroupingid;
    $glossary->groupmembersonly = 0;
//    gradecat    8   - uncategorised
    $glossary->groupmode = 0;
    $glossary->visible = '1';
    $glossary->module = get_field('modules', 'id', 'name', 'glossary');
    $glossary->id = glossary_add_instance($glossary);
    $glossary->modulename = 'glossary';
    $glossary->instance = $glossary->id;
    $glossary->section = 0; // default to first level section
    $glossary->coursemodule = add_course_module($glossary);
    $sectionid = add_mod_to_section($glossary);
    set_field("course_modules", "section", $sectionid, "id", $glossary->coursemodule);
    set_coursemodule_visible($glossary->coursemodule, $glossary->visible);
    set_coursemodule_idnumber($glossary->coursemodule, $glossary->cmidnumber);
    rebuild_course_cache($glossary->course);
    return $glossary;
}


/**
 * returns whether the current uri is considered a popup or not
 *
 * @return bool
 */

function local_is_in_popup() {
    global $CFG;
    $pathprefix = strstr(substr(strstr($CFG->wwwroot, '//'),2), '/'); //strip out http://mywebsite.com and https://mywebsite.com
    // array of uri's we know are displayed in popup windows
    $popups = array(
        $pathprefix.'/message/',
        $pathprefix.'/mod/chat/',
        $pathprefix.'/help.php',
        $pathprefix.'/local/lp/completion.php',
        $pathprefix.'/admin/roles/explain.php',
    );
    $uri = $_SERVER['REQUEST_URI']; // todo check for standard moodle method for getting a uri

    // check the popup uri list for our current uri
    foreach ($popups as $popup) {
        $popup = '/^' . preg_quote($popup, '/') . '/';;
        if (preg_match($popup, $uri)) {
            return true;
        }
    }

    return false;
}
function local_user_signup_validation() { //used in signup_form.php validation
    //TODO: need to check whether this user exists in local school db
}

function local_user_signup($user) { //used in auth/email/auth.php when a user is signed up.
   global $CFG;
   if (!empty($user)) {
       //all users who sign up should be automatically added to the PT role
       $ptrole = get_field('role', 'id', 'shortname', ROLE_PT);
       $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
       role_assign($ptrole, $user->id,0,$sitecontext->id);

       $sitecontext = get_context_instance(CONTEXT_SYSTEM);
       if (isloggedin() && has_capability('moodle/local:invitenewuser',$sitecontext)) {
           //this user has been created by an MT - set their password to require resetting.
           set_user_preference('auth_forcepasswordchange', '1', $user->id);
       }
   }
}
function local_send_confirmation_email($user) {
    global $CFG,$USER;
    if (function_exists('login_signup_form')) {
        $mform_signup = new login_signup_form();
        $formdata = $mform_signup->get_data();
    }
    $site = get_site();
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    $data = new object();
    $data->firstname = fullname($user);
    $data->sitename = format_string($site->fullname);
    $data->admin = generate_email_signoff();
    $data->custommsg = '';

    $invite= false;
    if (isloggedin() && has_capability('moodle/local:invitenewuser',$sitecontext)) {
        $supportuser = $USER;
        $data->fromuser = fullname($supportuser);
        $invite = true;
    } else {
        $supportuser = generate_email_supportuser();
    }
    if (!empty($formdata->message)) {
        $data->custommsg = $formdata->message;
    }
    $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));
    
    if ($invite) {
        $data->link = $CFG->wwwroot .'/local/login/confirm.php?data='. $user->secret .'/'. urlencode($user->username);
        $message = get_string('emailconfirmation', 'block_tao_team_groups', $data);
    } else {
        $data->link = $CFG->wwwroot .'/login/confirm.php?data='. $user->secret .'/'. urlencode($user->username);
        $message = get_string('emailconfirmation', 'block_tao_team_groups',$data);
    }

    $messagehtml = text_to_html($message, false, false, true);

    $user->mailformat = 1;  // Always send HTML version as well

    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
} 


/**
 * displays courses matching the passed tag
 *
 * @param object $tag  tag record
 *
 * @return bool
 */

function local_tag_search ($tag) {
    global $CFG;

    $sql = "SELECT c.id, c.fullname, c.summary
              FROM {$CFG->prefix}course c, {$CFG->prefix}tag_instance t
             WHERE t.tagid = {$tag->id}
               AND ( t.itemtype = 'courseclassification' or t.itemtype = 'course' )
               AND t.itemid = c.id
               AND c.approval_status_id =".COURSE_STATUS_PUBLISHED;

    if ($courses = get_records_sql($sql)) {

        print_box_start('generalbox', 'tag-blogs');
        print_heading(get_string('relatedcourses', 'local'));

        echo '<ul id="tagblogentries">';
        foreach($courses as $course) {

            echo '<li>';
            echo '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
            echo format_string($course->fullname);
            echo '</a>';
            echo ' - '. $course->summary;
            echo '</li>';
        }
        echo '</ul>';

        print_box_end();

     }
}

/**
 * returns extra button html for the passed in course  
 *
 * @param object $course  course record
 *
 * @return text 
 */

function local_course_buttons ($course) {
    global $USER, $CFG;
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
    if ($course->learning_path_mode == LEARNING_PATH_MODE_RAFL &&
       (has_capability('moodle/local:canviewraflmod', $coursecontext, NULL, false) or
       has_capability('moodle/local:canassignselftorafl', get_context_instance(CONTEXT_COURSE, SITEID)))) {

        tao_set_user_rafl_mode($course);

        if ($USER->raflmode == 1) {
            $string = get_string('standardview', 'local');
            $raflmode = '0';
        } else {
            $string = get_string('raflview', 'local');
            $raflmode = '1';
        }

        return '<form method="get" action="'.$CFG->wwwroot.'/course/view.php">'.
           '<div>'.
           '<input type="hidden" name="id" value="'.$course->id.'" />'.
           '<input type="hidden" name="rafl" value="'.$raflmode .'" />'.
           '<input type="hidden" name="sesskey" value="'.sesskey().'" />'.
           '<input type="submit" value="'.$string.'" />'.
           '</div></form>';

    }
}

function local_course_parameter_handler() {
    global $USER;

    $raflmode = optional_param('rafl', -1, PARAM_BOOL);

    if (($raflmode == 1) and confirm_sesskey()) {
        $USER->raflmode = 1;
    } else if (($raflmode == 0) and confirm_sesskey()) {
        $USER->raflmode = 0;
    }

}

function local_role_processing($course, $roleid) {
    global $CFG;

    $shortname = get_field('role', 'shortname', 'id', $roleid);

    // if we are in rafl mode and updating contributors, update the rafl module
    if ($course->learning_path_mode == LEARNING_PATH_MODE_RAFL && $shortname == ROLE_LPCONTRIBUTOR) {
         require_once($CFG->dirroot.'/mod/rafl/locallib.php');
         $rafl = new localLibRafl();

         // get a list of the contributors
         $users = tao_get_lpcontributors(get_context_instance(CONTEXT_COURSE, $course->id));

         $idarray = array();

         if (!empty($users)) {
             foreach ($users as $user) {
                 array_push($idarray, $user->id);
             }

             $rafl->update_share_contributors($course->id, $idarray);
         }

    }

}

function local_mahara_mnet_call() {
    global $CFG, $MNET;

    if ($CFG->mnet_dispatcher_mode != 'strict') {
        return;
    }

    if (!$host = get_record('mnet_host', 'name', 'localmahara')) {
        return;
    }

    require_once($CFG->dirroot . '/mnet/xmlrpc/client.php');
    if (empty($MNET)) {
        $MNET = new mnet_environment();
        $MNET->init();
    }

    $args = func_get_args();
    $method = array_shift($args);

    $mnet_peer = new mnet_peer();
    $mnet_peer->set_wwwroot($host->wwwroot);

    $client = new mnet_xmlrpc_client();
    $client->set_method($method);
    foreach ($args as $a) {
        $client->add_param($a);
    }
    $client->send($mnet_peer);

    return $client->response;
}

?>
