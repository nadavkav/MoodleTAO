<?php
global $CFG;

$string['modulename'] = 'Elément Pairform@nce';
$string['modulenameplural'] = 'Eléments Pairform@nce';
$string['name'] = 'Label';
$string['title'] = 'Titre ';
$string['labelclass'] = 'Type d\'élément ';
$string['customlabel:fullaccess'] = 'Accès total ';
$string['resourcetypecustomlabel'] = 'Insérer un élément Pairform@nce';
$string['hiddenrolesfor'] = 'Rôles n\'ayant pas accès au(x) ';

// known types
$string['text'] = 'Texte';
$string['taolpdata'] = 'Métadonnées de parcours';
$string['commentbox'] = 'Boîte de commentaires';


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
        $typelangfile = $CFG->dirroot."/mod/customlabel/type/{$atype->id}/fr_utf8/customlabel.php";
        if (file_exists($typelangfile)){
            include_once($typelangfile);
        }
    }
}

?>