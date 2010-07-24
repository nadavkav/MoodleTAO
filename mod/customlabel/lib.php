<?php  // $Id: lib.php,v 1.12.4.5 2008/06/18 15:26:34 diml Exp $

/// Library of functions and constants for module label

// TAO : disabled length limitation for labels
// define("LABEL_MAX_NAME_LENGTH", 50);

/**
* make the name (printable in course summary) from real content of the label
* @param string $customlabel
* @param array $data an associative array containing the data
*/

/**
* includes and requires
*/
require_once ($CFG->dirroot."/mod/customlabel/locallib.php");
require_once($CFG->libdir.'/pear/HTML/AJAX/JSON.php');
// include "debugging.php";


/**
* Given an object containing all the necessary data, 
* (defined by the form in mod.html) this function 
* will create a new instance and return the id number 
* of the new instance.
*
*/
function customlabel_add_instance($customlabel) {
    global $CFG;
    
    $customlabel->name = '';
    $customlabel->content = json_encode($customlabel);
    $instance = customlabel_load_class($customlabel);
    $instance->process_form_fields();
    $instance->preprocess_data();
    $customlabel->name = $instance->get_name();
    $customlabel->timemodified = time();
    $customlabel = customlabel_addslashes_fields($customlabel);
    
    return insert_record('customlabel', $customlabel);
}

/**
* Given an object containing all the necessary data, 
* (defined by the form in mod.html) this function 
* will update an existing instance with new data.
*/
function customlabel_update_instance($customlabel) {
    global $CFG;
    

    $oldinstance = get_record('customlabel', 'id', $customlabel->instance);
    if ($oldinstance->labelclass != $customlabel->labelclass){
        $customlabel->content = '';
        $customlabel->name = '';
    } else {
        $customlabel->content = json_encode($customlabel);
        $instance = customlabel_load_class($customlabel);
        $instance->process_form_fields();
        $instance->preprocess_data();
        $customlabel->name = $instance->get_name();
    }
    $customlabel->timemodified = time();
    $customlabel->id = $customlabel->instance;

    $customlabel = customlabel_addslashes_fields($customlabel);
    $result = update_record('customlabel', $customlabel);

    // needed to update modinfo
    rebuild_course_cache();
    return $result;
}

/**
* Given an ID of an instance of this module, 
* this function will permanently delete the instance 
* and any data that depends on it.  
*
*/
function customlabel_delete_instance($id) {

    if (! $customlabel = get_record('customlabel', 'id', "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records('customlabel', 'id', "$customlabel->id")) {
        $result = false;
    }

    return $result;
}

/**
* Returns the users with data in one resource
* (NONE, but must exist on EVERY mod !!)
*/
function customlabel_get_participants($customlabelid) {

    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 */
function customlabel_get_coursemodule_info($coursemodule) {
    global $CFG;
    
    if ($customlabel = get_record('customlabel', 'id', $coursemodule->instance, '', '', '', '', 'id, content, title, name')) {
        $info = new object();
        $name = $customlabel->name;
        $name = str_replace("'", "&quot;", $name); // fixes a serialisation bug on quote
        $info->name = urlencode($name);
        if (!empty($customlabel->content)){
            $customcontent = json_decode($customlabel->content);
            $info->extra = urlencode($customlabel->title);
        }
        return $info;
    } else {
        return null;
    }
}

/**
*
*
*/
function customlabel_get_view_actions() {
    return array();
}

/**
*
*
*/
function customlabel_get_post_actions() {
    return array();
}

/**
* TODO : check relevance
*
*/
function customlabel_get_types() {
    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = 'customlabel';
    $type->typestr = get_string('resourcetypecustomlabel', 'customlabel');
    $types[] = $type;

    return $types;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function customlabel_reset_userdata($data) {
    return array();
}

/**
* Other valuable API functions
**/

/**
* Returns a XML fragment with the stored metadata and the type information
*
*/
function customlabel_get_xml($clid){
    global $CFG;

    
    if ($customlabel = get_record('customlabel', 'id', "$clid")){
        
        $content = json_decode($customlabel->content);
        
        print_object($customlabel);
        
        $xml = "<datablock>\n";
        $xml .= "\t<instance>\n";
        $xml .= "\t\t<labeltype>{$customlabel->labelclass}</labeltype>\n";
        $xml .= "\t\t<title>{$customlabel->title}</title>\n";
        $xml .= "\t\t<timemodified>{$customlabel->timemodified}</timemodified>\n";
        $xml .= "\t</instance>\n";        
        $xml .= "\t<content>\n";
        foreach($content as $field => $value){
            $xml .= "\t\t<{$field}>";
            $xml .= "$value";
            $xml .= "</$field>\n";
        }
        $xml .= '\t</content>';        
        $xml .= '</datablock>';
        return $xml;        
    }
    
    return '';
}
?>