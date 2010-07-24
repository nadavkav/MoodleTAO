<?php // $Id: index.php,v 1.6.10.1 2008/03/23 09:36:06 julmis Exp $

/**
 * CMS administration index page for site/course level.
 *
 * @author Janne Mikkonen
 * @version  $Id: index.php,v 1.6.10.1 2008/03/23 09:36:06 julmis Exp $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package CMS_plugin
 */

    require_once("../config.php");
    
    $courseid = optional_param('course', SITEID, PARAM_INT);
    
    /// Check for valid admin user
    require_login($courseid);
    
    if (! file_exists($CFG->dirroot .'/blocks/cmsnavigation')) {
        error("Required block cmsnavigation is missing!",
              "$CFG->wwwroot/admin/");
    }

    if (! confirm_sesskey()) {
        error("Session key error! You need to login first!");
    }

    $contextinstance = null;
    if ($courseid==SITEID) {
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }
    $context = get_context_instance($contextinstance, $course->id);

    $stradministration = get_string("administration");
    $strcms            = get_string('cms','cms');
    $strmanagepages = get_string('managepages','cms');
    $strmanagemenus = get_string('managemenus','cms');

    ob_start();
    
    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($strcms, "", $navigation, "", "", true);
    print_simple_box_start("center", "100%", "", 20);
    ?>

    <table class="generaltable" border="0" cellpadding="4" cellspacing="2" align="center">
    <tr>
        <td class="generaltablecell">
        <h3><?php echo $strcms . ' '. $stradministration;
                  helpbutton("cms", get_string("cms"), "cms"); ?> </h3>
        <table border="0" cellpadding="4" cellspacing="2">
        <tr>
            <td align="center"><?php
            if ( has_capability('format/cms:createmenu', $context, $USER->id) ) {
            ?><a href="menus.php?course=<?php p($courseid) ?>&amp;sesskey=<?php p($USER->sesskey) ?>">
                <img src="<?php echo $CFG->wwwroot ?>/cms/pix/menus.gif" width="50" height="50" alt="<?php echo $strmanagemenus ?>"
                title="<?php echo $strmanagemenus; ?>" border="0" /></a><br />
                <a href="menus.php?course=<?php p($courseid) ?>&amp;sesskey=<?php p($USER->sesskey) ?>"><?php echo $strmanagemenus ?></a>
            <?php
            } else {
                echo "&nbsp;";
            }
            ?></td>
            <td align="center"><?php
            if ( has_capability('format/cms:publishpage', $context, $USER->id) or
                 has_capability('format/cms:createpage', $context, $USER->id) ) {
            ?><a href="pages.php?course=<?php p($courseid) ?>&amp;sesskey=<?php p($USER->sesskey) ?>">
                <img src="<?php echo $CFG->wwwroot ?>/cms/pix/pages.gif" width="50" height="50" alt="<?php echo $strmanagepages ?>"
                title="<?php echo $strmanagepages ?>" /></a><br />
                <a href="pages.php?course=<?php p($courseid) ?>&amp;sesskey=<?php p($USER->sesskey) ?>"><?php echo $strmanagepages ?></a>
           <?php
            } else {
                echo "&nbsp;";
            }
           ?></td>
        </tr>
        </table>

        </td>
    </tr>
    </table>
    <?php

    print_simple_box_end();
    print_footer($course);

    ob_end_flush();

?>