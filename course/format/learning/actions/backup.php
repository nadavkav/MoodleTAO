<?php 
    //This script is used to configure and execute the backup proccess in a learning path context.

    require_once ("$CFG->dirroot/config.php");
    require_once ("$CFG->dirroot/backup/lib.php");
    require_once ("$CFG->dirroot/backup/backuplib.php");
    require_once ("$CFG->libdir/blocklib.php");
    require_once ("$CFG->libdir/adminlib.php");

    global $SESSION;

    $id = optional_param( 'id' );       // course id
    $cancel = optional_param( 'cancel' );
    $launch = optional_param( 'launch' );
    $backup_unique_code = optional_param( 'backup_unique_code' );

    if (!empty($id)) {
        require_login($id);
        require_capability('moodle/site:backup', get_context_instance(CONTEXT_COURSE, $id));
    }


    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

    //Check backup_version
    if ($id) {
        $linkto = "backup.php?id=".$id.((!empty($to)) ? '&to='.$to : '');
    } else {
        $linkto = "backup.php";
    }
    upgrade_backup_db($linkto);

    //Get strings
    if (empty($to)) {
        $strcoursebackup = get_string("coursebackup");
    }
    else {
        $strcoursebackup = get_string('importdata');
    }
    $stradministration = get_string("administration");

    //Get and check course
    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    $PAGE->print_tabs('backup');

    //Print form
    print_heading(format_string("$strcoursebackup: $course->fullname ($course->shortname)"));
    print_simple_box_start("center");

    //Call the form, depending the step we are
    if (!$launch) {
      
        // if we're at the start, clear the cache of prefs
        if (isset($SESSION->backupprefs[$course->id])) {
            unset($SESSION->backupprefs[$course->id]);
        }

// TODO use form api 

// START BACKUP FORM //
?>
<form id="form1" method="post" action="<?php echo $CFG->wwwroot; ?>/course/view.php?action=backup">
<table cellpadding="5" style="margin-left:auto;margin-right:auto;">
</table>
<?php
    $backup_unique_code = time();
    $backup_name = backup_get_zipfile_name($course, $backup_unique_code);

// look for activities to backup

// note: this is a bit of mish mash between hardcoding 'quiz' and using $modname.  left $modname there if we decide to 
//         change this to backup more than just quiz activities in the template

if ($mods = get_records_sql("SELECT * FROM {$CFG->prefix}modules WHERE visible = 1")) {

    //print_object($mods);

    foreach ($mods as $mod) {
        $modname = $mod->name;

        $modfile = $CFG->dirroot.'/mod/'.$modname.'/backuplib.php';
        if (!file_exists($modfile)) {
            continue;
        }
        require_once($modfile);

        if ( $instances = get_all_instances_in_course($modname, $course, NULL, true) ) {
  
            print '<input type="hidden" name="backup_'.$modname.'" value="1" />';
            print '<input type="hidden" name="backup_user_info_'.$modname.'" value="1" />';

            $instancestopass = array();

            foreach ($instances as $instance) {
                $var = 'backup_'.$modname.'_instance_'.$instance->id;
                print '<input type="hidden" name="'.$var.'" value="1" />';
                $obj = new StdClass;
                $obj->name = $instance->name;
                $obj->userdata = 0;
                $obj->id = $instance->id;
                $instancestopass[$instance->id]= $obj;
            }

            $checkfunction = $modname . '_check_backup_mods';

            if (function_exists($checkfunction)) {
                $checkfunction($id,$modname.'_instances',$backup_unique_code,$instancestopass);
            } else {
                debugging('check function not found');
            }

         }

    }

}

?>

<div style="text-align:center;margin-left:auto;margin-right:auto">
<input type="hidden" name="backup_course_file" value="1">

<input type="hidden" name="id"     value="<?php  p($id) ?>" />
<input type="hidden" name="to"     value="<?php p($to) ?>" />
<input type="hidden" name="backup_unique_code" value="<?php p($backup_unique_code); ?>" />
<input type="hidden" name="backup_name" value="<?php p($backup_name); ?>" />
<input type="hidden" name="launch" value="check" />
<input type="submit" value="<?php  print_string("continue") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
</div>
</form>
<?php
// END BACKUP FORM //

    } else if ($launch == "check") {

    $backupprefs = new StdClass;
    $count = 0;
    backup_fetch_prefs_from_request($backupprefs,$count,$course);

    if ($count == 0) {
        notice("No backupable modules are installed!");
    }

?>
<form id="form" method="post" action="<?php echo $CFG->wwwroot; ?>/course/view.php?action=backup">
<table cellpadding="5" style="text-align:center;margin-left:auto;margin-right:auto">
<?php
    if (empty($to)) {
        //Now print the Backup Name tr
        echo "<tr>";
        echo "<td align=\"right\"><b>";
        echo get_string("name").":";
        echo "</b></td><td>";
        //Add as text field
        echo "<input type=\"text\" name=\"backup_name\" size=\"40\" value=\"".$backupprefs->backup_name."\" />";
        echo "</td></tr>";

        //Line
        echo "<tr><td colspan=\"2\"><hr /></td></tr>";

        //Now print the To Do list
        echo "<tr>";
        echo "<td colspan=\"2\" align=\"center\"><b>";

    }
?>
</table>


<div style="text-align:center;margin-left:auto;margin-right:auto">
<input type="hidden" name="to"     value="<?php p($to) ?>" />
<input type="hidden" name="id"     value="<?php  p($id) ?>" />
<input type="hidden" name="backup_unique_code"     value="<?php  p($backupprefs->backup_unique_code) ?>" />
<input type="hidden" name="launch" value="execute" />
<input type="submit" value="<?php  print_string("continue") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
</div>
</form>
<?php

        //include_once("backup_check.html");
    } else if ($launch == "execute") {
        include_once("$CFG->dirroot/backup/backup_execute.html");

        // remove backup ids
        if (!execute_sql("DELETE FROM {$CFG->prefix}backup_ids WHERE backup_code = '{$backup_unique_code}'",false)){
            error('Couldn\'t delete previous backup ids.');
        }
    }

    print_simple_box_end();

?>
