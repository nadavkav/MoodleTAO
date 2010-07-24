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
require_once($CFG->libdir . '/ddllib.php');

class mnetuserpofile_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $externalvalues = array_merge(array("" => get_string('dontmap', 'mnet')), $this->_customdata['externalvalues']);
        $internalvalues = $this->_customdata['internalvalues'];

        $mform->addElement('header', 'header', get_string('mnetuserprofileheader', 'mnet'));
        $mform->addElement('html', '');
        foreach ($internalvalues as $field => $value) {
            $mform->addElement('select', $field, $value, $externalvalues);
            if (!empty($externalvalues[strtoupper($field)])) { //if this same field exists in the external system, then use it.
                $mform->setDefault($field,strtoupper($field));
            }
        }
        $mform->addElement('hidden', 'hostid',$this->_customdata['hostid']);
        $mform->closeHeaderBefore('buttonar');
        //now set defaults using db values
        $this->add_action_buttons(false);
    }
}

?>