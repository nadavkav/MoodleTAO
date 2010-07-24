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
 * learning path list - meant to duplicate http://aoc.ssatrust.org.uk/index?s=13
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();

$strheading = get_string('learningpaths', 'local');
print_header($strheading, $strheading, build_navigation($strheading));
$url = $CFG->wwwroot . '/local/lp/list.php';

if ($type = optional_param('type', 0, PARAM_INT)) {
    $first = optional_param('first', 0, PARAM_INT);

    if (($ids = tao_get_courseids_with_classification($type, $first, COURSE_STATUS_PUBLISHED, $CFG->lppublishedcategory)) && ($courses = get_courses('all', 'c.sortorder ASC', 'c.*', array_keys($ids)))) {
        foreach ($courses as $course) {
            print_course($course);
        }
        echo '<a href="' . $url . '">' . get_string('backtolist', 'local') . '</a>';
        print_footer();
        exit;
    } else {
        notify(get_string('novisiblecourses', 'local'));
    }
}

$filtervalues = tao_get_classifications(true, null, COURSE_STATUS_PUBLISHED, $CFG->lppublishedcategory); // note only want 'published' on this screen
$filters = array();
$topcat = array();
$secondcat = array();

foreach ($filtervalues->allvalues as $f) {
    switch ($f->type) {
        case 'filter':
            if (!array_key_exists($f->typeid, $filters)) {
                $filters[$f->typeid] = array(0 => $f->name . ':');
            }
            $filters[$f->typeid][$f->id] = $f->value;
        break;
        case 'topcategory':
            if (empty($topcat)) {
                $topcat[0] = $f->name;
            }
            $topcat[$f->id] = $f->value;
        break;
        case 'secondcategory':
            if (empty($secondcat)) {
                $secondcat[0] = $f->name;
            }
            $secondcat[$f->id] = $f->value;
        break;
    }
}


foreach ($filters as $id => $filter) {
    popup_form($url . '?type=', $filter, $id, $type, null);
}

$title = $topcat[0]; unset($topcat[0]);
foreach ($topcat as $firstid => $name) {
    print_heading ($title . ' ' . $name);
    unset($secondcat[0]);
    foreach ($secondcat as $id => $name) {
        $key = $firstid . '|' . $id;
        $count = 0;
        if (array_key_exists($key, $filtervalues->secondcounts)) {
            $count = $filtervalues->secondcounts[$key]->count;
        }
        echo '<p class="lpcatlink"><a href="' . $url . '?type=' . $id . '&first=' . $firstid . '">' . $name . ' (' . $count . ')</a></p>';
    }
}

print_footer();


?>
