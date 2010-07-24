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

function taoimport_dbconnect() {
  global $CFG;
    //get db settings.
    $dbsettings = get_config('legacytao');
    if (empty($dbsettings->legacydbtype) or empty($dbsettings->legacydbname)) {
        return false;
    }
    // Try to connect to the external database (forcing new connection)
    $dbh = &ADONewConnection($dbsettings->legacydbtype);

    if ($dbh->Connect($dbsettings->legacydbhost, $dbsettings->legacydbuser, $dbsettings->legacydbpass, $dbsettings->legacydbname, true)) {
        $dbh->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection
        return $dbh;
    } else {
        trigger_error("Error connecting to enrolment DB backend with: "
                      . "$dbsettings->legacydbhost, $dbsettings->legacydbuser, $dbsettings->legacydbpass, $dbsettings->legacydbname");
         return false;
    }
}

?>