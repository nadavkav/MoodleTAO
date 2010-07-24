<?php  // $Id$
/**
 * This lib contains custom functions from the original rafl application and those to make this application talk to moodle
 * This page contains an AJAX page that is called by the flash to display a task's success criteria.
 * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
 * @version $Id$
 * @package mod/rafl
 */

// NOTICE: Ideally use static class vars instead (or better keep it PHP4-compatible) ???
define('RAFL_SCHOOL', '287');
define('RAFL_DBASE_PREFIX', 'rafl_');

$_SESSION['RealS_schoolid'] = RAFL_SCHOOL;
        
// WORKAROUND: Define moodle module table namespace
$_SESSION['RealS_prefix'] = $CFG->prefix . RAFL_DBASE_PREFIX;

class localLibRafl {
    /**
     * Get the main rafl item id of a country
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */

    function get_rafl_item_id_by_country($country_signifier) {
        switch ($country_signifier) {
            case 'uk':
                return 368597;
                break;
            default:
                die('Error: A country has been specified that does is not supported by the rafl module. Please contact technical support.');
        }
    }
    
    
    
    /**
     * Displays rafl main page, i.e. learner and mentor view with unit and task pies.
     * This page contains an AJAX page that is called by the flash to display a task's success criteria.
     * @author Daniel Dammann <dan@smartassess.com>
     */
    
    function display_rafl_component($course_id, $country_item_id, $arg_cfg) {
        // Debugging
        //error_reporting(E_ALL);

        // Set up session variables for the rafl pages
        // Only one school, so hard-coded
        $_SESSION['RealS_schoolid'] = RAFL_SCHOOL;

        // WORKAROUND: have to pass it, cos inside this function we loose scope with the configuration
        $CFG = $arg_cfg;

        // WORKAROUND: Define moodle module table namespace and web path
        $_SESSION['RealS_prefix'] = $CFG->prefix . RAFL_DBASE_PREFIX;
        //$_SESSION['RealS_raflroot'] = $CFG->wwwroot . '/mod/rafl/';

        print_simple_box_start('center');
    
    	// Disable login check for now, cos this share may be public. In checkSharedRights() we will know and do another login check.
    	$RL_disablelogincheck = 1;

    	// Gimme libraries
        require_once("../config.php");
    	require_once("includes/rlsmart/header.php");
        require_once("includes/rlsmart/general.php");
    	require_once('includes/common/KT_common.php');
    	require_once('includes/tng/tNG.inc.php');
    	require_once('userviews_class.php');
        require_once('classes/class_skin_data.php');
    	require_once("includes/top_menu/top_menu.php");
    	require_once("classes/item/Comments.class.php");
    	require_once('classes/class_comment_evidence_count.php');
    	require_once('classes/class_text_editor.php');

    	// Gimme the text editor
    	$editor = new textEditor();
    	$editor->setFormField('evidence_text');
    	$editor->setEditorType('full');
    	$editor->setContent('');
    	$editor->setWidth('571px');
    	$editor->setHeight('225px');
    	$editor->setCssPath($CFG->wwwroot . '/mod/rafl/layouts/rafl_css.php');
    
    	$userview = new userviews () ;

    	$parent_data = $userview->findParent ($country_item_id, "rafl" ) ;
    	//print_r($parent_data);
    
    	// Check viewing rights
    	if (! isset ($course_id) || $course_id == "") {
    		$userview->checkViewRights () ;
    		//$userview->viewRight =1;
    	} else {
    		$userview->checkSharedRights ($course_id) ;
    		//$userview->sharedRight=1;
    	}

    	$mysql = new mysqlquery ( ) ;
    

/*
    	// Learner or mentor view
    	//if (strlen($_GET['view'])) {
    		//$view = $_GET['view'];
    	//} elseif (strlen($_SESSION['USER']->id)) {
    		// If this user is the item creator or a share mentor, default to mentor view
    		if (($_SESSION['USER']->id == $parent_data['webcell_member']) || $userview->shareMentor==1) {
    			$view = 'mentor';
    		}
    	//}
*/

    	// Learner or mentor view
        $view = 'learner';
	if (($_SESSION['USER']->id == $parent_data['webcell_member']) || $userview->shareMentor==1) {
            $view = 'mentor';
    	}
    
    	// Gimme user id
    	$user_id = $_SESSION['USER']->id;
    	//$user_id = $userview->getMemberId($_SESSION['USER']->id, $_GET['mb_id'], $view);
    
    	// Get tasks
    	//          	INNER JOIN share ON {$_SESSION['RealS_prefix']}share.share_item = {$_SESSION['RealS_prefix']}items.item_parent_item
    	//          	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($course_id, "int") . "
    	//          	CASE share_unread_evidence_count WHEN 0 THEN webcell_title ELSE CONCAT(webcell_title, ' (', share_unread_evidence_count, ')') END AS webcell_title
    	$query = "SELECT
    	          	{$_SESSION['RealS_prefix']}webcells.webcell_title,
    	          	{$_SESSION['RealS_prefix']}webcells.webcell_text,
    	          	{$_SESSION['RealS_prefix']}items.item_id
    	          FROM {$_SESSION['RealS_prefix']}items
    	          	INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id={$_SESSION['RealS_prefix']}items.item_webcell
    	          	INNER JOIN {$_SESSION['RealS_prefix']}rafl ON {$_SESSION['RealS_prefix']}rafl.rafl_item={$_SESSION['RealS_prefix']}items.item_id
    	          	INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}webcells.webcell_member = {$_SESSION['RealS_prefix']}members.mb_id
    	          WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . $parent_data['item_id'] . "
    	          	AND {$_SESSION['RealS_prefix']}items.item_default_type = 1
    	          ORDER BY {$_SESSION['RealS_prefix']}rafl.rafl_order ASC";
    
    	$top_menu = $mysql->getrows ( $query ) ;
    
    	if (count ( $top_menu ) == 0) {
    		echo '<script type="text/javascript">alert(\'This page does not exist.\')</script>' ;
    		exit () ;
    	}
    
    	//$query = "SELECT tags.*, item_2_tag.item_tag_item FROM (item_2_tag LEFT JOIN tags ON tags.tag_id=item_2_tag.item_tag_tag) WHERE item_2_tag.item_tag_item=" . $parent_data [ 'item_id' ] ;
    	//$tags = $mysql->getrows ( $query ) ;
    
    	// User's skin
    	require_once("classes/class_skin_data.php");
    	$member_logged_in = $userview->getMemberData($_SESSION['USER']->id);
    	$skin_data = new SkinData("main/xml/skins/", $member_logged_in["mb_school"], $member_logged_in["mb_theme"]);
    	//$skin_path = $skin_data->getSkinPath();

    	// TAOC: Configure this when used in a location outside of the rafl mod directory
        $skin_path = $CFG->wwwroot . '/mod/rafl/main/skins/AfL%20Default/';

    	//echo "<pre>";
    	//echo 'Background colour: ' . $skin_data->getSkinColour("pieChartBackgroundColour") ."\n";
    	//echo 'Complete colour: ' . $skin_data->getSkinColour("pieChartCompleteColour") ."\n";
    	//echo "skin path is '$skin_path'\n";
    	//echo "</pre>";
    
    	function strip_bad_tags ( $html ) {
    		$s = preg_replace ( "@</?[^>]*>*@", "", $html ) ;
    		return $s ;
    	}
    
    	$taskArray = array ( ) ;
    	$idArray = array ( ) ;
    	$ta_help = array ( ) ;
    	$ta_helpData = array ( ) ;
    
    	if (count ( $top_menu ) > 0) {
    		foreach ( $top_menu as $row_top_menu ) {
    			array_push ( $taskArray, urlencode($row_top_menu['webcell_title']) ) ;
    			array_push ( $idArray, $row_top_menu['item_id'] ) ;
    			array_push ( $ta_help, ($row_top_menu['webcell_text'] != '') ? '1' : '0' ) ;
    			$ta_helpData [ $row_top_menu [ 'item_id' ] ] = $row_top_menu [ 'webcell_text' ] ;
    		}
    	}
    
    	if (isset ($user_id)) {
    		include ("count_rating.php") ;
    	}
    ?>
    
    <link href="<?php echo $CFG->wwwroot ?>/mod/rafl/layouts/rafl_css.php" rel="stylesheet" type="text/css" />
    <style type="text/css">
    <!--
    .style1 {
    	font-size: 10px;
    }
    
    #loading {
    	display: none;
    	text-align: center;
    	margin-bottom: 20px;
    }
    
    #topbar {
    	background-color: #AB1B15;
    	height: 19px;
    	width: 500px;
    	display: none;
    	position: absolute;
    	z-index: 100;
    }
    
    #topbar p {
    	font-family: Arial, Helvetica, sans-serif;
    	font-size: 10px;
    	font-weight: bold;
    	color: #FFFFFF;
    	padding-left: 5px;
    	padding-top: 2px;
    	margin: 0;
    }
    
    #topbar img {
    	float: right;
    	padding: 2px 4px;
    	margin: 0px;
    	cursor: pointer;
    }
    -->
    </style>
    <script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/mod/rafl/javascripts/swfobject.js"></script>
    <script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/mod/rafl/includes/kore/kore.js"></script>
    <script src="<?php echo $CFG->wwwroot ?>/mod/rafl/includes/common/js/base.js" type="text/javascript"></script>
    <script src="<?php echo $CFG->wwwroot ?>/mod/rafl/includes/common/js/utility.js" type="text/javascript"></script>
    <script src="<?php echo $CFG->wwwroot ?>/mod/rafl/javascripts/util.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/mod/rafl/ajax/jquery/jquery-1.2.6.min.js"></script>
    <script type="text/javascript">
    	var resultId = 0;
    	var successId = 0;
    	var myWidth = 0, myHeight = 0, myScroll = 0; myScrollWidth = 0; myScrollHeight = 0;
    
    	/**
    	 *
    	 */
    
    	function getSize() {
    		if (document.all) {
    			// IE4+ or IE6+ in standards compliant
    			myWidth  = (document.documentElement.clientWidth) ? document.documentElement.clientWidth : document.body.clientWidth;
    			myHeight = (document.documentElement.clientHeight) ? document.documentElement.clientHeight : document.body.clientHeight;
    			myScroll = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
    		} else {
    			// Non-IE
    			myWidth = window.innerWidth;
    			myHeight = window.innerHeight;
    			myScroll = window.pageYOffset;
    		}
    
    		// Core code from - quirksmode.org
    	    if (window.innerHeight && window.scrollMaxY) {
    	        myScrollWidth = document.body.scrollWidth;
    			myScrollHeight = window.innerHeight + window.scrollMaxY;
    		} else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
    			myScrollWidth = document.body.scrollWidth;
    			myScrollHeight = document.body.scrollHeight;
    		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
    			myScrollWidth = document.body.offsetWidth;
    			myScrollHeight = document.body.offsetHeight;
    		}
    	}
    
    	/**
    	 * Displays the guidance for the unit being viewed.
    	 * Called from within the flash.
    	 */
    
    	function displayUnitGuide () {
    		reset_all_sections('');
    		document.getElementById('unitGuide').style.display='block';
    	}
    
    	/**
    	 * Displays the guidance for the supplied task ID.
    	 * Called from within the flash.
    	 */
    
    	function displayTaskGuide (taskId) {
    		reset_all_sections(taskId);
    		document.getElementById('taskGuide'+taskId).style.display='block';
    	}
    
    	/**
    	 * Displays the guidance for a piece of success criteria.
    	 * Called when the guidance button on the criteria entry is clicked.
    	 */
    
    	function displaySuccessGuide (successId) {
    		document.getElementById('successGuide'+successId).style.display='block';
    	}
    
    	/**
    	 *
    	 */
    
    	function showSuccessRow(showThisRowId) {
    		reset_all_sections(showThisRowId);
    		document.getElementById('successTableRow' + showThisRowId).style.display = 'block';
    		document.getElementById('commentDiv').style.display='block';
    	}
    
    	/**
    	 * Closes the evidence panel
    	 */
    
    	function close_evidence() {
    		document.getElementById('evidence').style.display='none';
    		document.getElementById('commentDiv').style.display='none';
    		document.getElementById('commentDiv').innerHTML = '';
    
    		open_success_table();
    	}
    
    	/**
    	 * Displays the succes table
    	 */
    
    	function open_success_table() {
    		reset_all_sections('');
    		if (document.getElementById('successTable')) {
    			document.getElementById('successTable').style.display='block';
    		}
    	}
    
    	/**
    	 * Hides panels with exception of guidance id provided
    	 */
    
    function reset_all_sections(exceptId) {
        if (document.getElementById('unitGuide')) {
            document.getElementById('unitGuide').style.display='none';
        }
        if (document.getElementById('successTable')) {
            //document.getElementById('successTable').innerHTML = '';
            document.getElementById('successTable').style.display='none';
        }
        if (document.getElementById('evidence')) {
            document.getElementById('evidence').style.display='none';
        }
        if (document.getElementById('commentDiv')) {
            document.getElementById('commentDiv').innerHTML = '';
            document.getElementById('commentDiv').style.display='none';
        }
        for(var i = 0; i < document.getElementsByTagName('div').length; i++) {
            thisTag = document.getElementsByTagName('div')[i];
    
            if (thisTag.id.substring(0, 9) == 'taskGuide') {
                if (thisTag.id != document.getElementById('taskGuide' + exceptId)) {
                    thisTag.style.display= 'none';
                }
            }
    
            if (thisTag.id.substring(0, 12) == 'successGuide') {
                if (thisTag.id != document.getElementById('successGuide' + exceptId)) {
                    thisTag.style.display= 'none';
                }
            }
        }
        for(var i = 0; i < document.getElementsByTagName('table').length; i++) {
            thisTag = document.getElementsByTagName('table')[i];
    
            if (thisTag.id.substring(0, 15) == 'successTableRow') {
                if (thisTag.id != document.getElementById('successTableRow' + exceptId)) {
                    thisTag.style.display= 'none';
                }
            }
        }
    }
    
    	/**
    	 * Gets a reference to the supplied swf movie id
    	 */
    
    	function thisMovie(movieName) {
    	    if (navigator.appName.indexOf("Microsoft") != -1) {
    	        return window[movieName]
    	    }
    	    else {
    	        return document[movieName]
    	    }
    	}
    
    	/**
    	 * Displays the success criteria for the supplied task ID.
    	 * Called from within the flash.
    	 */

    	function displayTask(taskId) {
    		document.getElementById('loading').style.display = 'block';
    
    		$.ajax({
    			type: 'GET',
    			url: '<?php echo $CFG->wwwroot ?>/mod/rafl/getSuccess.php',
    			data: 'item_id=' + taskId + '&share_id=<?php echo $course_id; ?>&shareComment=<?php echo $userview->shareComment; ?>&view=<?php echo $view; ?>',
    			timeout: 20000,
    			success: function(arg_content) {
    				document.getElementById('loading').style.display = 'none';
    				reset_all_sections('');
    
    				// Fill DIV with content
    				result = arg_content.split('##');
    				document.getElementById('success').innerHTML = result[0];
    
    				// Evaluate javascript string
    				eval(result[1]);
    			},
    			error: function(request, errorType, errorThrown) {
    				// Go back to the listing
    				alert('Unfortunately, the guidance could not be retrieved. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
    				document.getElementById('loading').style.display = 'none';
    			}
    		});
    	}
    
    	/**
    	 * Opens the evidence (content) panel
    	 */
    
    	function displayEvidence (thesuccessId) {
    		if (<?php echo $editor->getEditorReadyToUseJavascriptVariable(); ?>) {
    			document.getElementById('loading').style.display = 'block';
    			successId = thesuccessId;
    
    			$.ajax({
    				type: 'GET',
    				url: '<?php echo $CFG->wwwroot ?>/mod/rafl/evidence.php',
    				data: 'item_id=<?php echo $country_item_id; ?>&success_id=' + successId + '&share_id=<?php echo $course_id; ?>',
    				timeout: 20000,
    				success: function(content) {
    					//alert(content);
    					document.getElementById('loading').style.display = 'none';
    
    					// Fill editor with content
    					<?php 
    					    if ($userview->shareMentor == 1) {
    					?>
        					var evidence_content;
        					evidence_content  = '<div class="popupHead">';
        					evidence_content += '<img src="<?php echo $CFG->wwwroot ?>/mod/rafl/images/close.gif" alt="close" onclick="close_evidence();" />';
        					evidence_content += '	<p>HERE IS THE EVIDENCE</p>';
        					evidence_content += '</div>';
        					evidence_content += '<div id="commentText">';
        					evidence_content += 	content;
        					evidence_content += '</div>';
        					evidence_content += '<br /><br />';

    					        document.getElementById("evidence").innerHTML = evidence_content;
    					<?php 
    					    } else {
    					        echo $editor->setContentViaJavascript('content') . ';';
    					    }
    					?>
    
    					// Show comment section
    			        	if (content.length > 0) {
    			        		displayEvidenceComment(successId);
    			        	}

                                        document.getElementById("evidence").style.display = "block";
    				},
    				error: function(request, errorType, errorThrown) {
    					// Go back to the listing
    					alert('Unfortunately, the evidence could not be retrieved. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
    					document.getElementById('loading').style.display = 'none';
    					close_evidence();
    					<?php echo $editor->setContentViaJavascript(''); ?>;
    				}
    			});
    		} else if(confirm('The web editor doesn\'t seem to have loaded properly. Do you want to refresh the page?')) {
    			window.location.reload();
    		}
    	}
    
    	/**
    	 *
    	 */
    
    	function displayEvidenceComment (thesuccessId) {
    		document.getElementById('loading').style.display = 'block';
    		successId = thesuccessId;

    		$.ajax({
    			type: 'GET',
    			url: '<?php echo $CFG->wwwroot ?>/mod/rafl/get_evidence_comment.php',
    			data: 'item_id=<?php echo $country_item_id; ?>&success_id=' + successId + '&share_id=<?php echo $course_id; ?>',
    			timeout: 20000,
    			success: function(arg_content) {
    				document.getElementById('loading').style.display = 'none';
    
    				// Remove star, when being viewed
    				if (document.getElementById("glass" + successId).src.indexOf("evidence_colour_unread.gif") != -1) {
    					document.getElementById("glass" + successId).src = "<?php echo $CFG->wwwroot ?>/mod/rafl/images/evidence_colour.gif";
    				}

    				// Fill DIV with content
    				if (arg_content.length > 0) {
    					var comment_content;
    					comment_content  = '<div class="popupHead">';
    					comment_content += '	<p>COMMENTS ON THE EVIDENCE</p>';
    					comment_content += '</div>';
    					comment_content += '<div id="commentText">';
    					comment_content += 	arg_content;
    					comment_content += '</div>';
    					comment_content += '<br /><br />';
    
    					document.getElementById('commentDiv').innerHTML = comment_content;
    					document.getElementById('commentDiv').style.display='block';
    				}
    			},
    			error: function(request, errorType, errorThrown) {
    				// Go back to the listing
    				alert('Unfortunately, the comment could not be retrieved. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
    				document.getElementById('loading').style.display = 'none';
    			}
    		});
    	}
    
    	/**
    	 * Saves evidence entered byt the user
    	 * Called when save button clicked.
    	 */
    
    	function saveEvidence(webcellText) {
    		//alert(webcellText);
    		document.getElementById('loading').style.display = 'block';
    
    		$.ajax({
    			type: 'POST',
    			url: '<?php echo $CFG->wwwroot ?>/mod/rafl/store_evidence.php',
    			data: 'unit_item_id=<?php echo $parent_data['item_id']; ?>&success_item_id=' + successId + '&webcelltext=' + encodeURIComponent(webcellText) + '&share_id=<?php echo $course_id; ?>&taskArray=<?php echo implode("||", $idArray); ?>',
    			timeout: 20000,
    			success: function(arg_content) {
    				//alert(arg_content);
    				document.getElementById('loading').style.display = 'none';
    				close_evidence();
    				<?php echo $editor->setContentViaJavascript(''); ?>;
    
    				if (webcellText!='') {
    					document.getElementById('glass'+successId).src = '<?php echo $CFG->wwwroot ?>/mod/rafl/images/evidence_colour.gif';
    				} else {
    					document.getElementById('glass'+successId).src = '<?php echo $CFG->wwwroot ?>/mod/rafl/images/evidence_ghost.gif';
    				}
    
    				// Capture return value and send to pies
    				result = arg_content.split("##");
                			sendToWidget(successId,result[0]);
    				sendToFlash(result[1],result[2],result[3],result[4]);
    			},
    			error: function(request, errorType, errorThrown) {
    				// Open editor back up with current content
    	            		document.getElementById('loading').style.display = 'none';
    				alert('Unfortunately, your evidence might not have saved correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
    				displayEvidence(successId);
    			}
    		});
    	}
    
    	/**
    	 * Sends data to the flash
    	 */
    
    	function sendToFlash (overall,doneArray,rate,rateArray) {
    		//var txt = document.getElementById("inputField").value;
    		//thisMovie("foo").UpdatePie();
    		thisMovie("foo").setRating(overall,doneArray,rate,rateArray);
    	}
    
    	/**
    	 * Sends data to a flash widget (success criteria button)
    	 */
    
    	function sendToWidget(widgetid,value) {
    		//alert(widgetid);
    		window.setTimeout("sendToWidgetNow(" + widgetid + "," + value + ")", 1000);
    	}
    
    	function sendToWidgetNow(widgetid,value) {
    		//alert(widgetid);
    		thisMovie("fooq"+widgetid).setRating(value);
    	}
    
    	/**
    	 * Sets a new rating for an item of success criteria
    	 * Called when a smiley / medal / etc is clicked on
    	 */
    
    	function receivedFromFlash(txt, success_id) {
    		document.getElementById('loading').style.display = 'block';
    
    		$.ajax({
    			type: 'GET',
    			url: '<?php echo $CFG->wwwroot ?>/mod/rafl/store_rating.php',
    			data: 'parent_id=' + success_id + '&rating=' + txt + '&share_id=<?php echo $course_id; ?>' + '&view=<?php echo $view; ?>&taskArray=<?php echo implode("||", $idArray); ?>',
    			timeout: 20000,
    			success: function(arg_content) {
    				document.getElementById('loading').style.display = 'none';
    
    				// Capture return value and send to pies
    				result = arg_content.split("##");
    				//alert("reset Overall: "+result[1]+" doneArray: "+result[2]+" Rate Total:"+result[3]+" rateArray:"+result[4]);
    				sendToFlash(result[1],result[2],result[3],result[4]);
    			},
    			error: function(request, errorType, errorThrown) {
    				// Go back to the listing
    				alert('Unfortunately, your rating might not have saved correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
    				document.getElementById('loading').style.display = 'none';
    			}
    		});
    	}
    </script>
    
    <div id="raflWrapper">
    	<div id="foo3">
    	  <p><strong>You need to upgrade your Flash Player</strong></p>
    	  <p><a href="#?detectflash=false">click here</a> to bypass Flash detection</p>
    	</div>
    	<script type="text/javascript">
    		// <![CDATA[
    		var so = new SWFObject("<?php echo $CFG->wwwroot ?>/mod/rafl/main/flash/afl_unitDisplay.swf", "foo", "595", "300", "8", "#FFFFFF");
    		so.addParam("wmode","transparent");
    		so.addVariable("skin_path", "<?php echo $skin_path; ?>"); // the current skin path
    		so.addVariable("un_help", "<?php echo ($parent_data[ 'webcell_text' ] != '') ? '1' : '0' ; ?>"); // unit help
    		so.addVariable("ta_id", "0"); // task id selected
    		so.addVariable("pic", "<?php echo rawurlencode($member_logged_in[ 'mb_pic' ]) ; ?>"); // users picture
    		so.addVariable("colours", "<?php echo $member_logged_in[ 'mb_colour' ] ; ?>"); // the colour scheme selected (traffic light / smiley / etc)
    		so.addVariable("taskArray", "<?php echo implode ( "||", $taskArray ) ; ?>"); // array of the task descriptions
    		so.addVariable("idArray", "<?php echo implode ( "||", $idArray ) ; ?>"); // array of the task item ids
    		so.addVariable("ta_help", "<?php echo implode ( "||", $ta_help ) ; ?>"); // array of the task help status Boolean
    
    		// $rateTotal, $rateArray & $doneArray are defined in "count_rating.php"
    		so.addVariable("rate", "<?php
    		if (isset ($user_id)) {
    			echo $rateTotal ;
    		}
    		?>");
    
    		so.addVariable("rateArray", "<?php
    		if (isset ($user_id)) {
    			echo implode ( "||", $rateArray ) ;
    		}
    		?>");
    
    		so.addVariable("overall", "<?php
    		if (isset($user_id) && count($doneArray) > 0) {
    			echo array_sum ( $doneArray ) / count($doneArray) ;
    		}
    		?>");
    
    		so.addVariable("percentArray", "<?php if (isset ($user_id)) { echo implode ( "||", $doneArray ) ; } ?>");
    
    		// write the flash content to the page
    		so.write("foo3");
    		// ]]>
    	    </script>
    	<div id="loading"><img src="/mod/rafl/main/images/loading.gif" alt="loading" /></div>
    	<div id="success">&nbsp;</div>
    	<div id="evidence" style="display: none;">
    		<div class="popupHead" style="width: 557px;">
    			<img src="/mod/rafl/images/close.gif" alt="close" onclick="close_evidence(); <?php echo $editor->setContentViaJavascript(''); ?>;" />
    			<p>HERE IS MY EVIDENCE</p>
    		</div>
    		<?php
    			// Only logged in users can contribute
    			if (strlen($_SESSION['USER']->id)) {
    				echo $editor->getEditor();
    			}
    		?>
    		<input type="button" name="KT_Insert1" id="KT_Insert1" value="Save Evidence" onclick="saveEvidence(<?php echo $editor->getContentViaJavascript(); ?>);" />
    		<input type="button" name="cancel" id="cancel" value="Cancel" onclick="close_evidence(); <?php echo $editor->setContentViaJavascript(''); ?>;" />
    		<br /><br /><br />
    	</div>
    	<div id="commentDiv" style="display:none;"></div>
    	<div id="unitGuide" style="display:none;">
    		<div class="popupHead">
    			<img src="/mod/rafl/images/close.gif" alt="close" onclick="open_success_table();" />
    			<p>HERE IS YOUR GUIDANCE</p>
    		</div>
    		<div id="unitGuideText">
    			<?php echo $parent_data [ 'webcell_text' ]; ?>
    		</div>
    		<br /><br />
    	</div>
    	<?php
    		foreach ($ta_helpData as $key=>$value) {
    	?>
    			<div id="taskGuide<?php echo $key; ?>" style="display:none;">
    				<div class="popupHead">
    					<img src="/mod/rafl/images/close.gif" alt="close" onclick="open_success_table();" />
    					<p>HERE IS YOUR GUIDANCE</p>
    				</div>
    				<div id="taskText">
    					<?php echo '<div class="guidanceContainer">' . $value . '</div>'; ?>
    				</div>
    				<br /><br />
    			</div>
    	<?php
    		}
    	?>
    	<br><br>
    </div>
    
    <?php
    	// Do comments
    	$obj_comment = new rs_itemComments($mysql, 1, 'rafl', $course_id, $parent_data['item_id'], '', '/mod/rafl/layouts/rafl_css.php');
    	$obj_comment->renderSupportingJavascript();
    
    	// Display the comments form
    	if (strlen($course_id)) {
    		$obj_comment->renderCommentSubmissionForms();
    	}
    
        print_simple_box_end();
    }
    
    
    
    /**
     * 
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function get_course_module_id ($courseid) {
        global $CFG;

        $sql = "SELECT min(cm.id) as id
                  FROM {$CFG->prefix}course_modules cm
                  JOIN {$CFG->prefix}modules m on cm.module = m.id
                 WHERE m.name = 'rafl'
                   AND cm.course = $courseid";

        if ($coursemodule = get_record_sql($sql)) {
            return $coursemodule->id;
        }
    }
    
    
    
    /**
     * Get the rafl item ids of the learning path descriptions.
     * These exist only once in rafl independant of each user.
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function get_lp_item_structure($parent_item_id) {
        // WARNING: First column must be unique id
        $sql = "SELECT
                    questions.item_id AS question_item_id,
                    pies.item_id AS pie_item_id,
                    webcells.webcell_title as title
                FROM {$_SESSION['RealS_prefix']}items AS pies
                    INNER JOIN {$_SESSION['RealS_prefix']}items questions ON questions.item_parent_item = pies.item_id
                    INNER JOIN {$_SESSION['RealS_prefix']}rafl rafl_pies ON rafl_pies.rafl_item = pies.item_id
                    INNER JOIN {$_SESSION['RealS_prefix']}rafl question_pies ON question_pies.rafl_item = questions.item_id
                    INNER JOIN {$_SESSION['RealS_prefix']}webcells webcells ON webcells.webcell_id = questions.item_webcell
                WHERE pies.item_parent_item = " . $parent_item_id . "
                ORDER BY rafl_pies.rafl_order, question_pies.rafl_order";

        return get_records_sql($sql);
    }
    
    
    
    /**
     * Insert evidence into the moodle table in addition to the realsmart one
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function update_moodle_item($courseid, $raflitemid, $text) {
        global $CFG;

        // attempt to get correct record of corresponding moodle page_item 
        $sql = "SELECT {$CFG->prefix}format_page_items.* 
                  FROM {$CFG->prefix}format_page_items 
                  JOIN {$CFG->prefix}format_page on {$CFG->prefix}format_page.id = {$CFG->prefix}format_page_items.pageid
                 WHERE {$CFG->prefix}format_page_items.rafl_item = $raflitemid
                   AND {$CFG->prefix}format_page.courseid = $courseid";

        if ($pageitem = get_record_sql($sql)) {
            // valid page item, now let's try to find and update the block data
            $instance = get_record('block_instance', 'id', $pageitem->blockinstance);
            $block = get_record('block', 'id', $instance->blockid);
            if ($obj = block_instance($block->name, $instance)) {
                $blockdata->title    = $obj->title;
                $blockdata->text     = $text;
                if(!$obj->instance_config_save($blockdata, false, false)) {
                    error('Error saving block configuration');
                }
            }
        } else {
            debugging('No matching page item found for this rafl. courseid: ' . $courseid . ' raflitemid: ' . $raflitemid);
        }

        return 1;
    }

    /**
     * submits evidence directly into the rafl table 
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function update_share_item($courseid, $raflitemid, $text) {
        global $CFG;

	require_once($CFG->dirroot . '/lib/filelib.php');

        // do a server side post to store_evidence.php
        $country_profile_id = $this->get_rafl_item_id_by_country('uk');

        if ($this->store_evidence($courseid, $raflitemid, $country_profile_id, $text)) {
            //error('Could not store RAFL evidence');
        }
    }

    /**
     * Saves evidence text in rafl tables
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function store_evidence($share_id, $success_item_id, $unit_item_id, $webcelltext) {
        global $CFG, $USER;

        // note: including more than we might need to avoid regression bugs - too much work right now to analyse and cull
        require_once('includes/rlsmart/header.php');
	require_once("includes/get_sql_value_string.php");
	require_once('includes/common/KT_common.php');
	require_once('includes/tng/tNG.inc.php');
	require_once('userviews_class.php');
	require_once("classes/class_comment_evidence_count.php");

        // Make unified connection variable // todo is this still necessary?
	$conn_smart = new KT_connection($smart, $CFG->dbname);

	$colname_rs_evidence = "-1";
	if (isset($success_item_id)) {
	  $colname_rs_evidence = $success_item_id;
	}
	$colname_rs_evidence2 = "-1";
	if (isset($_SESSION['USER']->id)) {
	  $colname_rs_evidence2= $_SESSION['USER']->id;
        }
	mysql_select_db($CFG->dbname);
	//mysql_select_db($CFG->dbname, $smart);

	// Check whether this evidence already exists
	// INNER JOIN share_cohort_members ON {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member = {$_SESSION['RealS_prefix']}webcells.webcell_member
	// AND {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share = " . GetSQLValueString($_POST['share_id'], 'int') . "
	$query_rs_evidence = sprintf("SELECT {$_SESSION['RealS_prefix']}items.item_id, 
		                              {$_SESSION['RealS_prefix']}items.*,
		                              {$_SESSION['RealS_prefix']}webcells.*,
		                              {$_SESSION['RealS_prefix']}rafl_res.*
	                              FROM {$_SESSION['RealS_prefix']}items
		                              INNER JOIN {$_SESSION['RealS_prefix']}webcells 
                                                   ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
		                              INNER JOIN {$_SESSION['RealS_prefix']}rafl_res 
                                                   ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
	                              WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = %s
	                              	AND {$_SESSION['RealS_prefix']}items.item_default_type = 6
	                              	AND rafl_res_share = " . GetSQLValueString($share_id, "int") . "
	                              	AND {$_SESSION['RealS_prefix']}webcells.webcell_member = %s", 
              GetSQLValueString($colname_rs_evidence, "int"), 
              GetSQLValueString($colname_rs_evidence2, "int"));

	//$rs_evidence = mysql_query($query_rs_evidence, $smart) or die(mysql_error());
	$rs_evidence = mysql_query($query_rs_evidence) or die(mysql_error());
	$row_rs_evidence = mysql_fetch_assoc($rs_evidence);
	$totalRows_rs_evidence = mysql_num_rows($rs_evidence);

        //debugging($query_rs_evidence);
        //debugging($totalRows_rs_evidence);

        // todo decide whether this is needed
        // HACK: Cos tinyMce goes mad with escaping
        //$_POST["webcelltext"] = str_replace('\"', '"', $_POST["webcelltext"]);

        if ( $totalRows_rs_evidence == 0 ) {

                //debugging('Inserting new RAFL webcell');

                $webcell_id = null;
                $item_id = null;

                $webcell = new stdclass();
                $webcell->webcell_title = 'Evidence';
                $webcell->webcell_text = addslashes($webcelltext);
                $webcell->webcell_member = $USER->id;
                $webcell->webcell_school = $_SESSION["RealS_schoolid"];

                if (!$webcell_id = insert_record('rafl_webcells', $webcell, true, 'webcell_id')) { // todo replace insert_record with something more appropriate
                    error('rafl_webcells INSERT failed');
                }

                //debugging('webcell: ' .  $webcell_id);

                $items = new stdclass();
                $items->item_webcell = $webcell_id;
                $items->item_parent_item = $success_item_id; 
                $items->item_school = $_SESSION["RealS_schoolid"];
                $items->item_default_type = 6; 
                //$items->item_access = 

                if (!$item_id = insert_record('rafl_items', $items, true, 'item_id')) { // todo replace insert_record with something more appropriate
                    error('rafl_items INSERT failed');
                }

                //debugging('item: ' .  $item_id);

                $rafl_res = new stdclass();
                $rafl_res->rafl_res_item = $item_id;
                //$rafl_res->rafl_res_date = now();
                $rafl_res->rafl_res_rate = 1; 
                $rafl_res->rafl_res_share = $share_id;

                if (!insert_record('rafl_rafl_res', $rafl_res, true, 'rafl_res_id')) { // todo replace insert_record with something more appropriate
                    error('rafl_rafl_res INSERT failed');
                }

		$rating = 1;

        } else {

            if (!empty($webcelltext)) {

               //debugging('Updating RAFL webcell ' . $row_rs_evidence['webcell_id']);

               $sql = "UPDATE {$CFG->prefix}rafl_webcells SET webcell_text = '" . addslashes($webcelltext) . "' WHERE webcell_id = " . $row_rs_evidence['webcell_id'];
               if (!execute_sql($sql)) {
                   error('rafl_webcells UPDATE failed');
               }

	       // todo rafl_res.rafl_res_date

               $rating = $row_rs_evidence['rafl_res_rate'];

            } else {
               //debugging(4);
               // todo delete

               debugging('Deleting RAFL webcell ' . $row_rs_evidence['webcell_id']);

               $rating = "0";
            }
        }

	// If evidence exists, else new evidence
	if ( $totalRows_rs_evidence == 0 ) {
		$query = "SELECT MAX(item_id) AS evidence_item_id
		          FROM {$_SESSION['RealS_prefix']}items";
		
		//$rows = mysql_query($query, $smart) or die(mysql_error());
		$rows = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($rows);
		$evidence_item_id = $row['evidence_item_id'];
	} else {
		$evidence_item_id = $row_rs_evidence['item_id'];
	}

        // todo test what this does
	// AFTER evidence creation save this member status count for unread evidence
	$obj_count = new commentEvidenceCount();
	$obj_count->increaseEvidenceCounter($unit_item_id, $evidence_item_id, $share_id);

        return $rating;

    }

    /**
     * When a learning path gets created in moodle, a share gets created in rafl with ONE member: the author.
     * You can call this function with an existing $courseid and a new $userid and then only the author gets updated
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function create_share($courseid, $userid) {
        create_member($userid);

        // Insert share, if not exists
        if (! get_record("rafl_share", "share_id", $courseid)) {
            $share = new object();
            $share->share_id = $courseid;
            $share->share_item = $this->get_rafl_item_id_by_country('uk');
    
            // Use pro-forma value
            $share->share_subject = $courseid;
    
            $share->share_public = 0;
            $share->share_type = 3;
            $share->share_school = $_SESSION['RealS_schoolid'];
            $share->share_active = 'ACCEPTED';
            $share->share_member = $userid;
            $share->share_status = 'OLD';
        
            // WARNING: Work around "primary-key" enforcement in dmlib.php
            insert_record('rafl_share', $share, false, 'share_permission');
        }

        // Re-do the user ANYWAY, in case he/she has changed
	$sql = "UPDATE {$_SESSION['RealS_prefix']}share
	        SET share_member = " . $userid . "
	        WHERE share_id = " . $courseid;

        execute_sql($sql, false);

        delete_records("rafl_share_indiv", "share_indiv_share", $courseid);
        delete_records("rafl_share_cohort_members", "s_c_m_share", $courseid);
    
        // Set up only one member, who also is the sharer
        $shareindiv = new object();
        $shareindiv->share_indiv_share = $courseid;
        $shareindiv->share_indiv_indiv = $userid;
        $shareindiv->share_indiv_member = $userid;
        $shareindiv->share_indiv_status = 'OLD';
    
        // WARNING: Work around "primary-key" enforcement in dmlib.php
        insert_record('rafl_share_indiv', $shareindiv, false, 'share_indiv_id');

        // Set up only one member, who also is the sharer
        $sharemember = new object();
        $sharemember->s_c_m_share = $courseid;
        $sharemember->s_c_m_member = $userid;
        $sharemember->s_c_m_status = 'OLD';
        $sharemember->s_c_m_sharer = $userid;
    
        // WARNING: Work around "primary-key" enforcement in dmlib.php
        insert_record('rafl_share_cohort_members', $sharemember, false, 's_c_m_id');
    }
    
    
    
    /**
     * When learning path roles get allocated in moodle, share mentors get allocated in rafl
     * You must call this function with an existing $courseid
     * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
     */
    
    function update_share_contributors($courseid, $useridarray) {
        delete_records("rafl_share_mentor", "share_mentor_share", $courseid);
    
        $row = get_record("rafl_share", "share_id", $courseid);

        if (! $row) {
            trigger_error("You have tried allocating commentators to a rafl share that doesn't exist.");
        } else {
            foreach ($useridarray as $userid) {
                create_member($userid);
    
                $sharementor = new object();
                $sharementor->share_mentor_share = $courseid;
                $sharementor->share_mentor_mentor = $userid;
                $sharementor->share_mentor_member = $row->share_member;
                $sharementor->share_mentor_status = 'OLD';
            
                // WARNING: Work around "primary-key" enforcement in dmlib.php
                insert_record('rafl_share_mentor', $sharementor, false, 'share_mentor_id');
            }
        }
    }
}
/**
* Checks table encoding - table creation is manual so need to check if correctly created utf8 tables.
* @author Dan Marsden <dan@danmarsden.com>
*/
function check_rafl_table_encoding() {
    global $db;
    $rs = $db->Execute("SHOW TABLE STATUS LIKE 'mdl_rafl_webcells'");
    if ($rs && !$rs->EOF) { // rs_EOF() not available yet
        $records = $rs->GetAssoc(true);
        $encoding = $records['mdl_rafl_webcells']['Collation'];
        $pos = strpos(strtoupper($encoding), 'UTF8');
        if ($pos === false) {
            return false;
        }
    }
    return true;
}
/**
 * Create rafl member, if not already exists
 * @author David Drummond <david@catalyst.net.nz>, Daniel Dammann <dan@smartassess.com>
 */
    
 function create_member($userid) {
    if (! get_record("rafl_members", "mb_id", $userid)) {
        $member = new object();
        $member->mb_id = $userid;
 
        // Use pro-forma values
        $member->mb_username = $userid;
        $member->mb_password = $userid;
            
        $member->mb_school = $_SESSION['RealS_schoolid'];
    
        // Make everyone a mentor for the activity notifications to work when evidence gets entered
        $member->mb_type = 'learner';
        
        insert_record('rafl_members', $member, false, 'mb_school_id');
    }
 }


?>
