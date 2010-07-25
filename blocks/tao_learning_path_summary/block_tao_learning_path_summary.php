<?php //$Id$

// generates a menu list of child pages ("stations") for a learning path course

class block_tao_learning_path_summary extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_tao_learning_path_summary') ;
        $this->version = 2008090909;
    }

    function get_content() {
        global $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($COURSE->format != 'learning') {
            $this->content = '';
            return $this->content;
        }

        $filteropt = new stdClass;
        $filteropt->noclean = true;

        $this->content = new stdClass;

        $this->content->text = $COURSE->summary . '<br/>';
        $this->content->text .= $this->display_stations();
        $this->content->footer = '';

        return $this->content;
    }

    function display_stations() {
        global $CFG, $USER;

        $courseid = $this->instance->pageid;
        $pages = tao_get_learning_path_stations($courseid);
        $stations = '';

        if (tao_is_my_learning_path($courseid)) {
            // get list of viewed pages
            $viewed = tao_get_viewed_learning_path_stations($courseid, $USER->id);
        } else { 
            // just say we've viewed none
            $viewed = array();
        }

        foreach ($pages as $page) {

            // have we viewed this one?
            if (in_array($page->id, $viewed) ) {
                $icon = "tick";
            } else {
                $icon = "caret_single_rtl"; // (nadavkav)
            }

            $stations .= '<p><img border="0" align="right" src="' . $CFG->wwwroot . '/theme/intel/pix/path/' . $icon . '.gif"/><a href="';
			$stations .= $CFG->wwwroot.'/course/view.php?id='.$courseid.'&page='.$page->id.'" class="library"><span class="stations">';
			$stations .= format_string($page->nameone).'</span></a></p>'; // (nadavkav)
        }

        $html = '<h2>' . get_string('learningstations', 'block_tao_learning_path_summary') . '</h2>';

        $html .= '
<table cellspacing="0" cellpadding="0" border="0" width="100%"> <tbody> <!-- (nadavkav)-->
  <tr>
    <td width="10"> <br />
    </td>
    <td width="100%">
      <p style="text-align: center;">
        <table cellspacing="0" cellpadding="0" border="0" id="lp-stations" width="100%"> <tbody> <!-- (nadavkav)-->
          <tr>
		  <td class="lp-stations-list" valign="top">' .
               $stations .
            '</td>
            <td class="lp-stations-image" valign="top"> <img border="0" align="left" src="'.$CFG->wwwroot.'/theme/intel/pix/path/stufe01.gif" /><br />
            </td>
            
          </tr> </tbody>
        </table> </p>
    </td>
  </tr></tbody>
</table>
        ';

        return $html;
    }

    function applicable_formats() {
        return array('course' => true);
    }

}
?>
