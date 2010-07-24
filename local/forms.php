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
 * this file should be used for all tao-specific forms.
 * I've put it in a separate file to avoid pulling in formslib unncessarily.
 *
 * form classes shlould all start with the tao_prefix.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

/**
* form for the 'byrole' messaging tab
class tao_message_byrole_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform =& $this->_form;

        $roles =  get_records_select('role', 'id IN ('. $CFG->messageenabledroles . ')', 'sortorder ASC');
        $checkboxes = tao_add_roles_to_form($mform, $roles);

        $mform->addElement('hidden', 'tab', 'byrole'); // make the correct tab show.
        $mform->addGroup($checkboxes, 'rolecheckboxes', get_string('messageroles', 'local'));

        $mform->closeHeaderBefore('body');
        $strrequired = get_string('required');
        $mform->addElement('htmleditor', 'body', get_string('messagebody', 'local'));
        $mform->setType('text', PARAM_RAW);
        $mform->addRule('body', null, 'required', null, 'client');
        $mform->addElement('format', 'format', get_string('format'));


        $mform->addRule('body', $strrequired, 'required', null, 'client');
        $mform->addRule('rolecheckboxes', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('sendmessage', 'local'));
    }
}

class tao_adminsettings_messagebyrole_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform =& $this->_form;

        $roles = get_all_roles();
        $checkboxes = tao_add_roles_to_form($mform, $roles, false);

        $mform->addElement('hidden', 'tab', 'byrole'); // make the correct tab show.
        $mform->addGroup($checkboxes, 'rolecheckboxes', get_string('messagerolesenabled', 'local'));

        $defaults = array();
        if (isset($CFG->messageenabledroles) && !empty($CFG->messageenabledroles)) {
            foreach (explode(',', $CFG->messageenabledroles) as $roleid) {
                $defaults['role' . $roleid] = 1;
            }
        }
        $mform->setDefault('rolecheckboxes', $defaults);
        $this->add_action_buttons(false);
    }
}
*/
class tao_message_lpsearch_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('hidden', 'tab', 'byrole');
        $mform->addElement('text', 'search', get_string('messagesearchforlp', 'local'));
        $this->add_action_buttons(false, $this->submitbutton_string());
    }

    public function submitbutton_string() {
        return get_string('search');
    }

    public function get_fake_url($baseurl, $extras=array()) {
        $urlbits = array(
            '_qf__' . get_class($this) => 1,
            'tab'                      => 'byrole',
            'submitbutton'             => $this->submitbutton_string(),
            'sesskey'                  => sesskey(),
        );
        $urlbits = array_merge($urlbits, $extras);
        $url = $baseurl . '?';
        foreach ($urlbits as $k => $v) {
            $url .= "$k=$v&amp;";
        }
        return $url;
    }
}

class tao_message_send_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $target = $this->_customdata['target'];
        $course = $this->_customdata['course'];
        $strrequired = get_string('required');
        $count = tao_message_count_recipients_by_target($target, $course);
        $a = (object)array(
            'target' => get_string('messagetarget' . $target->stringkey, 'local'),
            'course' => $course->fullname,
            'count'  => $count,
        );
        if ($course->id != SITEID) {
            $strheader = get_string('sendingmessagetocourse', 'local', $a);
        } else {
            $strheader = get_string('sendingmessageto', 'local', $a);
        }
        $mform->addElement('header', 'header', $strheader);
        $mform->addElement('hidden', 'target', $target->key);
        $mform->addElement('hidden', 'lp', $course->id);
        $mform->addElement('hidden', 'tab', 'byrole');
        $mform->closeHeaderBefore('body');
        $mform->addElement('textarea', 'body', get_string('messagebody', 'local'), 'rows="10"');
        $mform->setType('text', PARAM_RAW);
        $mform->addRule('body', null, 'required', null, 'client');
        $mform->addElement('format', 'format', get_string('format'));
        $mform->addRule('body', $strrequired, 'required', null, 'client');
        $this->add_action_buttons(true, get_string('sendmessage', 'local'));

    }
}
class tao_group_message_send_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $group = $this->_customdata['group'];
        $course = $this->_customdata['course'];
        $count = $this->_customdata['count'];
        $strrequired = get_string('required');
        $a = (object)array(
            'group' => $group->id,
            'course' => $course->fullname,
            'count' => $count,
            'target' => $group->name,
        );
        if ($course->id != SITEID) {
            $strheader = get_string('sendingmessagetocourse', 'local', $a);
        } else {
            $strheader = get_string('sendingmessageto', 'local', $a);
        }
        $mform->addElement('header', 'header', $strheader);
        $mform->addElement('hidden', 'id', $course->id);
        $mform->addElement('hidden', 'groupid', $group->id);
        $mform->closeHeaderBefore('body');
        $mform->addElement('htmleditor', 'body', get_string('messagebody', 'local'));
        $mform->setType('text', PARAM_RAW);
        $mform->addRule('body', null, 'required', null, 'client');
        $mform->addElement('format', 'format', get_string('format'));
        $mform->addRule('body', $strrequired, 'required', null, 'client');
        $this->add_action_buttons(true, get_string('sendmessage', 'local'));

    }
}
class tao_adminsettings_lpclassification_form extends moodleform {

    private $type;
    private $values;

    public function definition() {
        $mform =& $this->_form;
        $this->type = $this->_customdata['type'];
        $this->values = $this->_customdata['values'];

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'type', $this->type);
        $alternate = false;

        foreach ($this->values as $value) {
            $attr = array('class' => 'row' . (int)$alternate);
            $mform->addElement('text', 'edit' . $value->id, get_string('currentvalue', 'local'), $attr);			
            $mform->addElement('checkbox', 'delete' . $value->id, '' , get_string('delete'), $attr);
            $mform->addRule('edit' . $value->id, $strrequired, 'required', null, 'client');
            $mform->setDefault('edit' . $value->id, $value->value);

            $alternate = !($alternate);
        }
        $mform->addElement('text', 'new', get_string('addclass', 'local'));
        $this->add_action_buttons(false);
    }
}

class tao_classify_learningpath_form extends moodleform {

    private $course;

    public function definition() {
        $mform =& $this->_form;
        $this->course = $this->_customdata['course'];

        $mform->addElement('hidden', 'id', $this->course->id);
        $mform->addElement('hidden', 'action', 'classify');

        $mform->addElement('textarea', 'tags', get_string('tags'), 'cols="75" rows="3"');

        $allvalues = tao_get_classifications(false);
        $types = get_records('classification_type');
        $selected = array();
        if ($selectedtypes = get_records('course_classification', 'course', $this->course->id)) {
            foreach ($selectedtypes as $type) {
                $selected['value' . $type->value] = 1;
            }
        }

        $checkboxes = array();
        foreach ($allvalues as $value) {
            if (!array_key_exists($value->typeid, $checkboxes)) {
                $checkboxes[$value->typeid] = array();
            }
            $checkboxes[$value->typeid][] = $mform->createElement('checkbox', 'value' . $value->id, $value->value, $value->value);
        }
        foreach ($types as $type) {
            $mform->addElement('header', $type->name);
            $mform->addGroup($checkboxes[$type->id], 'checkboxes' . $type->id, $type->name);
            $mform->setDefault('checkboxes'. $type->id, $selected);
        }
        $this->add_action_buttons(false);
    }

}

class tao_classify_user_form extends moodleform {

    private $course;

    public function definition() {
        $mform =& $this->_form;
        $this->user = $this->_customdata['user'];
        
        $mform->addElement('hidden', 'action', 'classify');
        $mform->addElement('hidden', 'id', $this->user->id);
        $selected = array();
        if ($itemptags = tag_get_tags_array('userclassify', $this->user->id)) {
            foreach($itemptags as $itag) {
                $selected[strtolower($itag)] = 1;
            }
        }

        $types = get_records('classification_type');        
        $allvalues = tao_get_classifications(false);
        $checkboxes = array();
        foreach ($allvalues as $value) {
            if (!array_key_exists($value->typeid, $checkboxes)) {
                $checkboxes[$value->typeid] = array();
            }
            $checkboxes[$value->typeid][] = $mform->createElement('checkbox', strtolower($value->value), $value->value, $value->value);
        }
        foreach ($types as $type) {
            $mform->addElement('header', $type->name);
            $mform->addGroup($checkboxes[$type->id], 'checkboxes' . $type->id, $type->name);
            $mform->setDefault('checkboxes'. $type->id, $selected);
        }
        $this->add_action_buttons(false);
    }

}

class tao_finduser_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('text', 'idnumber', get_string('teacherid', 'local'), get_string('teacherid', 'local'));
        $mform->addElement('text', 'email', get_string('email'), get_string('email'));

        $strrequired = get_string('required');
        $mform->addRule('idnumber', 'required', 'required', null, 'client');
        $mform->addRule('email', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('search'));
    }
}

class tao_findusergroup_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $this->courseid = $this->_customdata['courseid'];
        $groupid = $this->_customdata['groupid'];
        
        $mform->addElement('text', 'email', get_string('email'), get_string('email'));
        $mform->addElement('hidden', 'id', $this->courseid);
        $mform->addElement('hidden', 'groupid', $groupid);
        $strrequired = get_string('required');
        $mform->addRule('email', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('search'));
    }
}

class tao_imagemapping_form extends moodleform {

    private $currentmappings;
    private $image;

    public function definition() {
        $mform =& $this->_form;
        $this->currentmappings = $this->_customdata['mappings'];
        $this->image = $this->_customdata['image'];

        $mform->addElement('hidden', 'mappings', $this->image);

        $url = get_string('url', 'local');
        $desc = get_string('description');
        $del = get_string('delete');
        $order = get_string('order');
        $strcourse = get_string('course');

        foreach ($this->currentmappings as $mapping) {
            $mform->addElement('header', 'currentmapping' . $mapping->id, get_string('currentmap', 'local'));
            $mform->addElement('text', 'url' . $mapping->id, $url);
            $mform->addElement('text', 'courseid' . $mapping->id, $strcourse, 'size="3"');
            $mform->addElement('text', 'desc' . $mapping->id, $desc);
            $mform->addElement('text', 'sortorder' . $mapping->id, $order, 'size="3"');
            $mform->addElement('checkbox', 'delete' . $mapping->id, $del);
            $mform->setDefaults(array(
                'url' . $mapping->id => $mapping->url,
                'courseid'. $mapping->id => $mapping->courseid,
                'desc' . $mapping->id => $mapping->description,
                'sortorder' . $mapping->id => $mapping->sortorder
            ));
            $mform->addRule('url' . $mapping->id, null, 'required', null, 'client');
            $mform->addRule('sortorder' . $mapping->id, null, 'required', null, 'client');
        }

        $mform->addElement('header', 'newmapping', get_string('addnewurlmap', 'local'));

        $mform->addElement('text', 'addurl', $url);
        $mform->addElement('text', 'addcourseid', $strcourse, 'size="3"');
        $mform->addElement('text', 'adddesc', $desc);
        $mform->addElement('text', 'addsortorder', $order, 'size="3"');
        $mform->setDefaults(array(
                'addsortorder' => '1'
            ));
        $this->add_action_buttons(false);
    }

    public function validation($data) {
        $errors = array();
        $strreq = get_string('required');
        if (!empty($data['addurl']) || !empty($data['adddesc']) || !empty($data['sortorder'])) {
            if (empty($data['addurl'])) {
                $errors['addurl'] = $strreq;
            }
            if (empty($data['addsortorder'])) {
                $errors['addsortorder'] = $strreq;
            }
        }
        return $errors;
    }
}

/**
* helper function to convert incoming roles lists into ids
* from formdata that names elements like role1, role2 etc.
*
* @param array $data formdata (role1 => 1 etc)
*
* @return array array of integers (role ids)
*/
function tao_formdata_to_roles($data) {
    $roles = array();
    foreach ($data as $key => $value) {
        $matches = array();
        if (preg_match('/role(\d+)/', $key, $matches) && !empty($value)) {
            $roles[] = $matches[1];
        }
    }
    return $roles;
}

/**
* helper function to create roles checkboxes on a form
*
* @param moodleform $mform form to create elements on
* @param array      $roles roles to add
* @param boolean    $ignorezeroes skip roles with no assignments (not admin usually)
*
* @return array of checkbox form elements
*/
function tao_add_roles_to_form(MoodleQuickForm &$mform, array $roles, $ignorezeros=true) {
    $checkboxes = array();
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $contextids = array(SYSCONTEXTID, $frontpagecontext->id);
    $counts = tao_count_users_on_exact_roles($roles, $contextids);
    $userstr = get_string('user');
    $usersstr = get_string('users');
    foreach ($roles as $role) {
        $count = $counts[$role->id];
        $name = $role->name . ' (' . $count . ' ' . ($count == 1 ? $userstr : $usersstr) . ')';
        $c = $mform->createElement('checkbox', 'role' . $role->id, $role->name, $name);
        if ($count == 0 && $ignorezeros) {
            $c->freeze();
        }
        $checkboxes[] = $c;
    }
    return $checkboxes;
}

class tao_stationcompletion_form extends moodleform {

    public function definition() {
        global $USER;
        $mform =& $this->_form;

        $this->courseid = $this->_customdata['courseid'];
        $stations       = $this->_customdata['stations'];
        $viewed         = $this->_customdata['viewed'];

        foreach ($stations as $station) {
            $mform->addElement('checkbox', 'page_'.$station->id, $station->nameone);
            if ( in_array($station->id, $viewed) ) {
                $mform->setDefault('page_'.$station->id, 1);
            }
        }
        $mform->addElement('hidden', 'id', $this->courseid);
        $this->add_action_buttons(false, get_string('update'));

    }
}
