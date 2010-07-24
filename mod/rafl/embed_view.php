<?php  // $Id: view.php,v 1.9 2009/04/09 15:19:29 arborrow Exp $
/**
 * This page prints a particular instance of rafl
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.9 2009/04/09 15:19:29 arborrow Exp $
 * @package mod/rafl
 */

/// (Replace rafl with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // rafl instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('rafl', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $rafl = get_record('rafl', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $rafl = get_record('rafl', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $rafl->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('rafl', $rafl->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "rafl", "view", "view.php?id=$cm->id", "$rafl->id");

/// Print the page header
$strrafls = get_string('modulenameplural', 'rafl');
$strrafl  = get_string('modulename', 'rafl');

$navlinks = array();
$navlinks[] = array('name' => $strrafls, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($rafl->name), 'link' => '', 'type' => 'activityinstance');


/**
 ********************************************** DAN HAS BEEN HERE START *************************************************
 */

// Better move to the top of this file
require_once(dirname(__FILE__).'/locallib.php');

$rafl = new localLibRafl();

// Debugging
//var_dump($rafl->get_lp_item_structure($rafl->get_rafl_item_id_by_country('uk')));
//$rafl->update_moodle_item($cm->course, $raflitemid, $text);
//$rafl->create_share(3456, 123456);
//$rafl->update_share_contributors(3456, array(7655, 7644));
//die('done!');

// In case we should ever do countries: Gimme the right rafl item in the right language for this country
$country_item_id = $rafl->get_rafl_item_id_by_country('uk');

// Print the rafl page
$rafl->display_rafl_component($cm->course, $country_item_id, $CFG->prefix);

/**
 ********************************************** DAN HAS BEEN HERE END *************************************************
 */



?>
