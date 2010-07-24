<?PHP //$Id$

class block_tao_course_status extends block_base {

    function init() {
        $this->title = get_string('coursestatus', 'block_tao_course_status');
        $this->version = 2008091900;
    }

    function specialization() {
    }

    function get_content() {
        global $CFG, $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

        if (!has_capability('moodle/local:viewcoursestatus', $context) && !has_capability('moodle/local:canselfassignheadeditor', $sitecontext)) {
            $this->content = NULL;
            return $this->content;
        }

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new stdClass;
        $this->content->text = tao_get_course_status_desc($COURSE);

        if (has_capability('moodle/local:canselfassignheadeditor', $sitecontext)) {
            // check if this needs an editor
            $editors = tao_get_headeditors($context);
            if (empty($editors)) {
                $this->content->text .= '<br /><br />' . get_string('needseditor', 'block_tao_course_status') . '<br />';
                $this->content->text .= '<a href="' . $CFG->wwwroot . '/local/lp/selfassignedit.php?id='.$COURSE->id.'">' . get_string('editlearningpath', 'block_tao_course_status') . '</a>';
            }
        }
        
        // footer
        $footer = "";
        if (has_capability('moodle/local:updatecoursestatus', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            $footer = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$COURSE->id.'&amp;action=changestatus">' . get_string('change', 'block_tao_course_status') . '...</a><br/>';
        }
        $footer .= '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$COURSE->id.'&amp;action=statushistory">' . get_string('history', 'block_tao_course_status') . '...</a><br/>';

        $this->content->footer = "<br/>$footer";

        return $this->content;
    }

    function preferred_width() {
        return 210;
    }

}

?>