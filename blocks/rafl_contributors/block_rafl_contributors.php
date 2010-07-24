<?PHP //$Id$

class block_rafl_contributors extends block_base {

    function init() {
        $this->title = get_string('lpcontributors', 'local');
        $this->version = 2009061600;
    }

    function specialization() {
    }

    function get_content() {
        global $CFG, $COURSE, $USER;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);


        // first check whether rafl mode is valid
        if (!$CFG->raflmodeenabled) {
            $this->content = NULL;
            return $this->content;
        }
        if ($COURSE->learning_path_mode != LEARNING_PATH_MODE_RAFL) {
            $this->content = NULL;
            return $this->content;
        }

        if (!has_capability('moodle/local:viewlpcontributors', $context)) {
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

        // display the author of the course
        $users = tao_get_lpauthors($context);

        $this->content->text = '<b>' . get_string('lpleader', 'local') . ': </b>';
        $this->content->text .= '<ul>';
            foreach($users as $user) {
                $this->content->text .= '<li><a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</a>';
            } 
        $this->content->text .= '</ul>';

        // display a list of contributors to the course
        $users = tao_get_lpcontributors($context);

        $this->content->text .= '<b>' . get_string('lpcontributors', 'local') . ': </b>'; 

        if (!empty($users)) {
            $this->content->text .= '<ul>';
            foreach($users as $user) {
                $this->content->text .= '<li><a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</a>';
            } 
            $this->content->text .= '</ul>';
        } else {
            $this->content->text .= get_string('nocontributors', 'local');
        }
        
        // footer
        $this->content->footer = '';
        // if has capability then provide link to manage the list
        if (has_capability('moodle/local:managelpcontributors', $context)) {
            $roleid = get_field('role', 'id', 'shortname', ROLE_LPCONTRIBUTOR);
            $link = $CFG->wwwroot . '/admin/roles/assign.php?contextid=' . $context->id . '&roleid=' . $roleid; 
            $this->content->footer .= '<br/><a href="' . $link . '">' . get_string('managelpcontributors', 'local') . '</a><br />';
        }


        return $this->content;
    }

    function preferred_width() {
        return 210;
    }

}

?>
