<?php

include $CFG->dirroot."/mod/customlabel/type/customtype.class.php";

/**
*
*
*/

class customlabel_type_commentbox extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'commentbox';
        $this->fields = array();
        
        $field->name = 'comment';
        $field->type = 'textarea';
        $this->fields[] = $field;
    }
}
 
?>