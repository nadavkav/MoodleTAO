<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_datarecord extends customlabel_type{

    function __construct($data){
        global $COURSE;
        
        parent::__construct($data);
        $this->type = 'datarecord';
        $this->fields = array();
        
        // fields are only used for pointing a data record
        
        $datainstances = get_records('data', 'course', $COURSE->id, '', 'id,name');
        foreach($datainstances as $data){
            $dataopts[] = $data->name;
        }

        unset($field);
        $field->name = 'key';
        $field->type = 'list';
        $field->options = $datainstances;
        $field->admin = true;
        $this->fields['key'] = $field;
        
        unset($field);
        $field->name = 'key';
        $field->type = 'list';
        $field->options = 'list';
        $field->admin = true;
        $this->fields['key'] = $field;

        $this->datasource = array(
            'USER/id',
            'USER/login',
            'COURSE/id',
            'COURSE/shortname',
            'COURSE/category',
        );
        
        unset($field);
        $field->name = 'keysource';
        $field->type = 'list';
        $field->options = $sourceopts;
        $field->admin = true;
        $this->fields['keysource'] = $field;

    }
}
 
?>