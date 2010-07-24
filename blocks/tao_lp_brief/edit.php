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
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * manages Team Groups
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lp_brief_form.php');

$strheading = get_string('editlpsummary', 'block_tao_lp_brief');

$courseid = required_param('id', PARAM_INT);

if (! ($COURSE = get_record('course', 'id', $courseid)) ) {
    error('Invalid course idnumber');
}

$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('block/tao_lp_brief:editlpsummary', $coursecontext);

print_header_simple($strheading, $strheading, build_navigation($strheading));

$lpform = new lp_brief_form('', array('course' => $COURSE));
if ($data = $lpform->get_data()) {
    $currentcourse = get_record('course', 'id', $courseid);
    
    $currentcourse->fullname = addslashes($data->name);
    $currentcourse->summary = addslashes($data->summary);
    update_record('course', $currentcourse);
    redirect($CFG->wwwroot.'/course/view.php?id='.$currentcourse->id, get_string('lpbriefsaved','block_tao_lp_brief'), 3);
} else {
    $lpform->display();
}     
print_footer();
?>