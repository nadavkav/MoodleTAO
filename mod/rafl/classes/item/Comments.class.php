<?php
	/**
	* 	Class to administer item {$_SESSION['RealS_prefix']}comments.
	*
	* 	@author 	Joel Rowbottom <joel@wrenthorpe.net>
	*/
	
	class rs_itemComments {
		private $mysql;
		private $share_id;
		private $editor;
		private $editor2;
		private $item_type_id;
		private $item_type;
		private $unit_item_id;
		private $comment_parent_item_id;
	
		/**
		*	Basic class initialisation
		* 	@param 	mysqlquery $mysql	MySQL Query Object
		* 	@return void
		*/
		public function __construct ($mysql, $arg_item_type_id, $arg_item_type, $arg_share_id, $arg_unit_item_id, $arg_comment_parent_item_id, $arg_css_path) {
			$this->mysql = $mysql;
			$this->share_id = $arg_share_id;
			$this->unit_item_id = $arg_unit_item_id;
			$this->comment_parent_item_id = $arg_comment_parent_item_id;
			$this->item_type = $arg_item_type;
			$this->item_type_id = $arg_item_type_id;

			// Prepare the text editor
			$this->editor = new textEditor();
			$this->editor->setEditorType('full');
			$this->editor->setFormField('webcell_text');
			$this->editor->setWidth('550px');
			$this->editor->setHeight('265px');
			$this->editor->setCssPath($arg_css_path);
		
			$this->editor2 = new textEditor();
			$this->editor2->setEditorType('full');
			$this->editor2->setFormField('editcell_text');
			$this->editor2->setWidth('550px');
			$this->editor2->setHeight('265px');
			$this->editor2->setCssPath($arg_css_path);

			//$this->renderSupportingJavascript();
		}
	
		/**
		* 	Render form
		*/
		public function renderCommentSubmissionForms() {
                        global $CFG;
			$KTColParam2_rs_quote="-1";
			if (isset($_GET["quote"])) {
			  $KTColParam2_rs_quote = (get_magic_quotes_gpc()) ? $_GET["quote"] : addslashes($_GET["quote"]);
			}
			$query_rs_quote = sprintf("SELECT {$_SESSION['RealS_prefix']}webcells.*, {$_SESSION['RealS_prefix']}comments.*, {$_SESSION['RealS_prefix']}members.mb_firstname, {$_SESSION['RealS_prefix']}members.mb_surmame, {$_SESSION['RealS_prefix']}items.*
			                           FROM ((({$_SESSION['RealS_prefix']}items
			                           	LEFT JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id={$_SESSION['RealS_prefix']}items.item_webcell)
			                           	LEFT JOIN {$_SESSION['RealS_prefix']}comments ON {$_SESSION['RealS_prefix']}comments.comment_item={$_SESSION['RealS_prefix']}items.item_id)
			                           	LEFT JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id={$_SESSION['RealS_prefix']}webcells.webcell_member)
			                           WHERE {$_SESSION['RealS_prefix']}items.item_id=%s
			                           ORDER BY {$_SESSION['RealS_prefix']}comments.comment_date DESC", GetSQLValueString($KTColParam2_rs_quote, "int"));
		
			$rs_quote = mysql_query($query_rs_quote) or die(mysql_error());
			$row_rs_quote = mysql_fetch_assoc($rs_quote);
			$totalRows_rs_quote = mysql_num_rows($rs_quote);
?>
			<style type="text/css">
				#add_comment {
				  z-index: 10000;
				  display: none;
				  overflow: hidden;
				  width: 0px;
				  height: 0px;
				  position: absolute;
				  background-color: #FFFFFF;
                                  border: 1px solid #000000;
				}
				
				#edit_comment {
				  z-index: 10000;
				  display: none;
				  overflow: hidden;
				  width: 0px;
				  height: 0px;
				  position: absolute;
				  background-color: #FFFFFF;
                                  border: 1px solid #000000;
				}
				
				div#black {
				  /* background-color: #000000; */
				  display:none;
				  position: absolute;
				  z-index: 99;
				  width:200px;
				  height:20px;
				}
				
				div.opaque {
				  /* opacity:.7; */
				  /* filter:alpha(opacity=70); */
				}

				div.comment_popup_head {
				  background-color: #ad211b;
				  padding: 5px;
				}
				
				div.comment_popup_head p {
				  margin: 0;
				  padding: 1px 0 2px 0;
				  color: #ffffff;
				  font-weight: bold;
				  font-size: 13px;
				}
				
				div.comment_popup_head img {
				  float: right;
				  cursor: pointer;
				}
				
				p.comment_popup_title {
				  margin-top: 9px;
				  margin-bottom: 3px;
				  font-size: 12px;
				  font-weight: normal;
				}
				
				input.add_comment {
					width: 636px;
				}
			</style>
	
			<div id="add_comment">
				<div class="comment_popup_head"><img src="<?php echo $CFG->wwwroot ?>/mod/rafl/images/close.gif" alt="close" onclick="cancel_editor(<?php echo $this->editor->getContentViaJavascript(); ?>);"/><p>Add Comment</p></div>
				<p class="comment_popup_title">Title: </p>
				<input name="webcell_title" id="webcell_title" type="text" class="add_comment" value="<?php echo (isset($_POST['webcell_title']) ? $_POST['webcell_title'] : ''); ?>" style="width: 544px;">
				<p class="comment_popup_title">Comment:</p>
				<?php
					$text_area_content = '';
					
                                        // Gimme content for the editor
                                        if (isset($_GET['quote']) && strlen($_GET['quote'])) {
                                            $text_area_content = htmlentities("<h2 class=\"quote2\">Quote</h2><div class=\"quote\" align=\"centre\"><p>". $row_rs_quote['mb_firstname'] ." ". $row_rs_quote['mb_surmame'] . " said:</p>". $row_rs_quote['webcell_text']."</div><br />", ENT_QUOTES);
                                        } elseif (isset($_POST['webcell_text']) && strlen($_POST['webcell_text'])) {
                                            $text_area_content = htmlentities($_POST['webcell_text'], ENT_QUOTES);
                                        } else {
                                            $text_area_content = '';
                                        }
				
					// Gimme the text editor
					$this->editor->setContent($text_area_content);

					// Only logged in users can contribute
					if (strlen($_SESSION['USER']->id)) {
						echo $this->editor->getEditor();
					}
				?>
				<input type="button" value="Add my comment" onclick="saveComment('insert', <?php echo $this->unit_item_id; ?>, document.getElementById('insert_item_id').value, '', <?php echo $this->editor->getContentViaJavascript(); ?>, document.getElementById('webcell_title').value);"/>
				<input type="button" value="Cancel" onclick="cancel_editor(<?php echo $this->editor->getContentViaJavascript(); ?>);" />
				<input type="hidden" name="item_id" id="insert_item_id" value="" />
			</div>
			
			<div id="edit_comment">
				<div class="comment_popup_head"><img src="<?php echo $CFG->wwwroot ?>/mod/rafl/images/close.gif" alt="close" onclick="cancel_editor(<?php echo $this->editor2->getContentViaJavascript(); ?>);"/><p>Edit Comment</p></div>
				<p class="comment_popup_title">Title: </p>
				<input name="editcell_title" id="editcell_title" type="text" class="add_comment" value="<?php echo (isset($_POST['webcell_title']) ? $_POST['webcell_title'] : ''); ?>" style="width: 544px;">
				<p class="comment_popup_title">Comment:</p>
				<?php
					if (isset($_GET['quote'])) {
						$text_area_content = htmlentities("<h2 class=\"quote2\">Quote</h2><div class=\"quote\" align=\"centre\"><p>". $row_rs_quote['webcell_text']."</div><br />", ENT_QUOTES);
					} else {
						$text_area_content = htmlentities($row_rs_quote['webcell_text'], ENT_QUOTES);
					}
				
					// Gimme the text editor
					$this->editor2->setContent($text_area_content);

					// Only logged in users can contribute
					if (strlen($_SESSION['USER']->id)) {
						echo $this->editor2->getEditor();
					}
				?>
				<input type="button" value="Edit my comment" onclick="saveComment('update', <?php echo $this->unit_item_id; ?>, document.getElementById('insert_item_id').value, document.getElementById('comment_item_id').value, <?php echo $this->editor2->getContentViaJavascript(); ?>, document.getElementById('editcell_title').value);"/>
				<input type="button" value="Cancel" onclick="cancel_editor(<?php echo $this->editor2->getContentViaJavascript(); ?>);"/>
				<input type="hidden" name="comment_item_id" id="comment_item_id" value="" />
			</div>
			
			<div id="black" class="opaque"></div>
<?php
		}

		/**
		* 	Get add comment link
		*/
		public function getAddCommentLink($arg_link_text) {
			return '<div class="comment_add">
			        	<a href="#" title="add comment" onclick="displayComment(' . $this->comment_parent_item_id . ');">' . $arg_link_text . '</a>
			        </div>';
		}

		/**
		* 	Get add comment top menu option
		*/
		//public function getAddCommentMenuOption($arg_link_text) {
		//	return '<option><optiontext>' . $arg_link_text . '</optiontext><optionurl>javascript:displayComment(' . $this->comment_parent_item_id . ');</optionurl></option>';
		//}

		/**
		* 	Process comment insert
		*/
		public function processCommentInsert() {
                        global $CFG;
			// Default
			if (strlen($_POST['webcell_title'])) {
				$webcell_title = $_POST['webcell_title'];
			} else {
				$webcell_title = 'Comment';
			}

			$mysql = new mysqlquery();

			$sql = "INSERT INTO {$_SESSION['RealS_prefix']}webcells (
			        	webcell_id,
			        	webcell_title,
			        	webcell_text,
			        	webcell_member,
			        	webcell_school
			        ) VALUES (
			        	NULL,
			        	" . $mysql->escape_value($webcell_title, 'text') . ",
			        	" . $mysql->escape_value($_POST['webcell_text'], 'text') . ",
			        	" . $_SESSION['USER']->id . ",
			        	" . $_SESSION['RealS_schoolid'] . "
			        )";

			// Debugging
			//echo $sql;

			$mysql->query($sql);
			$insert_webcell_id = $mysql->insertID;

			$sql = "INSERT INTO {$_SESSION['RealS_prefix']}items (
			        	item_id,
			        	item_webcell,
			        	item_parent_item,
			        	item_school,
			        	item_default_type
			        ) VALUES (
			        	NULL,
			        	" . $insert_webcell_id . ",
			        	" . $mysql->escape_value($this->comment_parent_item_id, 'int') . ",
			        	" . $_SESSION['RealS_schoolid'] . ",
			        	9
			        )";

			// Debugging
			//echo $sql;

			$mysql->query($sql);
			$insert_item_id = $mysql->insertID;

			$sql = "INSERT INTO {$_SESSION['RealS_prefix']}comments (
			        	comment_id,
			        	comment_item,
			        	comment_share,
			        	comment_date,
			        	comment_report
			        ) VALUES (
			        	NULL,
			        	" . $insert_item_id . ",
			        	" . $mysql->escape_value($_POST['share_id'], 'int') . ",
			        	NOW(),
			        	0
			        )";

			// Debugging
			//echo $sql;

			$mysql->query($sql);
		
			// Save comment member statuses, i.e. make this comment unread for all share cohort members
			$obj_count = new commentEvidenceCount();
			$obj_count->increaseCommentCounter($this->item_type_id, $this->unit_item_id, $insert_item_id, $_POST['share_id']);
		}

		/**
		* 	Process comment update
		*/
		public function processCommentUpdate() {
                        global $CFG;
			// Default
			if (strlen($_POST['webcell_title'])) {
				$webcell_title = $_POST['webcell_title'];
			} else {
				$webcell_title = 'Comment';
			}

			$mysql = new mysqlquery();

			$query = "SELECT webcell_id
			          FROM {$_SESSION['RealS_prefix']}webcells, {$_SESSION['RealS_prefix']}items
			          WHERE item_id = " . $mysql->escape_value($_POST['comment_item_id'], 'int') . "
			          	AND item_webcell=webcell_id
			          	AND webcell_member=" . $_SESSION['USER']->id;

			// Debugging
			//echo $query;

			$comments = $mysql->runsql($query);

			if (count($comments) > 0) {
				$query="UPDATE {$_SESSION['RealS_prefix']}webcells SET
				        	webcell_text = " . $mysql->escape_value($_POST['webcell_text'], 'text') . ",
				        	webcell_title = " . $mysql->escape_value($webcell_title, 'text') . "
				        WHERE webcell_id = " . $comments[0]['webcell_id'];

				// Debugging
				//echo $query;

				$mysql->query($query);
	
				// Save comment member statuses, i.e. make this comment unread for all share cohort members
				$obj_count = new commentEvidenceCount();
				$obj_count->increaseCommentCounter($this->item_type_id, $this->unit_item_id, $_POST['comment_item_id'], $_POST['share_id']);
			}
		}
	
		/**
		* 	Render comments
		* 	@return string			XHTML item
		*/
		public function getCommentHtml($shareComment=FALSE) {
                        global $CFG;
			$mysql = new mysqlquery();

			// WORKAROUND: Allow the page to accessed both ways
			if (! $_GET) {
				$_GET = $_POST;
			}

			// Get a list of comments and put them in an array called ${$_SESSION['RealS_prefix']}comments.
			$query = "SELECT {$_SESSION['RealS_prefix']}webcells.*, {$_SESSION['RealS_prefix']}comments.*, {$_SESSION['RealS_prefix']}members.*, {$_SESSION['RealS_prefix']}items.item_parent_item, {$_SESSION['RealS_prefix']}items.item_default_type
					FROM {$_SESSION['RealS_prefix']}items
						LEFT JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id={$_SESSION['RealS_prefix']}items.item_webcell
						LEFT JOIN {$_SESSION['RealS_prefix']}comments ON {$_SESSION['RealS_prefix']}comments.comment_item={$_SESSION['RealS_prefix']}items.item_id
						LEFT JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id={$_SESSION['RealS_prefix']}webcells.webcell_member
					WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . $mysql->escape_value($this->comment_parent_item_id, 'int') . "
						AND {$_SESSION['RealS_prefix']}items.item_default_type = 9
						AND {$_SESSION['RealS_prefix']}comments.comment_share = " . $mysql->escape_value($this->share_id, 'int') . "
					ORDER BY {$_SESSION['RealS_prefix']}comments.comment_date DESC";

			// Debugging
			//echo $query;

			$comments = $mysql->runsql($query);

			$html = '';

			if (count($comments) > 0) {
				//$this->renderSupportingJavascript();
		
				$userview = new userviews;
				//$mentorMemberId = $userview->getShareOrRequestMentorId($this->share_id);
				//$mentorName = $userview->getMemberName($mentorMemberId);
		
				$i = 1;
		
				foreach($comments as $comment) {
					// Return the number of ratings for this item
					$query="select count(*) as itemCount
							from {$_SESSION['RealS_prefix']}item_rating
							where ir_itemid=".$comment['comment_item'];
					$mysql=new mysqlquery;
					$ratings=$mysql->runsql($query);
					$itemRating=$ratings[0]['itemCount'];
		
					$mentorId=0;
		
					// Do highlight table
					$html .= "<table";
					//if ( $_GET['item_highlight'] === $comment['comment_item'] ) {
					//	$html .= " style=\"border: 3px solid red;\"";
					//}
					$html .= "><tr><td>";
		
					$reportQuery="select count(*) as reportCount
							from {$_SESSION['RealS_prefix']}item_reports
							where ir_itemid=".$comment['comment_item']."
								and ir_status!='DECLINED'";
					$reportResult=$mysql->runsql($reportQuery);
					$itemReports=$reportResult[0]['reportCount'];
		
					$html .= "\n\n<!-- Comment ".$comment['comment_item']." start -->\n\n";
		
					if ( ( $itemReports == 0 ) || ( isset($_GET['viewall']) ) ) {
						$html .= "<div class=\"profile\">";
						
						if ( $comment['mb_pic'] !="" ) {
							$html .= "<img src=\"".$CFG->wwwroot."/mod/rafl/st_pictures/".$comment['mb_pic']."\" width=\"62\" height=\"62\" alt=\"".$comment['mb_firstname']." ".$comment['mb_surmame']."\" />";
						} else {
							$html .= "<img src=\"".$CFG->wwwroot."/mod/rafl/images/learner.gif\" width=\"62\" height=\"62\" alt=\"".$comment['mb_firstname']." ".$comment['mb_surmame']."\" />";
						}

						$html .= "</div>";

						$html .= '<div class="comment_content" id="com_' . $i . '">';
						$html .= '<div class="comment_title">' . $comment['webcell_title'] . '</div>';
	
						// Stop public pages from having any comment functions
						if (strlen($_SESSION['USER']->id)) {
							// Rate this
							if ($comment['mb_id'] != $_SESSION['USER']->id) {
								// Do review smiley
								$html .= "<div class=\"comment_review\"><p>";
								$html .= "<span id=\"rating".$comment['comment_item']."\">".$itemRating."</span>";
								$html .= "<a href=\"#rate\" title=\"I found this useful\" onclick=\"rateItem(".$comment['comment_item'].",".$_SESSION['USER']->id.");\"><img src=\"".$CFG->wwwroot."/mod/rafl/images/review.gif\" width=\"18\" height=\"16\" alt=\"Review\" style=\"margin-left:4px;\" /></a>";
								$html .= "</p></div>";
							}
						}
	
						$html .= '<div class="comment_info">';
							$html .= '<span class="member_name">';
							$html .= date('d.m.y | H:i', strtotime($comment['comment_date']));
						$html .= '</div>';
						$html .= '<div class="comment_webcell" id="body' . $i . '">' . $comment['webcell_text'] . '</div>';

						// Stop public pages from having any comment functions
						if (strlen($_SESSION['USER']->id)) {
							$html .= '<div class="comment_tools">';
							$html .= '<p id="close_' . $i . '" style="display: block;">';

							$comment_tool_items = array();

							// Rate this
							if ($comment['mb_id'] != $_SESSION['USER']->id) {
								$comment_tool_items[] = '<a href="#rate" title="I found this useful" onclick="rateItem(' . $comment['comment_item'] . ',' . $_SESSION['USER']->id . ');">I found this useful</a>';
							}

							// Comment on this
							if ($shareComment == 1){
								if ($comment['mb_id'] == $_SESSION['USER']->id) {
									$add_phrase = 'add to my comment';
								} else {
									$add_phrase = 'add to this comment';
								}

								$comment_tool_items[] = '<a href="#" title="quote" onclick="displayComment(' . $this->comment_parent_item_id . '); quoteItem('. $comment['comment_item'] . ');">' . $add_phrase . '</a>';
							}

							// Edit/delete or report
							if ($comment['mb_id'] == $_SESSION['USER']->id) {
								// Edit and delete is only shown if you were the author of this comment
								$comment_tool_items[] = "<a href=\"#\" title=\"edit\" onclick=\"displayEdit(".$this->comment_parent_item_id."); editComment(".$comment['comment_item'].");\">edit my comment</a>";
								$comment_tool_items[] = "<a href=\"#\" title=\"delete\" onclick=\"deleteItem(".$comment['comment_item'].",".$i.");\">delete my comment</a>";
							} else {
								// We only show report if there is a mentor in charge of this share
								//if ( $mentorMemberId !== FALSE && $shareComment == 1) {
									//$comment_tool_items[] = "<a href=\"#\" title=\"report\" onclick=\"reportItem(".$comment['comment_item'].",".$_SESSION['USER']->id.",".$comment['comment_share'].",".$mentorMemberId.",".$i.");\">report this comment to " . $mentorName . "</a>";
								//}
							}
							
							$html .= implode(' | ', $comment_tool_items);
							$html .= '</p>';
							$html .= '</div>';
						}

						$html .= '</div>';
					} else {
						$html .= "This comment has been reported.";
					}
		
					$html .= "\n\n<!-- Comment ".$comment['comment_item']." end -->\n\n";
		
					// End highlight table
					$html .= "</td></tr></table>";
		
					// Do dotted line
					$html .= "<div class=\"comment\"></div>";
		
					$comment_ids_viewed[] = $comment['comment_item'];
		
					// The comments is getting viewed now on this page, so reset the "unviewed" counter
					//$obj_count = new commentEvidenceCount();
					//$obj_count->resetCommentCounter($comment['comment_item']);
		
		    			$i++;
				}
			}

                        $comment_html = '';

			if ($shareComment == 1){
				$comment_html .= $this->getAddCommentLink('Add a comment') . '<br />';
			}
		
			if (strlen($html)){
				$comment_html .= $html;
		
				if ($shareComment == 1){
					$comment_html .= $this->getAddCommentLink('Add a comment') . '<br />';
				}
			}
		
			if (strlen($this->comment_parent_item_id)) {
				$comment_html = '<div id="comment_container_' . $this->comment_parent_item_id . '">' . $comment_html . '</div>';
			}

			return $comment_html;
		}

		/**
		* 	Render comments javascript
		* 	WORKAROUND: Include javascript on page, cos we need to code some PHP within it
		*/
		public function renderSupportingJavascript() {
                        global $CFG;
			// WARNING: Make sure the javascript library only gets outputted once on the page
			//if (! strpos(ob_get_contents(), 'comment.css')) {
			//	ob_start();
?>
				<link type="text/css" rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/mod/rafl/layouts/comment.css" />
				<script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/mod/rafl/javascripts/util.js"></script>
				<script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/mod/rafl/ajax/jquery/jquery-1.2.6.min.js"></script>
		
				<script type="text/javascript">
					var myWidth = 0, myHeight = 0, myScroll = 0; myScrollWidth = 0; myScrollHeight = 0;
					
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

					function saveComment(arg_action, arg_item_id, arg_comment_parent_item_id, arg_comment_item_id, webcellText, webcellTitle) {
						//alert(arg_item_id + '-' + arg_comment_parent_item_id + '-' + webcellText);
						close_editor();

						if (document.getElementById('loading')) {
							document.getElementById('loading').style.display = 'block';
						}
				
						$.ajax({
							type: 'POST',
							url: '<?php echo $CFG->wwwroot ?>/mod/rafl/store_comment.php',
							data: 'action=' + arg_action + '&item_type_id=<?php echo $this->item_type_id;?>&item_type=<?php echo $this->item_type;?>&share_id=<?php echo $this->share_id;?>&unit_item_id=' + arg_item_id + '&comment_parent_item_id=' + arg_comment_parent_item_id + '&comment_item_id=' + arg_comment_item_id + '&webcell_text=' + encodeURIComponent(webcellText) + '&webcell_title=' + encodeURIComponent(webcellTitle) + '&mb_id=<?php echo $_GET['mb_id'];?>',
							timeout: 20000,
							success: function(arg_content) {
								//alert(arg_content);
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
								document.getElementById('comment_container_' + arg_comment_parent_item_id).innerHTML = arg_content;
							},
							error: function(request, errorType, errorThrown) {
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
								alert('Unfortunately, your comment might not have saved correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
							}
						});
					}
		
					function displayComment (arg_insert_item_id) {
						if (<?php echo $this->editor->getEditorReadyToUseJavascriptVariable(); ?>) {
							hideObjects();
							//document.getElementById('add_comment').innerHTML = "<img src=\"/mod/rafl/main/images/loading.gif\" alt=\"loading\" />";
							getSize();
							myLeft = (myWidth-550)/2;
							myTop = (myHeight-1050)/2;
							document.getElementById('black').style.width=myWidth+'px';
							document.getElementById('black').style.height=myHeight+'px';
							document.getElementById('black').style.top='-420px';
							document.getElementById('black').style.left='0px';
							document.getElementById('black').style.display='block';
							document.getElementById('add_comment').style.display='block';
							document.getElementById('add_comment').style.width='550px';
							document.getElementById('add_comment').style.height='408px';
							document.getElementById('add_comment').style.top=parseInt(myTop)+'px';
							document.getElementById('add_comment').style.left=parseInt(myLeft)+'px';
							//document.getElementById('add_comment').style.overflow='auto';
							document.getElementById('add_comment').style.padding='5px';
							document.getElementById('add_comment').style.display='block';
							document.getElementById('insert_item_id').value = arg_insert_item_id;
					                empty_editors();
						} else if(confirm('The web editor doesn\'t seem to have loaded properly. Do you want to refresh the page?')) {
							window.location.reload();
						}
					}
					
					function displayEdit (arg_insert_item_id) {
						if (<?php echo $this->editor->getEditorReadyToUseJavascriptVariable(); ?>) {
							hideObjects();
							getSize();
							myLeft = (myWidth-550)/2;
							myTop = (myHeight-1050)/2;
						  	document.getElementById('black').style.width=myWidth+'px';
							document.getElementById('black').style.height=myHeight+'px';
							document.getElementById('black').style.top='-420px';
							document.getElementById('black').style.left='0px';
							document.getElementById('black').style.display='block';
							document.getElementById('edit_comment').style.width='550px';
							document.getElementById('edit_comment').style.height='408px';
							document.getElementById('edit_comment').style.top=parseInt(myTop)+'px';
							document.getElementById('edit_comment').style.left=parseInt(myLeft)+'px';
							//document.getElementById('edit_comment').style.overflow='auto';
							document.getElementById('edit_comment').style.display='block';
							document.getElementById('edit_comment').style.padding='5px';
							document.getElementById('insert_item_id').value = arg_insert_item_id;
					                empty_editors();
						} else if(confirm('The web editor doesn\'t seem to have loaded properly. Do you want to refresh the page?')) {
							window.location.reload();
						}
					}

					function empty_editors() {
				                var content = '';
						<?php echo $this->editor->setContentViaJavascript('content'); ?>;
						<?php echo $this->editor2->setContentViaJavascript('content'); ?>;
						document.getElementById('webcell_title').value = '';
						document.getElementById('editcell_title').value = '';
					}

					function cancel_editor(arg_content) {
						if (arg_content.length == 0) {
							close_editor();
						} else if (confirm('This will close the editor WITHOUT SAVING. Is this ok?')) {
							close_editor();
						}
					}
				
					function close_editor() {
						document.getElementById('black').style.display='none'
						//document.getElementById('add_comment').style.overflow='hidden';
						document.getElementById('add_comment').style.width='0px';
						document.getElementById('add_comment').style.padding='0px';
						document.getElementById('add_comment').style.left='-6000px';
						document.getElementById('add_comment').style.display='none'
						//document.getElementById('edit_comment').style.overflow='hidden';
						document.getElementById('edit_comment').style.width='0px';
						document.getElementById('edit_comment').style.padding='0px';
						document.getElementById('edit_comment').style.left='-6000px';
						document.getElementById('edit_comment').style.display='none';
						showObjects();
					}
			
					// itemId = item in item table
					// memberId = member reporting the item
					// mentorId = mentor who the item is being reported to
					// enumId = count on the page (for closing the comment)
					
					function editComment(itemId) {
						if (document.getElementById('loading')) {
							document.getElementById('loading').style.display = 'block';
						}

						$.ajax({
							type: 'GET',
							url: '<?php echo $CFG->wwwroot ?>/mod/rafl/itemBody.php',
							data: 'itemId=' + itemId + '&context=edit&randomId=<?php echo mt_rand(0, 10000); ?>',
							timeout: 20000,
							success: function(arg_content) {
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}

								document.getElementById("comment_item_id").value = itemId;
								result = arg_content.split('--#--');
								//alert(result[0]);
						                var content = result[0]
								<?php echo $this->editor2->setContentViaJavascript('content'); ?>;
								document.getElementById("editcell_title").value = result[1];
								document.getElementById("editcell_title").focus();
							},
							error: function(request, errorType, errorThrown) {
								alert('Unfortunately, your comment could not be retrieved. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
							}
						});
					}
					
					// Delete an item AJAX call
					// Joel Rowbottom <joel@jml.net>
					
					// itemId = item in item table
					// enumId = count on the page (for closing the comment)
					function deleteItem(itemId,enumId) {
						if (confirm('Are you sure you want to DELETE this comment?')) {
							if (document.getElementById('loading')) {
								document.getElementById('loading').style.display = 'block';
							}
	
							$.ajax({
								type: 'GET',
								url: '<?php echo $CFG->wwwroot ?>/mod/rafl/itemDelete.php',
								data: 'itemId=' + itemId + '&share_id=<?php echo $this->share_id; ?>',
								timeout: 20000,
								success: function(arg_content) {
									//alert(arg_content);
									if (document.getElementById('loading')) {
							            		document.getElementById('loading').style.display ='none';
							            	}
	
									thisLabel=document.createTextNode("Item has been deleted.");
									clearInnerHTML(document.getElementById("com_"+enumId));
									document.getElementById("com_"+enumId).appendChild(thisLabel);
									alert('This item has been deleted.');
								},
								error: function(request, errorType, errorThrown) {
									alert('Unfortunately, your comment might not have deleted correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
									if (document.getElementById('loading')) {
							            		document.getElementById('loading').style.display ='none';
							            	}
								}
							});
						}
					}
			
					function rateItem(itemId,memberId) {
						if (document.getElementById('loading')) {
							document.getElementById('loading').style.display = 'block';
						}

						$.ajax({
							type: 'GET',
							url: '<?php echo $CFG->wwwroot ?>/mod/rafl/pageRating.php',
							data: 'memberId=' + memberId + '&itemId=' + itemId,
							timeout: 20000,
							success: function(arg_content) {
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}

								divElement = document.getElementById("rating"+itemId);
								divElement.innerHTML = arg_content;
							},
							error: function(request, errorType, errorThrown) {
								alert('Unfortunately, your rating might not have saved correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
							}
						});
					}
			
					// itemId = item in item table
					// memberId = member reporting the item
					// mentorId = mentor who the item is being reported to
					// enumId = count on the page (for closing the comment)
					function reportItem(itemId,memberId,shareId,mentorId,enumId) {
						if (document.getElementById('loading')) {
							document.getElementById('loading').style.display = 'block';
						}

						$.ajax({
							type: 'GET',
							url: '<?php echo $CFG->wwwroot ?>/mod/rafl/itemReport.php',
							data: 'memberId=' + memberId + '&itemId=' + itemId + '&mentorId=' + mentorId + '&shareId=' + shareId,
							timeout: 20000,
							success: function(arg_content) {
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}

								thisLabel=document.createTextNode("Item has been reported.");
								clearInnerHTML(document.getElementById("com_"+enumId));
								document.getElementById("com_"+enumId).appendChild(thisLabel);
								alert('This item has been reported.');
							},
							error: function(request, errorType, errorThrown) {
								alert('Unfortunately, your report might not have saved correctly. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
							}
						});
					}
					
					function quoteItem(itemId) {
						if (document.getElementById('loading')) {
							document.getElementById('loading').style.display = 'block';
						}

						$.ajax({
							type: 'GET',
							url: '<?php echo $CFG->wwwroot ?>/mod/rafl/itemBody.php',
							data: 'itemId=' + itemId + '&context=quote',
							timeout: 20000,
							success: function(arg_content) {
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}

						                var content = arg_content;
								<?php echo $this->editor->setContentViaJavascript('content'); ?>;
								document.getElementById("webcell_title").focus();
							},
							error: function(request, errorType, errorThrown) {
								alert('Unfortunately, your comment could not be retrieved. Please check your Internet connection. Should this keep happening even with a live Internet connection, please contact support@smartassess.com and quote: \n\n1. Error time: <?php echo gmdate(DATE_RFC822); ?> \n2. Error type: ' + errorType + ' \n3. Error: ' + errorThrown + ' \n4. User id: <?php echo $_SESSION['USER']->id; ?> \n5. Web page: http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
								if (document.getElementById('loading')) {
						            		document.getElementById('loading').style.display ='none';
						            	}
							}
						});
					}
				</script>
<?php
			//}
		}
	}
?>
