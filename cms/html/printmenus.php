<?php
//
// Edit menu form
// $Revision: 1.2 $
// $Author: julmis $
// $Date: 2006/06/03 09:22:48 $
//

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

    $tbl = new stdClass;

    $strname     = get_string('name');
    $stractions  = get_string('actions','cms');
    $strintro    = get_string('intro','cms');
    $strcreated  = get_string('created','cms');
    $strmodified = get_string('modified');
    $strrequirelogin = get_string('requirelogin','cms');
    $strallowguest   = get_string('allowguest','cms');
    $imgrlogin = '<img src="'. $CFG->wwwroot .'/pix/i/key.gif"' .
                 ' width="16" height="16" alt="'. $strrequirelogin .'"' .
                 ' title="'. $strrequirelogin .'" />';
    $imgallowguest = '<img src="'. $CFG->wwwroot .'/pix/i/guest.gif"' .
                     ' width="16" height="16" alt="'. $strallowguest .'"' .
                     ' title="'. $strallowguest .'" />';

    $tbl->head = array($strname, $stractions, $strintro,
                       $strcreated, $strmodified, $imgrlogin, $imgallowguest);

    $tbl->width = "100%";
    $tbl->align = array("left","left","left","center","center","center","center");
    $tbl->wrap  = array("nowrap","nowrap", "", "", "","","");
    $tbl->data  = array();

    foreach ($menus as $menu) {

        $editlink  = "<a href=\"menuedit.php?id=$menu->id&amp;sesskey=$USER->sesskey&amp;course=$courseid\">";
        $editlink .= "<img src=\"$CFG->pixpath/t/edit.gif\" alt=\"edit\" title=\"edit\" border=\"0\" /></a>";

        if ($menu->id != 1) {
            $dellink  = "<a href=\"menudelete.php?id=$menu->id&amp;sesskey=$USER->sesskey&amp;course=$courseid\">";
            $dellink .= "<img src=\"$CFG->pixpath/t/delete.gif\" alt=\"delete\" title=\"delete\" border=\"0\" /></a>";
        } else {
            $dellink = '';
        }

        $created  = userdate($menu->created, "%x %X");
        $modified = userdate($menu->modified, "%x %X");

        $menu->intro = stripslashes(strip_tags($menu->intro));
        $menu->name  = stripslashes(strip_tags($menu->name));
        $menuname    = '<a href="pages.php?sesskey='. $USER->sesskey .'&amp;course='. $courseid .
                       '&amp;menuid='. $menu->id .'">'. $menu->name .'</a>';

        $rlogin      = ($menu->requirelogin) ? get_string('yes') : get_string('no');
        $allowguest  = ($menu->allowguest or !$menu->requirelogin) ? get_string('yes') : get_string('no');

        $newrow = array($menuname, "$editlink $dellink", $menu->intro, $created, $modified, $rlogin, $allowguest);

        array_push($tbl->data, $newrow);

    }

    print_table($tbl);

?>