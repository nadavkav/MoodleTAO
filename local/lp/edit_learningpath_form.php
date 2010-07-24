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

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
class editlearningpath_form extends moodleform {

    // form definition
    function definition() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        $mform->addElement('header','enrolhdr', get_string('general'));

        // name fields
        $mform->addElement('text','fullname', get_string('fullname'),'maxlength="254" size="50"');
        $mform->setHelpButton('fullname', array('coursefullname', get_string('fullname')), true);
        $mform->setDefault('fullname', $COURSE->fullname);
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text','shortname', get_string('shortname'),'maxlength="100" size="20"');
        $mform->setHelpButton('shortname', array('courseshortname', get_string('shortname')), true);
        $mform->setDefault('shortname', $COURSE->shortname);
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        $mform->addElement('htmleditor','summary', get_string('summary'), array('rows'=> '10', 'cols'=>'65'));
        $mform->setHelpButton('summary', array('text', get_string('helptext')), true);
        $mform->setDefault('summary', $COURSE->summary);
        $mform->setType('summary', PARAM_RAW);

        $mform->addElement('hidden', 'id', $COURSE->id);

        // submit buttons
        $this->add_action_buttons(true, get_string('update'));

    }
}

?>