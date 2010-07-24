<?php //$Id$

class block_tao_legacy_lp extends block_base {

    function init() {
        $this->title = 'Legacy TAO LP';
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $COURSE;

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (!empty($COURSE->idnumber)) {
            $this->content->text = '<p>This Learning Path has been migrated from the Legacy TAO site: <a href="http://aoc.ssatrust.org.uk/index?s=4536&presid='.$COURSE->idnumber.'" target="_blank">View Legacy LP</a><br/><br/> <i>Make sure you are logged in to the legacy TAO site prior to clicking the above link</i></p>';
        }

        return $this->content;
    }
}
?>