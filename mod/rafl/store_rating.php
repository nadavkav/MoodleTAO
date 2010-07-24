<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Saves ratings value
	// Depd: Only used by {$_SESSION['RealS_prefix']}rafl.php viewer page and called by AJAX
	//----------------------------------------------------------------------------------------------

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	require_once("includes/get_sql_value_string.php");
	require_once('includes/common/KT_common.php');
	require_once('userviews_class.php');
	require_once('includes/tng/tNG.inc.php');
	
	// Make a transaction dispatcher instance
	$tNGs = new tNG_dispatcher("");
	
	// Make unified connection variable
	$conn_smart = new KT_connection($smart, $CFG->dbname);
	
	//start Trigger_LinkTransactions trigger
	//remove this line if you want to edit the code by hand 
	function Trigger_LinkTransactions(&$tNG) {
		global $ins_items;
	  $linkObj = new tNG_LinkedTrans($tNG, $ins_items);
	  $linkObj->setLink("item_webcell");
	  return $linkObj->Execute();
	}
	//end Trigger_LinkTransactions trigger
	
	//start Trigger_LinkTransactions1 trigger
	//remove this line if you want to edit the code by hand 
	function Trigger_LinkTransactions1(&$tNG) {
		global $ins_rafl_res;
	  $linkObj = new tNG_LinkedTrans($tNG, $ins_rafl_res);
	  $linkObj->setLink("rafl_res_item");
	  return $linkObj->Execute();
	}
	//end Trigger_LinkTransactions1 trigger
	$colname_rs_evidence = "-1";
	if (isset($_GET['parent_id'])) {
	  $colname_rs_evidence = $_GET['parent_id'];
	}
	
	// Gimme user id
	require_once ('userviews_class.php');
	$userview = new userviews();
	$user_id = $_SESSION['USER']->id;
	
	mysql_select_db($CFG->dbname, $smart);
	
	// Check whether this rating already exists
	// INNER JOIN share_cohort_members ON {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member = {$_SESSION['RealS_prefix']}webcells.webcell_member
	// AND {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share = 6107
	$query_rs_evidence = sprintf("SELECT {$_SESSION['RealS_prefix']}items.item_id,
		                      	{$_SESSION['RealS_prefix']}items.*,
		                      	{$_SESSION['RealS_prefix']}webcells.*,
		                      	{$_SESSION['RealS_prefix']}rafl_res.*
	                              FROM {$_SESSION['RealS_prefix']}items
	                              	INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
	                              	INNER JOIN {$_SESSION['RealS_prefix']}rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
	                              WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = %s
	                              	AND {$_SESSION['RealS_prefix']}items.item_default_type = 6
	                              	AND rafl_res_share = " . GetSQLValueString($_GET['share_id'], "int") . "
	                              	AND {$_SESSION['RealS_prefix']}webcells.webcell_member = %s", GetSQLValueString($colname_rs_evidence, "int"), GetSQLValueString($user_id, "int"));

	$rs_evidence = mysql_query($query_rs_evidence, $smart) or die(mysql_error());
	$row_rs_evidence = mysql_fetch_assoc($rs_evidence);
	$totalRows_rs_evidence = mysql_num_rows($rs_evidence);
	
	if ($totalRows_rs_evidence==0) {
		//insert New Evidence-----------------------------------
		
		// Make an insert transaction instance
		$ins_webcells = new tNG_insert($conn_smart);
		$tNGs->addTransaction($ins_webcells);
		// Register triggers
		$ins_webcells->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "parent_id");
		$ins_webcells->registerTrigger("AFTER", "Trigger_LinkTransactions", 98);
		$ins_webcells->registerTrigger("ERROR", "Trigger_LinkTransactions", 98);
		// Add columns
		$ins_webcells->setTable("{$_SESSION['RealS_prefix']}webcells");
		$ins_webcells->addColumn("webcell_title", "STRING_TYPE", "VALUE", "Evidence");
		$ins_webcells->addColumn("webcell_text", "STRING_TYPE", "VALUE", "");
		$ins_webcells->addColumn("webcell_member", "NUMERIC_TYPE", "VALUE", $user_id);
		$ins_webcells->addColumn("webcell_school", "NUMERIC_TYPE", "SESSION", "RealS_schoolid");
		$ins_webcells->setPrimaryKey("webcell_id", "NUMERIC_TYPE");
		
		// Make an insert transaction instance
		$ins_items = new tNG_insert($conn_smart);
		$tNGs->addTransaction($ins_items);
		// Register triggers
		$ins_items->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "parent_id");
		$ins_items->registerTrigger("AFTER", "Trigger_LinkTransactions1", 98);
		$ins_items->registerTrigger("ERROR", "Trigger_LinkTransactions1", 98);
		// Add columns
		$ins_items->setTable("{$_SESSION['RealS_prefix']}items");
		$ins_items->addColumn("item_webcell", "NUMERIC_TYPE", "POST", "item_webcell");
		$ins_items->addColumn("item_parent_item", "NUMERIC_TYPE", "GET", "parent_id", "{GET.parent_id}");
		$ins_items->addColumn("item_school", "NUMERIC_TYPE", "SESSION", "RealS_schoolid", "{SESSION.RealS_schoolid}");
		$ins_items->addColumn("item_default_type", "NUMERIC_TYPE", "VALUE", "6");
		$ins_items->addColumn("item_access", "NUMERIC_TYPE", "POST", "item_access", "0");
		$ins_items->setPrimaryKey("item_id", "NUMERIC_TYPE");
		
		// Make an insert transaction instance
		$ins_rafl_res = new tNG_insert($conn_smart);
		$tNGs->addTransaction($ins_rafl_res);
		// Register triggers
		$ins_rafl_res->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "parent_id");
		// Add columns
		$ins_rafl_res->setTable("{$_SESSION['RealS_prefix']}rafl_res");
		$ins_rafl_res->addColumn("rafl_res_item", "NUMERIC_TYPE", "POST", "rafl_res_item");
		$ins_rafl_res->addColumn("rafl_res_date", "DATE_TYPE", "VALUE", "{NOW_DT}");
		$ins_rafl_res->addColumn("rafl_res_rate", "NUMERIC_TYPE", "GET", "rating", "{GET.rating}");
		$ins_rafl_res->addColumn("rafl_res_share", "NUMERIC_TYPE", "GET", "share_id", "{GET.share_id}");
		$ins_rafl_res->setPrimaryKey("rafl_res_id", "NUMERIC_TYPE");
		
		$rating = $_GET['rating'];
		
	} else {
		// Update Evidence ----------------------------------
		// Make an update transaction instance
		$upd_rafl_res = new tNG_update($conn_smart);
		$tNGs->addTransaction($upd_rafl_res);
		// Register triggers
		$upd_rafl_res->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "parent_id");
		// Add columns
		$upd_rafl_res->setTable("{$_SESSION['RealS_prefix']}rafl_res");
		$upd_rafl_res->addColumn("rafl_res_date", "DATE_TYPE", "VALUE", "{NOW_DT}");
		$upd_rafl_res->addColumn("rafl_res_rate", "NUMERIC_TYPE", "GET", "rating", "{GET.rating}");
		$upd_rafl_res->setPrimaryKey("rafl_res_id", "NUMERIC_TYPE", "VALUE", $row_rs_evidence['rafl_res_id']);
		
		$rating = $_GET['rating'];
		
	} //end if evidence exists
	
	// Execute all the registered transactions
	$tNGs->executeTransactions();
	
	$idArray = explode("||",$_GET['taskArray']);
	
	include("count_rating.php");

	echo $rating."##".array_sum($doneArray)/count($doneArray)."##".implode("||",$doneArray)."##".$rateTotal."##".implode("||",$rateArray);
?>