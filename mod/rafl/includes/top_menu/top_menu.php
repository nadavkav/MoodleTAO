<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Rafl top menu
	// Depd: -
	// Auth: Daniel Dammann <dan@smartassess.com>
	//----------------------------------------------------------------------------------------------

	class top_menu {
		static function display($arg_config_file) {
			// Logged in users get a menu
			if (strlen($_SESSION['USER']->id)) {
				// Start sessions just in case
				session_start();
				
				// Append url variable
				if (strpos($arg_config_file, '?')) {
					$config_url = $arg_config_file . '&this_url=' . urlencode($_SERVER['REQUEST_URI']);
				} else {
					$config_url = $arg_config_file . '?this_url=' . urlencode($_SERVER['REQUEST_URI']);
				}

				// Build configuration path
				$config_url = urlencode('../includes/top_menu/' . $config_url);
				
				// Base path for config path and amfphp
				$base_url = "main/";
			
				// Set the session string so that mac's can call urls and retain their session id's
				$session_string = urlencode(session_name() . "=" . session_id());
				
				// Define user id for user image in menu
				$user_id = $_SESSION['USER']->id;
				
				// Build string of variables to send to flash
				$flashVarString = "config=$config_url&base_url=$base_url&session_string=$session_string&user_id=$user_id";
?>
				<style type="text/css">
					/* Position menu and content box */

					#divFlashContent {
					  position: fixed;
					  left: 0px;
					  top: 0px;
					  width: 595px;
					  height: 36px;
					  z-index: 2;
					}
					
					#pageWrapper {
					  position: absolute;
					  top: 37px;
					  width: 595px;
					  z-index: 1;
					}
				</style>

				<div id="divFlashContent">
					<!-- Menu will go here -->
				</div>
				
				<script type="text/javascript" src="main/js/swfobject.js"></script>
				
				<script type="text/javascript">
				// <![CDATA[
					// Menu creation
					var so = new SWFObject("main/rtool/rtool_menubar.swf", "menubar", "100%", "100%", "9");
					so.addParam("scale", "noscale");
					so.addParam("salign", "TL");
					so.addParam("allowFullScreen", "true");
					so.addParam("allowScriptAccess", "always");
					so.addParam("movie", "main/rtool/rtool_menubar");
					so.addParam("FlashVars", "<?php echo $flashVarString; ?>");
					so.addParam("wmode","transparent");
					so.write("divFlashContent");
				
					// Menu helper functions called from flash file (browser-fix)
					function expandCreative() {  
						// Expanded width
						document.getElementById("divFlashContent").style.height = "100%";
					}  
					
					function collapseCreative() {  
						// Normal width
						document.getElementById("divFlashContent").style.height = "36px";
					}
				// ]]>
				</script>
<?php
			} else {
				// Not logged in, don't get a menu, cos it must be an item of a public share viewed by a public user
?>
				<style type="text/css">
					/* Position content box alone */
		
					#pageWrapper {
					  position: absolute;
					  top: 0px;
					  width: 595px;
					  z-index: 1;
					  overflow: auto;
					}
				</style>
<?php
			}

			// WORKAROUND: This is nothing to do with the top menu, it is a tooltip function that I want on every page
			//echo '<script type="text/javascript" src="javascripts/wz_tooltip.php"></script>';
		}
	}
?>