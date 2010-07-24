<?php // $Id: cmslib.php,v 1.12.10.1 2008/03/23 09:36:06 julmis Exp $

/**
 * This file contains necessary functions to output
 * cms content on site or course level.
 *
 * @author Janne Mikkonen
 * @author Gustav Delius
 * @version  $Id: cmslib.php,v 1.12.10.1 2008/03/23 09:36:06 julmis Exp $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package CMS_plugin
 */

/**
* DEPRECATED. Shouldn't be called from anywhere.
* This function is only called from course/format/cms/format.php
* Print the selected page content.
*
* @param int $pageid
* @param object $course
* @param bool $editing
* @return void
*/
function cms_print_page ($pageid, $course, $editing=false) {

    if ( !is_object($course) ) {
        $courseid = intval($course);
        $course = get_record('course', 'id', $courseid);
        notify("Second parameter of cms_print_page has changed from integer to object!<br />".
               "To get rid of this message make appropriate changes to your index.php file!");
    }

    global $CFG, $USER;
    global $sections, $modnames, $mods, $modnamesused;

    $pageid   = clean_param($pageid, PARAM_INT);
    $courseid = clean_param($course->id, PARAM_INT);

    $sql =  "SELECT p.id, p.body, p.modified, nd.isfp, nd.parentid, ";
    $sql .= "n.requirelogin, n.allowguest, n.printdate FROM ";
    $sql .= "{$CFG->prefix}cmspages AS p ";
    $sql .= "INNER JOIN {$CFG->prefix}cmsnavi_data AS nd ON p.id = nd.pageid ";
    $sql .= "LEFT JOIN {$CFG->prefix}cmsnavi AS n ON nd.naviid = n.id ";

    if ($pageid == 0) {

        $sql .= "WHERE nd.isfp = 1 AND n.course = ". $courseid;

    } else {

        $sql .= "WHERE p.id = ". $pageid;

    }

    $pagedata = get_record_sql($sql);

    include('html/frontpage.php');

}

/**
 * Get page content.
 *
 * @param int $courseid Site or course id.
 * @param int $pageid   Page id.
 * @return string
 */
function cms_get_page_data_by_id( $courseid, $pageid ) {

    global $CFG;
    // Fetch pagedata from the database

    if (intval($pageid) != $pageid || intval($courseid) != $courseid) {
        return false;
    }

    $sql = "
            SELECT
                p.id,
                p.body,
                p.modified,
                nd.isfp,
                nd.parentid,
                nd.title,
                nd.pagename,
                nd.showblocks,
                n.requirelogin,
                n.allowguest,
                n.printdate,
                n.course
            FROM
                {$CFG->prefix}cmspages p
                INNER JOIN {$CFG->prefix}cmsnavi_data nd ON p.id = nd.pageid
                LEFT JOIN {$CFG->prefix}cmsnavi n ON nd.naviid = n.id
            WHERE
                p.id = {$pageid}
            AND
                n.course =  '$courseid'";

    $pagedata = get_record_sql($sql);
    if (empty($pagedata)) {
        return "<p>". get_string('nocontent','cms') ."</p>";
    }
    return $pagedata;
}

/**
 * Get page content. This function should decapricate
 * all previous funcitons including course format.
 *
 * @param int $courseid Site or course id.
 * @param mixed $pagename Page name or page id.
 * @return string
 */
function cms_get_page_data( $courseid, $pagename ) {

    global $CFG;
    // Fetch pagedata from the database
    $fields = "p.id, p.body, p.modified, nd.isfp, nd.parentid," .
              "nd.title, nd.pagename, nd.showblocks, " .
              "n.requirelogin, n.allowguest, n.printdate, n.course";

    $sql = "SELECT $fields FROM " .
           "{$CFG->prefix}cmspages p " .
           "INNER JOIN {$CFG->prefix}cmsnavi_data nd ON p.id = nd.pageid " .
           "LEFT JOIN {$CFG->prefix}cmsnavi n ON nd.naviid = n.id " .
           "WHERE ";

    if ($pagename) {
        $sql .= "nd.pagename = '$pagename'";
    } else {
        $sql .= " nd.isfp = '1'";
    }
    $sql .= " AND n.course =  '$courseid'";

    if (!$pagedata = get_record_sql($sql)) {
        //return dummy object.
        $fields = preg_replace("/ ?[a-z]{1,2}\./", "", $fields);
        $dummy = explode(",", $fields);
        $pagedata = array_flip($dummy);
        foreach ( $pagedata as $key => $value ) {
            $pagedata[$key] = null;
            if ( $key == 'body' ) {
                $pagedata[$key] = sprintf("<p>%s</p>\n", get_string('nocontent','cms'));
            }
        }
        $pagedata = (object)$pagedata;
    }
    return $pagedata;
}

/**
* Get section data. This is an alias function to print_section
*
* @see print_section()
*/
function cms_get_section ($course, $thissection, $mods, $modnamesused) {
    ob_start();
    print_section($course, $thissection, $mods, $modnamesused, true);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
* Print or get add menus for section. This is an alias to print_section_add_menus.
*
* @see print_section_add_menus()
*/
function cms_section_add_menus($course, $section, $modnames, $vertical=false) {
// Prints the menus to add activities and resources

    global $CFG, $USER;
    static $straddactivity, $stractivities, $straddresource, $resources;

    if (!isset($straddactivity)) {
        $straddactivity = get_string('addactivity');
        $straddresource = get_string('addresource');

        /// Standard resource types
        require_once("$CFG->dirroot/mod/resource/lib.php");
        $resourceraw = resource_get_resource_types();

        foreach ($resourceraw as $type => $name) {
            $resources["resource&amp;type=$type"] = $name;
        }
        $resources['label'] = get_string('resourcetypelabel', 'resource');
    }

    $output  = '<div style="text-align: right">';
    $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
                $resources, "cmsrsection$section", "", $straddresource, 'resource/types', $straddresource, true);

    if ($vertical) {
        $output .= '<div>';
    }

    $output .= ' ';
    $output .= popup_form("$CFG->wwwroot/course/mod.php?id=$course->id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
                $modnames, "cmssection$section", "", $straddactivity, 'mods', $straddactivity, true);

    if ($vertical) {
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}

function cms_print_toc($pid) {
    global $CFG;
    $return = '';
    if ($navidatas = get_records('cmsnavi_data', 'parentid', $pid, 'sortorder ASC')) {
        $return .= '<ul>';
        foreach ($navidatas as $navidata) {
            if ($navidata->showinmenu) {
                $return .= '<li><a href="'.$CFG->wwwroot.'/cms/view.php?page='.$navidata->pagename.'">'.$navidata->title.'</a></li>';
            }
        }
        $return .= '</ul>';
    }
    return $return;
}

//this function outputs to the output buffer the contents of the supplied http url.
function cms_safe_include($url, $setbase=false) {
    global $CFG;
    $url = trim($url);
    if (substr(trim(strtoupper($url)), 0, 7) !== "HTTP://") {
        $url = "http://".$url;
    }

    if (strpos($url,"?") === false) {
        $url = $url."?".$_SERVER["QUERY_STRING"];
    } else {
        $url = $url."&".$_SERVER["QUERY_STRING"];
    }

    if ($outstr = file_get_contents($url)) {
        if ($setbase) {
            $outstr = '<base href="'.$url.'" />'.$outstr.'<base href="'.$CFG->wwwroot.'/cms/" />';
        }
        return $outstr;
    } else {
        return '';
    }
}

function cms_include_page($pagename, $course) {
    $pagedata->id = get_field('cmsnavi_data', 'pageid', 'pagename', $pagename);
    $pagedata->body = get_field('cmspages', 'body', 'id', $pagedata->id);
    return cms_render($pagedata, $course);
}

function cms_breadcrumbs(&$path, $navidata) {
    global $CFG;
    $path[$navidata->title] = $CFG->wwwroot.'/cms/view.php?page='.$navidata->pagename;
    if ($navidata->parentid) {
        if (!$parent = get_record('cmsnavi_data', 'pageid', $navidata->parentid, '', '', '', '', 'title, pagename, parentid')) {
            error('Could not find data for page '.$navidata->parentid);
        }
        cms_breadcrumbs($path, $parent);
    }
}

/**
 * Create navigation string from breadcrumbs array
 *
 * @param array $breadcrumbs
 * @return mixed Returns string or false
 */
function cms_navigation_string ($breadcrumbs) {
    if ( !is_array($breadcrumbs) ) {
        return false;
    }
    $breadcrumbs = array_reverse($breadcrumbs);
    $navigation = '';
    $current = 1;
    $total = count($breadcrumbs);
    foreach ( $breadcrumbs as $key => $value ) {
        if ( $current++ == $total ) {
            $navigation .= ' '. $key;
        } else {
            $navigation .= '<a href="'. $value .'">'. s($key) .'</a> -> ';
        }
    }
    return $navigation;
}

function cms_render_link($link, $title) {
    return '<a href="'.$link.'">'.$title.'</a>';
}

function cms_print_preview($pagedata, $course) {

    notify(get_string('onlypreview','cms'));
    echo '<table id="layout-table" cellspacing="0">';
    echo '<tr><td style="width: 210px;" id="left-column">&nbsp;</td><td id="middle-column">';
    print_simple_box_start('center', '100%', '', 5, 'sitetopic');
    echo cms_render($pagedata, $course);
    print_simple_box_end();
    echo '</td>';
    if ($pagedata->showblocks) {
        echo '<td style="width: 210px;" id="right-column">&nbsp;</td>';
    }
    echo '</tr></table>';
}

function cms_render_news($course) {
    global $CFG;
    if ($course->newsitems) { // Print forums only when needed
        require_once($CFG->dirroot .'/mod/forum/lib.php');

        if (! $newsforum = forum_get_course_forum($course->id, 'news')) {
            error('Could not find or create a main news forum for the course');
        }

        if (isset($USER->id)) {
            $SESSION->fromdiscussion = $CFG->wwwroot;
            if (forum_is_subscribed($USER->id, $newsforum->id)) {
                $subtext = get_string('unsubscribe', 'forum');
            } else {
                $subtext = get_string('subscribe', 'forum');
            }
            $headertext = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>'.
                '<td><div class="title">'.$newsforum->name.'</div></td>'.
                '<td><div class="link"><a href="mod/forum/subscribe.php?id='.$newsforum->id.'">'.$subtext.'</a></div></td>'.
                '</tr></table>';
        } else {
            $headertext = $newsforum->name;
        }

        ob_start();
        print_heading_block($headertext);
        forum_print_latest_discussions($course, $newsforum, $course->newsitems, 'plain', 'p.modified DESC');
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    return '';
}

function cms_render($pagedata, $course) {
    global $sections, $USER;
    // Insert dynamic content

    // content marked as private should be shown with a special style to people with editing rights
    // and should not be shown to others
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $canedit = has_capability('format/cms:editpage', $context, $USER->id);

    $private = $canedit ? '<div class="private">$1</div>' : '';

    $search = array(
        "#\[\[INCLUDE (.+?)\]\]#ie",
        "#\[\[SCRIPT (.+?)\]\]#ie",
        "#\[\[PAGE (.+?)\]\]#ie", // [[PAGE subPage]] for including another page
        "#\[\[NEWS\]\]#ie",
        "#\[\[PRIVATE (.+?)\]\]#is" , // [[PRIVATE content]] for content only to be shown for users with writing privileges
        "#\[\[TOC\]\]#ie" , // [[TOC]] produces a table of contents listing all child pages
        "#\[\[([^\[\]]+?)\s*\|\s*(.+?)\]\]#es", // [[free link | title]]
        //"#\[\[(.+?)\]\]#e", // [[free link]]
        "#\._\.#ie" // escape string, to prevent recognition of special senquences
    );

    $replace = array(
        'cms_safe_include("$1", true)',
        'cms_safe_include("$1", false)',
        'cms_include_page("$1", $course)',
        'cms_render_news($course)',
        $private,
        'cms_print_toc($pagedata->id)',
        'cms_render_link("$1" ,"$2")',
        //'cms_render_link("$1")',
        ''
    );

    $body = preg_replace($search, $replace, $pagedata->body);

    // Search sections.
    preg_match_all("/{#section([0-9]+)}/i", $body, $match);
    $cmssections = $match[1];
    // At this point allow only course level not site level.
    if ( !empty($cmssections) ) {
        foreach ( $cmssections as $cmssection ) {
            if ( !empty($sections[$cmssection]) ) {
                $thissection = $sections[$cmssection];
            } else {
                unset($thissection);
                // make sure that the section doesn't exist.
                if ( !record_exists('course_sections',
                                    'section', $cmssection,
                                    'course', $course->id) ) {
                    $thissection->course = $course->id;   // Create a new section structure
                    $thissection->section = $cmssection;
                    $thissection->summary = '';
                    $thissection->visible = 1;
                    if (!$thissection->id = insert_record('course_sections', $thissection)) {
                        notify('Error inserting new topic!');
                    }
                } else {
                    $thissection = get_record('course_sections',
                                              'course', $course->id,
                                              'section', $cmssection);
                }
            }

            if ( !empty($thissection) ) {
                if ( empty($mods) ) {
                    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);
                }
                $showsection = ( $canedit or
                                $thissection->visible or
                                !$course->hiddensections);
                if ( $showsection ) {
                    $content  = '<div id="cms-section-'. $cmssection .'">';
                    $content .= cms_get_section($course,
                                                $thissection,
                                                $mods,
                                                $modnamesused);

                    if (isediting($course->id)) {
                        $content .= cms_section_add_menus($course,
                                                            $cmssection,
                                                            $modnames,
                                                          false);
                    }
                    $content .= '</div>';
                    $body = preg_replace("/{#section$cmssection}/",
                                                   $content,
                                                   $body);
                }
            } else {
                $body = preg_replace("/{#section$cmssection}/", "", $body);
            }
        }
    }

    $options = new stdClass;
    $options->noclean = true;
    return format_text(stripslashes($body), FORMAT_HTML, $options);
}
function cms_actions($pagedata,$course,$context) {
    global $CFG,$USER;
    
    if ( has_capability('format/cms:manageview', $context) ) {
            $stredit   = get_string('edit');
            $stradd    = get_string('addchild', 'cms');
            $strhistory    = get_string('pagehistory', 'cms');
            $strdelete = get_string('delete');

            $toolbar = '';

            if ( has_capability('format/cms:editpage', $context) ) {
            $editlink = $CFG->wwwroot .'/cms/pageupdate.php?id='. $pagedata->id .
                '&amp;sesskey='. $USER->sesskey .'&amp;course='. $course->id;
            $editicon = $CFG->wwwroot .'/pix/i/edit.gif';
                $toolbar = sprintf('<a href="%s"><img src="%s" width="16" height="16" ' .
                                   'alt="%s" title="%s" border="0" /></a>%s',
                                   $editlink, $editicon, $stredit, $stredit, "\n");
            }

            if ( has_capability('format/cms:createpage', $context, $USER->id) &&
                 !empty($pagedata->id) ) {
            $addlink = $CFG->wwwroot .'/cms/pageadd.php?id='. $pagedata->id .'&amp;'.
                  'sesskey='. $USER->sesskey .'&amp;parentid='.$pagedata->id.'&amp;course=' . $course->id .'';
            $addicon = $CFG->wwwroot .'/cms/pix/add.gif';
                $toolbar .= sprintf('<a href="%s"><img src="%s" width="16" '.
                                    'height="16" alt="%s" title="%s" border="0" /></a>%s',
                                    $addlink, $addicon, $stradd, $stradd, "\n");
            }

            if ( has_capability('format/cms:editpage', $context, $USER->id) &&
                 !empty($pagedata->id) ) {
            $historylink = $CFG->wwwroot .'/cms/pagehistory.php?pageid='. $pagedata->id .'&amp;'.
                  'sesskey='. $USER->sesskey ;
            $historyicon = $CFG->wwwroot .'/cms/pix/history.gif';
                $toolbar .= sprintf('<a href="%s"><img src="%s" width="16" '.
                                    'height="16" alt="%s" title="%s" border="0" /></a>%s',
                                    $historylink, $historyicon, $strhistory, $strhistory, "\n");
            }

            if ( has_capability('format/cms:deletepage', $context, $USER->id) &&
               ( !empty($pagedata->id) && intval($pagedata->isfp) !== 1) ) {
            $deletelink = $CFG->wwwroot .'/cms/pagedelete.php?id='. $pagedata->id .'&amp;'.
                  'sesskey='. $USER->sesskey .'&amp;course=' . $course->id .'';
            $deleteicon = $CFG->wwwroot .'/pix/t/delete.gif';
                $toolbar .= sprintf('<a href="%s"><img src="%s" width="11" '.
                                    'height="11" alt="%s" title="%s" border="0" /></a>%s',
                                    $deletelink, $deleteicon, $strdelete, $strdelete, "\n");
            }

            if ( !empty($toolbar) ) {
                $toolbar = '<div class="cms-frontpage-toolbar">'. $toolbar .'</div>'."\n";
            }
            return $toolbar;
        }
}

?>