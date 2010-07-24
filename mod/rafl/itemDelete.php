<?php
	/**
	* 	Delete an item.
	* 	Accepts the following GET vars:
	* 		itemId		Integer of item_id
	* 	Returns 'item deleted' on success.
	*/

	// Start the session, because we'll need the user ID
	//session_start();

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	require_once("classes/class_comment_evidence_count.php");
	require_once('userviews_class.php');
	
	// Bomb out if we haven't got itemId
	if (( !isset($_GET['itemId']) )) {
		exit(0);
	}
	
	// Quick DB query to find out if we've already reported this item
	// giving a cursory debounce.
	$itemQuery="select webcell_text,mb_firstname,mb_surmame,item_id
			from {$_SESSION['RealS_prefix']}items, {$_SESSION['RealS_prefix']}webcells, {$_SESSION['RealS_prefix']}members
			where item_webcell=webcell_id
				and webcell_member=mb_id
				and item_id=".mysql_escape_string($_GET['itemId'])."
				and mb_id=".mysql_escape_string($_SESSION['USER']->id);

	$itemResult=mysql_query($itemQuery);
	
	if ( mysql_num_rows($itemResult) > 0 ) {
		$itemRow=mysql_fetch_assoc($itemResult);
		$deleteResult=mysql_query("delete from {$_SESSION['RealS_prefix']}items
						where item_id=".$itemRow['item_id']);

		// Delete the notification counter
		$obj_count = new commentEvidenceCount();
		$obj_count->resetCommentCounterForAllViewers($_GET['share_id']);
	
		echo "Item deleted";
	} else {
		echo "";
	}
?>