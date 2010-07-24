<?php
    // allows user to change the status of the given course
    require_once("$CFG->dirroot/config.php");
    require_once("$CFG->dirroot/course/lib.php");
    require_once("changestatus_form.php");

    require_capability('moodle/local:updatecoursestatus', get_context_instance(CONTEXT_COURSE, $course->id));

    $mform = new changestatus_form($CFG->wwwroot.'/course/view.php?id='.$course->id.'&action=changestatus');

    // processing section
    if ($mform->is_cancelled()){  
        redirect($CFG->wwwroot .'/course/view.php?id='.$course->id, get_string('statusunchanged', 'local'));
    } else if (($data = $mform->get_data())) { 

        if ($course->approval_status_id == $data->approval_status_id) {
            //status wasn't changed
            redirect($CFG->wwwroot .'/course/view.php?id='.$course->id, get_string('statusunchanged', 'local'));
        } else {
            // set course status
            if (!tao_update_course_status($data->approval_status_id, $data->reason, $course)) {
                error('could not update status');
            }
            redirect($CFG->wwwroot .'/course/view.php?id='.$course->id, get_string('statusupdated', 'local'));
        }
    }

    print_heading(get_string('statuschangeheading', 'local'));
    $mform->display();

?>
