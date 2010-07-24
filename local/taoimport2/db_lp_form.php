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

class taodb_lp_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform =& $this->_form;

        // get a list of courses that are learning path templates
        $courses = tao_get_learning_path_templates();
        if (empty($courses)) {
            error("No templates available");
        }
        foreach($courses as $course) {
            $options[$course->id] = $course->fullname;
        }
        $mform->addElement('header','enrolhdr', get_string('general'));
        $mform->addElement('html', '<p>The following options should be the Translated names of the Learning Stations within your Learning Path as used on the legacy TAO system - the English versions are displayed for your reference</p>');
        $mform->addElement('text', 'legacyid21', '21 - About the Learning Path');
        $mform->addElement('text', 'legacyid26', '26 - How will it be relevant to me?');
        $mform->addElement('text', 'legacyid23', '23 - How will it work in the classroom?');
        $mform->addElement('text', 'legacyid24', '24 - Resource Requirements');
        $mform->addElement('text', 'legacyid25', '25 - How will the learning path be evaluated and developed?');
        $mform->addElement('select', 'course_template', get_string('choosetemplate', 'local'), $options);
        $mform->setDefault('legacyid21', '1 ');
        $mform->setDefault('legacyid26', '2 ');
        $mform->setDefault('legacyid23', '3 ');
        $mform->setDefault('legacyid24', '4 ');
        $mform->setDefault('legacyid25', '5 ');
        $mform->addRule('legacyid21', null, 'required', null, 'client');
        $mform->addRule('legacyid26', null, 'required', null, 'client');
        $mform->addRule('legacyid23', null, 'required', null, 'client');
        $mform->addRule('legacyid24', null, 'required', null, 'client');
        $mform->addRule('legacyid25', null, 'required', null, 'client');

        // submit buttons
        $this->add_action_buttons(true, get_string('create'));
    }
}

?>