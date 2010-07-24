<?php
/**
   Format control for the Learning Path course format.
   Recycles a lot of the flex page format functionality. 
   
   Important Concepts:
      - Difference between $PAGE and $page.  
            $PAGE is the 'moodle core' representation of the web page as a whole. being displayed. can be 
              of types e.g. course page, blog page, activity page.
            $page is the specific flexpage being asked for and is an actual database entity (format_page).  when referring to 'page'
              in the comments, this is what is meant.
  
*/

    // include the page format functions
    require_once($CFG->dirroot.'/course/format/page/lib.php');

    // include additional functions just for learning path format
    require_once($CFG->dirroot.'/course/format/learning/lib.php');

    $id     = optional_param('id', SITEID, PARAM_INT);    // Course ID
    $pageid = optional_param('page', 0, PARAM_INT);       // format_page record ID
    $action = optional_param('action', '', PARAM_ALPHA);  // What the user is doing

    if (!isset($course)) {
        if (!$course = get_record('course', 'id', $id)) {
            error(get_string('invalidcourseid', 'format_page'));
        }
        if ($course->id != SITEID or $CFG->forcelogin) {
            require_login($course->id);
        }
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $displayid = 0;

    // first we work out which page we want
    // TODO: this code could be improved
    if ($pageid > 0) {
        // we have been passed an id so use that - set in session.
        $pageid = page_set_current_page($course->id, $pageid);
    } else {
        // we weren't passed one, so check session for a saved 'last page'
        if ($page = page_get_current_page($course->id)) {
            $displayid = $page->id;
        } else {
            // nothing in session either so use '0' to represent nothing.
            $displayid = 0;
        }
        // set in session
        $pageid = page_set_current_page($course->id, $displayid);
    }

    /// Check out the $pageid - set? valid? belongs to this course?
    if (!empty($pageid)) {
        if (empty($page) or $page->id != $pageid) {
            // Didn't get the page above or we got the wrong one...
            if (!$page = page_get($pageid)) {
                error('Invalid page ID');
            }
        }
        // Ensure this page is with this course
        if ($page->courseid != $course->id) {
            error(get_string('invalidpageid', 'format_page', $pageid));
        }
    } else {
        // We don't have a page ID to work with
        if (has_capability('format/page:editpages', $context)) {
            $action = 'editpage';
            $page = new stdClass;
            $page->id = 0;
        } else {
            // Nothing this person can do about it, error out
            error(get_string('nopageswithcontent', 'format_page'));
        }
    }

    // TODO: put in equivalent of 'session hacks' used in page/format.php

    // Override PAGE_COURSE_VIEW class mapping
    //     we need to reset various things that have been previously set by moodle core
    page_import_types('course/format/learning');  // note hat this includes /course/format/page/pagelib.php

    $PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);  
    $PAGE->set_formatpage($page);

    $editing = $PAGE->user_is_editing();
    $pageblocks = page_blocks_setup();

/// Handle format actions
    page_format_execute_url_action($action, $course);

// ** Only get to his part if page_format_execute_url_action returns ** //

/// Make sure the individual page is 'published'
    if (!($page->display & DISP_PUBLISH) and !(has_capability('format/page:editpages', $context) and $editing)) {
        error(get_string('thispageisnotpublished', 'format_page'));
    }

/// Finally, we can print the page
    if ($editing) {
        $PAGE->print_tabs('layout');
        page_print_jump_menu();
        if (has_capability('format/learning:manageactivities', $context)) {
            page_print_add_mods_form($page, $course);
        }
        $class = 'format-page editing';
    } else {
        $class = 'format-page';
    }

//  get the overview page for the course
    $overview_page = page_get_default_page($course->id);
    $overview_link = "";
    if ( $overview_page->id != $page->id) {
       $overview_link = '<a href="'.course_page_uri($overview_page,$course).'">Overview Page</a>';
    }

    // create the 'my learning path' specific links
    $mypaths_link = "";
    if (!tao_is_my_learning_path($course->id) && !isguest()) {
        $mypaths_link  = '<a href="'.course_enrol_uri($overview_page,$course).'">'.get_string('addtomylearningpaths', 'format_learning').'</a>';
    }
    $completion_page = '/local/lp/completion.php?id='.$course->id;
    $completion_link = '<a href="'.$CFG->wwwroot.$completion_page.'" onclick="this.target=\'completion\'; return openpopup(\''.$completion_page.'\', \'completion\', \'menubar=0,location=0,scrollbars,status,resizable,width=700,height=500\', 0);">'.get_string('completion','format_learning').'</a>';

// print control row for the course
    echo '<table id="learning-path-header">';
    echo '<tr><td><h1>'.$course->fullname.'</h1></td><td align="right">'.$mypaths_link.'</td></tr>';
    echo '</table>';

// start the main layout table
    echo "\n<!-- start layout table -->\n";
    echo '<table id="layout-table" class="'.$class.'" cellspacing="0" summary="'.get_string('layouttable').'">';

    tao_set_user_rafl_mode($course);

    if (isset($USER->raflmode) && $USER->raflmode) {

         // check wheter i have rights on this learning path

         $canviewrafl = false;
         $notifymessage = '';
         

         if (has_capability('moodle/local:canviewraflmod', $context, NULL, false)) {

            $canviewrafl = true;

         } else {

            if (has_capability('moodle/local:canassignselftorafl', get_context_instance(CONTEXT_COURSE, SITEID))) {

                $roleid =  get_field('role', 'id', 'shortname', ROLE_LPCONTRIBUTOR);
                if (!user_has_role_assignment($USER->id, $roleid, $context->id)) { //if isn't already a contributor, assign as one
                    role_assign($roleid, $USER->id, 0, $context->id);
                    local_role_processing($course, $roleid);

                    $notifymessage = get_string('raflautoaddcontrib','rafl');
                }

                $canviewrafl = true;
            }
         }
 
         if ($canviewrafl) {

             require_once($CFG->dirroot . '/mod/rafl/locallib.php'); 
             $rafl = new localLibRafl();
         
             // get the right module id
             $moduleid = $rafl->get_course_module_id($course->id);
             $country_item_id = $rafl->get_rafl_item_id_by_country('uk');

             page_print_position($pageblocks, BLOCK_POS_LEFT, null);
             echo '<td id="middle-column">';
             if (!empty($notifymessage)){
                 notify($notifymessage);
             }
             $rafl->display_rafl_component($course->id, $country_item_id, $CFG);
             echo '</td>';
             page_print_position($pageblocks, BLOCK_POS_RIGHT, null);

         } else {

            echo '<tr>';
            page_print_position($pageblocks, BLOCK_POS_LEFT, null);
            echo '<td id="middle-column">';
	    notify('You do not have permission for RAFL View on this learning path.  Please switch back to Standard View.');
            echo '</td>';
            echo '<td id="right-column"></td>';
            echo '</tr>';

         }

    } else {
        // standard mode

        /// Layout the whole page as three columns.
        echo '<tr>';
        page_print_position($pageblocks, BLOCK_POS_LEFT, null);
        page_print_position($pageblocks, BLOCK_POS_CENTER, null);
        page_print_position($pageblocks, BLOCK_POS_RIGHT, null);
        echo '</tr>';

    }

/// Silently attempts to call a function from the block_recent_history block
    @block_method_result('recent_history', 'block_recent_history_record', $page);

/// Display navigation buttons
    if ($page->showbuttons && ! $USER->raflmode) {
        $nav     = page_get_next_previous_pages($page->id, $page->courseid);
        $buttons = '';

        if ($nav->prev and ($page->showbuttons & BUTTON_PREV)) {
            $buttons .= '<span class="prevpage"><a href="'.$PAGE->url_build('page', $nav->prev->id).'">'.get_string('previous', 'format_page', page_get_name($nav->prev)).'</a></span>';
        }
        if ($completion_link) {
            $buttons .= '<span class="completion">' . $completion_link . '</span>';
        }
        if ($overview_link) {
            $buttons .= '<span class="overview">' . $overview_link . '</span>';
        }
        if ($nav->next and ($page->showbuttons & BUTTON_NEXT)) {
            $buttons .= '<span class="nextpage"><a href="'.$PAGE->url_build('page', $nav->next->id).'">'.get_string('next', 'format_page', page_get_name($nav->next)).'</a></span>';
        }
        // Make sure we have something to print
        if (!empty($buttons)) {
            echo "\n<tr><td></td><td><div class=\"course-links\">$buttons</div></td><td></td></tr>\n";
        }
    }
    echo '</table>';
    echo "\n<!-- end layout table -->\n";
?>