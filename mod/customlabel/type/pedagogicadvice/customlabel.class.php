<?php

include $CFG->dirroot."/mod/customlabel/type/customtype.class.php";

/**
*
*
*/

class customlabel_type_pedagogicadvice extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'pedagogicadvice';
        $this->fields = array();
        
        $field->name = 'advice';
        $field->type = 'textarea';
        $this->fields['advice'] = $field;
    }
}
 
?>