<?php 
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/course/lib.php');
class changestatus_form extends moodleform {

    // form definition
    function definition() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        // get a list of courses that are learning path templates
        if ($status_arr = tao_get_course_status_options($COURSE)) {

            foreach($status_arr as $status) {
                $options[$status->id] = $status->description;
            }
            $mform->addElement('select', 'approval_status_id', "Change Status To", $options);
            $mform->setDefault('approval_status_id', $COURSE->approval_status_id);

            $mform->addElement('textarea','reason', 'Reason','rows="3" cols="50"');
            $mform->addRule('reason', get_string('missingstatusreason', 'local'), 'required', null, 'client');

        }


        // submit buttons
        $this->add_action_buttons(true, get_string('update'));

    }
} 
?>
