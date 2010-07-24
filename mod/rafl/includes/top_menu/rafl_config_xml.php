<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Rafl top menu
	// Depd: The $userview->checkSharedRights() method must run for menu includes to work

	// Auth: Daniel Dammann <dan@smartassess.com>
	//----------------------------------------------------------------------------------------------

	header('Content-Type: application/xml');

	// Init
	session_start();

	// Libraries
	require_once('../../Connections/smart.php');
	require_once('../get_sql_value_string.php');

	// Check viewing rights
	require_once ('../../userviews_class.php') ;
	$userview = new userviews();
	$parent_data = $userview->findParent($_GET['item_id'], "rafl") ;
	
	if (! isset($_GET['share_id']) || $_GET['share_id'] == "") {
		$userview->checkViewRights() ;
	} else {
		$userview->checkSharedRights($_GET['share_id']) ;
	}

	// Show menu
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<realsmart_rtool_menubar_config type="rafl">';
	echo '	<menu>';

/*
	// Views
	if ($userview->creator || $userview->shareMentor) {
		$query = "SELECT COUNT({$_SESSION['RealS_prefix']}item_view_status.item_id) AS unread_count
		          FROM {$_SESSION['RealS_prefix']}item_view_status
		          WHERE mb_id_viewer = " . $_SESSION['USER']->id . "
		          	AND share_id = " . GetSQLValueString($_GET['share_id'], "int") . "
		          HAVING COUNT({$_SESSION['RealS_prefix']}item_view_status.item_id) > 0";

		$mysql = new mysqlquery;
		$rows = $mysql->runsql($query);

		if ($rows) {
			$unreadCount = ' (' . $rows[0]['unread_count'] . ')';
		}

		if ($userview->isLearnerInThisShare($_SESSION['USER']->id, $_GET['share_id'])) {
			$viewsMenuItem[] = '	<option><optiontext>learner view</optiontext><optionurl>/rafl.php?item_id=' . $parent_data['item_id'] . '&amp;share_id=' . $_GET['share_id'] . '&amp;view=learner</optionurl></option>';
		}

		$viewsMenuItem[] = '		<option><optiontext>mentor view</optiontext><optionurl>/rafl.php?item_id=' . $parent_data['item_id'] . '&amp;share_id=' . $_GET['share_id'] . '&amp;view=mentor</optionurl></option>';

		// Hide, if only one
		if (count($viewsMenuItem) > 1) {
			echo '		<menuitem>';
			echo '			<header><title>views' . $unreadCount . '</title><url>none</url></header>';
			echo implode('', $viewsMenuItem);
			echo '		</menuitem>';
		}
	} else {
		// Hide, if only one
		//echo '		<menuitem>';
		//echo '			<header><title>views</title><url>none</url></header>';
		//echo '			<option><optiontext>learner view</optiontext><optionurl>/rafl.php?item_id=' . $parent_data['item_id'] . '&amp;share_id=' . $_GET['share_id'] . '</optionurl></option>';
		//echo '		</menuitem>';
	}
*/

	// Prepare help info
	$member_name = $userview->getMemberName($_SESSION['USER']->id);
	$member_name = str_replace('+', '%20', urlencode($member_name));
	$this_page = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_GET['this_url']);

	// Help
	echo '		<menuitem>';
	echo '			<header><title>help</title><url>none</url></header>';
	echo '			<option><optiontext>support website</optiontext><optionurl target="_blank">http://www.smartassess.com/support.php</optionurl></option>';
	echo '			<option><optiontext>report a bug</optiontext><optionurl>mailto:support@smartassess.com?subject=Report%20a%20bug&amp;body=Dear%20smartassess,%0A%0APlease%20allow%20me%20to%20report%20a%20bug%20on%20page%20' . $this_page . '%20...%0A%0AThanks%20very%20much,%0A%28' . $member_name . '%29</optionurl></option>';
	echo '			<option><optiontext>send a suggestion</optiontext><optionurl>mailto:support@smartassess.com?subject=Send%20a%20suggestion&amp;body=Dear%20smartassess,%0A%0APlease%20allow%20me%20to%20make%20a%20suggestion%20...%0A%0AThanks%20very%20much,%0A%28' . $member_name . '%29</optionurl></option>';
	echo '		</menuitem>';

	echo '	</menu>';
	echo '</realsmart_rtool_menubar_config>';
?>
