<?php // $Id: pageadd.php,v 1.7.10.1 2008/03/23 09:36:06 julmis Exp $

    // This page adds new cms page.

    require_once("../config.php");
    include_once('cmslocallib.php');

    // Required params.
    $id   = required_param('id', PARAM_INT); // Menu id
    // Optional params.
    $parentid = optional_param('parentid', 0, PARAM_INT);
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $pageparentid = optional_param('parentid',0,PARAM_INT);
    $pagenaviid = optional_param('naviid',0,PARAM_INT);
    $pagecancel = optional_param('cancel','',PARAM_ALPHA);
    $pagesv = optional_param('save','',PARAM_ALPHA);
    $pagepreview = optional_param('preview','',PARAM_ALPHA);
    $pagepublish = optional_param('publish','',PARAM_ALPHANUM);
    $pageisfp = optional_param('isfp','',PARAM_ALPHANUM);
    $pagename = optional_param('pagename','',PARAM_PATH);
    $pageshowinmenu = optional_param('showinmenu','',PARAM_ALPHANUM);
    $pageshowblocks = optional_param('showblocks','',PARAM_ALPHANUM);
    $pageparentname = optional_param('parentname','',PARAM_FILE);
    $pageurl = optional_param('url', '', PARAM_URL);
    $pagetarget = optional_param('target','',PARAM_ALPHAEXT);
    $pagetitle = optional_param('title','',PARAM_ALPHANUM);
    $pagebody     = optional_param('body','', PARAM_CLEANHTML);
    require_login($courseid);
    
    if (!confirm_sesskey()) {
        error("Session key error!!!");
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
    require_capability('format/cms:createpage', $context);

    $page = data_submitted();
    $pagesave = new stdclass();

    if (!empty($pagecancel)) { // Cancel button has been pressed
        if (!empty($pageparentid)) {
            redirect("view.php?pid=$pageparentid");
        }
        redirect('pages.php?sesskey='.sesskey().'&amp;course='.$course->id.'&amp;menuid='.$pagenaviid);

    } elseif (!empty($pagesv)) { // Save button has been pressed
        $pagenaviid = required_param('naviid',PARAM_INT);
        $pagesave->title = $pagetitle;

        if ( preg_match("/^\S{2,}/", $pagesave->title) ) {

            // insert page first
            $pagesave->created  = time();
            $pagesave->modified = time();
            $pagesave->body     = $pagebody;
            $pagesave->body     = addslashes($pagesave->body);

            if (!empty($pagepublish) or !empty($pageisfp)) {
                $pagesave->publish = 1;
            } else {
                $pagesave->publish = 0;
            }

            if (!$pageid = insert_record("cmspages", $pagesave)) {
                error("Couldn't create new page!");
            }

            // Insert title to cmsnavi_data

            $pagesave->pageid      = &$pageid;
            $pagesave->naviid = $pagenaviid;

            $pagesave->pagename = $pagename;
            if (empty($pagename) or is_numeric($pagename)) { // if no pagename is supplied, use page id
                $pagesave->pagename = $pageid;
            }            

            if (!empty($pageshowinmenu) ) {
                $pagesave->showinmenu = 1;
            } else {
                $pagesave->showinmenu = 0;
            }

            if (! empty($pageshowblocks) ) {
                $pagesave->showblocks = 1;
            } else {
                $pagesave->showblocks = 0;
            }

            $pagesave->isfp        = 0;
            $pagesave->sortorder   = 2000;
            $pagesave->parentid = 0; //see http://moodle.org/mod/forum/discuss.php?d=62456
            if (!empty($pageparentname)) {
                $pagesave->parentname = $pageparentname;
                if (!$parentid = get_field('cmsnavi_data', 'pageid', 'pagename', $pageparentname)) {
                    $parentid = 0;
                }
                $pagesave->parentid = $parentid;
            }
            $pagesave->url = $pageurl;
            if (!empty($pageurl) ) {                
                $pagesave->target = ($pagetarget != '_blank') ? '_top' : '_blank';
            } else {
                $pagesave->target = '';
            }

            if (cms_pagename_exists($pagesave->pagename, $course->id)) {
                $error = get_string('nameinuse', 'cms', $pagesave->pagename);
                delete_records("cmspages", "id", $pageid);
                $pagenameerror = true;
            } else {

                if (!$newid = insert_record("cmsnavi_data", $pagesave)) {
                    delete_records("cmspages", "id", $pageid);
                    error ("Error while linking page to menu! Page has been removed.");
                }

                if ( $pageid && $newid ) {
                    // Add entry to cmspage_history table.
                    $history = new stdClass;
                    $history->pageid = $pageid;
                    $history->modified = $pagesave->modified;
                    $history->version = '1.0';
                    $history->content = !empty($pagesave->url) ? $pagesave->url : $pagesave->body;
                    $history->author = (int) $USER->id;
                    insert_record("cmspages_history", $history);
                }

                if ( defined('SITEID') ) {
                    if ( $course->id != SITEID ) {
                        // We're in course level.
                        redirect("$CFG->wwwroot/course/view.php?id=".
                                 "{$course->id}&amp;page={$pagesave->pagename}");
                    }
                    // We're in site level.
                    redirect("$CFG->wwwroot/index.php?page={$pagesave->pagename}");
                }
                redirect("view.php?page=$pagesave->pagename");
            }
        } else {
            $error = get_string('missingtitle','cms');
        }

    }

    $usehtmleditor = can_use_html_editor();

    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    $strpages          = get_string("pages","cms");
    $straddnew         = get_string("addnewpage","cms");
    $strformtitle      = &$straddnew;

    if ($course->category) {
        $navigation = "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> -> ";
    } else {
        $navigation = '';
    }

    $navigation .= '<a href="./">'. $strcms .'</a> -> <a href="pages.php?course='. $course->id .
                       '&amp;sesskey='. $USER->sesskey .'&amp;menuid='. $id .'">'. $strpages .'</a> -> ';


    $form = new stdClass;
    $form->id = $id;
    $form->parentid = $parentid;
    $form->parentname = get_field('cmsnavi_data', 'pagename', 'pageid', $parentid);
    $form->menus = get_records("cmsnavi", "course", $course->id);

    // provide default values unless fields are already set
    $form->title = $pagetitle;
    $form->body = $pagebody;
    $form->pagename = $pagename;
    $form->parentname = $pageparentname;
    $form->showinmenu = !empty($pageshowinmenu) ? $pageshowinmenu : 1;
    $form->showblocks = !empty($pageshowblocks) ? $pageshowblocks : 0;
    $form->publish = !empty($pagepublish) ? $pagepublish : 1;

    ob_start();

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $straddnew, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($straddnew, "", $navigation, "", "", true);

    include_webfx_scripts();

    if (!empty($pagepreview)) { // Preview button has been pressed
        cms_print_preview(data_submitted(), $course);
    }

    print_simple_box_start("center", "100%", "", 20);

    if ( !empty($error) ) {
        notify($error);
    }

    include_once('html/editpage.php');

    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor();
    }

    print_footer($course);

    ob_end_flush();

?>