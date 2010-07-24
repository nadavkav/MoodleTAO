<?php // $Id$
//////////////////////////////////////////////////////////////
//  Progress Indicator filtering
//
//  This filter will replace tags with a visual activity progress 
//  indicator. 
//
//  To activate this filter, add a line like this to your
//  list of filters in your Filter configuration:
//
//  filter/progressindicator/filter.php
//
//////////////////////////////////////////////////////////////

/// This is the filtering function progressindicator_itself.  It accepts the
/// courseid and the text to be filtered (in HTML form).


function progressindicator_print_progress_indicator(){
    global $CFG;
    global $USER;

    // Create array of activities to print
    /*if(!$learning_paths = tao_get_learning_paths_by_role_of_user($USER->id, 'student')) {
        print '<p>You are not enrolled in any learning paths.</p>';
        return false;
    }*/
    //$learning_paths = tao_get_authored_learning_paths($USER->id);
    $learning_paths = get_my_courses($USER->id, 'visible DESC,sortorder ASC', '*', false, 21);
//print'<pre>'.print_r($learning_paths,1).'</pre>';
    $table_indicator_data = array();
    $table_indicator_cols=0;
    $table_data_count = 0;
    foreach($learning_paths as $lp){
        $table_data[$table_data_count]['id'] = $lp->id;
        $table_data[$table_data_count]['fullname'] = $lp->fullname;

        $sequence_array =  array();
        $sequence_sql = 'SELECT sequence FROM '.$CFG->prefix.'course_sections WHERE course='.$lp->id.' AND sequence <> \'\' ORDER BY section;';
        //$table_data[$table_data_count]['lp_mods'] = array();
        if( $sequences = get_records_sql($sequence_sql) ){
            foreach($sequences as $s){
                foreach (explode(',', $s->sequence) as $k => $v){
                   $sequence_array[] =  $v;
                }
            }
            foreach($sequence_array as $s) {
                //$sql = 'SELECT id, module, instance, score FROM '.$CFG->prefix.'course_modules WHERE m.course='.$lp->id.' AND id='.$s.' AND trackprogress=1 ORDER BY s.section;';
                $table_data[$table_data_count]['lp_mods'][] = (array)get_record('course_modules', 'course', $lp->id, 'id', $s, 'trackprogress', 1);
            }

            foreach($table_data[$table_data_count]['lp_mods'] as $key => $mod){
                if(!isset($mod['module'])) {
                    unset($table_data[$table_data_count]['lp_mods'][$key]);
                } else {
                    $table_data[$table_data_count]['lp_mods'][$key]['modname'] = get_field('modules', 'name', 'id', $mod['module']);
                    $table_data[$table_data_count]['lp_mods'][$key]['name'] = get_field($table_data[$table_data_count]['lp_mods'][$key]['modname'], 'name', 'id', $mod['instance']);
                    $table_data[$table_data_count]['lp_mods'][$key]['completed'] = progressindicator_get_completion($table_data[$table_data_count]['lp_mods'][$key]['modname'], $mod['id'], $USER->id );//($mod_type, $cmid, $imagedata, $userid, $passgrade=10)
                }
                $table_indicator_cols = max($table_indicator_cols, count($table_data[$table_data_count]['lp_mods']));
            }
        }
        ++$table_data_count;
    }
?>
    <table id="certification-table">
        <tr class="certification-table-header-row">
            <th class="certification-table-col0">Learning Path</th>
<?php
    for ($i=1; $i<=$table_indicator_cols; ++$i){
        print '<th>'.$i.'</th>';
    }
?>
        </tr>
<?php

    if (!empty($table_data)) {

        foreach($table_data as $row){
            print '<tr class="certification-table-data-row">';

            print '<td class="certification-table-col0"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$row['id'].'"><img src="'.$CFG->wwwroot.'/theme/intel/pix/progressindicators/arrow.gif" /> '.$row['fullname'].'</a></td>';
            for($i=0;$i<$table_indicator_cols;++$i){
                if(isset($row['lp_mods'][$i]['module'])){
                    $href  = $CFG->wwwroot.'/mod/'.get_field('modules', 'name', 'id', $row['lp_mods'][$i]['module']).'/view.php?id='.$row['lp_mods'][$i]['id'];
                    $data = '<a href='.$href.'><img src="'.$CFG->wwwroot.'/theme/intel/pix/progressindicators/check'.($row['lp_mods'][$i]['completed']?'_completed':'_not_completed').'.jpg" alt="'.$row['lp_mods'][$i]['name'].'" /></a>';
                } else {
                    $data = '&nbsp;';
                }
                print '<td class="certification-table-check">'.$data.'</td>';
            }
            print'</tr>';
        }
    } else {
        print '<tr class="certification-table-data-row"><td class="certification-table-col0">'. get_string('nolearningpaths', 'local') . '</td></tr>';
    }
?>
        </table>
<?php
}


function progressindicator_get_completion($mod_type, $cmid, $userid, $passgrade=10) {
    global $CFG; $USER;

    $inputsinvalid = false;
    $completed = false;
    switch ($mod_type) {
        case 'course':
            $sql = "SELECT c.id,c.fullname FROM {$CFG->prefix}role_assignments ra
                    JOIN {$CFG->prefix}context ctx
                      ON ctx.id=ra.contextid
                    JOIN {$CFG->prefix}course c
                      ON c.id=ctx.instanceid
                    WHERE ctx.instanceid = $cmid AND ra.userid=".$userid." AND ra.roleid=5";
            $rs = get_record_sql($sql);
            if (!empty($rs)) {
                $completed = true;
            }
        break;
        case 'feedback':
            $sql = "SELECT cm.id, f.name FROM {$CFG->prefix}feedback_completed fc
                    JOIN {$CFG->prefix}course_modules cm
                      ON cm.instance=fc.feedback
                    JOIN {$CFG->prefix}feedback f
                      ON f.id=cm.instance
                    WHERE cm.id = $cmid AND userid=$userid
                    ORDER BY fc.feedback";
            $rs = get_record_sql($sql);
            if (!empty($rs)) {
                $completed = true;
            }
        break;
        case 'quiz':
            $sql = "SELECT cm.id, g.grade, q.name FROM {$CFG->prefix}quiz q
                    JOIN {$CFG->prefix}quiz_grades g
                      ON q.id=g.quiz
                    JOIN {$CFG->prefix}course_modules cm
                      ON cm.instance=q.id
                    WHERE cm.id = $cmid AND g.userid=$userid";
            $rs = get_record_sql($sql);
            if (!empty($rs)) {
		        if(!isset($passgrade)){
		            error_log("completion indicator: Passgrade not set for quiz id: $cmid, inputtype: $mod_type");
                    $inputsinvalid = true;
	                continue;
	        	}

           		if ($rs->grade < $passgrade) {
                    $completed = false;
                } else {
                    $completed = true;
                }
            }
        break;
        case 'scorm':
            $sql = "SELECT cm.id, max(CAST(sst.value AS VARCHAR(3))) as value FROM {$CFG->prefix}scorm_scoes_track sst
                    JOIN {$CFG->prefix}course_modules cm ON cm.instance=sst.scormid
                    WHERE cm.id = $cmid AND sst.element='cmi.core.score.raw' AND sst.userid=$userid GROUP BY cm.id";
            $rs = get_record_sql($sql);
            if (!empty($rs)) {
	        	if(!isset($passgrade)){
                    error_log("completion indicator: Passgrade not set for scorm id: $u->id, inputtype: $mod_type");
	                $inputsinvalid = true;
	                continue;
	        	}
                if ($u->value >= $passgrade) {
                    $completed = true;
                } else {
                    $completed = false;
                }
            }
        break;
        case 'facetoface':
            $sql = "SELECT cm.id, s.grade, f.name FROM {$CFG->prefix}facetoface f
                    JOIN {$CFG->prefix}facetoface_submissions s
                      ON f.id=s.facetoface
                    JOIN {$CFG->prefix}course_modules cm
                      ON cm.instance=f.id
                    WHERE cm.id = $cmid AND s.userid=$userid";
            $rs = get_record_sql($sql);
            if (!empty($rs)) {
                if ( $u->grade == 100 ) {
                    $completed = true;
                } else {
                    $completed = false;
                }
            }
        break;
        case 'certificate':

            $sql = "SELECT cm.id, i.reportgrade, c.name FROM {$CFG->prefix}certificate c
                    JOIN {$CFG->prefix}certificate_issues i
                      ON c.id=i.certificateid
                    JOIN {$CFG->prefix}course_modules cm
                      ON cm.instance=c.id
                    WHERE cm.id = $cmid AND i.userid=$userid";
            $rs = get_record_sql($sql);

            if (!empty($rs)) {
                $completed = true;
            } else {
                $completed = false;
            }
        break;

    }
    return $completed;
}

?>
