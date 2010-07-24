<?php 
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
class addlearningpath_form extends moodleform {

    // form definition
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        $createtemplate = $this->_customdata['createtemplate'];
        $category       = $this->_customdata['category'];

        if (!$category) { 
            error('No category provided');
        }

        $categorycontext = get_context_instance(CONTEXT_COURSECAT, $category->id);

	/* display standard/rafl or both modes.
	 * for standard check: has capability moodle/course:create
	 *                                    local/standardcourse:create
	 * for rafl check:     has capability moodle/course:create
	 *                     rafl mode is on
	 */                                                       


        // select learning path authoring mode
        $modes = get_records('learning_path_mode');

	//print_object($modes);

        foreach($modes as $mode) {

	    if ($mode->id == LEARNING_PATH_MODE_STANDARD) {
		if(!has_capability('moodle/local:createstandardlp', get_context_instance(CONTEXT_COURSE, SITEID))) {
			// not allowed standard mode
			continue;
	        }
            }

	    if ($mode->id == LEARNING_PATH_MODE_RAFL) {
		if(!tao_rafl_mode_enabled()) {
		    continue;
                }
            }

            $moptions[$mode->id] = $mode->name;

        }

	if (empty($moptions)){
	    error('No authoring modes available');
	}

        // get a list of courses that are learning path templates
        $courses = tao_get_learning_path_templates();
        if (empty($courses)) {
            error("No templates available");
        }
        foreach($courses as $course) {
            $options[$course->id] = $course->fullname;
        }
        $mform->addElement('header','enrolhdr', get_string('general'));
        $mform->addElement('select', 'learning_path_mode', get_string('chooseauthoringmode', 'local'), $moptions);
        $mform->addElement('select', 'course_template', get_string('choosetemplate', 'local'), $options);

        //must have create course capability in both categories in order to move course
        if (has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSE, SITEID))) {
            $list[$category->id] = $category->name;
            $mform->addElement('select', 'category', get_string('category'), $list);
        } else {
            $mform->addElement('hidden', 'category', null);
        }
        $mform->setHelpButton('category', array('coursecategory', get_string('category')));
        $mform->setDefault('category', $category->id);
        $mform->setType('category', PARAM_INT);

        // name fields
        $mform->addElement('text','fullname', get_string('fullname'),'maxlength="254" size="50"');
        $mform->setHelpButton('fullname', array('coursefullname', get_string('fullname')), true);
        $mform->setDefault('fullname', get_string('defaultlearningpathfullname', 'local'));
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text','shortname', get_string('shortname'),'maxlength="100" size="20"');
        $mform->setHelpButton('shortname', array('courseshortname', get_string('shortname')), true);
        $mform->setDefault('shortname', get_string('defaultlearningpathshortname', 'local'));
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        $mform->addElement('htmleditor','summary', get_string('summary'), array('rows'=> '10', 'cols'=>'65'));
        $mform->setHelpButton('summary', array('text', get_string('helptext')), true);
        $mform->setType('summary', PARAM_RAW);

        $mform->addElement('hidden', 'defaultrole', $CFG->defaultcourseroleid);
        $mform->addElement('hidden', 'format', 'learning');
        $mform->addElement('hidden', 'guest', 1); 
        $mform->addElement('hidden', 'groupmode', 1); 
        $mform->addElement('hidden', 'ct', $createtemplate); 

        // submit buttons
        $this->add_action_buttons(true, get_string('create'));

    }
} 
?>
