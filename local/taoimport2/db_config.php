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

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('db_config_form.php');

require_capability('moodle/local:canimportlegacytao', get_context_instance(CONTEXT_SYSTEM));

$strheading = get_string('legacytaoimport', 'local');

print_header($strheading, $strheading, build_navigation($strheading));
$strdbconfig = get_string('legacydbconfig', 'local');
$strdbusers = get_string('legacydbusers', 'local');
$strdblp = get_string('legacydblp', 'local');

$tabs[] = new tabobject('dbconfig', 'db_config.php', $strdbconfig, $strdbconfig, false);
$tabs[] = new tabobject('langfix', 'fixclassifylang.php', 'Fix Classification Lang', 'Fix Classification Lang', false);
$tabs[] = new tabobject('dbusers', 'db_users.php', $strdbusers, $strdbusers, false);
$tabs[] = new tabobject('dblp', 'db_lp.php', $strdblp, $strdblp, false);

print_tabs(array($tabs), 'dbconfig');

$mform = new taoimport_form();
if ($data = $mform->get_data()) {
   set_config('legacydbtype', $data->dbtype, 'legacytao');
   set_config('legacydbhost', $data->dbhost, 'legacytao');
   set_config('legacydbname', $data->dbname, 'legacytao');
   set_config('legacydbuser', $data->dbuser, 'legacytao');
   set_config('legacydbpass', $data->dbpass, 'legacytao');

   //now check to see if can access db!
   require_once('taoimportlib.php');

   if(!$dbh = taoimport_dbconnect()) {
       error("Could not connect to SQL Server");
   } else {
       echo('<p><b>Successfully connected to ' . $data->dbhost . ':' . $data->dbname . '</b></p>');
   }
   
} else {
    $mform->display();

}

$mform->display();

print_footer();

?>