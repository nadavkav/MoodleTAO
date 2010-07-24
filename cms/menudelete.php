<?php // $Id: menudelete.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    // This page deletes selected menu.

    require_once("../config.php");
    include_once('cmslocallib.php');

    $id   = required_param('id', PARAM_INT);       // menu id
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

    require_capability('format/cms:deletemenu', $context);

    $stradministration = get_string("administration");
    $strdeletemenu     = get_string("deletemenu","cms");
    $strcms            = get_string("cms","cms");
    $strmenus          = get_string("menus","cms");

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strdeletemenu, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strdeletemenu, "", $navigation, "", "", true);
    
    if ($menu = data_submitted()) {

        // User pushed cancel button.
        if ( !empty($menu->cancel) ) {
            redirect("menus.php?course=$courseid&amp;sesskey=$USER->sesskey");
        }

        // Just to be sure!
        if (empty($menu->id)) {
            error("Required variable missing!");
        }

        $menu->id = clean_param($menu->id, PARAM_INT);

        $pagerecords = get_records("cmsnavi_data","naviid", $menu->id);

        // Remove related pages

        if (! empty($pagerecords)) {
            foreach ($pagerecords as $pr) {

                $pr->pageid = addslashes($pr->pageid);
                if (! delete_records("cmspages", "id", $pr->pageid)) {
                    error("Couldn't delete related page records!");
                }

                $pr->id = addslashes($pr->id);

                if ( ! delete_records("cmsnavi_data", "id", $pr->id)) {
                    error("Couldn't delete related navigation data!");
                }
            }
        }

        if (!delete_records("cmsnavi","id", $menu->id)) {
            error("Couldn't delete requested menu!");
        }

        $message = get_string("menudeleted","cms");
        redirect("menus.php?course=$courseid&amp;sesskey=$USER->sesskey", $message);

    } else {
        // Print confirmation page
        // Just to be sure!!
        if (empty($id)) {
            error("Required variable missing!");
        }

        ob_start();

        print_simple_box_start("center", "100%", "", 20);
        print_heading($strdeletemenu);

        $form = get_record("cmsnavi", "id", $id);

        $deletemessage = get_string("menudeletesure","cms", $form->name);
        $form->id = $id;
        include('html/delete.php');

        print_simple_box_end();
        print_footer($course);

        ob_end_flush();

    }
?>