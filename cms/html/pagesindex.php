<?php
//
// Edit menu form
// $Revision: 1.1.10.1 $
// $Author: julmis $
// $Date: 2008/03/23 09:36:08 $
//


    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

?>

<?php
    $strpagetitle = get_string('page','cms');
    $stractions   = get_string('actions','cms');
    $strpublish   = get_string('publish','cms');
    //$strcreated   = get_string('created','cms');
    $strversion   = get_string('version');
    $strmodified  = get_string('modified');

    $themenu = new cms_pages_menu($menuid, $courseid);
    $tbl = new stdClass;

    $tbl->head = array("","",$strpagetitle, "", $strpublish, $strversion, $strmodified);
    $tbl->align = array("left", "left", "left", "center", "center", "center", "center");
    $tbl->width = "100%";
    $tbl->cellpadding = 3;
    $tbl->cellspacing = 1;
    $tbl->nowrap = array("", "nowrap", "nowrap", "", "", "", "");
    $tbl->data  = array();

    //$tbl->data = cms_print_pages_menu(0, $menuid, $courseid);
    $tbl->data = $themenu->get_page_tree_rows(0);

    ?>
    <form id="cmsPages" name="cmsPages" method="get" action="pages.php">
    <input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
    <input type="hidden" name="menuid" value="<?php p($menuid)?>" />
    <input type="hidden" name="course" value="<?php p($courseid) ?>" />
    <?php


    print_table($tbl);


    $options = empty($tbl->data) ? array('add' => get_string('add')) :
                                   array('add' => get_string('add'),
                                         'edit' => get_string('edit'),
                                         'purge' => get_string('delete'));

    /*print $stractions .': ';
    choose_from_menu($options, "action", "", "choose", "javascript:document.cmsPages.submit();");
    print '<noscript>';
    print '<input type="submit" value="'. get_string('commitselectedaction','cms') .'" />';
    print '</noscript>';*/

    if ( has_capability('format/cms:createpage', $context, $USER->id) ) {
        echo '<input type="submit" name="add" value="'. get_string('add') .'" />'."\n";
    }
    if ( has_capability('format/cms:editpage', $context, $USER->id) ) {
        echo '<input type="submit" name="edit" value="'. get_string('edit') .'" />'."\n";
    }
    if ( has_capability('format/cms:deletepage', $context, $USER->id) ) {
        echo '<input type="submit" name="purge" value="'. get_string('delete') .'"/>'."\n";
    }

    print '</form>' . "\n";
?>
