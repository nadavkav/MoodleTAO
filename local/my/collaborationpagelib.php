<?php  //$Id$

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot . '/my/pagelib.php');

class page_my_collaboration extends page_my_moodle {

    function get_type() {
        return PAGE_MY_COLLABORATION;
    }

    function print_header($title) {

        global $USER, $CFG;

        $replacements = array(
                              '%fullname%' => get_string('mycollaboration','local')
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        $site = get_site();

        $nav = get_string('mycollaboration','local');
        $header = $site->shortname.': '.$nav;
        $navlinks = array(array('name' => $nav, 'link' => '', 'type' => 'misc'));
        $navigation = build_navigation($navlinks);

        print_header($title, $header,$navigation,'','',true);

    }

    function url_get_path() {
        global $CFG;
        page_id_and_class($id,$class);
        if ($id == PAGE_MY_MOODLE) {
            return $CFG->wwwroot.'/my/local/collaboration.php';
        } elseif (defined('ADMIN_STICKYBLOCKS')){
            return $CFG->wwwroot.'/'.$CFG->admin.'/stickyblocks.php';
        }
    }

}

define('PAGE_MY_COLLABORATION',   'my-collaboration');
page_map_class(PAGE_MY_COLLABORATION, 'page_my_collaboration');

?>
