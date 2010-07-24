<?php
	//----------------------------------------------------------------------------------------------
	// Desc: Administer comment/evidence member counts (the count that is displayed next to the subject/unit names in the dock)
	// Depd: Comment must already have been created and the LAST one
	// Auth: Daniel Dammann <dan@smartassess.com>
	//----------------------------------------------------------------------------------------------

	class commentEvidenceCount {
		//----------------------------------------------------------------------------------------------
		// Desc: Save comment member statuses (= unread)
		// Depd: -
		//----------------------------------------------------------------------------------------------

		public function increaseCommentCounter($argItemTypeId, $argUnitItemId, $argCommentItemId, $argShareId) {
			// Notify users (different rules for rafl comments)
			if ($argItemTypeId == 1) {
				if ($_SESSION['RealS_usertype'] == 'mentor' && $_POST['mb_id']) {
					// Notify the selected share member from the rafl_overview page (= querystring posted into store_comment.php)
					$sql = "SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
					        FROM {$_SESSION['RealS_prefix']}share
					        	INNER JOIN {$_SESSION['RealS_prefix']}share_cohort_members ON {$_SESSION['RealS_prefix']}share.share_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share
					        	INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member
					        WHERE {$_SESSION['RealS_prefix']}members.mb_type IN ('learner', 'mentor')
					        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
					        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id . "
					                AND s_c_m_member = " . GetSQLValueString($_POST['mb_id'], "int");
				} else {
					// Notify request and share mentor
					// WARNING: even RealS_usertype == 'mentor' can be learners when entering comments at {$_SESSION['RealS_prefix']}rafl.php
					$sql = "SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
					        FROM {$_SESSION['RealS_prefix']}share
					        	INNER JOIN {$_SESSION['RealS_prefix']}share_cohort_members ON {$_SESSION['RealS_prefix']}share.share_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share
					        	INNER JOIN {$_SESSION['RealS_prefix']}members ON ({$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_member OR {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_permission)
					        WHERE {$_SESSION['RealS_prefix']}members.mb_type IN ('mentor')
					        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
					        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id . "
					        UNION
					        SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
					        FROM {$_SESSION['RealS_prefix']}share
					        	INNER JOIN {$_SESSION['RealS_prefix']}share_mentor ON share_mentor_share = share_id
					        	INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = share_mentor_mentor
					        WHERE {$_SESSION['RealS_prefix']}members.mb_type IN ('mentor')
					        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
					        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id;
				}
			} else {
				// Notify request and share mentor and all share members
				$sql = "SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
				        FROM {$_SESSION['RealS_prefix']}share
				        	INNER JOIN {$_SESSION['RealS_prefix']}share_cohort_members ON {$_SESSION['RealS_prefix']}share.share_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share
				        	INNER JOIN {$_SESSION['RealS_prefix']}members ON ({$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_member OR {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_permission OR {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member)
				        WHERE {$_SESSION['RealS_prefix']}members.mb_type IN ('learner', 'mentor')
				        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
				        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id;
			}

			$mysql = new mysqlquery;
			$members = $mysql->getrows($sql);

			foreach ($members as $member) {
				// Avoid duplicates, cos comments can get updated
				$sql = "SELECT NULL
				        FROM {$_SESSION['RealS_prefix']}item_view_status
				        WHERE mb_id_writer = " . $_SESSION['USER']->id . "
				        	AND mb_id_viewer = " . $member['mb_id'] . "
				        	AND item_id_comment_evidence = " . GetSQLValueString($argCommentItemId, "int");
		
				$duplicates = $mysql->getrows($sql);
	
				if (! $duplicates) {
					// Insert status for the unit item that the comment was for
					$sql = "INSERT INTO {$_SESSION['RealS_prefix']}item_view_status (
					        	item_view_status_id,
					        	item_type_id,
					        	mb_id_writer,
					        	mb_id_viewer,
					        	item_id_comment_evidence,
					        	item_id,
					        	share_id,
					        	date_created
					        ) VALUES (
					        	NULL,
					        	'" . $argItemTypeId . "',
					        	'" . $_SESSION['USER']->id . "',
					        	'" . $member['mb_id'] . "',
					        	'" . $argCommentItemId . "',
					        	'" . $argUnitItemId . "',
					        	'" . $argShareId . "',
					        	NOW()
					        )";
	
					// Debugging
					//echo $sql;
		
					$mysql->query($sql);
				}
			}
		}

		/* public function deprecatedIncreaseCommentCounter($argShareId) {
			$sql = "UPDATE {$_SESSION['RealS_prefix']}share_cohort_members
			        SET s_c_m_unread_count = s_c_m_unread_count + 1
			        WHERE s_c_m_member != " . $_SESSION['USER']->id . "
		        		AND s_c_m_share = " . GetSQLValueString($argShareId, "int");

			$mysql = new mysqlquery;
			$mysql->query($sql);
		} */
	
		//----------------------------------------------------------------------------------------------
		// Desc: Clear comment member statuses (= become read now)
		// Depd: -
		//----------------------------------------------------------------------------------------------

		public function resetCommentCounter($argCommentIdViewed) {
			if (strlen($_SESSION['USER']->id) && strlen($argCommentIdViewed)) {
				// Delete this user's status for the unit and the comment
				$sql = "DELETE
				        FROM {$_SESSION['RealS_prefix']}item_view_status
				        WHERE mb_id_viewer = " . $_SESSION['USER']->id . "
				        	AND item_id_comment_evidence = " . GetSQLValueString($argCommentIdViewed, "int");
	
				// Debugging
				//echo($sql);
		
				$mysql = new mysqlquery;
				$mysql->query($sql);
			}
		}
	
		//----------------------------------------------------------------------------------------------
		// Desc: Delete comment member status as the comment itself gets deleted (by the writer of the comment)
		// Depd: -
		//----------------------------------------------------------------------------------------------

		public function resetCommentCounterForAllViewers($argShareId) {
			if (strlen($argShareId)) {
				$sql = "DELETE
				        FROM {$_SESSION['RealS_prefix']}item_view_status
				        WHERE share_id = " . GetSQLValueString($argShareId, "int");

				// Debugging
				echo($sql);

				$mysql = new mysqlquery;
				$mysql->query($sql);
			}
		}
	
		/* public function deprecatedResetCommentCounter($argShareId) {
			// Clear all counts for this share item for this user
			$sql = "UPDATE {$_SESSION['RealS_prefix']}share_cohort_members
			        SET s_c_m_unread_count = 0
			        WHERE s_c_m_member = " . $_SESSION['USER']->id . "
		        		AND s_c_m_share = " . GetSQLValueString($argShareId, "int");
	
			$mysql = new mysqlquery;
			$mysql->query($sql);

		} */

		//----------------------------------------------------------------------------------------------
		// Desc: Save evidence member statuses (= becomes unread)
		// Depd: -
		//----------------------------------------------------------------------------------------------

		public function increaseEvidenceCounter($argUnitItemId, $argEvidenceItemId, $argShareId) {
			// Notify request mentors UNION with share mentors (but not other share members)
			// Don't set a status for the current user, cos that's not very informative
			$sql = "SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
			        FROM {$_SESSION['RealS_prefix']}share
			        	INNER JOIN {$_SESSION['RealS_prefix']}members ON ({$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_member OR {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share.share_permission)
			        WHERE {$_SESSION['RealS_prefix']}members.mb_type = 'mentor'
			        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
			        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id . "
			        UNION
				SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_id
			        FROM {$_SESSION['RealS_prefix']}share
			        	INNER JOIN {$_SESSION['RealS_prefix']}share_mentor ON share_mentor_share = share_id
					INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = share_mentor_mentor
			        WHERE {$_SESSION['RealS_prefix']}members.mb_type = 'mentor'
			        	AND {$_SESSION['RealS_prefix']}share.share_id = " . GetSQLValueString($argShareId, "int") . "
			        	AND {$_SESSION['RealS_prefix']}members.mb_id != " . $_SESSION['USER']->id;
	
			$mysql = new mysqlquery;
			$members = $mysql->getrows($sql);

			// Debugging
			//echo($sql);

			foreach ($members as $member) {
				// Avoid duplicates, cos evidences can get updated
				$sql = "SELECT NULL
				        FROM {$_SESSION['RealS_prefix']}item_view_status
				        WHERE mb_id_writer = " . $_SESSION['USER']->id . "
				        	AND mb_id_viewer = " . $member['mb_id'] . "
				        	AND item_id_comment_evidence = " . GetSQLValueString($argEvidenceItemId, "int");
		
				$mysql = new mysqlquery;
				$duplicates = $mysql->getrows($sql);

				if (! $duplicates) {
					// Insert mentor status
					$sql = "INSERT INTO {$_SESSION['RealS_prefix']}item_view_status (
					        	item_view_status_id,
					        	item_type_id,
					        	mb_id_writer,
					        	mb_id_viewer,
					        	item_id_comment_evidence,
					        	item_id,
					        	share_id,
					        	date_created
					        ) VALUES (
					        	NULL,
					        	'6',
					        	'" . $_SESSION['USER']->id . "',
					        	'" . $member['mb_id'] . "',
					        	'" . $argEvidenceItemId . "',
					        	'" . $argUnitItemId . "',
					        	" . $argShareId . ",
					        	NOW()
					        )";
		
					$mysql->query($sql);
				}
			}
		}
	
		//----------------------------------------------------------------------------------------------
		// Desc: Clear evidence member statuses (= becomes read)
		// Depd: -
		//----------------------------------------------------------------------------------------------
	
		public function resetEvidenceCounter($argEvidenceItemId) {
			if (strlen($_SESSION['USER']->id) && strlen($argEvidenceItemId)) {
				$mysql = new mysqlquery;

				// Delete this user's status for the unit, task and success ids
				$sql = "DELETE
				        FROM {$_SESSION['RealS_prefix']}item_view_status
				        WHERE mb_id_viewer = " . $_SESSION['USER']->id . "
				          AND item_id_comment_evidence = " . GetSQLValueString($argEvidenceItemId, "int");
	
				// Debugging
				//echo $sql;
	
				$mysql->query($sql);
			}
		}
	}
?>