<?php // $Id: view.php,v 1.7.10.1 2008/03/23 09:36:07 julmis Exp $

//  Display a content page.
//  This is a slightly modified version of course/view.php
//  Whenever course/view.php is updated this page should probably be updated as well
    require_once('../config.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->libdir.'/ajax/ajaxlib.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->dirroot.'/cms/cmslib.php');

    $pagename = optional_param('page', '', PARAM_FILE);
    $courseid = optional_param('id', 0, PARAM_INT);
    $pageid = optional_param('pid', 0, PARAM_INT);
    // The following parameters are the same as on course/view.php minus $id, $name, $idnumber
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT);

    if (!$course = get_record('course', 'id', $courseid)) {
        error('Invalid course id');
    }

    if ( defined('SITEID') && SITEID == $course->id && $CFG->slasharguments ) {
        // Support sitelevel slasharguments
        // in form /index.php/<pagename>
        $relativepath = get_file_argument(basename($_SERVER['SCRIPT_FILENAME']));
        if ( preg_match("/^(\/[a-z0-9\_\-]+)/i", $relativepath) ) {
            $args = explode("/", $relativepath);
            $pagename = clean_param($args[1], PARAM_FILE);
        }
        unset($args, $relativepath);
    }

    if (empty($pagename) && !empty($pageid)) {
        $pid = explode(',',$_GET['pid']);
        $pageid = array_pop($pid);
        if (!$pagedata = cms_get_page_data_by_id( $courseid, $pageid )) {
            error("Error retrieving CMS page!");
        }
    } elseif ( !$pagedata = cms_get_page_data($course->id, $pagename) ) {
        error("Error retrieving CMS page!");
    }

    $contextinstance = null;
    if ( empty($courseid) ) {
        $contextinstance = CONTEXT_SYSTEM;
        $courseid = SITEID;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }

    if (!$context = get_context_instance($contextinstance, $course->id)) {
        print_error('nocontext');
    }
    // Remove any switched roles before checking login
    if ($switchrole == 0 && confirm_sesskey()) {
        role_switch($switchrole, $context);
    }

    //require_login($course); //CMS not used?

    // Switchrole - sanity check in cost-order...
    $reset_user_allowed_editing = false;
    if ($switchrole > 0 && confirm_sesskey() &&
        has_capability('moodle/role:switchroles', $context)) {
        // is this role assignable in this context?
        // inquiring minds want to know...
        $aroles = get_assignable_roles($context);
        if (is_array($aroles) && isset($aroles[$switchrole])) {
            role_switch($switchrole, $context);
            // Double check that this role is allowed here
            if (@!empty($pagedata->requirelogin)) { //CMS check if need to requirelogin.
                require_login($course->id);
            }
        }
        // reset course page state - this prevents some weird problems ;-)
        $USER->activitycopy = false;
        $USER->activitycopycourse = NULL;
        unset($USER->activitycopyname);
        unset($SESSION->modform);
        $USER->editing = 0;
        $reset_user_allowed_editing = true;
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter 
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }


    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    add_to_log($course->id, 'course', 'view', "view.php?id=$course->id", "$course->id");

    $course->format = clean_param($course->format, PARAM_ALPHA);
    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    $PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);
    $pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);

    if ($reset_user_allowed_editing) {
        // ugly hack
        unset($PAGE->_user_allowed_editing);
    }

    if (!isset($USER->editing)) {
        $USER->editing = 0;
    }
    if ($PAGE->user_allowed_editing()) {
        if (($edit == 1) and confirm_sesskey()) {
            $USER->editing = 1;
        } else if (($edit == 0) and confirm_sesskey()) {
            $USER->editing = 0;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
        }

        if ($hide && confirm_sesskey()) {
            set_section_visible($course->id, $hide, '0');
        }

        if ($show && confirm_sesskey()) {
            set_section_visible($course->id, $show, '1');
        }

        if (!empty($section)) {
            if (!empty($move) and confirm_sesskey()) {
                if (!move_section($course, $section, $move)) {
                    notify('An error occurred while moving a section');
                }
            }
        }
    } else {
        $USER->editing = 0;
    }

    $SESSION->fromdiscussion = $CFG->wwwroot .'/cms/view.php?page='. $pagedata->pagename;


   if ($course->id == SITEID) {
        // do these if we're on site level.
        $breadcrumbs = array();
        cms_breadcrumbs($breadcrumbs, $pagedata);
        $navigation = cms_navigation_string($breadcrumbs);

        //$PAGE->print_header('%fullname%', $breadcrumbs);
        // Use this since we might want to manipulate metadata. Otherwise we
        // should write derived page class for cms plugin.
        print_header(strip_tags($course->fullname), $course->fullname, $navigation, '',
                    '<meta name="description" content="'. s(strip_tags($course->summary)) .'" />',
                    true, '', user_login_string($course).$langmenu);
        echo '<div class="course-content">';  // course wrapper start


        // TODO: for efficiency, check first that sections are actually used in the page
        //if ( preg_match("/{#section([0-9]+)}/im", $pagedata->body) &&
        //     empty($GLOBALS['mods']) && empty($GLOBALS['modnames']) ) {
    $modinfo =& get_fast_modinfo($COURSE);
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
    foreach($mods as $modid=>$unused) {
        if (!isset($modinfo->cms[$modid])) {
            rebuild_course_cache($course->id);
            $modinfo =& get_fast_modinfo($COURSE);
            debugging('Rebuilding course cache', DEBUG_DEVELOPER);
            break;
        }
    }

    if (! $sections = get_all_sections($course->id)) {   // No sections found
        // Double-check to be extra sure
        if (! $section = get_record('course_sections', 'course', $course->id, 'section', 0)) {
            $section->course = $course->id;   // Create a default section.
            $section->section = 0;
            $section->visible = 1;
            $section->id = insert_record('course_sections', $section);
        }
        if (! $sections = get_all_sections($course->id) ) {      // Try again
            error('Error finding or creating section structures for this course');
        }
    }

        if (empty($course->modinfo)) {       // Course cache was never made
            rebuild_course_cache($course->id);
            if (! $course = get_record('course', 'id', $course->id) ) {
                error("That's an invalid course id");
            }
        }
    }

    // Bounds for block widths
    if ( !defined('BLOCK_L_MIN_WIDTH') ) { define('BLOCK_L_MIN_WIDTH', 100); }
    if ( !defined('BLOCK_L_MAX_WIDTH') ) { define('BLOCK_L_MAX_WIDTH', 210); }
    if ( !defined('BLOCK_R_MIN_WIDTH') ) { define('BLOCK_R_MIN_WIDTH', 100); }
    if ( !defined('BLOCK_R_MAX_WIDTH') ) { define('BLOCK_R_MAX_WIDTH', 210); }

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                            BLOCK_R_MAX_WIDTH);

    $editing = $PAGE->user_is_editing();

    echo '<table id="layout-table" cellspacing="0">'."\n";
    echo '<tr>'."\n";

    if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $editing) {
        echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>'."\n";
    }

    echo '<td id="middle-column">';
    print_simple_box_start('center', '100%', '', 5, 'sitetopic');

    if (! empty($pagedata->requirelogin) &&
       ( has_capability('moodle/legacy:guest', $context, $USER->id) && !$pagedata->allowguest) ) {
            print_string('pageviewdenied','cms');
    } else {

        print cms_actions($pagedata, $course,$context);
        print cms_render($pagedata, $course, $sections);

        if ( !empty($pagedata->printdate) ) {
            print '<p style="font-size: x-small;">'. get_string('lastmodified', 'cms', userdate($pagedata->modified)) .'</p>';
        }
        if ($editing) {
            $stradmin = get_string('admin');
            print "<p style=\"font-size: x-small;\"><a href=\"$CFG->wwwroot/cms";
            print "/index.php?course=$courseid&amp;sesskey=$USER->sesskey\">$stradmin</a></p>\n";

        }
    }

    print_simple_box_end();
    echo '</td>'."\n";

    // The right column
    $showblocks = ( $pagedata->showblocks &&
                  (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing )) ? 1 : 0;

    if ( has_capability('moodle/legacy:editingteacher', $context, $USER->id) ) {
        $showblocks = true;
    }

    if ( $showblocks ) {
        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
        if ( has_capability('moodle/legacy:admin', $context, $USER->id, false) ) {
            echo '<div align="center">'.update_course_icon($course->id).'</div>';
            if ( !$pagedata->showblocks && blocks_have_content($pageblocks, BLOCK_POS_RIGHT) ) {
                echo '<br />'. "\n".
                     '<div class="cms-warning" align="center">'.
                     get_string('nonvisibleblocks','cms') .
                     '</div>'."\n";
            }
            echo '<br />';
        }
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</td>'."\n";
    }

    echo '</tr>'."\n";
    echo '</table>'."\n";

    if ( defined('SITEID') && SITEID == $course->id ) {
        // Close the page when we're on site level.
        echo '</div>';  // content wrapper end
        print_footer(NULL, $course);
    }
?>
