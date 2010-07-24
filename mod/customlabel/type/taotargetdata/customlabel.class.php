<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_taotargetdata extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
                
        $this->type = 'taotargetdata';
        $this->fields = array();

        $field->name = 'sample';
        $field->type = 'textfield';
        $field->size = 60;
        $this->fields['sample'] = $field;

        unset($field);
        $field->name = 'goal';
        $field->type = 'textarea';
        $this->fields['goal'] = $field;

        unset($field);
        $field->name = 'b2i';
        $field->type = 'list';
        $field->multiple = 'multiple';
        $field->options = array('b2i_d1', 'b2i_d2', 'b2i_d3', 'b2i_d4', 'b2i_d5');
        $this->fields['b2i'] = $field;

        unset($field);
        $field->name = 'method';
        $field->type = 'textfield';
        $field->size = 100;
        $this->fields['method'] = $field;

        unset($field);
        $field->name = 'tools';
        $field->type = 'textarea';
        $this->fields['tools'] = $field;

        unset($field);
        $field->name = 'activityduration';
        $field->type = 'textfield';
        $field->size = 40;
        $this->fields['activityduration'] = $field;

    }
}
 
?>