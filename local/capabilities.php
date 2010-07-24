<?php

/***

This file sets the capabilities by role in the TAO system.

It can be executed by the tao_reassign_capabilities() function which can 
be included in the local/db/upgrade.php file.

**/


function get_custom_capabilities() {

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

return array(
    ROLE_HEADTEACHER => array(
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:canmessagehts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_PT          => array(
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/course:view' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:isassignablept' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:canmessagefellowpts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:ispt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'mod/quiz:attempt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'mod/quiz:view' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/user:viewdetails' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'moodle/local:viewcertificationblock' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:cancreatelearningpaths' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_CERTIFIEDPT          => array(
            'mod/certificate:view' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_MT          => array(
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:canassignpt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:isassignablemt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:canmessageownpts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagefellowmts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessageownalumni' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:ismt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:cansearchforlptomessage' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'moodle/user:viewdetails' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'moodle/local:viewresponsibleusers' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'mod/certificate:view' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:invitenewuser' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:bulkinvitenewuser' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewcertificationblock' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managemytasks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_ST          => array(
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:canassignmt' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:canmessageownmts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagemtspts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagefellowsts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagehts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagemtsalumni' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:isst' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/user:viewdetails' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'moodle/local:viewresponsibleusers' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'mod/certificate:view' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_LPEDITOR    => array(
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:classifylearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:updatecoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:islpeditor' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/site:manageblocks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managemytasks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/learning:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_LPAUTHOR    => array(
        'moodle/course:create' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:cancreatelearningpaths' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:canmessagealumnibylp' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewcoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:assign' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'block/tao_lp_qa:editanswer' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewlpcontributors' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managelpcontributors' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:createstandardlp' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:managefiles' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canchangelpsettings' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managemytasks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/learning:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canviewraflmod' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_LPCONTRIBUTOR => array(
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewlpcontributors' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canviewraflmod' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_HEADEDITOR  => array(
        'moodle/local:canselfassignheadeditor' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:isheadeditor' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewcoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:updatecoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:classifylearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/site:manageblocks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'block/tao_lp_qa:editanswer' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canchangelpsettings' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managemytasks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/learning:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_TEMPLATEEDITOR  => array(
        'moodle/local:canselfassigntemplateeditor' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canselfassignheadeditor' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => $sitecontext->id,
        ),
        'moodle/local:viewunpublishedlearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:viewcoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:hasdirectlprelationship' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:updatecoursestatus' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:classifylearningpath' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:cancreatetemplates' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:create' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/site:manageblocks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/page:addpages' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/page:managepages' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managepageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:savelearningpathtemplate' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/site:backup' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/course:managefiles' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'block/tao_lp_qa:editanswer' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'block/tao_lp_qa:editquestion' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/role:unassignself' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:createstandardlp' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canchangelpsettings' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:managemytasks' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'format/learning:manageactivities' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
    ),
    ROLE_ADMIN => array(
        'moodle/local:messagebyrole' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID
        ),
        'moodle/local:canmessageallalumni' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagealumnibylp' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessageallpts' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:canmessagefellowadmins' => array(
            'permission' => CAP_ALLOW,
            'contextid'  => SYSCONTEXTID,
        ),
        'moodle/local:cansearchforlptomessage' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
    ),
    ROLE_USER => array(
        'moodle/local:canmessageanyuser' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
        'moodle/user:viewdetails' => array(
            'permission' => CAP_ALLOW,
            'contextid' => SYSCONTEXTID,
        ),
    ),
);

}

?>
