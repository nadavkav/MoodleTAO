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
 * @subpackage local
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 */

// add new page to handle admin for messaging
//$ADMIN->add('roles', new admin_externalpage('messagebyrole', get_string('messaging', 'local'), $CFG->wwwroot . '/local/admin/messagebyrole.php'));
$ADMIN->add('courses', new admin_externalpage('lpclassification', get_string('lpclassification', 'local'), $CFG->wwwroot . '/local/admin/lpclassify.php'));

$ADMIN->add('appearance', new admin_externalpage('imagemap', get_string('imagemap', 'local'), $CFG->wwwroot . '/local/admin/imagemap.php'));
$ADMIN->add('accounts', new admin_externalpage('inviteusers', get_string('inviteauser', 'local'), "$CFG->wwwroot/local/login/signup.php", array('moodle/local:invitenewuser')));
$ADMIN->add('mnet', new admin_externalpage('mahoodle', get_string('mahoodle', 'configmahoodle'), "$CFG->wwwroot/admin/configmahoodle.php"));


//moved from admin/settings/tao.php
if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "locations" settingpage
    $temp2 = new admin_settingpage('taosettings', get_string('taosettings', 'local'));

    $temp2->add(new admin_setting_configcheckbox('raflmodeenabled', get_string('raflmodeenabled', 'local'), get_string('raflmodeenabledhelp', 'local'), 0));


    /// Get categories to used as options for the settings further below
    $options = array();
    $parentlist = array();
    make_categories_list($options, $parentlist);

    // Get sections in 'My Section' category, also to be used as options for the settings further below
    $courseoptions = array();
    if (isset($CFG->mysectioncategory)) {
        $courses = get_courses($CFG->mysectioncategory, 'c.sortorder ASC', 'c.id, c.fullname', null);
        foreach ($courses as $course) {
            $courseoptions["$course->id"] = $course->fullname;
        }
    }
    if(empty($courseoptions)) {
        $courseoptions[0] = get_string('metanopotentialcourses');
    }
    /// LP category settings
    $temp2->add(new admin_setting_configselect('lptemplatescategory', get_string('choosetemplatecategory', 'local'), get_string('configtemplatecategory', 'local'), 1, $options));

    $temp2->add(new admin_setting_configselect('lpautomatedcategorisation', get_string('lpautomatedcategorisation', 'local'),
                                          get_string('configlpautomatedcategorisation', 'local'), 1,
                                          array( '1' => get_string('yes'),
                                                 '0' => get_string('no'))));

    $temp2->add(new admin_setting_configselect('lpdefaultcategory', get_string('choosedefaultcategory', 'local'), get_string('configdefaultcategory', 'local'), 1, $options));

    $temp2->add(new admin_setting_configselect('lppublishedcategory', get_string('choosepublishedcategory', 'local'), get_string('configpublishedcategory', 'local'), 1, $options));

    $temp2->add(new admin_setting_configselect('lpsuspendedcategory', get_string('choosesuspendedcategory', 'local'), get_string('configsuspendedcategory', 'local'), 1, $options));

    $temp2->add(new admin_setting_configtext('groupmax', get_string('groupmax', 'block_tao_team_groups'), get_string('groupmaxdesc', 'block_tao_team_groups'), '4',PARAM_INT));

    $ADMIN->add('server', $temp2);

    $ADMIN->add('server', new admin_externalpage('taoimport', get_string('legacytaoimport', 'local'), "$CFG->wwwroot/local/taoimport2/db_config.php"));

} // end of speedup

$localpath = '/local';
// generic cascade down : set $localpath as suitable.
$cascadesettings = $CFG->dirroot.$localpath.'/local/settings.php';
if (is_dir($CFG->dirroot.$localpath.'/local') && file_exists($cascadesettings)){
    include ($cascadesettings);
}

?>
