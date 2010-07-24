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

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/forms.php');

admin_externalpage_setup('lpclassification');
admin_externalpage_print_header();
print_heading(get_string('lpclassificationheading', 'local'));

$type = optional_param('type', 0, PARAM_INT);
$types = get_records_menu('classification_type', '', '', 'name', 'id,name');

$url = $CFG->wwwroot . '/local/admin/lpclassify.php';

echo get_string('editlpclass', 'local') . ':';
popup_form($url . '?type=', $types, 'lpclassify', $type);

if (!$type) {
    admin_externalpage_print_footer();
    exit;
}

if (!$values = get_records('classification_value', 'type', $type, 'value')) {
    $values = array();
}

$mform = new tao_adminsettings_lpclassification_form('', array('type' => $type, 'values' => $values));
if ($formdata = $mform->get_data()) {
    $todelete = array();
    foreach ((array)$formdata as $key => $value) {
        if (preg_match('/edit(\d+)/', $key, $matches)) {
            $id = $matches[1];
            if (!empty($formdata->{'delete' . $id})) {
                $todelete[] = $id;
                continue;
            }
            if ($value == $values[$id]->value) {
                continue;
            }
            set_field('classification_value', 'value', $value, 'id', $id);
        }
    }
    if (count($todelete) > 0) {
        $in = ' IN (' . implode(',', $todelete) . ')';
        delete_records_select('course_classification', 'value ' . $in);
        delete_records_select('classification_value', 'id ' . $in);
    }
    if (!empty($formdata->new)) {
        insert_record('classification_value', (object)array('type' => $type, 'value' => $formdata->new));
    }
    print_continue($url . '?type=' . $type);
} else {
    $mform->display();
}

admin_externalpage_print_footer();


?>
