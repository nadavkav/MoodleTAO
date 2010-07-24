<?php
	/**
	* 	Startup functions used on each page render.
	* 	@author 	Joel Rowbottom <joel@jml.net>
	*/

	// Gimme libraries
        //require_once("../../../../../config.php");

	// "Not logged in" check
	// Set $RL_diasblelogincheck=1 in the calling script to disable this check.
	if ( (!isset($_SESSION['USER']->id)) && (!isset($RL_disablelogincheck)) ) {
		// Let ourselves know
		$log_variables[] = 'URL: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$log_variables[] = 'IP: ' . $_SERVER['REMOTE_ADDR'];
		$log_variables[] = 'Host: ' . $_SERVER['REMOTE_HOST'];
	
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
	
		// Let the user know
		header("Location: main/failed.php");
		exit(0);
	}

	// Create the database connection
	include_once $CFG->dirroot . '/mod/rafl/Connections/smart.php';
?>
