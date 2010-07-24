<?php 
	//----------------------------------------------------------------------------------------------
	// Desc: Counts rating values
	// Depd: Used by rafl.php, store_evidence.php & store_rating.php
	//----------------------------------------------------------------------------------------------
	
	// Gimme libraries
	//require_once("../../config.php");
	//require_once('includes/rlsmart/header.php');
	//require_once('userviews_class.php');

        // WORKAROUND: For the moodle module to work
        if (strlen($_GET['id'])) {
            $_GET['share_id'] = $_GET['id'];
        }

        // WORKAROUND: To avoid PHP warnings
        $_GET['mb_id'] = '';
        $_GET['view'] = 'learner';
	
	// Gimme user id
        //$mysql = new mysqlquery ( ) ;
	$userViewCountRating = new userviews();
	$user_id = $userViewCountRating->getMemberId($_SESSION['USER']->id, $_GET['mb_id'], $_GET['view']);
	
	$rateArray = array();
	$doneArray = array();
	
	foreach ($idArray as $value) {
		mysql_select_db($CFG->dbname, $smart);
		
		// Gimme each success criteria
		$query_rs_success = "SELECT {$_SESSION['RealS_prefix']}webcells.*, {$_SESSION['RealS_prefix']}items.item_id, {$_SESSION['RealS_prefix']}rafl.*
		                     FROM {$_SESSION['RealS_prefix']}rafl
		                        INNER JOIN {$_SESSION['RealS_prefix']}items ON {$_SESSION['RealS_prefix']}rafl.rafl_item = {$_SESSION['RealS_prefix']}items.item_id
		                        INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
		                     WHERE item_parent_item = ". $value ."
		                     ORDER BY rafl_order ASC";

		$rs_success = mysql_query($query_rs_success, $smart) or die(mysql_error());
		$row_rs_success = mysql_fetch_assoc($rs_success);
		$totalRows_rs_success = mysql_num_rows($rs_success);
		
		$rateTotal = 0;
		$ratecount = 0;
		$done = 0;
		$doneTotal = 0;
		if ($totalRows_rs_success>0) {
			do {
				mysql_select_db($CFG->dbname, $smart);  
	
				//$query_rs_result = "SELECT {$_SESSION['RealS_prefix']}items.item_id, {$_SESSION['RealS_prefix']}items.*,{$_SESSION['RealS_prefix']}webcells.*,{$_SESSION['RealS_prefix']}rafl_res.*
				//                    FROM items
				//                    INNER JOIN webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
				//                    INNER JOIN rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
				//                    WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = ". $row_rs_success['item_id'] ."
				//                    AND {$_SESSION['RealS_prefix']}items.item_default_type = 6
				//                    AND {$_SESSION['RealS_prefix']}rafl_res.rafl_res_rate >= 2
				//                    AND webcell_member = " . $_SESSION['USER']->id;
	
				// Gimme the rating for each success criteria in this share
				// INNER JOIN share_cohort_members ON {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member = {$_SESSION['RealS_prefix']}webcells.webcell_member
				// AND {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share = " . GetSQLValueString($_GET['share_id'], 'int') . "
				$query_rs_result = "SELECT
				                      {$_SESSION['RealS_prefix']}rafl_res.rafl_res_rate
				                    FROM {$_SESSION['RealS_prefix']}items
				                      INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
				                      INNER JOIN {$_SESSION['RealS_prefix']}rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
				                    WHERE {$_SESSION['RealS_prefix']}items.item_default_type = 6
				                      AND {$_SESSION['RealS_prefix']}rafl_res.rafl_res_rate >= 2
				                      AND {$_SESSION['RealS_prefix']}items.item_parent_item = " . $row_rs_success['item_id'] . "
				                      AND rafl_res_share = " . $_GET['share_id'] . "
				                      AND {$_SESSION['RealS_prefix']}webcells.webcell_member = " . $_SESSION['USER']->id;

				$rs_result = mysql_query($query_rs_result, $smart) or die(mysql_error());
				$row_rs_result = mysql_fetch_assoc($rs_result);
				$totalRows_rs_result = mysql_num_rows($rs_result);
				$ratecount = $ratecount + $totalRows_rs_result;
				if ($totalRows_rs_result>0) {
					$rateTotal = $rateTotal + $row_rs_result['rafl_res_rate'];
					$done = $done + 1;
				}
				mysql_free_result($rs_result);
		
			} while ($row_rs_success = mysql_fetch_assoc($rs_success));
			$doneTotal = $done/$totalRows_rs_success*100;
		}
		if ($ratecount>0) {
			$rate = ($rateTotal)/$ratecount;
		} else {
			$rate = 0;
		}
		array_push($rateArray,$rate);
		array_push($doneArray,$doneTotal);
		mysql_free_result($rs_success);
	}
	$rateTotal = 0;
	$ratecount = 0;
	foreach ($rateArray as $value) {
		if ($value>1) {
			$rateTotal = $rateTotal + $value;
			$ratecount = $ratecount + 1;
		}
	}
	if ($ratecount>0) { $rateTotal = $rateTotal / $ratecount;}	
	//print_r($rateArray);
?>