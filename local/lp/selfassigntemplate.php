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
 * @author     David Drummond <david@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * assign self as "template editor" of a learning path.
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$id   = required_param('id', PARAM_INT);

$sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

if (! ($course = get_record('course', 'id', $id)) ) {
    error('Invalid course id');
}

$returnurl = $CFG->wwwroot . '/local/my/work.php';

$context = get_context_instance(CONTEXT_COURSE, $course->id);

if (!has_capability('moodle/local:canselfassigntemplateeditor', $sitecontext)) {
    print_error('cannotselfassigntemplate', 'local');
}

if (!$roleid = get_field('role', 'id', 'shortname', ROLE_TEMPLATEEDITOR)) {
    print_error('unknownrole', 'error');
}

$assignedshortstr = get_string('assignedtemplateeditorshort', 'local');
print_header($assignedshortstr, $assignedshortstr, build_navigation($assignedshortstr));

if (!user_has_role_assignment($USER->id, $roleid, $context->id)) {

    if (!role_assign($roleid, $USER->id, 0, $context->id)) {
        print_error('couldnotunassignrole', 'local', $returnurl);
    }

    redirect($returnurl, get_string('assignedtemplateeditor', 'local'), 5);

} else {

    redirect($returnurl, get_string('alreadyassignedtemplateeditor', 'local'), 5);

}
?>
