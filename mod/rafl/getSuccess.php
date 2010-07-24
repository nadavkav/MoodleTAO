<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Shows rafl learner view with progress bars/evidence forms and mentor view with cohort stats
	// Depd: Only used by {$_SESSION['RealS_prefix']}rafl.php viewer page and called by Flash/AJAX
	//----------------------------------------------------------------------------------------------

	// Disable login check for now, cos this share may be public. In checkSharedRights() we will know and do another login check.
	$RL_disablelogincheck = 1;

	// Gimme libraries
	require_once("../../config.php");
	require_once('includes/rlsmart/header.php');
	require_once("includes/get_sql_value_string.php");
	require_once('userviews_class.php');
	require_once('classes/class_rafl.php') ;
	
	//find user
	$mysql = new mysqlquery();
	$userview = new userviews ( ) ;

	$parent_data = $userview->findParent($_GET['item_id'],"rafl");
	
	// Check rights
	if (strlen($_GET['share_id'])) {
		$userview->checkSharedRights($_GET['share_id']);
	} else {
		$userview->checkViewRights();
	}
	
	if (strlen($_SESSION['USER']->id)) {
		$userData = $mysql->getRow( "SELECT mb_icon FROM {$_SESSION['RealS_prefix']}members WHERE mb_id=" . $_SESSION['USER']->id);
		$icon = $userData['mb_icon'];
	}
	
	$colname_rs_success = "-1";
	if (isset($_GET['item_id'])) {
	  $colname_rs_success = $_GET['item_id'];
	}
	mysql_select_db($CFG->dbname, $smart);
	
	// Gimme all success criteria
	// Subquery: Conclude from the comment item id what the comment count for each success item must be
	$query_rs_success = "SELECT
	                     	{$_SESSION['RealS_prefix']}items.item_id,
	                     	{$_SESSION['RealS_prefix']}webcells.webcell_title,
	                     	{$_SESSION['RealS_prefix']}webcells.webcell_text,
	                     	{$_SESSION['RealS_prefix']}rafl.rafl_collective,
	                     	{$_SESSION['RealS_prefix']}rafl.rafl_success_evid_req,
	                     	{$_SESSION['RealS_prefix']}rafl.rafl_success_obj
	                     FROM {$_SESSION['RealS_prefix']}items
	                     	LEFT OUTER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id={$_SESSION['RealS_prefix']}items.item_webcell
	                     	LEFT OUTER JOIN {$_SESSION['RealS_prefix']}rafl ON {$_SESSION['RealS_prefix']}rafl.rafl_item={$_SESSION['RealS_prefix']}items.item_id
	                     	LEFT OUTER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}webcells.webcell_member = {$_SESSION['RealS_prefix']}members.mb_id
	                     WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . GetSQLValueString($colname_rs_success, "int") . "
	                     	AND {$_SESSION['RealS_prefix']}items.item_default_type = 1
	                     ORDER BY {$_SESSION['RealS_prefix']}rafl.rafl_order";

	//$query_rs_success = sprintf("SELECT {$_SESSION['RealS_prefix']}webcells.*, {$_SESSION['RealS_prefix']}items.item_id, {$_SESSION['RealS_prefix']}rafl.* FROM rafl INNER JOIN items ON {$_SESSION['RealS_prefix']}rafl.rafl_item = {$_SESSION['RealS_prefix']}items.item_id INNER JOIN webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id WHERE item_parent_item = %s", GetSQLValueString($colname_rs_success, "int"));

	$mysql = new mysqlquery;
	$successes = $mysql->runsql($query_rs_success);
	
	//$rs_success = mysql_query($query_rs_success, $smart) or die(mysql_error());
	//$success = mysql_fetch_assoc($rs_success);
	
	$Javascript="";

	if (count($successes) > 0) {
?>
		<table cellspacing="2" class="successTable" id="successTable" summary="Rating of a success criteria">
		 <tr>
		    <th class="task_a"><?php if ($successes[0]['rafl_collective'] != "1") {?>
		      I
		        <?php } else { ?>
		      WE
		      <?php }?>
		      CAN</th>
		    <th class="task_b"><?php if ($successes[0]['rafl_collective'] != "1") {
		        echo 'BECAUSE&nbsp;I&nbsp;HAVE';
		      } else {
		        echo 'BECAUSE&nbsp;WE&nbsp;HAVE';
		      }?></th>
		    <th class="guidance">GUIDANCE</th>
		    <?php
		    	if (isset($_GET['share_id']) && $_GET['shareComment']==1){
		    		echo '<th class="evidence">';
		    		if ($_GET['view'] == 'mentor') {
		    			echo 'EVIDENCE';
		    		} else {
		    			echo 'EVIDENCE';
		    		}
		    		echo '</th>';
			    	echo '<th class="progress">PROGRESS</th>';
		    	}
		    ?>
		  </tr>

		  <?php
			// Show ALL success criteria of a task when being clicked into
		  	foreach ($successes as $success) {
                                // WORKAROUND: To avoid PHP warnings
                                $_GET['mb_id'] = '';
                                $_GET['view'] = 'learner';

				  // Gimme user id
				  $userview = new userviews();
				  $member = $userview->getMemberId($_SESSION['USER']->id, $_GET['mb_id'], $_GET['view']);
				
				  // Gimme ALL the members ratings for each success criteria in this share (cos filter the right member later in the loop)
                                  //AND webcell_member = " . $_SESSION['USER']->id . "
				  $query_rs_result = "SELECT
				                        {$_SESSION['RealS_prefix']}items.item_id, 
				                        {$_SESSION['RealS_prefix']}rafl_res.rafl_res_rate,
				                        {$_SESSION['RealS_prefix']}webcells.webcell_member,
				                        {$_SESSION['RealS_prefix']}webcells.webcell_text
				                      FROM {$_SESSION['RealS_prefix']}items
				                        INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}items.item_webcell = {$_SESSION['RealS_prefix']}webcells.webcell_id
				                        INNER JOIN {$_SESSION['RealS_prefix']}rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item
				                      WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . $success['item_id'] . "
				                      	AND rafl_res_share = " . GetSQLValueString($_GET['share_id'], "int") . "
				                        AND {$_SESSION['RealS_prefix']}items.item_default_type = 6";
				
				  // Debugging
				  //echo $query_rs_result;
				
				  $rs_result = mysql_query($query_rs_result, $smart) or die(mysql_error());

				  // Gimme the comments counter
				  if (strlen($_SESSION['USER']->id)) {
					  $query = "SELECT 
					            	SUM((SELECT COUNT({$_SESSION['RealS_prefix']}item_view_status.item_id)
					            	FROM {$_SESSION['RealS_prefix']}item_view_status
					            	WHERE {$_SESSION['RealS_prefix']}item_view_status.item_id_comment_evidence = comments.item_id
					            	AND {$_SESSION['RealS_prefix']}item_view_status.mb_id_viewer = " . $_SESSION['USER']->id . ")) AS unread_count
					            FROM {$_SESSION['RealS_prefix']}items
					            	LEFT OUTER JOIN {$_SESSION['RealS_prefix']}items AS comments ON comments.item_parent_item = {$_SESSION['RealS_prefix']}items.item_id
					            WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . $success['item_id'];

					  $rows = mysql_query($query, $smart) or die(mysql_error());
					  $row = mysql_fetch_assoc($rows);
					  $unread_count = $row['unread_count'];
				  } else {
					  $unread_count = 0;
				  }

				  // Reset
				  $item_id = '';
				  $rafl_res_rate = '';
				  $evidence_path = $CFG->wwwroot . '/mod/rafl/images/evidence_ghost.gif';
				  $total_learner_ratings = 0;
				  $ratings_count = 0;
				
				  while ($row_rs_result = mysql_fetch_assoc($rs_result)) {
				  	//if ($row_rs_result['webcell_member'] == $member) {
						$item_id = $row_rs_result['item_id'];
						$rafl_res_rate = $row_rs_result['rafl_res_rate'];

						// Icon depends on evidence text
						if ($row_rs_result['webcell_text'] == "") {
							$evidence_path = $CFG->wwwroot . '/mod/rafl/images/evidence_ghost.gif';
						} else {
							if ($unread_count > 0) {
								$evidence_path = $CFG->wwwroot . '/mod/rafl/images/evidence_colour_unread.gif';
							} else {
								$evidence_path = $CFG->wwwroot . '/mod/rafl/images/evidence_colour.gif';
							}
						}
					//} elseif ($row_rs_result['rafl_res_rate'] > 1) {
					//	// Add up all the ratings, but NOT the current user's rating
					//	$total_learner_ratings += $row_rs_result['rafl_res_rate'];
					//	$ratings_count++;
					//}
				  }
				?>
				  <!-- Success item <?php echo $success['item_id']; ?> start -->
				  <tr valign="top">
					  <td valign="top">
					  	<?php
					  		if (strlen($success['rafl_success_obj']) && $success['rafl_success_obj'] != '<p></p>') {
					  			echo $success['rafl_success_obj'];
					  		} else {
					  			echo "&nbsp;";
					  		}
					  	?>
					  	</td>
					  <td valign="top"><?php echo $success['webcell_title'];?></td>
					  <td valign="middle">
					      	<div align="center">
							<?php
								// Prepare guidance text for the success criteria
								if($success['webcell_text'] != "") {
									echo '<a href="javascript:void(0);" onclick="showSuccessRow(' . $success['item_id'] . '); displaySuccessGuide(' . $success['item_id'] . ');"><img src="' . $CFG->wwwroot . '/mod/rafl/images/guidance.gif" width="38" height="34" border="0" alt="View Guidance" title="View Guidance"/></a>';
								} else {
									echo '&nbsp;';
								}
							?>
					        </div>
					      </td>
					<?php if (isset($_GET['share_id'])&&$_GET['shareComment']==1){?>
					  <td valign="middle">
					    <div align="center">
						  <?php
						    if ($_GET['view'] == 'doNotSetToMentorButLetGoIntoElseClause') {
							// Evidence cohort images go cohorte0.gif, cohorte1.gif, ...
							if($success['rafl_success_evid_req'] == "1") {
								$cohort_gif_base_path = 'cohorte';
							} else {
								$cohort_gif_base_path = 'cohort';
							}
							
							// Choose coloured GIF
							if ($total_learner_ratings > 0) {
								$cohort_gif = $cohort_gif_base_path . round($total_learner_ratings / $ratings_count) . '.gif';
							} else {
								$cohort_gif = $cohort_gif_base_path . '0.gif';
							}
							
							echo '<a href="rafl_cohort.php?item_id=' . $success['item_id'] . '&share_id=' . $_GET['share_id'] . '&view=' . $_GET['view'] . '"><img src="' . $CFG->wwwroot . '/mod/rafl/main/images/' . $cohort_gif . '" alt="Cohort View" width="40" height="40" /></a>';
						    } else {
						      echo '<input id="result' . $success['item_id'] . '" type="hidden" value="' . $item_id . '" />';
					
						      if($success['rafl_success_evid_req'] == "1") {
						        if (isset($_GET['share_id'])) {
								echo '<a href="javascript:void(0);" onclick="showSuccessRow(' . $success['item_id'] . '); displayEvidence(' . $success['item_id'] . ');"><img id="glass' . $success['item_id'] . '" src="' . $evidence_path . '" alt="Upload Evidence" width="34" height="35" border="0" title="Upload Evidence" /></a>';
						        } else {
								echo '<img id="glass' . $success['item_id'] . '" src="' . $CFG->wwwroot . '/mod/rafl/main/images/' . $evidence_path . '" alt="Upload Evidence" width="34" height="34" border="0" title="Upload Evidence" />';
						        }
						      }
						    }
						  ?>
					    </div>
					  </td>
					      <td valign="middle"><div align="center">
					      <?php if (isset($_GET['share_id'])) {?>
					        <div id="rateDiv<?php echo $success['item_id']; ?>"> <strong>You need to upgrade your Flash Player</strong> </div>
					        <?php
							$Javascript = $Javascript . "var so = new SWFObject(\"" . $CFG->wwwroot . "/mod/rafl/main/flash/progressChooser.swf\", \"fooq".$success['item_id']."\", \"40\", \"40\", \"8\", \"#FFFFFF\");
							so.addVariable(\"rating\", \"".$rafl_res_rate."\");
							so.addVariable(\"icon\", \"".$icon."\");
							so.addVariable(\"success\", \"".$success['item_id']."\");
							so.addVariable(\"task\", \"1\");
							so.addVariable(\"theResult\", \"1\");
							so.write(\"rateDiv" . $success['item_id'] ."\");";
						?>
					      </div>
					      <?php } //end if?>
					      </td>
					  <?php } //isset($_GET['share_id'])?>    
				  </tr>
		<?php
		    		}
	    	?>
				</table>
	    	<?php
			// Show EACH success criteria when being clicked into
		  	foreach ($successes as $success) {
		?>
				<table cellspacing="2" class="successTable" id="successTableRow<?php echo $success['item_id']; ?>" style="display:none;" summary="Rating of a success criteria">
				 <tr>
				    <th class="task_a"><?php if ($success['rafl_collective'] != "1") {?>
				      I
				        <?php } else { ?>
				      WE
				      <?php }?>
				      CAN</th>
				    <th class="task_b">BECAUSE
				      <?php if ($success['rafl_collective'] != "1") {?>
				      I
				      <?php } else { ?>
				      WE
				      <?php }?>
				      HAVE</th>
				      <?php if (isset($_GET['share_id_not_taoc'])&&$_GET['shareComment']==1){?>
				    	<th class="who_is">WHO IS HAPPY?</th>
				      <?php } //isset($_GET['share_id'])?>    
				  </tr>
		<?php
				// Remember
		  		if (strlen($success['rafl_success_obj']) && $success['rafl_success_obj'] != '<p></p>') {
					$rafl_success_obj_remembered = $success['rafl_success_obj'];
				}
		?>
				  <tr valign="top">
					  <td valign="top">
					  	<?php
					  		if (strlen($rafl_success_obj_remembered)) {
					  			echo $rafl_success_obj_remembered;
					  		} else {
					  			echo "&nbsp;";
					  		}
					  	?>
					  	</td>
					  <td valign="top"><?php echo $success['webcell_title'];?></td>
					<?php if (isset($_GET['share_id_not_taoc'])&&$_GET['shareComment']==1){?>
					  <td valign="middle">
					    <div align="center">
						<?php
							$obj_rafl = new rafl();
							echo $obj_rafl->get_success_happy_drop_down($success['item_id'], $_GET['share_id']);
						?>
					    </div>
					  </td>
					  <?php } //isset($_GET['share_id']) ?>    
				  </tr>
				</table>
		    <?php
				// Show EACH success guidance when being clicked into
				echo '<div id="successGuide' .  $success['item_id'] . '" style="display:none;">';
				echo '	<br /><br />';
				echo '	<div class="popupHead">';
				echo '		<img src="' . $CFG->wwwroot . '/mod/rafl/images/close.gif" alt="close" onclick="open_success_table();" />';
				echo '		<p>HERE IS YOUR GUIDANCE</p>';
				echo '	</div>';
				echo '	<div id="successText">';
				echo '		<div class="guidanceContainer">' . $success['webcell_text'] . '</div>';
				echo '	</div>';
				echo '	<br /><br />';
				echo '</div>';
		    	}
		echo '<br>##' . $Javascript;
	} else {
		echo '<p>There are no success criteria for this task.</p>';
	}
?>
