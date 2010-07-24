<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Share top menu
	// Depd: -
	// Auth: Daniel Dammann <dan@smartassess.com>
	//----------------------------------------------------------------------------------------------

	header('Content-Type: application/xml');

	// Init
	session_start();

	// Libraries
	require_once('../../Connections/smart.php');
	require_once('../get_sql_value_string.php');
	require_once('../../userviews_class.php') ;

	// Build menu
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<realsmart_rtool_menubar_config type="share">';
	echo '	<menu>';

	// Prepare help info
	$userview = new userviews();
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
