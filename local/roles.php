<?php

/***

This file defines the custom roles TAO system.

It is used by the tao_reset_custom_roles function.

See here for online documentation of roles: http://dev.to/code/TAO/UserRoles

note: roles that can only be assigned by automated processes are give no context.

**/

$customroles = array(
    'templateeditor' => array(
        'name'        => 'Template Editor',
        'description' => 'Creates the templates that are provided to authors to create learning paths.',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'headeditor' => array(
        'name'        => 'Head Editor',
        'description' => 'Reviews, Approves and Publishes learning paths.',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'lpauthor' => array(
        'name'        => 'Learning Path Author',
        'description' => 'Develops new Learning Paths',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'lpeditor' => array(
        'name'        => 'Learning Path Editor',
        'description' => 'Grants editing rights on a Learning Path.',
        'context'     => array(),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'lpcontributor' => array(
        'name'        => 'Learning Path Contributor',
        'description' => 'Helps Learing Path Author develop the learning paths',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN, ROLE_LPAUTHOR) 
    ),
    'headteacher' => array(
        'name'        => 'Head Teacher',
        'description' => 'Oversees the professional development of the teachers at his/her school and other schools that s/he has been assigned to cover.',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'seniorteacher' => array(
        'name'        => 'Senior Teacher',
        'description' => 'Has already achieved Participating Teacher and Master Teacher certification and is now pursuing Senior Teacher certification.',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'masterteacher' => array(
        'name'        => 'Master Teacher',
        'description' => 'Has already achieved Participating Teacher certification and is now pursuing Master Teacher certification. Can mentor Participant Teachers',
        'context'     => array(CONTEXT_COURSE, CONTEXT_USER),
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'certifiedpt' => array(
        'name'        => 'Certified Participating Teacher',
        'description' => 'Designates the additional capabilities for a partipant once they are certified.',
        'context'     => array(), 
        'canassign'   => array(ROLE_ADMIN) 
    ),
    'participatingteacher' => array(
        'name'        => 'Participating Teacher',
        'description' => 'The general, default user role on the platform.  Participant teachers are pursuing certification by completing Learning Paths.',
        'legacy'      => 'student',
        'context'     => array(CONTEXT_COURSE),
        'canassign'   => array(ROLE_ADMIN) 
    ),
);


?>
