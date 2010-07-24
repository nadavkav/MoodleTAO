<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_taolpheading extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'taolpheading';
        $this->fields = array();

        $field->name = 'subtitle';
        $field->type = 'textfield';
        $field->size = 60;
        $this->fields['subtitle'] = $field;

        unset($field);
        $field->name = 'disciplin';
        $field->type = 'list';
        $field->options = array('mathematics',
                                'biology',
                                'chemistry',
                                'physics',
                                'computers',
                                'othersciences',
                                'french',
                                'english',
                                'german',
                                'spanish',
                                'italian',
                                'foreignlanguages',
                                'social',
                                'geography',
                                'economy',
                                'hostory',
                                'arts',
                                'music',
                                'sport',
                                'it',  
                                'other');
        $this->fields['disciplin'] = $field;

    }
    
    /**
    * If exists, this method can process local alternative values for
    * realizing the template. Type information structure dependant.
    * @param object $data the hash of label content fields
    */
    function field_preprocess(&$data){
        global $CFG;
        
        $data->disciplinimage = str_replace(' ', '_', $data->disciplinoption);
        if (!file_exists($CFG->dirroot."/theme/".current_theme().'/lppix/disciplin/'.$data->disciplinimage.".jpg")){
            $data->disciplinimage = 'other';
        }
    }
}
 
?>