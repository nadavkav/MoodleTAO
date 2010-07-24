<?php
global $CFG;

$string['modulename'] = 'Custom label';
$string['modulenameplural'] = 'Custom labels';
$string['name'] = 'Label';
$string['title'] = 'Title';
$string['labelclass'] = 'Label type';
$string['customlabel:fullaccess'] = 'Full access to all fields ';
$string['resourcetypecustomlabel'] = 'Add a custom predefined label';
$string['hiddenrolesfor'] = 'Roles that CANNOT USE ';

// known types
$string['text'] = 'Text';
$string['taolpdata'] = 'Course Metadata';
$string['commentbox'] = 'Comment Box';

// this language files loads dynamically discovered label types
if (!function_exists('local_customlabel_get_classes')){
    function local_customlabel_get_classes(){
        global $CFG;
        
        $classes = array();
        $basetypedir = $CFG->dirroot."/mod/customlabel/type";
        
        $classdir = opendir($basetypedir);
        while ($entry = readdir($classdir)){
            if (preg_match("/^[.!]/", $entry)) continue; // ignore what need to be ignored
            if (!is_dir($basetypedir.'/'.$entry)) continue; // ignore real files
            unset($obj);
            $obj->id = $entry;
            $classes[] = $obj;
        }
        closedir($classdir);
        return $classes;
    }
}


// get strings for known types
$classes = local_customlabel_get_classes();
if (!empty($classes)){
    foreach($classes as $atype){
        $typelangfile = $CFG->dirroot."/mod/customlabel/type/{$atype->id}/en_utf8/customlabel.php";
        if (file_exists($typelangfile)){
            include_once($typelangfile);
        }
    }
}

?>