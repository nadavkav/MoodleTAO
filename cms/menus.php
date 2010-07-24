<?php // $Id: menus.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    // Menu index page in cms administration.

    require_once("../config.php");
    include_once('cmslocallib.php');

    $id       = optional_param('id', 0, PARAM_INT);
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
    if ($courseid==SITEID) {
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }

    $context = get_context_instance($contextinstance, $course->id);
    require_capability('format/cms:manageview', $context);


    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    $strmenus          = get_string("menus","cms");

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strmenus, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strcms, "", $navigation, "", "", true);

    ob_start();

    print_simple_box_start("center", "100%", "", 20);
    print_heading($stradministration);

    // Print list of menus
    cms_print_menus($course->id);

    print_simple_box_end();
    print_footer($course);

    ob_end_flush();
?>