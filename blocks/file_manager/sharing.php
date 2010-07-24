<?php
/**
* sharing.php
* 
* This file allows users to view a list of people who are
* currently enrolled in the current course (if courseid == 1
* then all people are shown)  This also shows groups as specified
* in the course variables. This allows students to share a link
* or group of links or a folder/category? to an individual, 
* group, all students, or any combination.
*
* @package block_file_manager
* @category block
*
*/

    /**
    * Includes and requires
    */
    require_once("../../config.php");
    require_once("{$CFG->dirroot}/blocks/file_manager/lib.php");
	require_once("{$CFG->dirroot}/blocks/file_manager/print_lib.php");
	
	$id = required_param('id', PARAM_INT);
	$groupid = optional_param('groupid', 0, PARAM_INT);
	$linkid = optional_param('linkid', NULL, PARAM_INT);		// only used when clicking action button, not by using dropdown menu
	$from = optional_param('from', "link", PARAM_ALPHA);		// link/cat/folder
	$rootdir = optional_param('rootdir', 0, PARAM_INT);		// specifies the working dir
	$tsort = optional_param('tsort', "sortlast", PARAM_ALPHA);       // link/cat/folder
    $page = optional_param('page', 0, PARAM_INT);
    $msg = '';  // For storing various warnings
	$cb = fm_clean_checkbox_array();

    if (! $course = get_record('course', 'id', $id) ) {
        error('Invalid course id', "view.php?id={$id}&amp;rootdir={$rootdir}");
    }
    require_login($course->id);

    $coursecontext = get_context_instance(CONTEXT_COURSE, $id);
    $canmanagegroups = has_capability('block/file_manager:canmanagegroups', $coursecontext);
	
	if ($linkid != NULL && $linkid != 0) {
		$cb = array($linkid);
	} 

	// Cleaning for checkbox array (users)
	/*
	$cbu = NULL;
	if (isset($_POST['cbu'])) {
		$cbu = $_POST['cbu'];		// Checkbox array
	}
	*/
	$cbu = optional_param('cbu', null, PARAM_INT);
	if (is_array($cbu)) {
		$tmp = array();
		foreach($cbu as $c) {
			$tmp[] = (int)$c;
		}
		$cbu = $tmp;
	} else {
		//$cbu[] = (int)$cb;
	}
	
	// Ensures the user is able to view the fmanager and can share files
	fm_check_access_rights($course->id, true, true);
    
	$strtitle = get_string('addlink','block_file_manager');
    $nav[] = array('name'=>get_string('filemanager','block_file_manager'), 'link'=>"view.php?id=$id&groupid={$groupid}", 'type'=>'misc');
    $nav[] = array('name'=>$strtitle, 'link'=>null, 'type'=>'misc');
    $navigation = build_navigation($nav);
    print_header($strtitle, format_string($course->fullname), $navigation, "", "", false, "&nbsp;", "&nbsp;");

	if ($groupid != 0){
		// Depending on the groupmode, ensures that the user is member of the group and is allowed to access
		$groupmode = groups_get_course_groupmode($course);
		switch ($groupmode){
			case NOGROUPS : 
				// Should no to be there ...
				error(get_string('errnogroups', 'block_file_manager'), "$CFG->wwwroot/course/view.php?id={$course->id}");
				break;
			case VISIBLEGROUPS :
			case SEPARATEGROUPS : 
				if (!$canmanagegroups && !groups_is_member($groupid)){ // Must check if the user is member of that group
					error(get_string('errnotmemberreadonly', 'block_file_manager'), "view.php?id={$id}&groupid={$groupid}&rootdir={$rootdir}");
				}
				break;
		}
	}
	
	$type = 0; 		// Link shared by default
	if ($from == 'link') {
		$type = 0;
		foreach ($cb as $c) {
			if (substr($c, 0, 2) == 'f-') {
				if ($groupid == 0){
					fm_user_owns_folder(substr($c,2));
				} else {
					fm_group_owns_folder(substr($c,2), $groupid);
				}
			} else {
				if ($groupid == 0){
					fm_user_owns_link($c);
				} else {
					fm_group_owns_link($c, $groupid);
				}
			}
		}
	} elseif ($from == 'category') {
		$type = 1;
		foreach ($cb as $c) {
			if ($groupid == 0){
				fm_user_owns_cat($c);
			} else {
				fm_group_owns_cat($c, $groupid);
			}
		}
	} elseif ($from == 'folder') {
		// $from is folder only when sharing 1 folder
		$type = 2;
	}
	
	if(isset($_POST['share'])) {
		if ($course->id != 1) {
			$sql = "
			    SELECT 
			        u.firstname 
			    FROM 
			        {$CFG->prefix}user u, 
			        {$CFG->prefix}course_display cd 
			    WHERE 
			        course = {$id} AND 
			        cd.userid = u.id
			";
			$count = count_records_sql($sql);
		} else {
			$count = count_records('user', 'idnumber', '');
		}
		// Deletes all existing records for the file in the course
		foreach ($cb as $c) {
			$tmp = $c;
			$tmptype = $type;
			if (substr($c, 0, 2) == "f-") {
				$tmp = substr($c, 2);
				$tmptype = 2;
			}
			if ($tmp != 0) {
				$link = NULL;
				if ($groupid == 0){
					$link->owner = $USER->id; 
					$link->ownertype = OWNERISUSER;
				} else {
					$link->owner = $groupid;  
					$link->ownertype = OWNERISGROUP;
				}
				delete_records('fmanager_shared', "owner = ".$link->owner." AND ownertype = ".$link->ownertype." AND type", $tmptype, 'course', $course->id, 'sharedlink', $tmp);
			}
		}
		if (is_array($cbu)) {
			// If they are sharing to everyone in the course
			if ($cbu[0] != 0) { $count--;}		// Dont include the check-all checkbox
			
			if (count($cbu) == $count) {
				foreach($cb as $c) {
					if ($c != 0 || substr($c, 0, 2) == "f-") {
						$entry = NULL;
						if ($groupid == 0){
							$entry->owner = $USER->id;
							$entry->ownertype = OWNERISUSER;
						} else {
							$entry->owner = $groupid;  
							$entry->ownertype = OWNERISGROUP;
						}
						$entry->course = $course->id;
						if (substr($c, 0, 2) == "f-") {
							$entry->type = STYPE_FOLD;		// Folder type
							$entry->sharedlink = substr($c, 2);
						} else {
							$entry->type = $type;
							$entry->sharedlink = $c;
						}
						$entry->userid = 0;			// Represents share to all
						$entry->viewed = 1;			// Removes the viewed flag
						if (!insert_record('fmanager_shared', $entry)) {
							notify(get_string('errnoinsert', 'block_file_manager'));
						}
					}
				}
			} else {
				// Shares each file to each user
				foreach($cbu as $u) {
					if ($u != 0) {
						foreach($cb as $c) {
							if ($c != 0 || substr($c, 0, 2) == "f-") {
								$entry = NULL;
								if ($groupid == 0){
									$entry->owner = $USER->id;
									$entry->ownertype = OWNERISUSER;
								} else {
									$entry->owner = $groupid;  
									$entry->ownertype = OWNERISGROUP;
								}
								$entry->course = $course->id;
								if (substr($c, 0, 2) == "f-") {
									$entry->type = STYPE_FOLD;
									$entry->sharedlink = substr($c,2);
								} else {
									$entry->type = $type;
									$entry->sharedlink = $c;
								}
								$entry->userid = $u;
								if (!insert_record('fmanager_shared', $entry)) {
									notify(get_string('errnoinsert', 'block_file_manager'));
								}					
							}
						}
					}
				}
			}		
		}
		notify(get_string('msgshared', 'block_file_manager'), 'notifysuccess');
		redirect("view.php?id={$id}&amp;groupid={$groupid}&amp;rootdir={$rootdir}");
	} else if (isset($_POST['cancel'])) {
        notify(get_string('msgcancelok', 'block_file_manager'), 'notifysuccess');
		redirect("view.php?id={$id}&amp;groupid={$groupid}&amp;rootdir={$rootdir}");
	}

	// selects all the checkboxes for the form
	echo "<script language=\"javascript\">
			<!--
				function selectboxes(allbox, whichform) {
					if (whichform == 1) {
						for (var i = 0; i < document.shareform[\"cbu[]\"].length; i++) {
							document.shareform[\"cbu[]\"][i].checked = allbox.checked;
						}
					} else if (whichform == 2) {
						for (var i = 0; i < document.gshareform[\"cbu[]\"].length; i++) {
							document.gshareform[\"cbu[]\"][i].checked = allbox.checked;
						}
					}
				}
			-->
			</script>";
    
	// Means that the all checkbox was checked, but there were no files checked
	$tmp = $cb[0];
	if (substr($tmp, 0, 2) == "f-") {
		$tmp = substr($tmp,2);
	}
	if (($tmp == 0) && (count($cb) == 1)) {
		notify(get_string('msgnofilessel', 'block_file_manager'));		
		redirect("view.php?id={$id}&amp;groupid={$groupid}&amp;rootdir={$rootdir}", 3);
	}
	
	print_heading(get_string('msgsharetoothers', 'block_file_manager'));
    echo '<br/>';
	// Warns that sharing more than one file will delete all their other shared properties
	if (count($cb) > 1) {
		$msg = text_to_html(get_string('msgsharemulti','block_file_manager'));
	}
	$msg = text_to_html(get_string('msgshare', 'block_file_manager')).$msg;
    print_simple_box($msg, 'center');
	echo '<br/>';
	
	echo "<form name=\"shareform\" action=\"sharing.php?id={$id}&amp;groupid={$groupid}&amp;linkid={$linkid}&amp;from={$from}&amp;rootdir={$rootdir}\" method=\"post\">";
	if (count($cb) == 1) {
		$linkid = $cb[0];
	}

    $orderby ='';
    if (empty($tsort) or $tsort=='sortlast') {
        $orderby = "u.lastname, u.firstname";
    } elseif($tsort=='sortfirst') {
        $orderby = "u.firstname, u.lastname";
    }
    //todo:fix this so it pages correctly.
    $perpage = 25;
    $context = get_context_instance(CONTEXT_COURSE,$course->id);
    $allpersons = sharing_get_users($context, 'moodle/course:view', 'u.id, u.lastname, u.firstname', $orderby, $page*$perpage, $perpage,$groupid,'', false);
    print_paging_bar($allpersons->usercount, $page, $perpage, $CFG->wwwroot . "/blocks/file_manager/sharing.php?id=$id&groupid=$groupid&linkid=$linkid&from='$from'&rootdir=$rootdir&tsort=$tsort&");
    if ($allpersons->usercount > $perpage) {
        print_table(fm_print_share_course_members($course->id, $linkid, $type, $groupid, $allpersons->users, true));
    } else {
        print_table(fm_print_share_course_members($course->id, $linkid, $type, $groupid, $allpersons->users, false));
    }
	// Stores $cb id's
	if ($cb != NULL) {
		foreach($cb as $c) {
			echo "<input type=\"hidden\" name=\"cb[]\" value=\"$c\" />";
		}
	}
	echo "<center>";
	echo "<input type=\"submit\" value=\"".get_string('btnshare', 'block_file_manager')."\" name=\"share\" />";
	echo "&nbsp;&nbsp;<input type=\"submit\" value=\"".get_string('btncancel', 'block_file_manager')."\" name=\"cancel\" />";
	echo "</center></form>";
	
	/*
	// If no groups...wont display any group info
	if ($course->groupmode == NOGROUPS) {
		print_footer();
		die();
	}
	echo "<form name=\"gshareform\" action=\"sharing.php?id={$id}&linkid={$linkid}&from={$from}&rootdir={$rootdir}\" method=\"post\">";
	// Always displays all groups to teachers
	if (isteacher($course->id) || ($course->groupmode == VISIBLEGROUPS)) {
		echo fm_print_visible_groups();
	} else {
		if ($course->groupmode == SEPARATEGROUPS) {
			echo fm_print_separate_groups();
		}
	}
	*/
	// Stores $cb id's
	if ($cb != NULL) {
		foreach($cb as $c) {
			echo "<input type=\"hidden\" name=\"cb[]\" value=\"$c\">";
		}
	}
	echo "<input type=\"hidden\" value=\"$rootdir\" name=\"rootdir\">";
	echo "</form>";	
	print_footer();

///reproduces some of the work get_users_by_capability does, using custom function as get_users_by_cap is heavy and doesn't easily retrun a count.
function sharing_get_users($context, $capability, $fields='', $sort='',
        $limitfrom='', $limitnum='', $groups='', $exceptions='', $doanything=true,
        $view=false, $useviewallgroups=false) {
    global $CFG;
    
    $ctxids = substr($context->path, 1); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    // Context is the frontpage
    $isfrontpage = false;
    $iscoursepage = false; // coursepage other than fp
    if ($context->contextlevel == CONTEXT_COURSE) {
        if ($context->instanceid == SITEID) {
            $isfrontpage = true;
        } else {
            $iscoursepage = true;
        }
    }
    $groupby = '';
    if ($fields == "count(u.id)") {
        $groupby = " GROUP BY u.id ";
    }
    // What roles/rolecaps are interesting?
    if (is_array($capability)) {
        $caps = "'" . implode("','", $capability) . "'";
        $capabilities = $capability;
    } else {
        $caps = "'" . $capability . "'";
        $capabilities = array($capability);
    }
    if ($doanything===true) {
        $caps .= ",'moodle/site:doanything'";
        $capabilities[] = 'moodle/site:doanything';
        $doanything_join='';
        $doanything_cond='';
    } else {
        // This is an outer join against
        // admin-ish roleids. Any row that succeeds
        // in JOINing here ends up removed from
        // the resultset. This means we remove
        // rolecaps from roles that also have
        // 'doanything' capabilities.
        $doanything_join="LEFT OUTER JOIN (
                              SELECT DISTINCT rc.roleid
                              FROM {$CFG->prefix}role_capabilities rc
                              WHERE rc.capability='moodle/site:doanything'
                                    AND rc.permission=".CAP_ALLOW."
                                    AND rc.contextid IN ($ctxids)
                          ) dar
                             ON rc.roleid=dar.roleid";
        $doanything_cond="AND dar.roleid IS NULL";
    }
    
    // fetch all capability records - we'll walk several
    // times over them, and should be a small set

    $negperm = false; // has any negative (<0) permission?
    $roleids = array();

    $sql = "SELECT rc.id, rc.roleid, rc.permission, rc.capability,
                   ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
            FROM {$CFG->prefix}role_capabilities rc
            JOIN {$CFG->prefix}context ctx on rc.contextid = ctx.id
            $doanything_join
            WHERE rc.capability IN ($caps) AND ctx.id IN ($ctxids)
                  $doanything_cond
            ORDER BY rc.roleid ASC, ctx.depth ASC";
    if ($capdefs = get_records_sql($sql)) {
        foreach ($capdefs AS $rcid=>$rc) {
            $roleids[] = (int)$rc->roleid;
            if ($rc->permission < 0) {
                $negperm = true;
            }
        }
    }
    
    //
    // Prepare query clauses
    //
    $wherecond = array();

    // Non-deleted users. We never return deleted users.
    $wherecond['nondeleted'] = 'u.deleted = 0';

    /// Groups
    if ($groups) {
        if (is_array($groups)) {
            $grouptest = 'gm.groupid IN (' . implode(',', $groups) . ')';
        } else {
            $grouptest = 'gm.groupid = ' . $groups;
        }
        $grouptest = 'ra.userid IN (SELECT userid FROM ' .
            $CFG->prefix . 'groups_members gm WHERE ' . $grouptest . ')';

        if ($useviewallgroups) {
            $viewallgroupsusers = get_users_by_capability($context,
                    'moodle/site:accessallgroups', 'u.id, u.id', '', '', '', '', $exceptions);
            $wherecond['groups'] =  '('. $grouptest . ' OR ra.userid IN (' .
                                    implode(',', array_keys($viewallgroupsusers)) . '))';
        } else {
            $wherecond['groups'] =  '(' . $grouptest .')';
        }
    }
    
    /// Set up hidden role-assignments sql
    if ($view && !has_capability('moodle/role:viewhiddenassigns', $context)) {
        $condhiddenra = 'AND ra.hidden = 0 ';
        $sscondhiddenra = 'AND ssra.hidden = 0 ';
    } else {
        $condhiddenra = '';
        $sscondhiddenra = '';
    }
    
    // Collect WHERE conditions
    $where = implode(' AND ', array_values($wherecond));
    if ($where != '') {
        $where = 'WHERE ' . $where;
    }
    
    $sortby = $sort ? " ORDER BY $sort " : '';

        /// Simple SQL assuming no negative rolecaps.
        /// We use a subselect to grab the role assignments
        /// ensuring only one row per user -- even if they
        /// have many "relevant" role assignments.
        $select = " SELECT $fields";
        $from   = " FROM {$CFG->prefix}user u
                    JOIN (SELECT DISTINCT ssra.userid
                          FROM {$CFG->prefix}role_assignments ssra
                          WHERE ssra.contextid IN ($ctxids)
                                AND ssra.roleid IN (".implode(',',$roleids) .")
                                $sscondhiddenra
                          ) ra ON ra.userid = u.id ";
 
        $userarr = new stdClass();
        $userarr->usercount = array_shift(get_records_sql("SELECT count(DISTINCT u.id)".$from.$where))->count; //get count of all users
        $userarr->users = get_records_sql($select.$from.$where.$sortby, $limitfrom, $limitnum);
        return $userarr;
}
?>