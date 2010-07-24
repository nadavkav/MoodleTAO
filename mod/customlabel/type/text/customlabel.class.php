<?php

include $CFG->dirroot."/mod/customlabel/type/customtype.class.php";

/**
*
*
*/

class customlabel_type_text extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'text';
        $this->fields = array();
        
        $field->name = 'textcontent';
        $field->type = 'textarea';
        $this->fields[] = $field;
    }
}
 
?>