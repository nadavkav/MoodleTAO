<?php

// very quick & dirty hack to get a database full of users

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$strole = get_field('role', 'id', 'shortname', 'seniorteacher');
$mtrole = get_field('role', 'id', 'shortname', 'masterteacher');
$ptrole = get_field('role', 'id', 'shortname', 'participatingteacher');

$users = array(
    'st' => array(
        'firstname' => 'Senior',
        'lastname'  => 'Teacher 1',
        'email'     => 'st@example.com',
        'roleid'    => $strole,
    ),
    'mt' => array(
        'firstname' => 'Master',
        'lastname'  => 'Teacher 1',
        'email'     => 'mt@example.com',
        'roleid'    => $mtrole,
    ),
    'pt1' => array(
        'firstname' => 'Participating 1',
        'lastname'  => 'Teacher 1',
        'email'     => 'pt1@example.com',
        'roleid'    => $ptrole,
    ),
    'pt2' => array(
        'firstname' => 'Participating 2',
        'lastname'  => 'Teacher 2',
        'email'     => 'pt2@example.com',
        'roleid'    => $ptrole,
    ),
    'pt3' => array(
        'firstname' => 'Participating 3',
        'lastname'  => 'Teacher 3',
        'email'     => 'pt3@example.com',
        'roleid'    => $ptrole,
    ),
);

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

foreach ($users as $user) {
    $user = (object)$user;
    $u = create_user_record(str_replace(' ', '', strtolower($user->firstname)), 'moodle');
    $user->id = $u->id;
    update_record('user', $user);
    role_assign($user->roleid, $user->id, 0, $sitecontext->id);
}

echo 'done';
?>