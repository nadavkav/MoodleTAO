<?php
	/**
	* 	Update rating within the database of an item.
	* 	Accepts the following GET vars:
	* 		itemId		Integer of item_id
	* 		memberId	Member ID making the request (used to control dupes)
	* 	Returns a single integer which is the new rating count.
	*/

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	
	// Bomb out if we haven't got itemId and memberId.
	if (( !isset($_REQUEST['memberId']) ) || ( !isset($_REQUEST['itemId']) )) {
		exit(0);
	}

	// Quick DB query to find out if we've already rated this item
	$itemQuery="select count(*) as ratingCount
			from {$_SESSION['RealS_prefix']}item_rating
			where ir_memberid=".mysql_escape_string($_GET['memberId'])."
				and ir_itemid=".mysql_escape_string($_GET['itemId']);

	$itemResult=mysql_query($itemQuery);
	$itemRow=mysql_fetch_assoc($itemResult);
	if ( $itemRow['ratingCount'] == 0 ) {
		// This person hasn't rated, so allow them
		// We got this far, so insert a new rating
		$addQuery="insert into {$_SESSION['RealS_prefix']}item_rating set
				ir_memberid=".mysql_escape_string($_GET['memberId']).",
				ir_itemid=".mysql_escape_string($_GET['itemId']);
		$addResult=mysql_query($addQuery);
	}

	// Now pick up the new rating
	$itemQuery="select count(*) as ratingCount
			from {$_SESSION['RealS_prefix']}item_rating
			where ir_itemid=".mysql_escape_string($_GET['itemId']);

	$itemResult=mysql_query($itemQuery);
	$itemRow=mysql_fetch_assoc($itemResult);

	// Return the rating count back.
	echo $itemRow['ratingCount'];
?>