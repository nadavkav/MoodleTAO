<?php

class approve_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE;
        $id = required_param('id', PARAM_INT);    // user id; -1 if creating new user

        $mform =& $this->_form;
        //Accessibility: "Required" is bad legend text.

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id', $id);

        $mform->addElement('textarea', 'description', get_string('description'),'wrap="virtual" rows="5" cols="50"');
        $mform->addRule('description', get_string('descriptionrequired', 'block_tao_certification_path'), 'required', null, 'client');
        
        $choices = array();
        $choices['approved'] = get_string('approvecertification', 'block_tao_certification_path');
        $choices['declined'] = get_string('denycertification', 'block_tao_certification_path');
        $mform->addElement('select', 'action', get_string('approveordeny', 'block_tao_certification_path'), $choices);

        $this->add_action_buttons(false);
    }

}

?>