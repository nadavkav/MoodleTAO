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

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$confirm = optional_param('type', 0, PARAM_INT);

$types = get_records('classification_type');
$values = get_records('classification_value');

//copy of lang strings
$string = array();
$string['teaching_strategies'] = 'Teaching strategies';
$string['connected_model'] = 'Connected Model';
$string['constructivist_model'] = 'Constructivist Model';
$string['integrated_model'] = 'Integrated Model';
$string['nested_model'] = 'Nested Model';
$string['networked/extended_model'] = 'Networked/Extended Model';
$string['sequenced_model'] = 'Sequenced Model';
$string['shared_model'] = 'Shared Model';

$string['teaching_methods'] = 'Teaching methods';
$string['action-oriented_learning'] = 'Action-Oriented Learning';
$string['active_video_work'] = 'Active video work';
$string['ball_bearings'] = 'Ball Bearings';
$string['case_study'] = 'Case Study';
$string['creative_writing'] = 'Creative writing';
$string['discovery_of_learning'] = 'Discovery of learning';
$string['excursion'] = 'Excursion';
$string['experiment'] = 'Experiment';
$string['free_work'] = 'Free work';
$string['group_puzzle'] = 'Group Puzzle';
$string['learning_circle'] = 'Learning Circle';
$string['learning_through_teaching'] = 'Learning through teaching';
$string['letter_method'] = 'Letter method';
$string['mind_mapping'] = 'Mind mapping';
$string['portfolio'] = 'Portfolio';
$string['project_work'] = 'Project Work';
$string['sol_method'] = 'SOL method';
$string['station_work'] = 'Station Work';
$string['traffic_lights'] = 'Traffic Lights';
$string['using_tools_and_resources'] = 'Using Tools and Resources';
$string['web_quest'] = 'Web quest';
$string['weekly_plan'] = 'Weekly Plan';
$string['workshop'] = 'Workshop';
$string['other'] = 'Other';

$string['learning_styles'] = 'Learning styles';
$string['visual/spatial'] = 'Visual/spatial';
$string['verbal/linguistic'] = 'Verbal/linguistic';
$string['logical/mathematical'] = 'Logical/mathematical';
$string['musical/rhythmic'] = 'Musical/rhythmic';
$string['bodily/kinaesthetic'] = 'Bodily/kinaesthetic';
$string['interpersonal/social'] = 'Interpersonal/social';
$string['intrapersonal/introspective'] = 'Intrapersonal/introspective';
$string['communication'] = 'Communication';
$string['information'] = 'Information';
$string['simulation'] = 'Simulation';
$string['presentation'] = 'Presentation';
$string['production'] = 'Production';
$string['visualisation'] = 'Visualisation';


$string['key_stages'] = 'Key Stages';
$string['1_and_2'] = '1 and 2';
$string['3_and_4'] = '3 and 4';

$string['subject'] = 'Subject';
$string['english'] = 'English';
$string['mathematics'] = 'Mathematics';
$string['science'] = 'Science';
$string['design_and_technology'] = 'Design and Technology';
$string['ict'] = 'ICT';
$string['history'] = 'History';
$string['geography'] = 'Geography';
$string['art_and_design'] = 'Art and Design';
$string['music'] = 'Music';
$string['physical_education'] = 'Physical Education';

//reverse array key
$string = array_flip($string);

//print_object($types);
$showconfirm = false;
$updatedstrings = false;
$strheader = 'Fix classification lang strings';
$navlinks = array();
$navlinks[] = array('name' => $strheader, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);
print_header($strheader, $strheader, $navigation, "");

$strdbconfig = get_string('legacydbconfig', 'local');
$strdbusers = get_string('legacydbusers', 'local');
$strdblp = get_string('legacydblp', 'local');

$tabs[] = new tabobject('dbconfig', 'db_config.php', $strdbconfig, $strdbconfig, false);
$tabs[] = new tabobject('langfix', 'fixclassifylang.php', 'Fix Classification Lang', 'Fix Classification Lang', false);
$tabs[] = new tabobject('dbusers', 'db_users.php', $strdbusers, $strdbusers, false);
$tabs[] = new tabobject('dblp', 'db_lp.php', $strdblp, $strdblp, false);

print_tabs(array($tabs), 'langfix');

echo '<ul>';
foreach ($types as $type) {
    if (!empty($string[$type->name])) {
        $typestring = get_string($string[$type->name], 'course_classification');
        if ($typestring <> $type->name &&
            $typestring <> '[['.$string[$type->name].']]') {
            if ($confirm) {
                $type->name = $typestring;
                update_record('classification_type', $type);
                $updatedstrings = true;
                echo '<li>Type:'.$type->name. ' has been converted to: '.$typestring.'</li>';
            } else {
                echo '<li>Type:'.$type->name. ' will be converted to: '.$typestring.'</li>';
                $showconfirm = true;
            }
        }
    }
}
foreach ($values as $value) {
    if (!empty($string[$value->value])) {
        $typestring = get_string($string[$value->value], 'course_classification');
        if ($typestring <> $value->value &&
            $typestring <> '[['.$string[$value->value].']]') {
            if ($confirm) {
                $value->value = $typestring;
                update_record('classification_value', $type);
                $updatedstrings = true;
                echo '<li>Value:'.$value->value. ' has been converted to: '.$typestring.'</li>';
            } else {
                echo '<li>Value:'.$value->value. ' will be converted to: '.$typestring.'</li>';
                $showconfirm = true;
            }
        }
    }
}


echo '</ul>';
if ($showconfirm) {
    notice_yesno ('Do you want to convert these strings?', 'fixclassifylang.php?confirm=1', $CFG->wwwroot);
}
if ($updatedstrings) {
    notify('Strings have been converted sucessfully', 'green');
}
if (!$showconfirm && !$updatedstrings) {
    if (current_language() == 'en_utf8') {
        notify('the English language pack is currently selected, strings do not need to be converted.');
    } else {
        notify('strings cannot be converted - the strings for Classifications may not be translated in your language pack');
    }
}
print_footer();
?>