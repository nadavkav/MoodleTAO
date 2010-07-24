<?php // $Id: pages.php,v 1.2.10.1 2008/03/23 09:36:07 julmis Exp $

    // Administration page for cms pages.
    require_once("../config.php");
    include_once('cmslocallib.php');

    $id     = optional_param('id',     0, PARAM_INT);
    $menuid = optional_param('menuid', 1, PARAM_INT);    //
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $setfrontpage = optional_param('setfp', 0, PARAM_INT);
    
    if (! confirm_sesskey()) {
        error("Session key error!");
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }
    
    require_login($courseid);
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
    $strpages          = get_string("pages","cms");

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strpages, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strpages, "", $navigation, "", "", true);

    if ( !empty($setfrontpage) && has_capability('format/cms:editpage', $context) ) {
        if (! $olddefault = get_field_sql("SELECT nd.id FROM
                                           {$CFG->prefix}cmsnavi_data AS nd, {$CFG->prefix}cmsnavi AS n
                                           WHERE nd.naviid = n.id
                                           AND n.course = $course->id
                                           AND nd.isfp = 1")) {

            set_field("cmsnavi_data", "isfp", 1, "id", $setfrontpage);
        } else {
            set_field("cmsnavi_data", "isfp", 1, "id", $setfrontpage);
            set_field("cmsnavi_data", "isfp", 0, "id", $olddefault);
        }

        $strsuccess = get_string("defaultpagechanged","cms");
        redirect("$CFG->wwwroot/cms/pages.php?course=$course->id&amp;sesskey=$USER->sesskey", $strsuccess, 2);

    } else if ( !empty($_GET['add']) && has_capability('format/cms:createpage', $context) ) {

        $parentid = !empty($id) ? $id : 0;
        redirect("$CFG->wwwroot/cms/pageadd.php?id=$menuid&amp;sesskey=$USER->sesskey&amp;parentid=$parentid&amp;course=$course->id");

    } else if (! empty($_GET['edit']) && has_capability('format/cms:editpage', $context) ) {

        $id = required_param('id', PARAM_INT);
        redirect("$CFG->wwwroot/cms/pageupdate.php?id=$id&amp;sesskey=$USER->sesskey&amp;course=$course->id");

    } else if (! empty($_GET['purge']) && has_capability('format/cms:deletepage', $context) ) {

        $id = required_param('id', PARAM_INT);
        redirect("$CFG->wwwroot/cms/pagedelete.php?id=$id&amp;sesskey=$USER->sesskey&amp;course=$course->id");

    } else {

        // Sort.
        $sort = optional_param('sort', '', PARAM_ALPHA);
        $publish = optional_param('publish', '', PARAM_ALPHA);

        if ( $sort && ($sort == 'up' or $sort == 'down') &&
             has_capability('moodle/cms:movepage', $context) ) {

            $pageid    = required_param('pid', PARAM_INT);
            $parentid  = required_param('mid', PARAM_INT);
            $direction = required_param('sort', PARAM_ALPHA);

            if (! cms_reorder($pageid, $parentid, $menuid, $direction) ) {
                $strerr = "Couldn't reorder pages!";
            }

        }

        if ( $publish && ( $publish == 'yes' or $publish == 'no' ) &&
             has_capability('format/cms:publishpage', $context) ) {
            $pageid = required_param('pid', PARAM_INT);
            $publish = ($publish != 'no') ? '1' : '0';

            set_field("cmspages", "publish", $publish, "id", $pageid);

        }

        if ( isset($_GET['move']) &&
             has_capability('moodle/cms:movepage', $context) ) {

            $pageid = required_param('pid', PARAM_INT);
            $move   = optional_param('move', '0', PARAM_INT);
            set_field("cmsnavi_data", "parentid", $move, "pageid", $pageid);

        }
    /// Check if there is any menus builded.

        if (! get_record("cmsnavi", "id", $menuid)) {

            $strnomenusyet = get_string("nomenus","cms");
            redirect("$CFG->wwwroot/cms/menus.php?course=$courseid&amp;sesskey=$USER->sesskey", $strnomenusyet, 2);
        }

        $usehtmleditor = can_use_html_editor();

        ob_start();

        print_simple_box_start("center", "100%", "", 20);
        if (! empty($strerr) ) {
            notify($strerr);
        } else {
            print_heading($stradministration);
        }

        cms_print_pages($menuid, $course->id);

        print_simple_box_end();
        print_footer($course);

        ob_end_flush();

    }

?>