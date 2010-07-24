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
 * ability for authors to classify their learning paths.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/local/forms.php');
require_once($CFG->dirroot . '/tag/lib.php');

$id = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', $id)) {
    print_error('invalidcourse');
}

$context = get_context_instance(CONTEXT_COURSE, $id);

require_login($course->id);

require_capability('moodle/local:classifylearningpath', $context);

$strclassify = get_string('classifylp', 'local');

$PAGE->print_tabs('classify');

$mform = new tao_classify_learningpath_form($CFG->wwwroot.'/course/view.php', array('course' => $course));
if ($formdata = $mform->get_data()) {
    begin_sql();
    delete_records('course_classification', 'course', $course->id);
    $values = array();
    $cleanvalues = array();
    foreach ((array)$formdata as $key => $value) {
        if (preg_match('/checkboxes(\d+)/', $key, $matches) && !empty($value)) {
            $values = array_merge($values, array_keys($value));
        }
    }
    foreach ($values as $value) {
        if (!preg_match('/value(\d+)/', $value, $matches)) {
            continue;
        }
        insert_record('course_classification', (object)array('course' => $course->id, 'value' => $matches[1]));
        $cleanvalues[] = $matches[1];  
    }

    // update the manual tags
    tag_set('course', $course->id, explode(',', $formdata->tags));

    // update the classification tags
    $ctag_arr = array();
    $sql = "SELECT value FROM {$CFG->prefix}classification_value WHERE id IN ( " . implode(',', $cleanvalues) . ")";
    if (!empty($cleanvalues) && $ctags = get_records_sql($sql)) {
        foreach ($ctags as $ctag) {
            $ctag_arr[] = strtolower($ctag->value);
        }
    
    }
    tag_set('courseclassification', $course->id, $ctag_arr); //set the tags (or clear them if none selected)

    commit_sql();
    events_trigger('lp_classification', $course->id);
    notify(get_string('changessaved'), 'notifysuccess');
    print_continue($CFG->wwwroot . '/course/view.php?id=' . $course->id);
} else {
    $post = new object();
    if ($itemptags = tag_get_tags_csv('course', $course->id, TAG_RETURN_TEXT, 'default')) {
        $post->tags = $itemptags;
    }
    $mform->set_data($post);
    $mform->display();
}



?>
