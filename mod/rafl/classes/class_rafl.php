<?php
	require_once ('includes/get_sql_value_string.php') ;

	//----------------------------------------------------------------------------------------------
	// Desc: Tools for the rafl pages
	// Depd: -
	// Auth: Daniel Dammann <dan@smartassess.com>
	//----------------------------------------------------------------------------------------------

	class rafl {
		
		//----------------------------------------------------------------------------------------------
		// Desc: Gimme smileys for who has ticked "happy" for a success criteria
		// Depd: -
		// Auth: Daniel Dammann <dan@smartassess.com>
		//----------------------------------------------------------------------------------------------

		function get_success_happy_drop_down($arg_success_item_id, $arg_share_id) {
			if (strlen($arg_success_item_id)) {
				// Gimme rafl happy results
				$query = "SELECT DISTINCT {$_SESSION['RealS_prefix']}members.mb_firstname, {$_SESSION['RealS_prefix']}members.mb_surmame
				          FROM {$_SESSION['RealS_prefix']}items
				          	INNER JOIN {$_SESSION['RealS_prefix']}webcells ON {$_SESSION['RealS_prefix']}webcells.webcell_id = {$_SESSION['RealS_prefix']}items.item_webcell
				          	INNER JOIN {$_SESSION['RealS_prefix']}rafl_res ON {$_SESSION['RealS_prefix']}items.item_id = {$_SESSION['RealS_prefix']}rafl_res.rafl_res_item 
				          	INNER JOIN {$_SESSION['RealS_prefix']}items AS successes ON successes.item_id = {$_SESSION['RealS_prefix']}items.item_parent_item 
				          	INNER JOIN {$_SESSION['RealS_prefix']}items AS tasks ON tasks.item_id = successes.item_parent_item 
				          	INNER JOIN {$_SESSION['RealS_prefix']}items AS units ON units.item_id = tasks.item_parent_item 
				          	INNER JOIN {$_SESSION['RealS_prefix']}share ON {$_SESSION['RealS_prefix']}share.share_item = units.item_id 
				          	INNER JOIN {$_SESSION['RealS_prefix']}share_cohort_members ON ({$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_share = {$_SESSION['RealS_prefix']}share.share_id AND {$_SESSION['RealS_prefix']}webcells.webcell_member = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member)
				          	INNER JOIN {$_SESSION['RealS_prefix']}members ON {$_SESSION['RealS_prefix']}members.mb_id = {$_SESSION['RealS_prefix']}share_cohort_members.s_c_m_member
				          WHERE {$_SESSION['RealS_prefix']}items.item_parent_item = " . GetSQLValueString($arg_success_item_id, "int") . "
				          	AND {$_SESSION['RealS_prefix']}share.share_id =  " . GetSQLValueString($arg_share_id, 'int') . "
				          	AND {$_SESSION['RealS_prefix']}members.mb_type != 'cohort'
				          	AND {$_SESSION['RealS_prefix']}items.item_default_type = 6
				          	AND rafl_res_rate = 2
				          ORDER BY {$_SESSION['RealS_prefix']}members.mb_firstname, {$_SESSION['RealS_prefix']}members.mb_surmame";

				// Debugging
				//echo $query;
		
				$mysql = new mysqlquery;
				$rows = $mysql->getrows($query);
		
				if (count($rows)) {
					$drop_down  = '<select size="1" class="who_is">';
					//$drop_down .= '		<option>who is happy?</option>';
			
					foreach ($rows as $row) {
						$drop_down .= '	<option>' . trim($row['mb_firstname'] . ' ' . $row['mb_surmame']) . '</option>';
					}
			
					$drop_down .= '</select>';
					
					return $drop_down;
				} else {
					return '&nbsp;';
				}
			} else {
				return '&nbsp;';
			}
		}
	}
?>