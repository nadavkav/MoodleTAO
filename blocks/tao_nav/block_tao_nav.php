<?php //$Id$

class block_tao_nav extends block_base {

    function init() {
        $this->title = get_string('title', 'block_tao_nav');
        $this->version = 2008112100;
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function specialization() {
        $this->title = get_string('displaytitle', 'block_tao_nav');
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = tao_print_static_nav(true);
        $this->content->footer = '';

        return $this->content;
    }

}
?>
