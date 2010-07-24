<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 * @subpackage taoresource block
 * @date: 2008 10 30
 *
 */
     
  class block_taoresource extends block_base {
    
    function init() {
      $this->title = get_string('blockname', 'block_taoresource');
      //$this->cron = 1;
      $this->cron = 0;
      $this->version = 2008103001;
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
        global $CFG, $COURSE, $PAGE;
      
      //cache block contents
        if ($this->content !== NULL) {
            return $this->content;
        } //if
      
        $this->content = new stdClass;
      
        //fetch values if defined in admin, otherwise use defaults
        $label  = (!empty($CFG->block_search_text)) ? $CFG->block_search_text : get_string('searchresources', 'block_taoresource');
        $button = (!empty($CFG->block_search_button)) ? $CFG->block_search_button : get_string('go', 'block_taoresource');
    
        //basic search form
        $query = $CFG->wwwroot."/mod/taoresource/search.php?course={$COURSE->id}&section=block&type=file&add=taoresource&return=0";
        $this->content->text =
            '<form id="searchquery" method="get" action="'.$query.'"><div>'
          . '<label for="block_taoresource_q">'. $label .'</label>'
          . '<input id="block_taoresource_q" type="text" name="search" />'
          . '<input type="hidden" name="course" value="'.$COURSE->id.'" />'
          . '<input type="hidden" name="section" value="block" />'
          . '<input type="hidden" name="type" value="file" />'
          . '<input type="submit" value="'.$button.'" />'
          . '</div></form>'
          . '<a href="'.$query.'&search=*">'.get_string('browseresources', 'block_taoresource').'</a>';
          
        //no footer, thanks
        $this->content->footer = '';
        
        return $this->content;      
    } 
    
    function specialisation() {
      //empty!
    }
    
  }
?>