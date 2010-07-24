<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require_once $CFG->libdir.'/formslib.php';

class mod_taoresource_taoresource_entry_extra_form extends moodleform {
    function mod_taoresource_taoresource_entry_extra_form($mode) {
        $this->taoresource_entry_mode = $mode;
        parent::moodleform();
    }
    
    function definition (){
        global $CFG, $USER;

        $mform =& $this->_form;

        // let the plugins see the form definition
        $plugins = taoresource_get_plugins();
        foreach ($plugins as $plugin) {
            $rc = $plugin->taoresource_entry_extra_definition($mform);
            if (!$rc) {
                break;
            }
        }
        $this->add_action_buttons(true, get_string($this->taoresource_entry_mode.'taoresource', 'taoresource'));
        $mform->addElement('hidden', 'pagestep', 2);
    }

    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // let the plugins see the form validation
        $plugins = taoresource_get_plugins();
        foreach ($plugins as $plugin) {
            $rc = $plugin->taoresource_entry_extra_validation($data, $files, $errors, $this->taoresource_entry_mode);
            if (!$rc) {
                break;
            }
        }
        
        return $errors;
    }

    
    function get_data($slashed=true) {
        $data = parent::get_data($slashed);

        return $data;
    }
    
    
    function set_data($default_values, $slashed=false) {
        // poke all the basic metadata elements into defaults so 
        // that they get set in the form
        if (isset($default_values->metadata_elements)) {
            foreach ($default_values->metadata_elements as $element) {
                if ($element->namespace == '') {
                    if ($element->element == 'issuedate') {
                        $element->value = strtotime($element->value);
                    }
                    $key = $element->element;
                    $default_values->$key = $element->value;
                }
            }
        }
        
        // clean the hash off of the front as this maybe misleading - even though we need it
        // to guarantee that the file is unique on the filesystem.
        if (!empty($default_values->taoresourcefile) && preg_match('/^\w+\-(.*?)$/', $default_values->taoresourcefile, $matches)) {
            $default_values->taoresourcefile = $matches[1];
        }
        $errors = parent::set_data($default_values, $slashed=false);
    }
}

?>