<?php

    require('../config.php');
    require('cmslocallib.php');

    $pageid   = required_param('pageid', PARAM_INT);
    $courseid = required_param('course', PARAM_INT);

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }

    require_login($course->id);

    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    require_capability('format/cms:editpage', $context);

    print_header();

    if ( $pagedata = get_record("cmspages_history", "id", $pageid) ) {
        $options = new stdClass;
        $options->noclean = true;
        print format_text(stripslashes($pagedata->content), FORMAT_HTML, $options);
    }

    print_footer($course);
?>