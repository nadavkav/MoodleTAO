<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Gets a mentor's comment based on a success criteria
	// Depd: This page is an AJAX snipplet
	//----------------------------------------------------------------------------------------------

	// Gimme libraries
	require_once("../../config.php");
	include_once('includes/rlsmart/header.php');
	require_once('userviews_class.php');
	require_once('classes/item/Comments.class.php');
	require_once('classes/class_text_editor.php');

	$userview = new userviews;

	$parent_data = $userview->findParent($_GET['item_id'], "rafl");

	if (strlen($_GET['share_id'])) {
		$userview->checkSharedRights($_GET['share_id']);
	} else {
		$userview->checkViewRights();
	}

	$mysql = new mysqlquery;

	// Gimme the evidence item id
	$sql = "SELECT {$_SESSION['RealS_prefix']}items.item_id AS evidence_item_id
	        FROM {$_SESSION['RealS_prefix']}items
	        	INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
	        WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . $mysql->escape_value($_GET['success_id'], "int") . "
	        	AND {$_SESSION['RealS_prefix']}items.item_default_type = 6";

	$row = $mysql->getrow($sql);

  	// Gimme the mentor's comments on the evidence
	$obj_comment = new rs_itemComments($mysql, 1, 'rafl', $_GET['share_id'], $parent_data['item_id'], $row['evidence_item_id'], 'layouts/rafl_css.php');
	//$obj_comment->renderSupportingJavascript();
	echo $obj_comment->getCommentHtml($userview->shareComment);
?>