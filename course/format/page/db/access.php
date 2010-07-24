<?php
/**
 * Format capabilities
 *
 * @version $Id$
 * @package format_page
 **/

$format_page_capabilities = array( 

    'format/page:editpages' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW
        )
    ),

    'format/page:addpages' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW
        )
    ),

    'format/page:managepages' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW
        )
    ),

    'format/page:viewpagesettings' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW
        )
    )
);

?>