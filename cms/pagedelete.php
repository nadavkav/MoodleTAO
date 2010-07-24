<?php // $Id: pagedelete.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    // This script deletes selected page and associated data.

    require_once("../config.php");
    include_once('cmslocallib.php');

    $id   = required_param('id', PARAM_INT); // Menu id
    $courseid = optional_param('course', SITEID, PARAM_INT);

    require_login();

    if (! confirm_sesskey()) {
        error("Session key error!");
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }
    /// Define context
    $contextinstance = null;
    $context = null;
    if ($courseid==SITEID ) {
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }
    $context = get_context_instance($contextinstance, $course->id);
    require_capability('format/cms:deletepage', $context);

    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    $strpages          = get_string("pages","cms");
    $strdelete         = get_string("deletepage","cms");

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strdelete, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strdelete, "", $navigation, "", "", true);

    if (data_submitted()) {
        $pagecancel = optional_param('cancel', '', PARAM_ALPHANUM);
        $pagenaviid = optional_param('naviid','',PARAM_INT);
        if ( !empty($pagecancel) ) {
            redirect("pages.php?course=$course->id&amp;menuid=$pagenaviid&amp;sesskey=$USER->sesskey");
        }

        // Get page data to see if this user can delete this page.
        $page->id = optional_param('id', 0, PARAM_INT);
        if (! $navidata = get_record("cmsnavi_data", "pageid", $page->id) ) {
            redirect("pages.php?course=$course->id&amp;menuid=$pagenaviid&amp;sesskey=$USER->sesskey",
                     "Could not get navidata! You cant delete this page!", 2);
        }

        if (! $navi = get_record("cmsnavi", "id", $navidata->naviid) ) {
            redirect("pages.php?course=$course->id&amp;menuid=$pagenaviid&amp;sesskey=$USER->sesskey",
                     "Could not get navi and course id's! You cant delete this page!", 2);
        }

        if ( intval($navi->course) !== intval($course->id) ) {
            error("You have no rights to delete page $navidata->title","$CFG->wwwroot/");
        }

        // Delete child pages first if any.
        $childpages = cms_get_children_ids($page->id);
        if ( !empty($childpages) ) {
            foreach ( $childpages as $childpage ) {
                delete_records("cmspages","id", $childpage);
                delete_records("cmsnavi_data", "pageid", $childpage);
            }
        }

        // Delete page first
        if (!delete_records("cmspages","id", $page->id)) {
            error("Could not delete page!");
        }
        // Delete navidata
        if (!delete_records("cmsnavi_data","id", $navidata->id)) {
            error("Could not delete navigation data!");
        }

        // Delete page history.
        if (!delete_records("cmspages_history", "pageid", $page->id) ) {
            error("Could not delete page history!");
        }

        $message = get_string("pagedeleted","cms");
        redirect("pages.php?course=$course->id&amp;menuid=$pagenaviid&amp;sesskey=$USER->sesskey", $message);

    } else {

        $form = cms_get_pagedata($id);

        ob_start();

        print_simple_box_start("center", "100%", "", 20);
        print_heading($stradministration);

        $deletemessage = get_string("pagedeletesure","cms", $form->title);
        include_once('html/delete.php');

        print_simple_box_end();
        print_footer($course);

        ob_end_flush();

    }
?>