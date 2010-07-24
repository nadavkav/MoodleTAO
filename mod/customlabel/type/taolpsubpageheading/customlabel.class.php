<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_taolpsubpageheading extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        
        $this->type = 'taolpsubpageheading';
        $this->fields = array();

        $field->name = 'pagetitle';
        $field->type = 'textfield';
        $field->size = 255;
        $this->fields['pagetitle'] = $field;

        unset($field);
        $field->name = 'step';
        $field->type = 'list';
        $field->options = array('taostep1', 'taostep2', 'taostep3', 'taostep4', 'taostep5', 'taostep6', 'taostep7');
        $this->fields['step'] = $field;

    }
    
    /**
    * If exists, this method can process local alternative values for
    * realizing the template. Type information structure dependant.
    * @param object $data the hash of label content fields
    */
    function preprocess_data(){
        global $CFG;

        $this->data->stepimage = str_replace(' ', '_', $this->data->stepoption);
        if (!file_exists($CFG->dirroot."/theme/".current_theme().'/lppix/taosteps/'.$this->data->stepimage.".jpg")){
            $this->data->stepimage = 'outofsteps';
        }
    }
}
 
?>