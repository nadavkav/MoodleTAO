<?PHP //$Id$
// display the name and status of my /authored/ learning paths

class block_tao_authored_learning_paths extends block_base {

    function init() {
        $this->title = get_string('title', 'block_tao_authored_learning_paths');
        $this->version = 2008091900;
    }

    function specialization() {
    }

    function get_content() {
        global $USER, $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new stdClass;

        $courses = tao_get_authored_learning_paths($USER);

        $html = '<table id="authored_learning_paths" width="100%" border="0" cellspacing="0" cellpadding="0">';

        if (!empty($courses)) {

            foreach($courses as $course) {
                $html .= '<tr><td><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a></td><td align="right">'.$course->status.'</td></tr>';
            }

        } else {
            $html .= '<tr><td>'.get_string('nolearningpaths', 'block_tao_authored_learning_paths').'</td></tr>';
        }
    
        $html .= '</table>';
        $this->content->text = $html;
        $this->content->footer = '<br/><a href="/course/addlearningpath.php?category=' . $CFG->lpdefaultcategory . '">'.get_string('createnew', 'block_tao_authored_learning_paths').'...</a>';


        return $this->content;
    }

    function preferred_width() {
        return 210;
    }

}

?>