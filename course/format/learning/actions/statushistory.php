<?php
    // reports the status history of the given course 
    require_once("$CFG->dirroot/config.php");
    require_once("$CFG->dirroot/course/lib.php");

    global $COURSE;
    require_capability('moodle/local:viewcoursestatus', get_context_instance(CONTEXT_COURSE, $COURSE->id));


    $sql = "SELECT h.id, s.description, h.timestamp, h.reason, u.username
             FROM {$CFG->prefix}course_status_history h, {$CFG->prefix}course_approval_status s, {$CFG->prefix}user u
            WHERE h.courseid = {$course->id}
              AND h.approval_status_id = s.id
              AND u.id = h.userid
            ORDER by timestamp desc";

    $table = new stdClass();

    if ($records=get_records_sql($sql)) {

        $table->head = array('&nbsp;', 'Status');
        $table->align = array('center', 'left');
        $table->wrap = array('nowrap', 'nowrap');
        $table->width = '100%';
        $table->size = array(10, '*');

        $table->head[]= get_string('time'); 
        $table->align[] = 'center';
        $table->wrap[] = 'nowrap';
        $table->size[] = '*';

        $table->head[]= get_string('user');
        $table->align[] = 'center';
        $table->wrap[] = 'nowrap';
        $table->size[] = '*';

        $table->head[]= get_string('reason', 'local');;
        $table->align[] = 'left';
        $table->wrap[] = 'nowrap';
        $table->size[] = '*';

        foreach ($records as $record) {

            $row = array();

            $row[] = ''; //TODO not sure why there's a column first
            $row[] = $record->description;
            $row[] = userdate($record->timestamp);
            $row[] = $record->username;
            $row[] = $record->reason;

            $table->data[] = $row;

        }
    }

    print_heading(get_string('historyheading', 'local'));
    print_table($table);

?>
