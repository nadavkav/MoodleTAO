<?php  //$Id$

// This file keeps track of upgrades to this block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_task_list_upgrade($oldversion=0) {
    $result = true;

    if ($result and $oldversion < 2007011501) {

    /// Define field format to be added to block_task_list
        $table = new XMLDBTable('block_task_list');
        $field = new XMLDBField('format');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'name');

    /// Launch add field format
        $result = $result and add_field($table, $field);
    }

    if ($result and $oldversion < 2007011503) {

    /// Manually remove bad capabilities
        $result = $result and delete_records('capabilities', 'name', 'block/tast_list:manage');
        $result = $result and delete_records('capabilities', 'name', 'block/tast_list:checkofftasks');
    }
    if ($result and $oldversion < 2007011505) {
      //TODO: The info field might be able to be removed, as it doesn't seem to be used anywhere, but in the meantime, change it to allow nulls

    /// Changing nullability of field info on table block_task_list to allow null
        $table = new XMLDBTable('block_task_list');
        $field = new XMLDBField('info');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'checked');
    /// Launch change of nullability for field info
        $result = $result && change_field_notnull($table, $field);

    }

    return $result;
}

?>