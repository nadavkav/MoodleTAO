<?php // $Id: pagehistory.php,v 1.2.10.1 2008/03/23 09:36:07 julmis Exp $

    require('../config.php');
    require('cmslocallib.php');

    $pageid   = required_param('pageid',  PARAM_INT);

    if (!$pageinfo = get_record("cmsnavi_data", "pageid", $pageid)) {
        error('Invalid page id');
    }
    if (!$navi = get_record('cmsnavi', 'id', $pageinfo->naviid)) {
        error('Invalid menu');
    }
    $menuid = $navi->id;
    if ( !$course = get_record("course", "id", $navi->course) ) {
        error("Invalid course id!!!");
    }

    require_login($course->id);

    
    if ( !confirm_sesskey() ) {
        error("Session key error!!!");
    }
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('format/cms:editpage', $context);

    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    $strpages          = get_string("pages","cms");
    $strhistory        = get_string("pagehistory","cms");
    $strunknown        = get_string("unknownauthor", "cms");
    $strview           = get_string("view");
    $strfetchback      = get_string("fetchback","cms");
    $strdiff           = get_string("diff","cms");


    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $strpages, 'link' => "pages.php?course=$course->id&amp;sesskey=$USER->sesskey&amp;menuid=$navi->id", 'type' => 'misc');
    $navlinks[] = array('name' => $strhistory .': '. s($pageinfo->title), 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strhistory, "", $navigation, "", "", true);
    
    print_simple_box_start('center');
    print_heading(s($pageinfo->title));

    if ( $pagehistory = cms_get_page_history($pageid) ) {
        $tbl = new stdClass;
        $tbl->head  = array(get_string('author'), get_string('version'), get_string('modified'), get_string('action'));
        $tbl->align = array("left", "center", 'left', 'left');
        $tbl->width = '100%';
        $tbl->wrap  = array("nowrap", "", '', "nowrap");
        $tbl->data  = array();

        foreach ( $pagehistory as $page ) {
            $row = array();
            $row[] = !empty($page->firstname) ? fullname($page) : $strunknown;
            $row[] = $page->version;
            $row[] = userdate($page->modified, "%x %X");

            $viewurl  = '/cms/historyview.php?pageid='. $page->id .'&amp;course='. $course->id;
            $viewlink = link_to_popup_window($viewurl, 'preview', $strview, 600, 800,
                                             $pageinfo->title, 'none', true);

            $fetchback = '<a href="pageupdate.php?id='. $pageinfo->pageid .'&amp;sesskey='.
                         $USER->sesskey .'&amp;course='. $course->id .'&amp;version='.
                         $page->id .'">'. $strfetchback .'</a>';

            if ( floatval($page->version) >= 1.1 ) {
                $diffurl  = '/cms/pagediff.php?id='. $page->id .'&amp;course='. $course->id .
                            '&amp;sesskey='. $USER->sesskey;
                $difflink =  link_to_popup_window($diffurl, 'diff', $strdiff, 600, 800,
                                                  $pageinfo->title, 'none', true);
            } else {
                $difflink = '';
            }

            $row[] = " $viewlink | $fetchback | $difflink ";
            array_push($tbl->data, $row);
        }

        print_table($tbl);}

        print_simple_box_end();
        print_footer($course);



//////////////////////////////// Supporting functions ////////////////////////////////

function cms_get_page_history ( $pageid ) {

    global $CFG;
    return get_records_sql("SELECT h.id, h.version, h.modified,
                                   u.firstname, u.lastname
                            FROM
                                {$CFG->prefix}cmspages_history h
                            LEFT JOIN
                                {$CFG->prefix}user u ON h.author = u.id
                            WHERE h.pageid = $pageid
                            ORDER BY h.modified DESC");

}
?>