<?php
	/**
	* 	Returns an item's title and text
	*/

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	
	// Bomb out if we haven't got itemId and memberId.
	if (( !isset($_REQUEST['itemId']) )) {
		exit(0);
	}

	// Quick DB query to find out if we've already reported this item
	// giving a cursory debounce.
	$itemQuery="select webcell_title,webcell_text,mb_firstname,mb_surmame
			from {$_SESSION['RealS_prefix']}items, {$_SESSION['RealS_prefix']}webcells, {$_SESSION['RealS_prefix']}members
			where item_webcell=webcell_id
				and webcell_member=mb_id
				and item_id=".mysql_escape_string($_GET['itemId']);
	$itemResult=mysql_query($itemQuery);
	if ( mysql_num_rows($itemResult) > 0 ) {
		$itemRow=mysql_fetch_assoc($itemResult);
		switch($_GET['context']) {
			case "edit":
				echo $itemRow['webcell_text'] . "--#--" . $itemRow['webcell_title'];
				break;

			default:
				echo "<h2 class=\"quote2\">Quote</h2>";
				echo "<div class=\"quote\" align=\"centre\">";
				echo $itemRow['webcell_text'];
				echo "</div><br />";
				break;
		}
	} else {
		echo "";
	}

?>