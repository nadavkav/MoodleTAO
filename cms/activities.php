<?php // $Id: activities.php,v 1.1.10.1 2008/03/23 09:36:06 julmis Exp $

    /**
     * This page lists activities and resources in course.
     */

    require_once("../config.php");
    include_once('cmslocallib.php');

    $courseid = required_param('course',  PARAM_INT);

    // Require login
    require_login();
    
    if (! confirm_sesskey()) {
        error("Session key error!");
    }

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }

    // Set context to null
    $contextinstance = null;
    if ($courseid==SITEID) {
        $contextinstance = CONTEXT_SYSTEM;
    } else {
        $contextinstance = CONTEXT_COURSE;
    }

    $context = get_context_instance($contextinstance, $course->id);

    require_capability('format/cms:editpage', $context);

    $modinfo = unserialize($course->modinfo);
    $table = new stdClass;

    $stractres = get_string('activities') .'/'. get_string('resources');
    $straction = get_string('action');
    $strchoose = get_string('choose');
    $stradministration = get_string("administration");
    $strcms            = get_string("cms","cms");
    
    $navlinks = array();
    $navlinks[] = array('name' => $strcms.' '.$stradministration, 'link' => "index.php?course=$course->id&amp;sesskey=$USER->sesskey", 'type' => 'misc');
    $navlinks[] = array('name' => $stractres, 'link' => "", 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header_simple($stractres, "", $navigation, "", "", true);

    ?>

<script type="text/javascript">
// <![CDATA[
function set_value(url) {
    var baseurl = '<?php p($CFG->wwwroot) ?>';
    var frm = opener.document.forms['cmsEditPage'].elements['url'];

    if ( frm ) {
        frm.value = baseurl + url;
        frm.select();
        frm.focus();
    }
    window.close();
}
// ]]>
</script>

    <?php
    $table->head = array($stractres, $straction);
    $table->align = array('left', 'left');
    $table->cellpadding = 2;
    $table->data = array();

    if ( !empty($modinfo) ) {
        foreach ( $modinfo as $mod ) {
            $row = array();
            if ( empty($mod->visible) ) {
                continue;
            }
            if ( !empty($mod->icon) ) {
                $icon = "$CFG->pixpath/$mod->icon";
            } else {
                $icon = "$CFG->wwwroot/mod/$mod->mod/icon.gif";
            }
            $icon = '<img src="'. $icon .'" alt="" />';
            $instancename = urldecode($mod->name);
            $instancename = format_string($instancename, true,  $course->id);

            $javascript  = "<a href=\"javascript: void(set_value('/mod/$mod->mod/view.php?id=$mod->cm'));\">";
            $javascript .= $strchoose .'</a>';
            //echo $icon . ' ';
            //echo $instancename . "<br />\n";
            $row[] = $icon . ' '. $instancename;
            $row[] = $javascript;
            array_push($table->data, $row);
        }
    }

    print_table($table);
    print_footer($course);
?>