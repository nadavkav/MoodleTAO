<?php // $Id: pageupdate.php,v 1.7.10.1 2008/03/23 09:36:07 julmis Exp $
// update selected page

    require_once("../config.php");
    include_once('cmslocallib.php');

    $id   = required_param('id', PARAM_INT); // Menu id
    $courseid = optional_param('course', SITEID, PARAM_INT);
    $version = optional_param('version', 0, PARAM_INT);

    if (! confirm_sesskey()) {
        error("Session key error!");
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }

    require_login($course->id);

    /// Define context
    $contextinstance = null;
    $context = null;
    if ($courseid==SITEID ) {
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }
    $context = get_context_instance($contextinstance, $course->id);

    require_capability('format/cms:editpage', $context);

    $form = cms_get_pagedata($id);
    
    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    $strpages          = get_string("pages","cms");
    $strupdatepage     = get_string("updatepage","cms");
    $strformtitle      = &$strupdatepage;

    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strpages, 'link' => "pages.php?course=$course->id&amp;sesskey=$USER->sesskey&amp;menuid=$form->naviid", 'type' => 'misc');
    $navlinks[] = array('name' => $strupdatepage, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strupdatepage, "", $navigation, "", "", true);

    $page = data_submitted();

    if (isset($page->cancel)) { // Cancel button has been pressed
        redirect("view.php?page=$page->pagename");
    } elseif (isset($page->save)) { // Save button has been pressed

        $page->pagename = clean_param($page->pagename, PARAM_PATH);
        if (empty($page->pagename) or is_numeric($page->pagename)) { // if no pagename is supplied, use page id
            $page->pagename = $page->pageid;
        }
        $oldname = get_field('cmsnavi_data', 'pagename', 'pageid', $page->pageid);

        if (($oldname != $page->pagename) and
             cms_pagename_exists($page->pagename, $course->id)) {
            // the name has changed but new name is already taken
            $error = get_string('nameinuse', 'cms', $page->pagename);
            $pagenameerror = true;
        } else {

            // Update title to cmsnavi_data
            $page->id     = clean_param($page->nid,    PARAM_INT);
            $page->naviid = clean_param($page->naviid, PARAM_INT);
            $page->title = stripslashes(strip_tags($page->title));

            if ( !empty($page->title) and preg_match("/^\S{3,}/", $page->title) ) {

				// commented out and replaced according to: http://moodle.org/mod/forum/discuss.php?d=62456
                // $page->parentid = !empty($page->parentid) ? intval($page->parentid) : 0;
                
                $page->parentid = 0; 
                if (isset($page->parentname)) {
                    $page->parentname = clean_param($page->parentname, PARAM_FILE);
                    if (!$parentid = get_field('cmsnavi_data', 'pageid', 'pagename', $page->parentname)) {
                        $parentid = 0;
                    }
                    $page->parentid = $parentid;
                }

                // If menu has been changed set parentid to zero.
                // And get all pages underneath this page and move them too
                // into that new menu.
                $oldnaviid = get_field("cmsnavi_data", "naviid", "id", $page->id);
                if ( intval($oldnaviid) !== intval($page->naviid) ) {
                    $children = cms_get_children_ids($page->pageid);

                    foreach ( $children as $childid ) {
                        if ( !set_field("cmsnavi_data", "naviid", $page->naviid,
                                        "pageid", $childid) ) {
                            error("Cannot modify menu information for child pages!",
                                  "$CFG->wwwroot/cms/pages.php?course=$course->id".
                                  "&amp;menuid=$oldnaviid");
                        }
                    }

                    $page->parentid = 0;
                    $page->sortorder = 2000;
                }

                if ( !empty($page->url) ) {
                    $page->url = clean_param($page->url, PARAM_URL);
                    $page->target = ($page->target != '_blank') ? '_top' : '_blank';
                } else {
                    $page->url = '';
                    $page->target = '';
                }

                $page->title = addslashes($page->title);
                if (! empty($page->showinmenu) ) {
                    $page->showinmenu = 1;
                } else {
                    $page->showinmenu = 0;
                }
                if (! empty($page->showblocks) ) {
                    $page->showblocks = 1;
                } else {
                    $page->showblocks = 0;
                }

                if (!update_record("cmsnavi_data", $page)) {
                    error ("Error while linking page to menu! Page has been removed.");
                }

                // update page first
                $page->id       = clean_param($page->pageid, PARAM_INT);
                $page->modified = time();

                if (! empty($page->publish) ) {
                    $page->publish = 1;
                } else {
                    $page->publish = 0;
                }

                $page->body = strip_tags($page->body, $ALLOWED_TAGS);
                $page->body = addslashes($page->body);

                $oldbody = addslashes(get_field('cmspages', 'body', 'id', $page->id));
                if (!update_record("cmspages", $page)) {
                    error("Couldn't update page: $page->title!");
                }

                if ($oldbody != $page->body) {
                    // Get old version info and add new entry to history table.
                    if ( $history = cms_get_page_version($page->id, true) ) {
                        $history->version = floatval($history->version);
                        $history->version = (string) ($history->version + 0.1);
                        if ( strpos($history->version, ".") === FALSE ) {
                            $history->version .= '.0';
                        }
                        $history->modified = time();
                        $history->content = !empty($page->url) ? $page->url : $page->body;
                        $history->author = (int) $USER->id;
                        unset($history->id);
                        insert_record("cmspages_history", $history);
                    }
                }

                if ( defined('SITEID') ) {
                    if ( $course->id != SITEID ) {
                        // We're in course level.
                        redirect("$CFG->wwwroot/course/view.php?id=".
                                 "{$course->id}&amp;page={$page->pagename}");
                    }
                    // We're in site level.
                    redirect("$CFG->wwwroot/index.php?page={$page->pagename}");
                }
                redirect("view.php?page=$page->pagename");

            } else {
                $error = get_string('missingtitle','cms');
            }
        }
    }

    $usehtmleditor = can_use_html_editor();

    $form = empty($page) ? cms_get_pagedata($id) : $page;

    if ( !empty($version) ) {
        if ( $versiondata = get_record("cmspages_history", "id", $version) ) {
            $form->body = $versiondata->content;
        }
    }

    $form->menus = get_records("cmsnavi", "course", $course->id);
    $form->id = $form->naviid;
    $form->pageid = empty($page) ? $id : intval($page->pageid);


    ob_start();

    include_webfx_scripts();

    if (isset($page->preview)) { // Preview button has been pressed
        cms_print_preview($page, $course);
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