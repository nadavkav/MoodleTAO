<?PHP

function xmldb_tracker_upgrade($oldversion=0) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    $result = true;
    
    if ($oldversion < 2006062300) {
    }

    if ($result && $oldversion < 2008091900) {
    
    /// Define field parent to be added to tracker
        $table = new XMLDBTable('tracker');
        $field = new XMLDBField('parent');
        $field->setAttributes(XMLDB_TYPE_CHAR, '80', null, null, null, null, null, null, 'timemodified');

    /// Launch add field parent
        $result = $result && add_field($table, $field);
    }
    if ($result && $oldversion < 2008092400) {

        // setup XML-RPC services for tracker
        
        if (!get_record('mnet_service', 'name', 'tracker_cascade')){
            $service->name = 'tracker_cascade';
            $service->description = get_string('transferservice', 'tracker');
            $service->apiversion = 1;
            $service->offer = 1;
            if (!$serviceid = insert_record('mnet_service', $service)){
                notify('Error installing tracker_cascade service.');
                $result = false;
            }
            
            $rpc->function_name = 'tracker_rpc_get_instances';
            $rpc->xmlrpc_path = 'mod/tracker/rpclib.php/tracker_rpc_get_instances';
            $rpc->parent_type = 'mod';  
            $rpc->parent = 'tracker';
            $rpc->enabled = 0; 
            $rpc->help = 'Get instances of available trackers for cascading.';
            $rpc->profile = '';
            if (!$rpcid = insert_record('mnet_rpc', $rpc)){
                notify('Error installing tracker_cascade RPC calls.');
                $result = false;
            }
            $rpcmap->serviceid = $serviceid;
            $rpcmap->rpcid = $rpcid;
            insert_record('mnet_service2rpc', $rpcmap);
            
            $rpc->function_name = 'tracker_rpc_get_infos';
            $rpc->xmlrpc_path = 'mod/tracker/rpclib.php/tracker_rpc_get_infos';
            $rpc->parent_type = 'mod';  
            $rpc->parent = 'tracker';
            $rpc->enabled = 0; 
            $rpc->help = 'Get information about one tracker.';
            $rpc->profile = '';
            if (!$rpcid = insert_record('mnet_rpc', $rpc)){
                notify('Error installing tracker_cascade RPC calls.');
                $result = false;
            }
            $rpcmap->rpcid = $rpcid;
            insert_record('mnet_service2rpc', $rpcmap);

            $rpc->function_name = 'tracker_rpc_post_issue';
            $rpc->xmlrpc_path = 'mod/tracker/rpclib.php/tracker_rpc_post_issue';
            $rpc->parent_type = 'mod';  
            $rpc->parent = 'tracker';
            $rpc->enabled = 0; 
            $rpc->help = 'Cascades an issue.';
            $rpc->profile = '';
            if (!$rpcid = insert_record('mnet_rpc', $rpc)){
                notify('Error installing tracker_cascade RPC calls.');
                $result = false;
            }
            $rpcmap->rpcid = $rpcid;
            insert_record('mnet_service2rpc', $rpcmap);
        }
    }

    if ($result && $oldversion < 2008092602) {

    /// Define field supportmode to be added to tracker
        $table = new XMLDBTable('tracker');
        $field = new XMLDBField('supportmode');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'bugtracker', 'parent');

    /// Launch add field supportmode
        $result = $result && add_field($table, $field);
    }
    
    return $result;
}

?>