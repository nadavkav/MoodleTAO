<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_taolpdistheading extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'taolpdistheading';
        $this->fields = array();

        $field->name = 'subtitle';
        $field->type = 'textfield';
        $field->size = 60;
        $this->fields['subtitle'] = $field;

        unset($field);
        $field->name = 'disciplin';
        $field->type = 'list';
        $field->multiple = 'multiple';
        $field->options = array('maths', 'francais', 'histoire', 'geographie', 'technologie', 'arts_plastiques', 'informatique', 'biologie', 'langues');
        $this->fields['disciplin'] = $field;

    }
}
 
?>