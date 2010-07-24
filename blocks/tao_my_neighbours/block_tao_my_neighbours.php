<?php //$Id$

class block_tao_my_neighbours extends block_base {

    function init() {
        $this->title = get_string('myneighbours', 'block_tao_my_neighbours');
        $this->version = 2009062300;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $users = tao_get_similar_users($USER, 6);           

        //print_object($users); 

        if (!empty($users)) {

            $this->content->text .= get_string('myneighboursdesc', 'block_tao_my_neighbours') . ':';

            $this->content->text .= '<br/><br/>';

            foreach ($users as $userref) {
                $user = get_record('user', 'id', $userref->id);
                $this->content->text .= tao_print_neighbour_box($user, true);
            }

            $this->content->text .= '<div class="clearer"></div>';


        } else {
            $this->content->text .= get_string('noneighbours', 'block_tao_my_neighbours');
        }

        $this->content->footer = '';

        return $this->content;
    }

}
?>