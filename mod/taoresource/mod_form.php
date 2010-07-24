<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_taoresource_mod_form extends moodleform_mod {
    var $_resinstance;

     function definition() {
        global $CFG;
        $mform =& $this->_form;
        
        // this hack is needed for different settings of each subtype
        if (!empty($this->_instance)) {
            if($res = get_record('taoresource', 'id', (int)$this->_instance)) {
                $type = $res->type;
            } else {
                error('incorrect assignment');
            }
        } else {
            $type = required_param('type', PARAM_ALPHA);
        }
        $mform->addElement('hidden', 'type', $type);
        $mform->setDefault('type', $type);

        require($CFG->dirroot.'/mod/taoresource/type/'.$type.'/resource.class.php');
        $resclass = 'taoresource_'.$type;
        $this->_resinstance = new $resclass();

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'taoresource'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        $mform->setHelpButton('description', array('description', get_string('description'), 'taoresource'));
        $mform->setAdvanced('description');
        
        $mform->addElement('header', 'typedesc', get_string('resourcetype'.$type,'taoresource'));
        $this->_resinstance->setup_elements($mform);

        $this->standard_coursemodule_elements(array('groups'=>false, 'groupmembersonly'=>true, 'gradecat'=>false));

        $this->add_action_buttons();
        
    }

    function data_preprocessing(&$default_values){
        $this->_resinstance->setup_preprocessing($default_values);
    }
    
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }

}
?>