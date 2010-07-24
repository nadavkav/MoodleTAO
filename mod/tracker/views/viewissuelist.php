<?PHP

/**
* @package mod-tracker
* @category mod
* @author Clifford Thamm, Valery Fremaux > 1.8
* @date 02/12/2007
*
* Print Bug List
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from view.php in mod/tracker
}

include_once "$CFG->libdir/tablelib.php";

$STATUSKEYS = array(POSTED => get_string('posted', 'tracker'), 
                    OPEN => get_string('open', 'tracker'), 
                    RESOLVING => get_string('resolving', 'tracker'), 
                    WAITING => get_string('waiting', 'tracker'), 
                    RESOLVED => get_string('resolved', 'tracker'), 
                    ABANDONNED => get_string('abandonned', 'tracker'),
                    TRANSFERED => get_string('transfered', 'tracker'));

/// get search engine related information
// fields can come from a stored query,or from the current query in the user's client environement cookie
if (!isset($fields)){
    $fields = tracker_extractsearchcookies();
}
if (!empty($fields)){
    $searchqueries = tracker_constructsearchqueries($tracker->id, $fields);
}

$limit = 20;
$page = optional_param('page', 1, PARAM_INT);

if ($page <= 0){
    $page = 1;
}

if (isset($searchqueries)){
    /* SEARCH DEBUG 
    $strsql = str_replace("\n", "<br/>", $searchqueries->count);
    $strsql = str_replace("\t", "&nbsp;&nbsp;&nbsp;", $strsql);
    echo "<div align=\"left\"> <b>count using :</b> ".$strsql." <br/>";
    $strsql = str_replace("\n", "<br/>", $searchqueries->search);
    $strsql = str_replace("\t", "&nbsp;&nbsp;&nbsp;", $strsql);
    echo " <b>search using :</b> ".str_replace("\n", "<br/>", $strsql)." <br/></div>";
    */
    $sql = $searchqueries->search;
    $numrecords = count_records_sql($searchqueries->count);
} else {
    $sql = "
        SELECT 
            i.id, i.summary, i.datereported, i.reportedby, i.assignedto, i.status,
            u.firstname AS firstname, 
            u.lastname AS lastname,
            COUNT(ic.issueid) as watches
        FROM 
            {$CFG->prefix}user as u,
            {$CFG->prefix}tracker_issue as i
        LEFT JOIN
            {$CFG->prefix}tracker_issuecc as ic 
        ON
            ic.issueid = i.id
        WHERE 
            i.reportedby = u.id AND 
            i.trackerid = {$tracker->id}
        GROUP BY 
            i.id, i.summary, i.datereported, i.reportedby, i.assignedto, i.status, u.firstname, u.lastname
    ";

    $sqlcount = "
        SELECT 
            COUNT(*)
        FROM 
            {$CFG->prefix}tracker_issue as i, 
            {$CFG->prefix}user as u
        WHERE 
            i.reportedby = u.id AND 
            i.trackerid = {$tracker->id}
        GROUP BY
            i.id
    ";
    $numrecords = count_records_sql($sqlcount);
}



/// display list of issues
?>
<center>
<table border="1" width="100%">
<?php
if (isset($searchqueries)){
?>
    <tr>
        <td colspan="2">
            <?php print_string('searchresults', 'tracker') ?>: <?php echo $numrecords ?> <br/>
        </td>
        <td colspan="2" align="right">
                <a href="view.php?id=<?php p($cm->id) ?>&amp;what=clearsearch"><?php print_string('clearsearch', 'tracker') ?></a>
        </td>
    </tr>
<?php
}
?>      
</table>
</center>
<form name="manageform" action="view.php" method="post">
<input type="hidden" name="id" value="<?php p($cm->id) ?>" />
<input type="hidden" name="what" value="updatelist" />
<?php       

/// define table object
$issuenumberstr = get_string('issuenumber', 'tracker');
$summarystr = get_string('summary', 'tracker');
$datereportedstr = get_string('datereported', 'tracker');
$reportedbystr = get_string('reportedby', 'tracker');
$assignedtostr = get_string('assignedto', 'tracker');
$statusstr = get_string('status', 'tracker');
$watchesstr = get_string('watches', 'tracker');
$actionstr = '';
if(!empty($tracker->parent)){
    $transferstr = get_string('transfer', 'tracker');
    $tablecolumns = array('id', 'summary', 'datereported', 'reportedby', 'assignedto', 'status', 'watches', 'transfered', 'action');
    $tableheaders = array("<b>$issuenumberstr</b>", "<b>$summarystr</b>", "<b>$datereportedstr</b>", "<b>$reportedbystr</b>", "<b>$assignedtostr</b>", "<b>$statusstr</b>", "<b>$watchesstr</b>", "<b>$transferstr</b>", "<b>$actionstr</b>");
} else {
    $tablecolumns = array('id', 'summary', 'datereported', 'reportedby', 'assignedto', 'status', 'watches', 'action');
    $tableheaders = array("<b>$issuenumberstr</b>", "<b>$summarystr</b>", "<b>$datereportedstr</b>", "<b>$reportedbystr</b>", "<b>$assignedtostr</b>", "<b>$statusstr</b>", "<b>$watchesstr</b>", "<b>$actionstr</b>");
}

$table = new flexible_table('mod-tracker-issuelist');
$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);

$table->define_baseurl($CFG->wwwroot.'/mod/tracker/view.php?id='.$cm->id);

$table->sortable(true, 'datereported', SORT_DESC); //sorted by datereported by default
$table->collapsible(true);
$table->initialbars(true);

// allow column hiding
// $table->column_suppress('reportedby');
// $table->column_suppress('watches');

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'issues');
$table->set_attribute('class', 'issuelist');
$table->set_attribute('width', '100%');

$table->column_class('id', 'list_issuenumber');
$table->column_class('summary', 'list_summary');
$table->column_class('datereported', 'timelabel');
$table->column_class('reportedby', 'list_reportedby');
$table->column_class('assignedto', 'list_assignedto');
$table->column_class('watches', 'list_watches');
$table->column_class('status', 'list_status');
$table->column_class('action', 'list_action');
if (!empty($tracker->parent)){
    $table->column_class('transfered', 'list_transfered');
}

$table->setup();


/// set list length limits
/*
if ($limit > $numrecords){
    $offset = 0;
}
else{
    $offset = $limit * ($page - 1);
}
$sql = $sql . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
*/

/// get extra query parameters from flexible_table behaviour
$where = $table->get_sql_where();
$sort = $table->get_sql_sort();
$table->pagesize($limit, $numrecords);

if (!empty($sort)){
    $sql .= " ORDER BY $sort";
}

$issues = get_records_sql($sql, $table->get_page_start(), $table->get_page_size());

if (!empty($issues)){
    /// product data for table
    foreach ($issues as $issue){
        $issuenumber = "<a href=\"view.php?id={$cm->id}&amp;issueid={$issue->id}\">{$tracker->ticketprefix}{$issue->id}</a>";
        $summary = "<a href=\"view.php?id={$cm->id}&amp;view=view&amp;page=viewanissue&amp;issueid={$issue->id}\">".format_string($issue->summary).'</a>';
        $datereported = date('Y/m/d h:i', $issue->datereported);
        $user = get_record('user', 'id', $issue->reportedby);
        $reportedby = fullname($user);
        $user = get_record('user', 'id', $issue->assignedto);
        if (has_capability('mod/tracker:manage', $context)){ // managers can assign bugs
            $status = choose_from_menu($STATUSKEYS, "status{$issue->id}", $issue->status, '', "document.forms['manageform'].schanged{$issue->id}.value = 1;", '', true). "<input type=\"hidden\" name=\"schanged{$issue->id}\" value=\"0\" />";
            $developers = tracker_getdevelopers($context);
            foreach($developers as $developer){
                $developersmenu[$developer->id] = fullname($developer);
            }
            $assignedto = choose_from_menu($developersmenu, "assignedto{$issue->id}", $issue->assignedto, get_string('unassigned', 'tracker'), "document.forms['manageform'].changed{$issue->id}.value = 1;", '', true) . "<input type=\"hidden\" name=\"changed{$issue->id}\" value=\"0\" />";
        } elseif (has_capability('mod/tracker:resolve', $context)){ // resolvers can give a bug back to managers
            $status = choose_from_menu($STATUSKEYS, "status{$issue->id}", $issue->status, '', "document.forms['manageform'].schanged{$issue->id}.value = 1;", '', true) . "<input type=\"hidden\" name=\"schanged{$issue->id}\" value=\"0\" />";
            $managers = tracker_getadministrators($context);
            foreach($managers as $manager){
                $managersmenu[$manager->id] = fullname($manager);
            }
            $managersmenu[$USER->id] = fullname($USER);
            $assignedto = choose_from_menu($developersmenu, "assignedto{$issue->id}", $issue->assignedto, get_string('unassigned', 'tracker'), "document.forms['manageform'].changed{$issue->id}.value = 1;", '', true) . "<input type=\"hidden\" name=\"changed{$issue->id}\" value=\"0\" />";
        } else {
            $status = $STATUSKEYS[0 + $issue->status]; 
            $assignedto = fullname($user);
        }
        $status = '<div class="status_'.$STATUSCODES[$issue->status].'" style="width: 110%; height: 105%; text-align:center">'.$status.'</div>';
        $hassolution = $issue->status == RESOLVED && !empty($issue->resolution);
        $solution = ($hassolution) ? "<img src=\"{$CFG->wwwroot}/mod/tracker/pix/solution.gif\" height=\"15\" alt=\"".get_string('hassolution','tracker')."\" />" : '' ;
        $actions = '';
        if (has_capability('mod/tracker:manage', $context) || has_capability('mod/tracker:resolve', $context)){
            $actions = "<a href=\"view.php?id={$cm->id}&amp;issueid={$issue->id}&page=editanissue\" title=\"".get_string('update')."\" ><img src=\"{$CFG->pixpath}/t/edit.gif\" border=\"0\" /></a>";
        }
        if (has_capability('mod/tracker:manage', $context)){
            $actions .= "&nbsp;<a href=\"view.php?id={$cm->id}&amp;issueid={$issue->id}&what=delete\" title=\"".get_string('delete')."\" ><img src=\"{$CFG->pixpath}/t/delete.gif\" border=\"0\" /></a>";
        }
        $actions .= "&nbsp;<a href=\"view.php?id={$cm->id}&amp;view=profile&amp;page=mywatches&amp;issueid={$issue->id}&what=register\" title=\"".get_string('register', 'tracker')."\" ><img src=\"{$CFG->wwwroot}/mod/tracker/pix/register.gif\" border=\"0\" /></a>";
        if (!empty($tracker->parent)) {
            $transfer = ($issue->status == TRANSFERED) ? tracker_print_transfer_link($tracker, $issue) : '' ;
            $dataset = array($issuenumber, $summary.' '.$solution, $datereported, $reportedby, $assignedto, $status, 0 + $issue->watches, $transfer, $actions);
        } else {
            $dataset = array($issuenumber, $summary.' '.$solution, $datereported, $reportedby, $assignedto, $status, 0 + $issue->watches, $actions);
        }
        $table->add_data($dataset);     
    }
    $table->print_html();
} else {
?>
<table border="1">
    <tr>
        <td colspan="5" align="center">
            <?php print_string('noissuesreported', 'tracker'); ?>
        </td>
    </tr>
</table>
<?php
}

if (has_capability('mod/tracker:manage', $context) || has_capability('mod/tracker:resolve', $context)){
?>
<center>
    <p><input type="submit" name="go_btn" value="<?php print_string('savechanges') ?>" /></p>
</center>
</form>
<?php

$nohtmleditorneeded = true;
}
?>
