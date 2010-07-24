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

class mod_taoresource_taoresource_entry_form extends moodleform {
    function mod_taoresource_taoresource_entry_form($mode) {
        $this->taoresource_entry_mode = $mode;
        parent::moodleform();
    }
    
    function definition (){
        global $CFG, $USER;

        $mform =& $this->_form;
        $this->set_upload_manager(new upload_manager('taoresourcefile', false, false, null, false, 0, true, true, false));

        $mform->addElement('header', 'resourceheader', get_string('resource'));
        $mform->addElement('text', 'title', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('title', PARAM_TEXT);
        } else {
            $mform->setType('title', PARAM_CLEAN);
        }
        $mform->addRule('title', null, 'required', null, 'client');
        $mform->setHelpButton('title', array('name', get_string('name'), 'taoresource'));
        $mform->addElement('htmleditor', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        $mform->setHelpButton('description', array('description', get_string('description'), 'taoresource'));
        $mform->addElement('text', 'keywords', get_string('keywords', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('keywords', array('keywords', get_string('keywords', 'taoresource'), 'taoresource'));
        
        if ($this->taoresource_entry_mode == 'update') {
            $mform->addElement('static', 'url_display', get_string('url', 'taoresource').': ', '');
            $mform->addElement('static', 'taoresourcefile', get_string('file').': ', '');
        }
        else {
            $mform->addElement('text', 'url', get_string('url', 'taoresource'), array('size'=>'48'));
            $mform->setHelpButton('url', array('url', get_string('url', 'taoresource'), 'taoresource'));
            $mform->addElement('file', 'taoresourcefile', get_string('file'), 'size="40"');
            $mform->setHelpButton('taoresourcefile', array('taoresourcefile', get_string('file'), 'taoresource'));
        }

        $mform->addElement('text', 'Contributor', get_string('contributor', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('Contributor', array('contributor', get_string('contributor', 'taoresource'), 'taoresource'));
        $mform->addElement('date_selector', 'IssueDate', get_string('issuedate', 'taoresource'), array('optional'=>true));
        $mform->setHelpButton('IssueDate', array('issuedate', get_string('issuedate', 'taoresource'), 'taoresource'));
        $mform->addElement('text', 'TypicalAgeRange', get_string('typicalagerange', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('TypicalAgeRange', array('typicalagerange', get_string('typicalagerange', 'taoresource'), 'taoresource'));
        $mform->addElement('text', 'LearningResourceType', get_string('learningresourcetype', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('LearningResourceType', array('learningresourcetype', get_string('learningresourcetype', 'taoresource'), 'taoresource'));
        $mform->addElement('text', 'Rights', get_string('rights', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('Rights', array('rights', get_string('rights', 'taoresource'), 'taoresource'));
        $mform->setAdvanced('Rights');
        $mform->addElement('text', 'RightsDescription', get_string('rightsdescription', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('RightsDescription', array('rightsdescription', get_string('rightsdescription', 'taoresource'), 'taoresource'));
        $mform->setAdvanced('RightsDescription');
        $mform->addElement('text', 'ClassificationPurpose', get_string('classificationpurpose', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('ClassificationPurpose', array('classificationpurpose', get_string('classificationpurpose', 'taoresource'), 'taoresource'));
        $mform->setAdvanced('ClassificationPurpose');
        $mform->addElement('text', 'ClassificationTaxonPath', get_string('classificationtaxonpath', 'taoresource'), array('size'=>'48'));
        $mform->setHelpButton('ClassificationTaxonPath', array('classificationtaxonpath', get_string('classificationtaxonpath', 'taoresource'), 'taoresource'));
        $mform->setAdvanced('ClassificationTaxonPath');

        // let the plugins see the form definition
        $plugins = taoresource_get_plugins();
        foreach ($plugins as $plugin) {
            $rc = $plugin->taoresource_entry_definition($mform);
            if (!$rc) {
                break;
            }
        }

        $btext = '';
        if (taoresource_extra_resource_screen()) {
            $btext = get_string('step2', 'taoresource');
        }
        else {
            $btext = get_string($this->taoresource_entry_mode.'taoresource', 'taoresource');
        }
        $this->add_action_buttons(true, $btext);

        // mark this as the first step page
        $mform->addElement('hidden', 'pagestep', 1);
//        }
        
    }

    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($this->taoresource_entry_mode == 'add') {
            // make sure that either the file or the URL are supplied
            if (empty($data['url']) && $_FILES['taoresourcefile']['size'] <= 0) {
                $errors['url'] = get_string('missingresource','taoresource');
            }
            
            // if file is uploaded - check that there was no problem
            if (empty($data['url']) && $_FILES['taoresourcefile']['error'] != 0) {
                // error - physical upload of file failed
                $errors['taoresourcefile'] = get_string('fileuploadfailed','taoresource');
            }
            
            // check that this resource signature does not allready exist
            if (!empty($data['url'])) {
                $hash = sha1($data['url']);
                $result = count_records('taoresource_entry', 'identifier', $hash) + count_records('taoresource_entry', 'url', $data['url']);
                if ($result > 0) {
                    $errors['url'] = get_string('resourceexists','taoresource');
                }
            }
            else {
                $tempfile = $_FILES['taoresourcefile']['tmp_name'];
                $hash = taoresource_sha1file($tempfile);
                $taoresource_entry->identifier = $hash;
                $uri = $hash.'-'.$_FILES['taoresourcefile']['name'];
                $result = count_records('taoresource_entry', 'identifier', $hash) + count_records('taoresource_entry', 'url', $uri);
                if ($result > 0) {
                    $errors['taoresourcefile'] = get_string('resourceexists','taoresource');
                }
            }
        }

        // let the plugins see the form validation
        $plugins = taoresource_get_plugins();
        foreach ($plugins as $plugin) {
            $rc = $plugin->taoresource_entry_validation($data, $files, $errors, $this->taoresource_entry_mode);
            if (!$rc) {
                break;
            }
        }
        
        return $errors;
    }

    function get_data($slashed=true) {
        $data = parent::get_data($slashed);
        if ($data == NULL) {
            return $data;
        }
        if (!empty($data->IssueDate)) {
            $data->IssueDate = date("Y-m-d\TH:i:s.000\Z", $data->IssueDate);
        }
        else {
            $data->IssueDate = '0000-00-00T00:00:00.000Z';
        }
        return $data;
    }
    
    
    function set_data($default_values, $slashed=false) {
        // poke all the basic metadata elements into defaults so 
        // that they get set in the form
        if (isset($default_values->metadata_elements)) {
            foreach ($default_values->metadata_elements as $element) {
                if ($element->namespace == '') {
                    if ($element->element == 'IssueDate') {
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