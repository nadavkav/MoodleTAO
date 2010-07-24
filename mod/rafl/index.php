<?php // $Id: index.php,v 1.9 2009/03/31 13:03:28 mudrd8mz Exp $

/**
 * This page lists all the instances of rafl in a particular course
 *
 * @author  Your Name <your@email.address>
 * @version $Id: index.php,v 1.9 2009/03/31 13:03:28 mudrd8mz Exp $
 * @package mod/rafl
 */

/// Replace rafl with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = get_record('course', 'id', $id)) {
    error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'rafl', 'view all', "index.php?id=$course->id", '');


/// Get all required stringsrafl

$strrafls = get_string('modulenameplural', 'rafl');
$strrafl  = get_string('modulename', 'rafl');


/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strrafls, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($strrafls, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $rafls = get_all_instances_in_course('rafl', $course)) {
    notice('There are no rafls', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($rafls as $rafl) {
    if (!$rafl->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$rafl->coursemodule\">$rafl->name</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$rafl->coursemodule\">$rafl->name</a>";
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($rafl->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

print_heading($strrafls);
print_table($table);

/// Finish the page

print_footer($course);

?>
