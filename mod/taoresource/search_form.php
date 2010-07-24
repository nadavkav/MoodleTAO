<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require_once ($CFG->libdir.'/formslib.php');

class mod_taoresource_search_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $add     = optional_param('add', 0, PARAM_ALPHA);
        $return  = optional_param('return', 0, PARAM_BOOL); //return to course/view.php if false or mod/modname/view.php if true
        $type    = optional_param('type', '', PARAM_ALPHANUM);
        $section = required_param('section', PARAM_INT);
        $course  = required_param('course', PARAM_INT);
        
        $mform->addElement('header', 'searchheader',  get_string('searchheader', 'taoresource'));
        
        $plugins = taoresource_get_plugins();
        
        // let the plugins see the form definition
        foreach ($plugins as $plugin) {
            $rc = $plugin->search_definition($mform);
            if (!$rc) {
                break;
            }
        }
        $this->add_action_buttons(true, get_string('searchtaoresource', 'taoresource'));
        if (! $course = get_record("course", "id", $course)) {
            error("This course doesn't exist");
        }
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        if (has_capability('moodle/course:manageactivities', $context)) {
            $mform->addElement('header', 'addheader',  get_string('addheader', 'taoresource'));
    		$addbutton = $mform->addElement('submit', 'addtaoresource', get_string('addtaoresource', 'taoresource'));
            $buttonattributes = array('title'=> get_string('addtaoresource', 'taoresource'), 'onclick'=>"location.href = '"
                              . $CFG->wwwroot."/mod/taoresource/edit.php?course={$course->id}&section={$section}&type={$type}&add={$add}&return={$return}&mode=add'; return false;");
            $addbutton->updateAttributes($buttonattributes);
        }
	}
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
?>