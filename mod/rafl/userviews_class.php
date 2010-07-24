<?php
// Load the common classes
require_once ('includes/common/KT_common.php') ;
require_once ('includes/get_sql_value_string.php') ;

class userviews {
	var $parentData;
	var $viewRight;
	var $shareData;
	var $sharedRight;
	var $sharePublic;
	var $shareType;
	var $cohortName;
	var $shareComment = 0;
	var $shareMentor = 0;
	var $sharer = 0;
	var $creator = 0;
	var $shareMember = 0;

	function findParent($itemId, $type) {
		// Get parents data
		while ($itemId != 0) {
			$mysql = new mysqlquery();

			$query = "SELECT {$_SESSION['RealS_prefix']}members.*, {$_SESSION['RealS_prefix']}webcells.*, {$_SESSION['RealS_prefix']}items.*, {$_SESSION['RealS_prefix']}".$type.".*
					FROM {$_SESSION['RealS_prefix']}items
					INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id = {$_SESSION['RealS_prefix']}items.item_webcell
					INNER JOIN {$_SESSION['RealS_prefix']}" . $type . " ON {$_SESSION['RealS_prefix']}" . $type . "." . $type . "_item = {$_SESSION['RealS_prefix']}items.item_id
					INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}webcells.webcell_member = {$_SESSION['RealS_prefix']}members.mb_id
					WHERE {$_SESSION['RealS_prefix']}items.item_id = " . $mysql->escape_value($itemId, 'int');

			$result = $mysql->getrow($query);
			$itemId = $result['item_parent_item'];

			// Are you the creator ?
			if ($result['webcell_member'] == $_SESSION['USER']->id) {
				$this->creator = 1;
			}
		}

		$this->parentData = $result;
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Return the member name of a member id
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getMemberName($member_id) {
		$query = "SELECT
		            mb_firstname,
		            mb_surmame
		          FROM {$_SESSION['RealS_prefix']}members
		          WHERE mb_id = " . $member_id;

		$mysql = new mysqlquery;
		$result = $mysql->runsql($query);

		if ($result) {
			return $result[0]['mb_firstname'] . ' ' . $result[0]['mb_surmame'];
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Return the member ID of the share mentor, or if there isn't any, the request mentor
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getShareOrRequestMentorId($shareId) {
		$query = "SELECT
		            {$_SESSION['RealS_prefix']}share.share_member,
		            {$_SESSION['RealS_prefix']}share.share_permission,
		            sharer.mb_type
		          FROM {$_SESSION['RealS_prefix']}share
		            LEFT OUTER JOIN {$_SESSION['RealS_prefix']}members AS sharer ON sharer.mb_id = {$_SESSION['RealS_prefix']}share.share_member
		            LEFT OUTER JOIN {$_SESSION['RealS_prefix']}members AS request_mentor ON request_mentor.mb_id = {$_SESSION['RealS_prefix']}share.share_permission
		          WHERE share_id = " . $shareId;

		$mysql = new mysqlquery;
		$result = $mysql->runsql($query);

		if ($result) {
			if ($result[0]['mb_type'] == 'mentor') {
				return $result[0]['share_member'];
			} else {
				return $result[0]['share_permission'];
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Return the member ID of and individual added to a share, BUT ONLY IF IT IS A MENTOR
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function isShareCohortMemberAMentor($shareId) {
		if (! strlen($shareId)) {
			return FALSE;
		} else {
			$query = "SELECT NULL
			          FROM {$_SESSION['RealS_prefix']}share_cohort_members
			          	INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member
			          WHERE {$_SESSION['RealS_prefix']}members.mb_type = 'mentor'
			          	AND {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member = " . $_SESSION['USER']->id . "
			          	AND {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share = " . $shareId;
			// Debugging
			//die($query . '-' . $_SESSION['USER']->id);
	
			$mysql = new mysqlquery;
			$result = $mysql->runsql($query);
	
			return $result;
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Is this member id in a certain share
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function isLearnerInThisShare($userId, $shareId) {
		$query = "SELECT NULL
		          FROM {$_SESSION['RealS_prefix']}share_cohort_members
		          WHERE s_c_m_member = " . KT_escapeForSql($userId, "STRING_TYPE") . "
		          AND s_c_m_share = " . KT_escapeForSql($shareId, "STRING_TYPE");

		$mysql = new mysqlquery;
		$rows = $mysql->runsql($query);

		return count($rows);
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets member id from various sources
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getMemberId($userIdSession, $userIdQueryString, $viewQueryString) {
		if (isset($userIdQueryString)) {
			return $userIdQueryString;
		} elseif ($viewQueryString == 'mentor') {
			// Mentor has it's own cohort (= non-learner) member id
			$query = "SELECT mb_id
			          FROM {$_SESSION['RealS_prefix']}members
			          WHERE mb_type = 'cohort'
			          AND mb_school = " . GetSQLValueString($_SESSION['RealS_schoolid'], 'int');
		
			$mysql = new mysqlquery;
		
			if ($rows = $mysql->runsql($query)) {
		  		return $rows[0]['mb_id'];
		  	} else {
		  		return -1;
		  	}
		} elseif (isset($userIdSession)) {
			return $userIdSession;
		} else {
			return -1;
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets a whole member record
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getMemberData($argUserId) {
		$query = "SELECT *
		          FROM {$_SESSION['RealS_prefix']}members
		          WHERE mb_id = " . GetSQLValueString($argUserId, 'int');

		$mysql = new mysqlquery;
		return $mysql->getrow($query);
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets a subject name
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getSubjectName($arg_subject_id) {
		$mysql = new mysqlquery;

		$query = "SELECT subject_name
		          FROM {$_SESSION['RealS_prefix']}subjects
		          WHERE subject_id = " . $mysql->escape_value($arg_subject_id, 'int');

		if ($row = $mysql->getrow($query)) {
	  		return $row['subject_name'];
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets a webcell title
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getWebcellTitle($arg_item_id) {
		$mysql = new mysqlquery;

		$query = "SELECT webcell_title
		          FROM {$_SESSION['RealS_prefix']}webcells
		          	INNER JOIN {$_SESSION['RealS_prefix']}items ON item_webcell = webcell_id
		          WHERE item_id = " . $mysql->escape_value($arg_item_id, 'int');

		if ($row = $mysql->getrow($query)) {
	  		return $row['webcell_title'];
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets the standardised breadcrumb for the page header
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getFullBreadCrumb() {
		$bread_crumb = '';

		if (strlen($this->shareData['share_name'])) {
			// Shorten share name
			if (strlen($this->shareData['share_name']) > 20) {
				$bread_crumb .= substr($this->shareData['share_name'], 0, 18) . '... . ';
			} else {
				$bread_crumb .= $this->shareData['share_name'] . ' . ';
			}
		}
	
		if (strlen($this->shareData['subject_name'])) {
			$bread_crumb .= $this->shareData['subject_name'];
		}

		//$bread_crumb .= $this->parentData['webcell_title'];

  		return $bread_crumb;
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Gets a basic breadcrumb for the page header (so that a customised item title can be appended later)
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function getBasicBreadCrumb() {
		$bread_crumb = '';

		if (strlen($this->shareData['share_name'])) {
			// Shorten share name
			if (strlen($this->shareData['share_name']) > 20) {
				$bread_crumb .= substr($this->shareData['share_name'], 0, 18) . '... . ';
			} else {
				$bread_crumb .= $this->shareData['share_name'] . ' . ';
			}
		}
	
		if (strlen($this->shareData['subject_name'])) {
			$bread_crumb .= $this->shareData['subject_name'] . ' . ';
		}

  		return $bread_crumb;
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Checks whether this user has a required role, e.g. mentor, learner
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function checkRoleRights($argRoleRequired) {
		if ($_SESSION['RealS_usertype'] != $argRoleRequired) {
			echo '<script type="text/javascript">alert(\'You need to have the user role "' . $argRoleRequired . '" to be able to view this page.\')</script>';
			exit;
		}
	}

	//----------------------------------------------------------------------------------------------
	// Desc: Checks whether this item should be viewed by you and end the script, if not
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function checkViewRights() {
		//check if user allowed to view page
		switch ($this->parentData['item_access']) {
			case 3:
				//community
				if (!isset($_SESSION['USER']->id)) {
					$this->viewRight = 0;
					echo '<script type="text/javascript">alert(\'You need to log in before viewing this page.\')</script>';
					exit;
				} else {
					$this->viewRight = 1;
				}//end if (!isset($_SESSION['USER']->id))
				break;
			case 2:
				//site
				if ($_SESSION['RealS_schoolid'] != $this->parentData['item_school']) {
					$this->viewRight = 0;
					echo '<script type="text/javascript">alert(\'You need to log in and belong to a specific school before viewing this page.\')</script>';
					exit;
				} else {
					$this->viewRight = 1;
				}//end if ($_SESSION['RealS_schoolid'] != $this->parentData['item_school'])
				break;
			case 1:
				//private
				if ($_SESSION['USER']->id != $this->parentData['webcell_member']) {
					//check for pending permission request
					$query = "SELECT * FROM share WHERE share_item = ".$this->parentData['item_id'];
					$mysql = new mysqlquery;
					$preview = $mysql->runsql($query);
					if ($_SESSION['USER']->id != $preview[0]['share_permission']) {
						$this->viewRight = 0;
						echo '<script type="text/javascript">alert(\'Private page.\')</script>';
						exit;
					} else {
						// You can view this cause you have permission request
						$this->viewRight = 1;
					}
				} else {
					$this->viewRight = 1;
				}//end if ($_SESSION['USER']->id != $this->parentData['webcell_member'])
				break;
		}

	}

	//----------------------------------------------------------------------------------------------
	// Desc: Checks whether this item should be shared with you and end the script, if not
	// Depd: -
	//----------------------------------------------------------------------------------------------

	function checkSharedRights($shareId) {
		if (! strlen($this->parentData['item_id'])) {
			echo '<script type="text/javascript">alert("Error: No parent found.")</script>';
			exit;
		} else {
			// Check shareid and itemid match
			$query = "SELECT {$_SESSION['RealS_prefix']}share.*, {$_SESSION['RealS_prefix']}subjects.*, {$_SESSION['RealS_prefix']}share_type.*, {$_SESSION['RealS_prefix']}cohorts.cohort_name, {$_SESSION['RealS_prefix']}members.mb_type
			          FROM {$_SESSION['RealS_prefix']}share
	    		          	LEFT JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_member
			          	LEFT JOIN {$_SESSION['RealS_prefix']}share_cohort ON {$_SESSION['RealS_prefix']}share_cohort.share_cohort_share = {$_SESSION['RealS_prefix']}share.share_id
			          	LEFT JOIN {$_SESSION['RealS_prefix']}cohorts ON {$_SESSION['RealS_prefix']}cohorts.cohort_id = {$_SESSION['RealS_prefix']}share_cohort.share_cohort_cohort
			          	LEFT JOIN {$_SESSION['RealS_prefix']}subjects ON {$_SESSION['RealS_prefix']}share.share_subject = {$_SESSION['RealS_prefix']}subjects.subject_id
			          	LEFT JOIN {$_SESSION['RealS_prefix']}share_type ON {$_SESSION['RealS_prefix']}share_type.invite_type_id = {$_SESSION['RealS_prefix']}share.share_type
			          WHERE share_item = " . $this->parentData['item_id'] . "
			          	AND share_id = " . $shareId;

			$mysql = new mysqlquery;
			$share_data = $mysql->runsql($query);
	
			$this->shareData = $share_data[0];
			$this->sharePublic = $share_data[0]['share_public'];
			$this->shareType = $share_data[0]['invite_type_name'];
			$this->cohortName = $share_data[0]['cohort_name'];
	
			// Debugging
			//die($query);
			
			$_SESSION['RealS_usertype'] = 'learner';
	
			// Check share rights
			if (! strlen($_SESSION['USER']->id) && $this->sharePublic == 0) {
				// Not logged in and not public
				require_once("main/failed.php");
				exit(0);
			} elseif (count($this->shareData)==0) {
				// Wrong share id
				echo '<script type="text/javascript">alert(\'Wrong share id.\')</script>';
				exit;
			} elseif ($this->shareData['share_active'] == "PENDING" || $this->shareData['share_active'] == "DECLINED") {
				// Not accepted
				echo '<script type="text/javascript">alert(\'You can not view a ' . $this->shareData['share_active'] . ' share. Please check your sharing request status for this item.\')</script>';
				exit;
			} elseif (strlen($_SESSION['USER']->id)) {
				if ($this->shareData['share_member'] == $_SESSION['USER']->id) {
					// You are the sharer
					$this->shareComment = 1;
					$this->sharer = 1;
		
					if ($this->shareData['mb_type'] == 'mentor') {
						$this->shareMentor = 1;
						$_SESSION['RealS_usertype'] = 'mentor';
					}
				} else if ($this->shareData['share_permission'] == $_SESSION['USER']->id) {
					// You are the request mentor
					$this->shareComment = 1;
					$this->shareMentor = 1;
					$_SESSION['RealS_usertype'] = 'mentor';
				} else if (isset($_SESSION['USER']->id)){
					// Check if you are an additional share mentor
					$query = "SELECT {$_SESSION['RealS_prefix']}share_mentor.share_mentor_mentor, {$_SESSION['RealS_prefix']}share_mentor.share_mentor_status
					          FROM {$_SESSION['RealS_prefix']}share
					          INNER JOIN {$_SESSION['RealS_prefix']}share_mentor ON {$_SESSION['RealS_prefix']}share.share_id = {$_SESSION['RealS_prefix']}share_mentor.share_mentor_share
					          WHERE {$_SESSION['RealS_prefix']}share_mentor.share_mentor_mentor = ". $_SESSION['USER']->id ."
					          AND {$_SESSION['RealS_prefix']}share_mentor.share_mentor_share=".$shareId;
		
					// Debugging
					//die($query . '-' . $_SESSION['USER']->id);

					$mysql = new mysqlquery;
					$result = $mysql->runsql($query);
		
					if (count($result)>0) {
						// You are an additional mentor of this item
						$this->shareComment = 1;
						$this->shareMentor = 1;
						$_SESSION['RealS_usertype'] = 'mentor';

						if ($result[0]['share_mentor_mentor'] == $_SESSION['USER']->id && $result[0]['share_mentor_status'] == 'NEW') {
							// Update status
							$query = "UPDATE {$_SESSION['RealS_prefix']}share_mentor
							          SET share_mentor_status = 'OLD'
							          WHERE share_mentor_share = " . $this->shareData['share_id'] . "
							          AND share_mentor_mentor = ".$_SESSION['USER']->id;
							$mysql = new mysqlquery;
							$result = $mysql->runsql2($query);
						}
					}
				}
			}
	
			// Allow public shares
			if (strlen($_SESSION['USER']->id)) {
				// Update sharer status
				if ($this->shareData['share_member'] == $_SESSION['USER']->id && $this->shareData['share_status'] == 'NEW') {
					//update status
					$query = "UPDATE {$_SESSION['RealS_prefix']}share
					          SET share_status = 'OLD'
					          WHERE share_id = " . $this->shareData['share_id'];
					$mysql = new mysqlquery;
					$result = $mysql->runsql2($query);
				}
		
				// Check if you are part of cohort
				$query = "SELECT {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member, {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_status
				          FROM {$_SESSION['RealS_prefix']}share
				          	INNER JOIN {$_SESSION['RealS_prefix']}share_cohort_members ON {$_SESSION['RealS_prefix']}share.share_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share
				          WHERE {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member = " . $_SESSION['USER']->id . "
				          	AND {$_SESSION['RealS_prefix']}share.share_id = " . $shareId;
		
				$mysql = new mysqlquery;
				$result = $mysql->runsql($query);
		
				if (count($result)>0) {
					// This is shared with you
					$this->shareComment = 1;
					$this->shareMember = 1;

					if ($result[0]['s_c_m_member'] == $_SESSION['USER']->id && $result[0]['s_c_m_status'] == 'NEW') {
						// Update cohort member status
						$query = "UPDATE {$_SESSION['RealS_prefix']}share_cohort_members
						          SET s_c_m_status = 'OLD'
						          WHERE s_c_m_share = " . $this->shareData['share_id'] . "
						          AND s_c_m_member = ".$_SESSION['USER']->id;
		
						$mysql = new mysqlquery;
						$result = $mysql->runsql2($query);
					}
				} else if ($this->sharer == 0 && $this->shareMentor == 0 && $this->sharePublic == 0){
					// Nothing to do with you and not public
					echo '<script type="text/javascript">alert(\'You may need to login to view this page.\')</script>';
					exit;
				}
			}
		}
	}

	function checkNew() {
		//check if the mentor
		return $this->parentData['item_id']. " -- " . $this->sharePublic;
	}

}//end class


/***************
*
* DB Connection Class
*
*
*
****************/

class mysqlquery {

	var $hostname;
	var $database;
	var $username;
	var $password;
	var $dbconnect;

	var $insertID;
	var $rowcount;
	var $result;

	function mysqlquery() {
                global $CFG;

                include "Connections/smart.php";

		$this->hostname = $CFG->dbhost;
		$this->database = $CFG->dbname;
		$this->username = $CFG->dbuser;
		$this->password = $CFG->dbpass;
		$this->dbconnect = $smart;

	}//end constructor

	/******
	*
	* Queries the DB. Used for inserts or getrows.
	*
	******/

	function query ($query) {
		mysql_select_db($this->database, $this->dbconnect);
		$this->result = mysql_query($query, $this->dbconnect) or die(mysql_error());
		$this->insertID = mysql_insert_id();
	}

	/******
	*
	* Gets rows from the database based on the supplied query
	*
	******/

	function getrows ($query) {

		$this->query($query);

		$data = array();
		$rows = array();
		while($data = mysql_fetch_assoc($this->result)){
			array_push($rows, $data);
        }//end while

		$this->rowcount = count($rows);

		return $rows;
	}

	/******
	*
	* Gets a single row from the database based on the supplied query
	*
	******/

	function getrow ($query) {
		$rows = $this->getrows ($query);
		return $rows[0];
	}

	/******
	*
	* Tidys a value for use in an SQL statement
	*
	******/

	function escape_value($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {

		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

		switch ($theType) {
			case "htmlcolour":
				
				$theValue = strtoupper((string)$theValue);
				$strLength = strlen($theValue);
				if ($strLength < 6) {
					$zeros = 6 - $strLength;
					while ($zeros > 0) {
						$theValue = "0".$theValue;
						$zeros--;
					}
				}
				$theValue = "'" . $theValue . "'";
				break;    
			case "blanktext":
				$theValue = "'" . trim ($theValue) . "'";
				break;
			case "text":
				$theValue = trim ($theValue);
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
				break;
			case "long":
			case "int":
				$theValue = ($theValue != "") ? intval($theValue) : "NULL";
				break;
			case "double":
				$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
				break;
			case "date":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
				break;
			case "defined":
				$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
				break;
		}
		return $theValue;
	}



	// kept for legacy reasons
	function runsql($query) {
		return $this->getrows ($query);
	}// runsql

	// kept for legacy reasons
	function runsql2($query) {
		$this->query ($query);
	}// runsql



}//end class
?>