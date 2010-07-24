<?PHP

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   blocks-lp_brief
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class block_tao_lp_brief extends block_base {
    function init() {
        $this->title = get_string('blocktitle', 'block_tao_lp_brief');
        $this->version = 2009060400;
    }

    function get_content() {
        global $CFG, $COURSE;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new object();
        $options = new object();
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($COURSE->summary, FORMAT_HTML, $options);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (has_capability('block/tao_lp_brief:editlpsummary', $coursecontext)) {
            $editpage = $CFG->wwwroot.'/blocks/tao_lp_brief/edit.php?id='.$COURSE->id;
            $this->content->text .= "<div class=\"editbutton\"><a href=\"$editpage\"><img src=\"$CFG->pixpath/t/edit.gif\" alt=\"".get_string('edit')."\" /></a></div>";
        }
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {
        return true;
    }

    function preferred_width() {
        return 210;
    }

}

?>