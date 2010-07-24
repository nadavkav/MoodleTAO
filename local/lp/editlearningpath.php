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

require_once('../../config.php');
require_once('edit_learningpath_form.php');
$courseid       = required_param('id', PARAM_INT); // course id
require_login();

if (! ($COURSE = get_record('course', 'id', $courseid)) ) {
    error('Invalid courseid');
}

$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('moodle/local:canchangelpsettings', $coursecontext);
$strheader = get_string('editlpsettings','local');

$navlinks = array();
$navlinks[] = array('name' => $strheader, 'link' => "", 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header_simple($strheader, '', $navigation);

$mform = new editlearningpath_form();
if (($data = $mform->get_data())) {
    $COURSE->fullname = $data->fullname;
    $COURSE->summary = $data->summary;
    $COURSE->shortname = $data->shortname;
    update_record('course', $COURSE);
    redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id, get_string('courseupdated','local'), 2);
} else {
    $mform->display();

}

print_footer();

?>