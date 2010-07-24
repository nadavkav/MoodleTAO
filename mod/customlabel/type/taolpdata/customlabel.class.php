<?php

require_once ($CFG->dirroot."/mod/customlabel/type/customtype.class.php");

/**
*
*
*/

class customlabel_type_taolpdata extends customlabel_type{

    function __construct($data){
        parent::__construct($data);
        $this->type = 'taolpdata';
        $this->fields = array();

        $field->name = 'topic';
        $field->type = 'textfield';
        $field->size = 60;
        $this->fields['topic'] = $field;

        unset($field);
        $field->name = 'goal';
        $field->type = 'textarea';
        $this->fields['goal'] = $field;

        unset($field);
        $field->name = 'c2i';
        $field->type = 'list';
        $field->multiple = 'multiple';
        $field->options = array('A1', 'A11', 'A12', 'A13', 'A14', 'A15', 'A2', 'A21', 'A22', 'A23', 'A3', 'A31', 'A32', 'A33', 'A34', 'B1', 'B11', 'B12', 'B13', 'B2', 'B21', 'B22', 'B23', 'B24', 'B3', 'B31', 'B32', 'B33', 'B34', 'B35', 'B4', 'B41', 'B42', 'B43');
        $this->fields['c2i'] = $field;

        unset($field);
        $field->name = 'disciplin';
        $field->type = 'list';
        $field->multiple = 'multiple';
        $field->options = array('anglais', 'allemand', 'arts_plastiques', 'education_civique', 'espagnol', 'francais', 'geographie', 'histoire', 'informatique', 'it', 'italien', 'langues', 'mathematiques', 'musique', 'physique', 'technologie', 'sciences_de_la_vie');
        $this->fields['disciplin'] = $field;

        unset($field);
        $field->name = 'target';
        $field->type = 'textfield';
        $field->size = 80;
        $this->fields['target'] = $field;

        unset($field);
        $field->name = 'duration';
        $field->type = 'textfield';
        $field->size = 80;
        $this->fields['duration'] = $field;

        unset($field);
        $field->name = 'prerequisite';
        $field->type = 'textarea';
        $this->fields[] = $field;

        unset($field);
        $field->name = 'equipment';
        $field->type = 'textarea';
        $this->fields[] = $field;
    }

    /**
    * CUSTOMIZED FOR PAIRFORMANCE
    * needs to get additional information about TAO classification
    *
    */
    function preprocess_data(){
        global $COURSE, $CFG;
        
        $sql = "
            SELECT 
                ct.name,
                cv.value
            FROM
                {$CFG->prefix}classification_type ct,
                {$CFG->prefix}classification_value cv,
                {$CFG->prefix}course_classification cc
            WHERE
                ct.id = cv.type AND
                cc.value = cv.id AND
                cc.course = $COURSE->id                
        ";
        if ($classification = get_records_sql($sql)){
            foreach($classification as $qualifier){
                $name = $qualifier->name;
                $this->data->{$name} = $qualifier->value;
            }
        }         
    }
}

?>