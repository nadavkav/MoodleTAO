<?php

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
 * @package   blocks-tao-certification_path
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_tao_certification_path extends block_base {

    function init() {
        $this->title = get_string('certification', 'block_tao_certification_path');
        $this->version = 2008111204;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $COURSE,$CFG,$USER;
        
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        if (!has_capability('moodle/local:viewcertificationblock', $context)) {
            $this->content = NULL;
            return $this->content;
        }
        
        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';
        if (isloggedin()) {
            $hascerts =  tao_display_certifications($USER->id);
            $showrequestlink = $hascerts->notpt;
            $linkedactivities = tao_certificate_get_certification_tasks($USER->id, $COURSE->id, $showrequestlink);
            if (!empty($linkedactivities[$COURSE->id])) {
                $this->content->text .= $linkedactivities[$COURSE->id];
            }
            $this->content->text .= $hascerts->text;
        }
        if (empty($this->content->text)) {
            $this->content->text = get_string('nothingtodisplay');
        }
        return $this->content;
    }
}
?>