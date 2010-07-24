<?php //$Id$

class block_tao_group_activities extends block_list {
    function init() {
        $this->title = get_string('groupactivities', 'block_tao_group_activities');
        $this->version = 2009161700;
    }

    function get_content() {
        global $CFG, $COURSE, $USER;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        //cehck to see if this course is published:
        if ($COURSE->approval_status_id != COURSE_STATUS_PUBLISHED) {
            return '';
        }

        $groups = groups_get_all_groups($COURSE->id, $USER->id);
        if (empty($groups)) {
            $this->content->items[] = get_string('nogroupset', 'block_tao_group_activities');
            return $this->content;
        }

        if ($COURSE->id == $this->instance->pageid) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->instance->pageid);
        }

        if (empty($course)) {
            return '';
        }

        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $modfullnames = array();

        foreach($modinfo->cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $modfullnames[$cm->modname] = $cm->modplural;
        }

        asort($modfullnames, SORT_LOCALE_STRING);

        $validmods = $this->valid_modules();

        foreach ($modfullnames as $modname => $modfullname) {
            $target = '';
            if (in_array($modname, $validmods)) {
                if ($modname == 'chat') {
                    $target = ' target="_blank"';
                }
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/'.$modname.'/index.php?id='.$this->instance->pageid.'"' . $target . '>'.$modfullname.'</a>';
                $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$modname.'/icon.gif" class="icon" alt="" />';
            }
        }

        if (empty($this->content->items)) {
            $this->content->items[] = get_string('noactivites', 'block_tao_group_activities');
        }



        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false, 'admin' => false,
                     'tag' => false);
    }

    function valid_modules() {
        // define here the modules we want to show
        return array('forum', 'chat', 'wiki', 'taoresource');
    }
}

?>
