<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage 
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/ddllib.php');

class taoimport_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $strrequired = get_string('required');
        $dbtypes = array("access","ado_access", "ado", "ado_mssql", "borland_ibase", "csv", "db2", "fbsql", "firebird", "ibase", "informix72", "informix", "mssql", "mssql_n", "mysql", "mysqlt", "oci805", "oci8", "oci8po", "odbc", "odbc_mssql", "odbc_oracle", "oracle", "postgres64", "postgres7", "postgres", "proxy", "sqlanywhere", "sybase", "vfp");

        foreach ($dbtypes as $dbtype) {
            $dboptions[$dbtype] = get_string($dbtype, 'install');
        }

        if (!isset($frm->dbtype)) {
            $frm->dbtype = 'mysql';
        }
        $mform->addElement('header', 'header', 'Legacy TAO tables (usually MS SQL)');
        $mform->addElement('html', '');
        $mform->addElement('select', 'dbtype', get_string('dbtype', 'install'), $dboptions);
        $mform->addElement('text', 'dbhost', get_string('dbhost', 'install'));
        $mform->addElement('text', 'dbname', get_string('database', 'install'));
        $mform->addElement('text', 'dbuser', get_string('user'));
        $mform->addElement('text', 'dbpass', get_string('password'));
        $mform->addRule('dbtype', null, 'required', null, 'client');
        $mform->addRule('dbhost', null, 'required', null, 'client');
        $mform->addRule('dbname', null, 'required', null, 'client');
        $mform->addRule('dbuser', null, 'required', null, 'client');
        $mform->addRule('dbpass', null, 'required', null, 'client');
        $mform->closeHeaderBefore('buttonar');
        //now set defaults using db values
        $dbsettings = get_config('legacytao');
        if (!empty($dbsettings->legacydbtype)) {
            $mform->setDefault('dbtype',$dbsettings->legacydbtype);
        } else {
            $mform->setDefault('dbtype','mssql_n');
        }
        if (!empty($dbsettings->legacydbhost)) {
            $mform->setDefault('dbhost',$dbsettings->legacydbhost);
        }
        if (!empty($dbsettings->legacydbname)) {
            $mform->setDefault('dbname',$dbsettings->legacydbname);
        }
        if (!empty($dbsettings->legacydbuser)) {
            $mform->setDefault('dbuser',$dbsettings->legacydbuser);
        }
        if (!empty($dbsettings->legacydbpass)) {
            $mform->setDefault('dbpass',$dbsettings->legacydbpass);
        }

        //set dbhost to raw types to prevent any munging
        $mform->setType('dbhost', PARAM_RAW);

        $this->add_action_buttons(false);
    }
}

?>