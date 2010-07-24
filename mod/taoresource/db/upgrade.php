<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */


// This file keeps track of upgrades to 
// the resource module
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

function xmldb_taoresource_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }


//===== 1.9.0 upgrade line ======//

    // change the remoteid key to be non-unique
    if ($result && $oldversion < 2007101510) {
        $table = new XMLDBTable('taoresource_entry');
        $index = new XMLDBIndex('remoteid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('remoteid'));
        
        if (index_exists($table, $index)) {
            $result = $result && drop_index($table, $index);
        }
        $result = $result && add_index($table, $index);
    }


    // change the remoteid key to be non-unique
    if ($result && $oldversion < 2007101511) {
        $table = new XMLDBTable('taoresource_metadata');
        $field = new XMLDBField('entry_id');

        // change the field type from ext to int
        if (field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');
            $result = $result && change_field_type($table, $field); 
        }
    }
    
    return $result;
}

?>