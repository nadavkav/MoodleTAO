<?php // $Id$
/**
 * Page class for learning paths
 *
 * @author David Drummond
 * @version $Id$
 * @package learning
 **/

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // Needed for some blocks
require_once($CFG->dirroot.'/course/format/page/pagelib.php'); // Needed for inheritance 

if (!defined('BLOCK_POS_CENTER')) {
    define('BLOCK_POS_CENTER', 'c');
}

if (defined('ADMIN_STICKYBLOCKS')) {
    define('PAGE_FORMAT_LEARNING', 'format_learning');
    page_map_class(PAGE_FORMAT_LEARNING, 'format_learning');
}

/**
 * Remapping PAGE_COURSE_VIEW to format_page class
 *
 **/
page_map_class(PAGE_COURSE_VIEW, 'format_learning');

/**
 * Add the page types defined in this file
 *
 **/
$DEFINEDPAGES = array(PAGE_COURSE_VIEW);

/**
 * Class that models the behavior of a format page
 *
 * @package format_page
 **/
class format_learning extends format_page {

    /**
     * Prints the tabs for the learning path type
     *
     * @param string $currenttab Tab to highlight
     * @return void
     **/
    function print_tabs($currenttab = 'layout') {
        global $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        $tabs = $row = $inactive = $active = array();

        $row[] = new tabobject('view', $this->url_get_full(), get_string('editpage', 'format_page'));
        if ( has_capability('format/page:addpages', $context) ) {
            $row[] = new tabobject('addpage', $this->url_build('action', 'editpage'), get_string('addpage', 'format_page'));
        } 
        if ( has_capability('format/page:managepages', $context) ) {
            $row[] = new tabobject('manage', $this->url_build('action', 'manage'), get_string('manage', 'format_page'));
        }
        if ( has_capability('moodle/local:managepageactivities', $context) ) {
            $row[] = new tabobject('activities', $this->url_build('action', 'activities'), get_string('managemods', 'format_page'));
        }
        if ( has_capability('moodle/local:classifylearningpath', $context) ) {
            $row[] = new tabobject('classify', $this->url_build('action', 'classify'), get_string('classification', 'local'));
        }
        if ( has_capability('moodle/local:savelearningpathtemplate', $context) ) {
            $row[] = new tabobject('backup', $this->url_build('action', 'backup'), get_string('makebackup', 'local'));
        }
        $tabs[] = $row;

        if (in_array($currenttab, array('layout', 'settings', 'view'))) {
            $active[] = 'view';

            $row = array();
            $row[] = new tabobject('layout', $this->url_get_full(), get_string('layout', 'format_page'));
            $row[] = new tabobject('settings', $this->url_get_full(array('action' => 'editpage')), get_string('settings', 'format_page'));
            $tabs[] = $row;
        }

        print_tabs($tabs, $currenttab, $inactive, $active);
    }

    function get_type() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return 'format_learning';
        }
        return parent::get_type();
    }

    function blocks_default_position() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return BLOCK_POS_RIGHT;
        }
        return parent::blocks_default_position();
    }

    function url_get_path() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            global $CFG;
            return $CFG->wwwroot . '/admin/stickyblocks.php';
        }
        return parent::url_get_path();
    }

    function url_get_parameters() {
        if (defined('ADMIN_STICKYBLOCKS')) {
            return array('pt' => 'format_learning');
        }
        return parent::url_get_parameters();
    }

    function get_hacked_object_for_sticky() {
        return new format_learning_hack();
    }

    /**
     * Override else when we're editing stickyblocks, moving blocks causes them to disappear from the middle column.
     *
     * @param object $instance Block instance
     * @param int $move Move constant (BLOCK_MOVE_RIGHT or BLOCK_MOVE_LEFT). This is the direction that we are moving
     * @return char
     **/
    function blocks_move_position(&$instance, $move) {
        if (defined('ADMIN_STICKYBLOCKS')) {
            if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
                return BLOCK_POS_RIGHT;
            } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
                return BLOCK_POS_LEFT;
            }
            return $instance->position;
        }
        return parent::blocks_move_position($instance, $move);
    }
}

class format_learning_hack extends format_learning {

    function get_type() {
        return 'format_learning';
    }
}
