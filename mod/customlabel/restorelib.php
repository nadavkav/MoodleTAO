<?php //$Id: restorelib.php,v 1.13 2006/09/18 09:13:04 moodler Exp $
    //This php script contains all the stuff to backup/restore
    //customlabel mods

    //This is the "graphical" structure of the customlabel mod:   
    //
    //                       customlabel 
    //                    (CL,pk->id)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the restore procedure about this mod
    function customlabel_restore_mods($mod, $restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code, $mod->modtype, $mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
          
            //Now, build the LABEL record structure
            $customlabel->course = $restore->course_id;
            $customlabel->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $customlabel->title = backup_todb($info['MOD']['#']['TITLE']['0']['#']);
            $customlabel->labelclass = backup_todb($info['MOD']['#']['LABELCLASS']['0']['#']);
            $customlabel->content = backup_todb($info['MOD']['#']['CONTENT']['0']['#']);
            $customlabel->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];

            if (empty($customlabel->title)){
                $customlabel->title = '';
            } 
            //The structure is equal to the db, so insert the customlabel
            $newid = insert_record ('customlabel', $customlabel);

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string('modulename', 'customlabel').' "'.format_string(stripslashes($customlabel->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
   
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    function customlabel_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        $sql = "
            SELECT 
                l.id, 
                l.content
            FROM 
                {$CFG->prefix}customlabel l
            WHERE 
                l.course = $restore->course_id
        ";
        if ($customlabels = get_records_sql ($sql)) {
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($customlabels as $customlabel) {
                //Increment counter
                $i++;
                $content = $customlabel->content;
                $result = restore_decode_content_links_worker($content, $restore);

                if ($result != $content) {
                    //Update record
                    $customlabel->content = addslashes($result);
                    $status = update_record("customlabel", $customlabel);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function customlabel_restore_logs($restore,$log) {
                    
        $status = false;
                    
        //Depending of the action, we recode different things
        switch ($log->action) {
        case 'add':
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code, $log->module, $log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case 'update':
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
