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
require_once('taoimportlib.php');
require_once($CFG->dirroot.'/local/lib.php');
require_capability('moodle/local:canimportlegacytao', get_context_instance(CONTEXT_SYSTEM));
$confirm = optional_param('confirm', '', PARAM_INT);

$strheading = get_string('legacytaoimport', 'local');
print_header($strheading, $strheading, build_navigation($strheading));

$dbh = taoimport_dbconnect();
if (!$dbh) {
    error("couldn't connect to db");
}

$strdbconfig = get_string('legacydbconfig', 'local');
$strdbusers = get_string('legacydbusers', 'local');
$strdblp = get_string('legacydblp', 'local');

$tabs[] = new tabobject('dbconfig', 'db_config.php', $strdbconfig, $strdbconfig, false);
$tabs[] = new tabobject('langfix', 'fixclassifylang.php', 'Fix Classification Lang', 'Fix Classification Lang', false);
$tabs[] = new tabobject('dbusers', 'db_users.php', $strdbusers, $strdbusers, false);
$tabs[] = new tabobject('dblp', 'db_lp.php', $strdblp, $strdblp, false);

print_tabs(array($tabs), 'dbusers');

$errors = "";
$count = 0;
$sql = "SELECT participant.*, school.id, school.url, school.name1, school.region_id, region.show_name ".
       "FROM participant, school, region WHERE participant.school_id=school.id AND region.id=school.region_id";
 $rs = $dbh->Execute($sql);
 if ( $rs->RecordCount() ) {
     if (!$confirm) {
         echo "<p>This script will try to import ".$rs->RecordCount(). " users - are you sure you want to do this?";
         print_single_button('db_users.php', array('confirm'=>1));
         print_footer();
         exit();
     }
     while ($rec = $rs->FetchRow()) {
         //first sanity check on email and login - don't import if these don't exist!
         if (empty($rec['login']) or empty($rec['email'])) {
             continue;
         }
         if (!record_exists('user', 'email', $rec['email'])) {
             if (!record_exists('user', 'username', $rec['login'])) {
                 $newuser = new stdclass();
                 $newuser->username = addslashes($rec['login']);
                 $newuser->email = $rec['email'];
                 $newuser->firstname = addslashes($rec['firstname']);
                 $newuser->lastname = addslashes($rec['name']);
                 if (!empty($rec['phone'])) {
                     $newuser->phone1 = $rec['phone'];
                 } else {
                     $newuser->phone1 = '';
                 }
                 if (!empty($rec['mobile'])) {
                     $newuser->phone2 = $rec['mobile'];
                 } else {
                     $newuser->phone2 = '';
                 }
                 if (!empty($rec['show_name'])) {
                     $newuser->city = addslashes($rec['show_name']);
                 }
                 if (!empty($rec['name1'])) {
                     $newuser->institution = addslashes($rec['name1']);
                 }
                 if (!empty($rec['url'])) {
                     $newuser->url = addslashes($rec['url']);
                 }
                 $newuser->password = md5($rec['password']);
                 $newuser->confirmed = 1;
                 $newuser->policyagreed = 0;
                 $newuser->deleted = 0;
                 $newuser->mnethostid = 1;
                 $newuser->emailstop = 0;
                 $newuser->timezone = '99';
                 $newuser->mailformat = 1;
                 $newuser->maildigest = 0;
                 $newuser->maildisplay = 2;
                 $newuser->htmleditor = 1;
                 $newuser->ajax = 1;
                 $newuser->autosubscribe = 1;
                 $newuser->timemodifed = time();
                 if (!insert_record('user', $newuser)) {
                     $errors .= "insert failed for user with email:".$rec['email']."</br>";
                 }
                 $user = get_record('user', 'email', $rec['email']);
                 // local_user_signup($user); //disable as we might not want to force a password change
                 //do normal role ass as would be done in local_user_signup
                 $ptrole = get_field('role', 'id', 'shortname', ROLE_PT);
                 $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
                 role_assign($ptrole, $user->id,0,$sitecontext->id);
                 $count++;
             } else {
                 $errors .= 'Username: '. $rec['login']. ' already exists, so did not import!</br>';
             }

         } else {
             $user = get_record('user', 'email', $rec['email']);
             create_member($user->id);
             $errors .= 'User with email: '. $rec['email']. ' already exists, so did not import!</br>';
         }
     }
 }
 notify("(".$count.") Users imported successfully!",'notifysuccess');
 notify($errors);
 print_footer();

?>