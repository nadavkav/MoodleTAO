<?php
/**
 * Format capabilities
 *
 * @version $Id$
 * @package format_learning
 **/

$format_learning_capabilities = array(

    'format/learning:manageactivities' => array (
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW
        )
    )
);

?>