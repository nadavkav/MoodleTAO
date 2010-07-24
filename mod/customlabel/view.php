<?php  // $Id: view.php,v 1.5 2007/10/09 21:43:29 vfremaux Exp $

    require_once("../../config.php");
    require_once("{$CFG->dirroot}/mod/customlabel/lib.php");

    $id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
    $l = optional_param('l',0,PARAM_INT);     // Label ID
    $what = optional_param('what', '', PARAM_ALPHA);     // What to be seen
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('customlabel', $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $customlabel = get_record('customlabel', 'id', $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $customlabel = get_record('customlabel', 'id', $l)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record('course', 'id', $customlabel->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("customlabel", $customlabel->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    if ($what == 'xml'){
        print_header_simple();
        
        $customlabel = get_record('customlabel', 'id', $l);
        $instance = customlabel_load_class($customlabel);
        $xml = $instance->get_xml();
        $xml = str_replace('<', '&lt;', $xml);
        $xml = str_replace('>', '&gt;', $xml);
        echo "<pre>".$xml."</pre>";        
        die;
    }

    redirect("$CFG->wwwroot/course/view.php?id={$course->id}");

?>
