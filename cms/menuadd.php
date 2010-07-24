<?php // $Id: menuadd.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    // This page adds new menu.

    require_once("../config.php");

    $courseid = optional_param('course', SITEID, PARAM_INT);

    require_login();

    if (!confirm_sesskey()) {
        error("Session key error!");
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }

    include_once('cmslib.php');

    /// Define context
    $contextinstance = null;
    $context = null;
    if ($courseid == SITEID) {        
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }

    $context = get_context_instance($contextinstance, $course->id);
    require_capability('format/cms:createmenu', $context);

    $stradministration = get_string("administration");
    $straddnew         = get_string("addnewmenu","cms");
    $strcms            = get_string("cms","cms");
    $strmenus          = get_string("menus","cms");

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $straddnew, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($straddnew, "", $navigation, "", "", true);

    if ($menu = data_submitted() ) {

        if (!empty($menu->name) and preg_match("/^\S{2,}/", $menu->name) ) {
            $menu->id   = NULL;
            $menu->course = clean_param($menu->course, PARAM_INT);
            $menu->name = stripslashes(strip_tags($menu->name));
            $menu->name = trim($menu->name);
            $menu->name = addslashes($menu->name);

            if  (!empty($menu->requirelogin)) {
                $menu->requirelogin = clean_param($menu->requirelogin, PARAM_INT);
            }

            $menu->created = time();
            $menu->modified = time();

            if (!$rs = insert_record("cmsnavi", $menu)) {
                error("Couldn't create new menu!");
            }

            $message = get_string("menuadded","cms");
            redirect("menus.php?course=$courseid&amp;sesskey=$USER->sesskey", $message);
        } else {
            $form  = $menu;
            unset($menu);
            $error = get_string('missingtitle','cms');
        }
    }

    ob_start();

    print_simple_box_start("center", "100%", "", 20);
    print_heading($straddnew);

    // Print form to add new menu

    if ( empty($form) ) {
        $form->name  = '';
        $form->intro = '';
        $form->allowguest   = 0;
        $form->requirelogin = 0;
        $form->printdate = 1;
    }

    if ( !empty($error) ) {
        notify($error);
    }

    include_once('html/editmenu.php');

    print_simple_box_end();
    print_footer($course);

    ob_end_flush();

?>