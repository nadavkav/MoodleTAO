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

admin_externalpage_setup('messagebyrole');
admin_externalpage_print_header();
print_heading(get_string('messagetargets', 'local'));

$form = new tao_adminsettings_messagebyrole_form();
if ($fromform = $form->get_data()) {
    $roles = tao_formdata_to_roles($fromform->rolecheckboxes);
    set_config('messageenabledroles', implode(',', $roles));
    redirect($CFG->wwwroot . '/local/admin/messagebyrole.php', get_string('changessaved'));
    exit;
} else {
    print_simple_box_start();
    $form->display();
    print_simple_box_end();
}

admin_externalpage_print_footer();






?>
