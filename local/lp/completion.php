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
 * Learning Path Station Completion Indicator Setting
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot.'/course/format/page/lib.php');
require_once($CFG->dirroot.'/course/format/learning/lib.php');
require_once($CFG->dirroot.'/local/forms.php');
require_login();

$courseid = required_param('id', PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE, $courseid);

$strtitle = get_string('lpcompletionchecklist', 'local');

print_header($strtitle, $strtitle);

echo '<h2>' . $strtitle . '</h2>';

// check whether in 'my learning paths'
if (!tao_is_my_learning_path($courseid)) {

    notify(get_string('cannotcompletelearningpath', 'local'));

} else {

    // get list of all pages (stations)
    $stations = tao_get_learning_path_stations($courseid);
    
    // get list of viewed pages
    $viewed = tao_get_viewed_learning_path_stations($courseid, $USER->id);

    $mform = new tao_stationcompletion_form('', array('courseid' => $courseid, 'stations' => $stations, 'viewed' => $viewed));

    if (($data = $mform->get_data())) {

       $selected = array_keys(get_object_vars($data)); 

       foreach ($stations as $station) {
           $str = 'page_' . $station->id;
 
           // if selected
           if ( in_array($str, $selected) ) {

              // check whether we have it recorded already
              if (!in_array($station->id, $viewed) ) {
                  // no - so insert
                  $record = new object();
                  $record->format_page_id = $station->id;
                  $record->userid = $USER->id;

                  if (!insert_record('format_page_user_view', $record)) {
                      print_error('Couldn\'t update station completion');
                  }
              }

           } else {
              if (in_array($station->id, $viewed) ) {
                  // remove the record 
                  delete_records('format_page_user_view', 'format_page_id', $station->id, 'userid', $USER->id);
              }
           }
       }

       notify(get_string('updated'));
    }

    $mform->display();

}

print_footer();

?>
