<?php

/**
* @package mod-tracker
* @category mod
* @author Valery Fremaux
* @date 02/12/2007
*
* A generic class for collecting all that is common to all elements
*/

class customlabel_type{
    var $title;
    var $type;
    var $fields;
    var $data;
    
    /**
    * A customlabel has a type
    * A custom label is made of fields objects within an array. A field
    * determines the nature of the data and the way it can be input.
    */
    function __construct($data){
       $this->title = $data->title;
       $this->type = 'undefined';
       $this->fields = array();
       $this->data = clone($data);
    }
    
    /**
    * makes a suitable options list for available options
    * for a list field.
    */
    function get_options($fieldname){
        if (!array_key_exists($fieldname, $this->fields)){
            return array();
        }

        if (empty($this->fields[$fieldname]->options)){
            return array();
        }
        
        //get all code / translations for the option list
        $options = array();
        foreach($this->fields[$fieldname]->options as $option){
            $options[$option] = get_string($option, 'customlabel');
        }
        return $options;
    }
    
    /**
    * preprocess data values to get definitive data object.
    * Usual customlabels will not overload this function, unless
    * some form information must be used to fetch additional information. 
    *
    *   @param object $data
    */
    function preprocess_data(){
    }

    /**
    *
    *
    */
    function get_name() {
        // $textlib = textlib_get_instance();
    
        $this->data->customlabelcss = customlabel_get_stylesheet($this->type);
        $this->data->currenttheme = current_theme();
        $this->data->title = $this->title;
        
        
        $name = $this->make_template();
    
        if (empty($name)) {
            // arbitrary name
            $name = "customlabel{$customlabel->instance}";
        }
    
        return $name;
    }
    
    /**
    * realizes the template (the standard way is to compile content fields 
    * in a HTML template. 
    */
    function make_template($lang=''){
        global $CFG, $USER;
        
        $content = '';
        if (method_exists ($this, 'field_preprocess')){
            $this->field_preprocess($this->data);
        }
        if (empty($lang)) $lang = current_language($USER);
        $template = $CFG->dirroot ."/mod/customlabel/type/{$this->type}/{$lang}/template.tpl";
        if (file_exists($template)){
            $content = implode('', file($template));
            $content = str_replace("'", "\\'", $content);
            if (!empty($this->data)){
                foreach($this->data as $key => $value){
                    $content = str_replace("<%%{$key}%%>", $value, $content);
                }
            }
        } else {
            error("Template $template not found");
        }
        return $content;
    }
    
    /**
    * post processes fields for rendering in form
    */
    function process_form_fields(){
    
        // assembles multiple list answers
        foreach($this->fields as $key => $field){
            if ($field->type == 'list'){
                if (@$field->multiple){
    
                    if (!function_exists('get_string_for_list')){
                        function get_string_for_list(&$a){
                            $a = get_string($a, 'customlabel');
                        }
                    }
    
                    $name = str_replace('[]', '', $field->name);
                    $valuearray = @$this->data->{$name};
                    if (is_array($valuearray)){
                        if (!empty($valuearray)){
                            array_walk($valuearray, 'get_string_for_list');
                            $this->data->{$name} = implode(', ', $valuearray);
                        }
                    } else {
                        if (!empty($this->data->{$name})){
                            $this->data->{$name} = get_string($this->data->{$name}, 'customlabel');
                        }
                    }
                } else {
                    $name = $field->name;
                    $nameoption = "{$name}option";
                    $this->data->{$nameoption} = $this->data->{$name};
                    $this->data->{$name} = get_string($this->data->{$name}, 'customlabel');
                }
            }
        }
    }

    function get_xml(){
        global $CFG;

        require_once($CFG->libdir.'/pear/HTML/AJAX/JSON.php');

        $content = json_decode($this->data->content);
        
        $xml = "<datablock>\n";
        $xml .= "\t<instance>\n";
        $xml .= "\t\t<labeltype>{$this->type}</labeltype>\n";
        $xml .= "\t\t<title>{$this->title}</title>\n";
        $xml .= "\t</instance>\n";        
        $xml .= "\t<content>\n";
        foreach($this->fields as $field){
            $fieldvalue = '';
            $fieldname = $field->name;
            $xml .= "\t\t<{$fieldname}>";
            if ($field->type == "list" && !empty($field->multiple)){
                if (is_array(@$content->{$fieldname})){
                    $fieldvalue = implode (',', $content->{$fieldname});
                }
            } else {
                $fieldvalue = @$content->{$fieldname};
                $fieldvalue = str_replace("\\'", "'", $fieldvalue);
            }
            $xml .= $fieldvalue;
            $xml .= "</$fieldname>\n";
        }
        $xml .= "\t</content>\n";        
        $xml .= '</datablock>';
        return $xml;        
        
        return '';
    }

}

?>