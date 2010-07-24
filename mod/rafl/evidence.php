<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Returns evidence text
	// Depd: Used by {$_SESSION['RealS_prefix']}rafl.php, rafl_cohort.php, rafl_overview2.php and called by AJAX
	//----------------------------------------------------------------------------------------------

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	require_once("includes/get_sql_value_string.php");
	require_once('includes/common/KT_common.php');
	require_once('includes/tng/tNG.inc.php');
	require_once('userviews_class.php');
	require_once("classes/class_comment_evidence_count.php");
	
	$colname_rs_evidence2 = "-1";
	
	// The current user views their own evidence
	if (isset($_SESSION['USER']->id)) {
	  $colname_rs_evidence2= $_SESSION['USER']->id;
	}
	
	// The mentor views some learners evidence
	if (isset($_GET['mb_id'])) {
		$colname_rs_evidence2 = $_GET['mb_id'];
	}
	
	mysql_select_db($CFG->dbname, $smart);
	
	// AND webcell_member = " . GetSQLValueString($colname_rs_evidence2, "int") . "
	$sql = "SELECT
	          {$_SESSION['RealS_prefix']}items.item_id,
		  {$_SESSION['RealS_prefix']}items.*,
	          {$_SESSION['RealS_prefix']}webcells.*
	        FROM {$_SESSION['RealS_prefix']}items
	          INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
	          INNER JOIN {$_SESSION['RealS_prefix']}rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
	        WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . GetSQLValueString($_GET['success_id'], "int") . "
	          AND {$_SESSION['RealS_prefix']}items.item_default_type = 6
	          AND rafl_res_share = " . GetSQLValueString($_GET['share_id'], "int");
	
	$rs_evidence = mysql_query($sql, $smart) or die(mysql_error());
	$row_rs_evidence = mysql_fetch_assoc($rs_evidence);
	$totalRows_rs_evidence = mysql_num_rows($rs_evidence);

        $_GET['view'] = 'learner';
	
	// The evidence is getting viewed now on this page, so reset the "unviewed" counter
	if ($_GET['view'] == 'mentor') {
		$obj_count = new commentEvidenceCount();
		$obj_count->resetEvidenceCounter($row_rs_evidence['item_id']);
	}
	
	echo $row_rs_evidence['webcell_text'];
	
	mysql_free_result($rs_evidence);
?>
