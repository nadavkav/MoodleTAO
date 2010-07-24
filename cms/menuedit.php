<?php // $Id: menuedit.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    // This page edits selected menu.

    require_once("../config.php");
    include_once('cmslocallib.php');

    $id   = required_param('id', PARAM_INT);       // menu id
    $courseid = optional_param('course', SITEID, PARAM_INT);

    require_login();
    
    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }
    
    if ( !confirm_sesskey() ) {
        error("Session key error!!!");
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

    require_capability('format/cms:editmenu', $context);
    
    $stradministration = get_string("administration");
    $streditmenu       = get_string("editmenu","cms");
    $strcms            = get_string("cms","cms");
    $strmenus          = get_string("menus","cms");
    
    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $streditmenu, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($streditmenu, "", $navigation, "", "", true);
    
    if ( $menu = data_submitted() ) {

        if (! empty($menu->name) and preg_match("/^\S{2,}/", $menu->name)  ) {

            $menu->id    = (int) $id;
            $menu->name  = strip_tags($menu->name);
            $menu->name  = trim($menu->name);
            $menu->name  = addslashes($menu->name);
            $menu->intro = strip_tags($menu->intro);
            $menu->intro = addslashes($menu->intro);

            if  (!empty($menu->requirelogin)) {
                $menu->requirelogin = clean_param($menu->requirelogin, PARAM_INT);
            } else {
                $menu->requirelogin = 0;
            }

            $menu->modified = time();

            if (! update_record("cmsnavi", $menu) ) {
                error("Couldn't update menu record!");
            }

            $message = get_string("updatedmenu", "cms");
            redirect("menus.php?course=$course->id&amp;sesskey=$USER->sesskey", $message);

        } else {

            $error = get_string('missingtitle','cms');
            $form  = $menu;
            unset($menu);
        }

    }

    ob_start();

    print_simple_box_start("center", "100%", "", 20);
    print_heading($streditmenu);

    // Print form to add new menu
    if ( empty($form) ) {
        $form = get_record("cmsnavi","id", $id);
    }

    if ( !empty($error) ) {
        notify($error);
    }

    include_once('html/editmenu.php');

    print_simple_box_end();
    print_footer($course);

    ob_end_flush();

?>