<?php
class lp_brief_form extends moodleform {

    private $course;

    public function definition() {
        $mform =& $this->_form;
        $this->course = $this->_customdata['course'];
        $mform->addElement('hidden', 'id', $this->course->id);
        //$mform->addElement('text', 'name', get_string('name'), get_string('teacherid', 'local'), 'size="50"');
        $mform->addElement('text', 'name', get_string('name'), 'size="50"');
        
        $mform->addElement('textarea', 'summary', get_string('summary'), 'cols="75" rows="3"');        
        
        $mform->setDefaults(array('name' => $this->course->fullname, 'summary' => $this->course->summary));
        $this->add_action_buttons(false);
    }

}
?>
