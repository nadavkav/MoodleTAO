<?php // $Id: view.php,v 1.2 2006/12/08 20:54:38 mark-nielsen Exp $
/**
 * Prints the detailed view of task list
 *
 * Prints either the categories and tasks with their checkboxes or
 * the editing interface.
 *
 * @author Mark Nielsen
 * @version $Id: view.php,v 1.2 2006/12/08 20:54:38 mark-nielsen Exp $
 * @package block_task_list
 **/

    require_once('../../config.php');
    require_once($CFG->libdir.'/blocklib.php');

    $id = optional_param('id', 0, PARAM_INT);
    $instanceid = optional_param('instanceid', 0, PARAM_INT);

    if ($instanceid) {
        if (!$instance = get_record('block_instance', 'id', $instanceid)) {
            error('Invalid block instance ID: '.$instanceid);
        }
    } else if ($id) {
        if (!$blockid = get_field('block', 'id', 'name', 'task_list')) {
            error('Block not installed correctly');
        }
        if (!$instance = get_record('block_instance', 'blockid', $blockid, 'pageid', $id, 'pagetype', 'course-view')) {
            error('Instance not found in course.  Please add the task list block to your course.');
        }
    } else {
        error('Programmer Error: Must pass instance ID or course ID');
    }

    require_login($instance->pageid);

    if (!$tasklist = block_instance('task_list', $instance)) {
        error('Could not make block instance');
    }
    if (!$tasklist->can_checkoff() && !$tasklist->can_view()) {
        error('You are not allowed to view this page.');
    }

    $tasklist->set_baseurl();

    $tasklist->title = format_text($tasklist->title, FORMAT_HTML);

    $course = $tasklist->course; // Must set

    $output = $tasklist->make_task_view();

    $CFG->pagepath = 'block_task_list/view';  // Trick body class >:^)

    $navlinks[] = array('name' => $tasklist->title, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("{$course->shortname}: $tasklist->title", $course->fullname, $navigation);
    echo $output;
    print_footer($course);
?>