<?php

// Functions and variables common to both visual indicator files

function get_dimensions($id, $modname, $table) {
    $dimensions = null;
    for ($i=0; $i<count($table); $i++) {
        for ($j=0; $j<count($table[$i]); $j++) {
            $search = array_search($id, $table[$i][$j]);
            if (strlen($search) > 0 && $search >= 0) {
                if ($table[$i][$j]['inputtype'] == $modname) {
                    $dimensions = new object();
                    $dimensions->x = $i;
                    $dimensions->y = $j;
                }
            }
        }
    }
    return $dimensions;
}

function get_image_filename($colour, $inputtype, $theme) {

    global $CFG, $imgarray;

    $filename = "completion_indicator_$colour";
    switch ($inputtype) {
    case 'course':
        $filename .= '_c';
        break;
    case 'feedback':
        $filename .= '_f';
        break;
    case 'quiz':
        $filename .= '_q';
        break;
    case 'scorm':
        $filename .= '_s';
        break;
    }

    $fullpath = $CFG->dirroot . '/pix/' . $theme . $filename . '.gif';

    if (!file_exists($fullpath)) {
        $fullpath = $CFG->dirroot . '/pix/completion_indicator_error.gif';
    }
    // Quick and dirty hack to get new images to work
    if($colour=='green'){
        $fullpath = $CFG->dirroot . '/pix/check.jpg';
    } else {
        $fullpath = $CFG->dirroot . '/pix/check_grey.jpg';
    }

    return $fullpath;
}

?>
