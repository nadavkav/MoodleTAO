<?php
    // Little popup window for reordering sibling pages
    
    require_once('../config.php');
    require_once('cmslocallib.php');
    
    $sourceid = required_param('source', PARAM_FILE); // the page whoose siblings we want to show and which would be affected by any actions
    $direction = optional_param('direction', '', PARAM_ALPHA); // up or down if page is to be moved up or down
    
    if (!$source = get_record('cmsnavi_data', 'pageid', $sourceid)) {
        error('Page with id '.$sourceid.' does not exist');
    }
    
    if (!$navi = get_record('cmsnavi', 'id', $source->naviid)) {
        error('Source has invalid menu');
    }
    
    if (!$course = get_record('course', 'id', $navi->course)) {
        error('Source has invalid course');
    }
    
    require_login($course->id);
    
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/cms:movepage', $context);
    
    if ($direction) { // We want to reorder
        cms_reorder($source->id, $source->parentid, $source->naviid, $direction);
    }

    $siblings = get_records_select('cmsnavi_data', "parentid = '$source->parentid' AND naviid = '$source->naviid'", 'sortorder ASC');
    print_header();
    $first = true;
    echo '<ol>';
    foreach($siblings as $sibling) {
        if ($first) {
            $uplink = '&nbsp;';
            $first = false;
        } else {
            $uplink = '<a href="reorder.php?source='.$sibling->pageid.'&amp;direction=up">'
                    .'<img src="../pix/t/up.gif" alt="'.get_string('up').'" /></a> ';
        }
        echo '<li>'.$uplink.$sibling->title.'</li>';
    }
    echo '</ol>';
    print_footer();

?>