<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Saves a comment
	// Depd: This page is being called via AJAX
	// Retv: Returns all comments for this parent id (= page)
	//----------------------------------------------------------------------------------------------

	//ob_start();

	// Gimme libraries
	require_once("../../config.php");
	include_once('includes/rlsmart/header.php');
	require_once('userviews_class.php');
	require_once('classes/class_text_editor.php');
	require_once('classes/item/Comments.class.php');
	require_once('classes/class_comment_evidence_count.php');

	$mysql = new mysqlquery ( ) ;
	$userview = new userviews;
	$parent_data = $userview->findParent($_POST['unit_item_id'], $_POST['item_type']);

	if (strlen($_POST['share_id'])) {
		$userview->checkSharedRights($_POST['share_id']);
	} else {
		$userview->checkViewRights();
	}

	// Do comments
	$obj_comment = new rs_itemComments($mysql, $_POST['item_type_id'], $_POST['item_type'], $_POST['share_id'], $_POST['unit_item_id'], $_POST['comment_parent_item_id'], '/layouts/rafl_css.php');
	//$obj_comment->renderSupportingJavascript();

	// Process this motherfucker
	if ($_POST['action'] == 'insert') {
		$obj_comment->processCommentInsert();
	} elseif ($_POST['action'] == 'update') {
		$obj_comment->processCommentUpdate();
	}

	//ob_flush();
	//flush();

	echo $obj_comment->getCommentHtml($userview->shareComment);
?>