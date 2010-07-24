<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/site_forum is a work derived from Moodle mod/resoruce
 * @package forum
 * @subpackage forum block
 * @date: 2009 01 19
 *
 */
     
  class block_site_forum extends block_base {
    
    function init() {
      $this->title = get_string('blockname', 'block_site_forum');
      //$this->cron = 1;
      $this->cron = 0;
      $this->version = 2009011901;
    }
    
    // only one instance of this block is required
    function instance_allow_multiple() {
      return false;
    } 
    
    // label and button values can be set in admin
    function has_config() {
      return false;
      //return true;
    }

    function hide_header() {
      return true;
    }
    
    function get_content() {
        global $CFG, $THEME, $COURSE;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content->text   = '';
            return $this->content;
        }

        $forums = get_records_select('forum', 'course = ' . $COURSE->id . ' AND type=\'general\'');
        $this->content->text  = '<div class="site_forum_block">';
        foreach ($forums as $forum) {
            $this->content->text .= '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.$forum->name.'</a><br/>';
        }
        $this->content->text .= '</div>';

        return $this->content;
    } 
    
    function _print_block() {
        global $USER;
        // make sure they are looged in or they cant see it
        if (isloggedin() && isset($USER) && $USER->username != 'guest') {
            return parent::_print_block();
        }
        else {
            return "";
        }
    }
    
    function specialisation() {
      //empty!
    }
    
  }
?>
