<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Page access denied
	// Depd: User is either denied access to a certain resource or their session has expired
	// Auth: Various
	//----------------------------------------------------------------------------------------------

	// Return right error code
	header('HTTP/1.1 401 Unauthorized');

	// Use sessions
	session_start();
	
	if (count($_SESSION)) {
		foreach ($_SESSION as $session_key=>$session_value) {
			$log_variables[] = $session_key . ': ' . $session_value;
		}
	}
	if (count($_POST)) {
		foreach ($_POST as $post_key=>$post_value) {
			$log_variables[] = $post_key . ': ' . $post_value;
		}
	}

	error_log('User session expired. The user variables of this request are ' . implode(', ', $log_variables));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>realsmart || failed login</title>
	<style type="text/css">
	<!--
		#page {
			width: 387px;
			margin: 0 auto;
		}
	-->
	</style>
</head>
<body>
	<div id="page">
		<img src="images/failed.jpg" alt="image" alt="Failed Login"/>
	</div>
</body>
</html>