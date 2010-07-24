<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_tao_lp_station1 extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        
        $this->type = 'tao_lp_station1';
        $this->fields = array();

        $field->name = 'lp_content';
        $field->type = 'textarea';
        $field->size = 255;
        $this->fields['lp_content'] = $field;

        unset($field);
        $field->name = 'status';
        $field->type = 'list';
        $field->options = array('draft', 'completed', 'reviewed');
        $this->fields['status'] = $field;

    }    
}
 
?>