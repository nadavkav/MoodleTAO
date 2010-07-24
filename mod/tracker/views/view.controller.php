<?php

/**
* @package mod-tracker
* @category mod
* @author Valery Fremaux
* @date 02/12/2007
*
* Controller for all "view" related views
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from view.php in mod/tracker
}

/************************************* Submit an issue *****************************/
if ($action == 'submitanissue'){
	if (!$issue = tracker_submitanissue($tracker->id)){
	   error("Bad issue id");
    }
    print_simple_box_start('center', '80%', '', '', 'generalbox', 'bugreport');
    print_string('thanks', 'tracker');
    print_simple_box_end();
    print_continue("view.php?id={$cm->id}view=view&amp;page=browse");
    
    // notify all admins
    if ($tracker->allownotifications){
        tracker_notify_submission($issue, $cm, $tracker);
    }
    
    return -1;
}
/************************************* update an issue *****************************/
elseif ($action == 'updateanissue'){
    $issue->id = required_param('issueid', PARAM_INT);
    $issue->status = required_param('status', PARAM_INT);
    $issue->assignedto = required_param('assignedto', PARAM_INT);
    $issue->summary = required_param('summary', PARAM_TEXT);
    $issue->description = addslashes(required_param('description', PARAM_CLEANHTML));
    $issue->format = required_param('format', PARAM_INT);
    $issue->datereported = required_param('datereported', PARAM_INT);
    $issue->resolution = addslashes(required_param('resolution', PARAM_CLEANHTML));
    $issue->resolutionformat = required_param('resolutionformat', PARAM_INT);
    $issue->trackerid = $tracker->id;
    if (!empty($issue->resolution) && $issue->status < RESOLVED) $issue->status = RESOLVED;

    // if ownership has changed, prepare logging
    $oldrecord = get_record('tracker_issue', 'id', $issue->id);
    
    if ($oldrecord->assignedto != $issue->assignedto){
        $ownership->trackerid = $tracker->id;
        $ownership->issueid = $oldrecord->id;
        $ownership->userid = $oldrecord->assignedto;
        $ownership->bywhomid = $oldrecord->bywhomid;
        $ownership->timeassigned = ($oldrecord->timeassigned) ? $oldrecord->timeassigned : time();
        if (!insert_record('tracker_issueownership', $ownership)){
            error ("Could not log old ownership");
        }
    }    

    $issue->assignedto = required_param('assignedto', PARAM_INT);
    $issue->bywhomid = $USER->id;
    $issue->timeassigned = time();

    if (!update_record('tracker_issue', $issue)){
        error ("Could not update tracker issue");
    }

    /// send state change notification
    if ($oldrecord->status != $issue->status){
        tracker_notifyccs_changestate($issue->id, $tracker);
    }

    tracker_clearelements($issue->id);    
    tracker_recordelements($issue);
    
    // TODO : process dependancies
    $dependancies = optional_param('dependancies', null, PARAM_INT);
    if (is_array($dependancies)){
        // cleanup previous depdendancies
        if (!delete_records('tracker_issuedependancy', 'childid', $issue->id)){
            error ("Could not delete old dependancies");
        }
        // install back new one
        foreach($dependancies as $dependancy){
            $dependancyrec->trackerid = $tracker->id;
            $dependancyrec->parentid = $dependancy;
            $dependancyrec->childid = $issue->id;
            $dependancyrec->comment = '';
            if (!insert_record('tracker_issuedependancy', $dependancyrec)){
                error ("Could not write dependancy record");
            }
        }
    }
}
/************************************* delete an issue record *****************************/
elseif ($action == 'delete'){
    $issueid = required_param('issueid', PARAM_INT);
    delete_records('tracker_issue', 'id', $issueid);
    delete_records('tracker_issuedependancy', 'childid', $issueid);
    delete_records('tracker_issuedependancy', 'parentid', $issueid);
    delete_records('tracker_issueattribute', 'issueid', $issueid);
    delete_records('tracker_issuecomment', 'issueid', $issueid);
    delete_records('tracker_issueownership', 'issueid', $issueid);

    // todo : send notification to all cced

    delete_records('tracker_issuecc', 'issueid', $issueid);
}
/************************************* updating list and status *****************************/
elseif ($action == 'updatelist'){
	$keys = array_keys($_POST);							    // get the key value of all the fields submitted
	$statuskeys = preg_grep('/status./' , $keys);  	        // filter out only the status
	$assignedtokeys = preg_grep('/assignedto./' , $keys);  	// filter out only the assigned updating
	$newassignedtokeys = preg_grep('/assignedtoi./' , $keys);  // filter out only the new assigned
	foreach($statuskeys as $akey){
	    $issueid = str_replace('status', '', $akey);
	    $haschanged = optional_param('schanged'.$issueid, 0, PARAM_INT);
	    if ($haschanged){
    	    $issue->id = $issueid;
    	    $issue->status = required_param($akey, PARAM_INT);
    	    $oldstatus = get_field('tracker_issue', 'status', 'id', $issue->id);
    	    update_record('tracker_issue', $issue);    
    	    /// check status changing and send notifications
    	    if ($oldstatus != $issue->status){
        	    if ($tracker->allownotifications){
        	        tracker_notifyccs_changestate($issue->id, $tracker);
        	    }
        	}
        }
	}
	// always add a record for history
	foreach($assignedtokeys as $akey){
	    $issueid = str_replace('assignedto', '', $akey);
	    // new ownership is triggered only when a change occured
	    $haschanged = optional_param('changed'.$issueid, 0, PARAM_INT);
	    if ($haschanged){
	        // save old assignement in history
            $oldassign = get_record('tracker_issue', 'id', $issueid);
            if ($oldassign->assignedto != 0){
                $ownership->trackerid = $tracker->id;
                $ownership->issueid = $issueid;
        	    $ownership->userid = $oldassign->assignedto;
        	    $ownership->bywhomid = $oldassign->bywhomid;
        	    $ownership->timeassigned = $oldassign->timeassigned;
        	    if (!insert_record('tracker_issueownership', $ownership)){
        	        notice ("Error saving ownership for issue $issueid");
        	    }
        	}

            // update actual assignement
    	    $issue->id = $issueid;
    	    $issue->bywhomid = $USER->id;
    	    $issue->timeassigned = time();
    	    $issue->assignedto = required_param($akey, PARAM_INT);
    	    if (!update_record('tracker_issue', $issue)){
    	        notice ("Error updating assignation for issue $issueid");
    	    }    	    

    	    if ($tracker->allownotifications){
    	        tracker_notifyccs_changeownership($issue->id, $tracker);
    	    }
    	}
	}
}
/********************************* requires the add a comment form **************************/
elseif ($action == 'addacomment'){
    $form->issueid = required_param('issueid', PARAM_INT);
    include "views/addacomment.html";
    return -1;
}
/***************************************** add a comment ***********************************/
elseif ($action == 'doaddcomment'){
    $issueid = required_param('issueid', PARAM_INT);
    $comment->comment = addslashes(required_param('comment', PARAM_CLEANHTML));
    $comment->commentformat = required_param('commentformat', PARAM_INT);
    
    $comment->userid = $USER->id;
    $comment->trackerid = $tracker->id;
    $comment->issueid = $issueid;
    $comment->datecreated = time();
    if (!insert_record('tracker_issuecomment', $comment)){
        error ("Error writing comment");
    }
}
/************************************ reactivates a stored search *****************************/
elseif($action == 'usequery'){
    $queryid = required_param('queryid', PARAM_INT);
    $fields = tracker_extractsearchparametersfromdb($queryid);
}
/******************************* unregister administratively a user *****************************/
elseif ($action == 'unregister'){
	$issueid = required_param('issueid', PARAM_INT);
	$ccid = required_param('ccid', PARAM_INT);
	if (!delete_records ('tracker_issuecc', 'trackerid', $tracker->id, 'issueid', $issueid, 'userid', $ccid)){
		error ("Cannot delete carbon copy {$tracker->ticketprefix}{$issueid} for user : " . $ccid);
	}
}
elseif ($action == 'register'){
	$issueid = required_param('issueid', PARAM_INT);
	$ccid = required_param('ccid', PARAM_INT);
	if (!get_record('tracker_issuecc', 'trackerid', $tracker->id, 'issueid', $issueid, 'userid', $ccid)){
	    $cc->trackerid = $tracker->id;
	    $cc->issueid = $issueid;
	    $cc->userid = $ccid;
	    $cc->events = 31 ;
	    insert_record('tracker_issuecc', $cc);
	}
}
/******************************* copy an issue to a parent tracker *****************************/
elseif ($action == 'cascade'){
    global $USER;

	$issueid = required_param('issueid', PARAM_INT);
	$issue = get_record('tracker_issue', 'id', $issueid);
	$attributes = get_records('tracker_issueattribute', 'issueid', $issue->id);

	// remaps elementid to elementname for 
	tracker_loadelementsused($tracker->id, $used);
	if (!empty($attributes)){
    	foreach(array_keys($attributes) as $attkey){
    	    $attributes[$attkey]->elementname = $used[$attributes[$attkey]->id]->name;
    	}
    }
    $issue->attributes = $attributes;

    // We get comments and make a single backtrack. There should not 
    // be usefull to bring along full user profile. We just want not
    // to loose usefull information the previous track collected.
	$comments = get_records('tracker_issuecomment', 'issueid', $issue->id);
    $track = '';
	if (!empty($comments)){
	    // collect userids
	    foreach($comments as $comment){
	        $useridsarray[] = $comment->userid;
	    }	    
	    $idlist = implode("','", $useridsarray);
	    $users = get_records_select('user', "id IN ('$idlist')", '', 'id, firstname, lastname');

        // make backtrack
        foreach($comments as $comment){
            $track .= get_string('commentedby', 'tracker').fullname($users[$comment->userid]).get_string('on', 'tracker').userdate($comment->datecreated);
            $track .= '<br/>';
            $track .= format_text($comment->comment, $comment->format);
            $track .= '<hr width="60%"/>';
        }
	}
	$issue->comment = $track;

    include_once($CFG->dirroot."/mod/tracker/rpclib.php");

    if (is_numeric($tracker->parent)){
        // tracker is local, use the rpc entry point anyway
    	$result = tracker_rpc_post_issue($tracker->parent, $USER->id, json_encode($issue));
    } else {
        // tracker is remote, make an RPC call

        list($remoteid, $mnet_host) = explode('@', $tracker->parent);

        // get network tracker properties
        include_once $CFG->dirroot."/mnet/xmlrpc/client.php";
        $rpcclient = new mnet_xmlrpc_client();
        $rpcclient->set_method('mod/tracker/rpclib.php/tracker_rpc_post_issue');
        $rpcclient->add_param($USER->username, 'string');
        $rpcclient->add_param($CFG->wwwroot, 'string');
        $rpcclient->add_param($remoteid, 'int');
        $rpcclient->add_param(json_encode($issue), 'string');
        
        $parent_mnet = new mnet_peer();
        $parent_mnet->set_wwwroot($mnet_host);
        
        $result = $rpcclient->send($parent_mnet);               
    }
    if ($result){
        if ($rpcclient->response['status'] == RPC_SUCCESS){
            $issue->status = TRANSFERED;
            $issue->followid = $rpcclient->response['followid'];
            if (!update_record('tracker_issue', $issue)){
                error ("Could not update issue for cascade");
            }
        } else {
            error ("Error on remote side<br/>".$result->error);            
        }
    } else {
        error ("Error on sending cascade :<br/>".implode('<br/>', $rpcclient->error));
    }
}

?>