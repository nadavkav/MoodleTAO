<?php

function upgrade_to_new_roles_ui() {

    global $CFG;

/// New table for storing which roles can be assigned in which contexts.
/// Define table role_context_levels to be created
    $table = new XMLDBTable('role_context_levels');

/// Adding fields to table role_context_levels
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
    $table->addFieldInfo('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('contextlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

/// Adding keys to table role_context_levels
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->addKeyInfo('contextlevel-roleid', XMLDB_KEY_UNIQUE, array('contextlevel', 'roleid'));
    $table->addKeyInfo('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

/// Conditionally launch create table for role_context_levels
    if (!table_exists($table)) {
        create_table($table);
    }

/// Now populate the role_context_levels table with the defaults that match
/// moodle_install_roles, and any other combinations that exist in this system.
    $roleids = get_records_menu('role', '', '', '', 'shortname,id');

/// Defaults, should match moodle_install_roles.
    $rolecontextlevels = array();
    if (isset($roleids['admin'])) {
        $rolecontextlevels[$roleids['admin']] = get_default_contextlevels('admin');
    }
    if (isset($roleids['coursecreator'])) {
        $rolecontextlevels[$roleids['coursecreator']] = get_default_contextlevels('coursecreator');
    }
    if (isset($roleids['editingteacher'])) {
        $rolecontextlevels[$roleids['editingteacher']] = get_default_contextlevels('editingteacher');
    }
    if (isset($roleids['teacher'])) {
        $rolecontextlevels[$roleids['teacher']] = get_default_contextlevels('teacher');
    }
    if (isset($roleids['student'])) {
        $rolecontextlevels[$roleids['student']] = get_default_contextlevels('student');
    }
    if (isset($roleids['guest'])) {
        $rolecontextlevels[$roleids['guest']] = get_default_contextlevels('guest');
    }
    if (isset($roleids['user'])) {
        $rolecontextlevels[$roleids['user']] = get_default_contextlevels('user');
    }

/// See what other role assignments are in this database, extend the allowed
/// lists to allow them too.
    $existingrolecontextlevels = get_recordset_sql('SELECT DISTINCT ra.roleid, con.contextlevel FROM
            {role_assignments} ra JOIN {context} con ON ra.contextid = con.id');
    foreach ($existingrolecontextlevels as $rcl) {
        $rcl = (object)$rcl;
        if (!isset($rolecontextlevels[$rcl->roleid])) {
            $rolecontextlevels[$rcl->roleid] = array($rcl->contextlevel);
        } else if (!in_array($rcl->contextlevel, $rolecontextlevels[$rcl->roleid])) {
            $rolecontextlevels[$rcl->roleid][] = $rcl->contextlevel;
        }
    }

/// Put the data into the database.
    foreach ($rolecontextlevels as $roleid => $contextlevels) {
        set_role_contextlevels($roleid, $contextlevels);
    }

/// Remove any role overrides for moodle/site:doanything, or any permissions
/// for it in a role without legacy:admin.
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    // Remove all overrides.
    delete_records_select('role_capabilities', "capability = 'moodle/site:doanything'
        AND contextid <> $systemcontext->id");

    $roletest = '';
    // Get the ids of all the roles that are moodle/legacy:admin.
    if ($adminroleids = get_records_select_menu('role_capabilities',
        "capability = 'moodle/legacy:admin' AND permission = 1 AND contextid = $systemcontext->id",
        '', 'id, roleid')) {
        $roletest = 'IN ( ' . implode(',', $adminroleids) . ')';
    } else {
        $adminroleids = array();
    }

    delete_records_select('role_capabilities', "roleid NOT $roletest
        AND capability = 'moodle/site:doanything'AND contextid = $systemcontext->id");

    set_field_select('role_capabilities', 'permission', 1,
            "roleid $roletest AND capability = 'moodle/site:doanything' AND contextid = $systemcontext->id");

    // And for any admin-y roles where moodle/site:doanything is not set, set it.
    $doanythingroleids = get_records_select_menu('role_capabilities',
        "capability = 'moodle/site:doanything' AND permission = 1 AND contextid = $systemcontext->id",
        '', 'id, roleid');
    foreach ($adminroleids as $roleid) {
        if (!in_array($roleid, $doanythingroleids)) {
            $rc = new stdClass;
            $rc->contextid = $systemcontext->id;
            $rc->roleid = $roleid;
            $rc->capability = 'moodle/site:doanything';
            $rc->permission = 1;
            $rc->timemodified = time();
            insert_record('role_capabilities', $rc);
        }
    }

    set_config('roles_ui_backport_upgraded', 1);
}

?>
