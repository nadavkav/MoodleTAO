<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

    //This php script contains all the stuff to backup/restore
    //taoresource mods
    //This is the "graphical" structure of the taoresource mod:
    //
    //                 taoresource                                      
    //                 (CL,pk->id)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //
    //-----------------------------------------------------------

    require_once($CFG->dirroot.'/mod/taoresource/lib.php');
    
    //This function executes all the restore procedure about this mod
    function taoresource_restore_mods($mod,$restore) {

        global $CFG;
        
        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);
        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;

            //Now, start building the TAORESOURCE record structure
            $taoresource = new object();
            $taoresource->course = $restore->course_id;
            $taoresource->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $taoresource->type = $info['MOD']['#']['TYPE']['0']['#'];
            $taoresource->identifier = backup_todb($info['MOD']['#']['IDENTIFIER']['0']['#']);
            $taoresource->description = backup_todb($info['MOD']['#']['DESCRIPTION']['0']['#']);
            $taoresource->alltext = backup_todb($info['MOD']['#']['ALLTEXT']['0']['#']);
            $taoresource->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
            $taoresource->options = backup_todb($info['MOD']['#']['OPTIONS']['0']['#']);
            $taoresource->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];

            //The structure is equal to the db, so insert the taoresource
            $newid = insert_record ("taoresource",$taoresource);
          
            // restore any associated files and the taoresource_entry ...
            if ($taoresource->type == 'file') {
                // if the user wants to, then lets try restoring index entries anyway, and even then we dont overwrite them
                if ($CFG->taoresource_restore_index && !empty($info['MOD']['#']['ENTRIES']['0']['#'])) {
                    $trentries_data = $info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'];
                    foreach ($trentries_data as $tre_data) {
                        // Only grab this particular one - we don't know if this was a complete backup or not
                        $taoresource_entry = taoresource_entry::read($tre_data['#']['IDENTIFIER']['0']['#']);
                        if ($taoresource_entry) {
                            continue;
                        }
                        if (!empty($tre_data['#']['FILE']['0']['#'])) {
                            $tre_data['#']['URL']['0']['#'] = preg_replace('/\$\@TAORESOURCEINDEX\@\$/', $CFG->wwwroot, $tre_data['#']['URL']['0']['#']);
                        }
                        
                        // restore the entry
                        taoresource_restore_mods_one_index($tre_data);
                        // restore files if necessary
                        taoresource_restore_files($mod->id,$newid,$taoresource_entry,$restore);
                    }
                }
                else {
                    // does the physical resource entry exist?  We only resotre if it doesn't exist - we dont overwrite
                    $taoresource_entry = taoresource_entry::read($taoresource->identifier);
                    if (!$taoresource_entry && !empty($info['MOD']['#']['ENTRIES']['0']['#'])) {
                        $trentries_data = $info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'];
                        foreach ($trentries_data as $tre_data) {
                            // Only grab this particular one - we don't know if this was a complete backup or not
                            if ($tre_data['#']['IDENTIFIER']['0']['#'] != $taoresource->identifier) {
                                continue;
                            }
                            // we are not restoring the index locally so we must reove the file reference
                            // this will force the resource to look to the original source 
                            $tre_data['#']['FILE']['0']['#'] = '';
                            // restore the entry
                            taoresource_restore_mods_one_index($tre_data);
                            // restore files if necessary
                            taoresource_restore_files($mod->id,$newid,$taoresource_entry,$restore);
                        }
                    }
                }
            }
            
            // does the physical resource entry exist?
            $taoresource_entry = taoresource_entry::read($taoresource->identifier);
            if (!$taoresource_entry) {
                print_error('cannotrestore', 'taoresource', '', $taoresource->identifier);
            }

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","taoresource")." \"".format_string(stripslashes($taoresource->name),true)."\"</li>";
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

    
    // given the node of the XML document that pertains to a single physical TAO Resource
    // restore (recreate) the taoresource_entry, and associated taoresouce_metadata records
    function taoresource_restore_mods_one_index($tredata) {
       // restore the entry
        $taoresource_entry = new object();
        $taoresource_entry->id           = $tredata['#']['ID']['0']['#'];
        $taoresource_entry->title        = $tredata['#']['TITLE']['0']['#'];
        $taoresource_entry->type         = $tredata['#']['TYPE']['0']['#'];
        $taoresource_entry->mimetype     = $tredata['#']['MIMETYPE']['0']['#'];
        $taoresource_entry->identifier   = $tredata['#']['IDENTIFIER']['0']['#'];
        $taoresource_entry->remoteid     = $tredata['#']['REMOTEID']['0']['#'];
        if ($taoresource_entry->remoteid == '$@NULL@$') {
            $taoresource_entry->remoteid = '';
        }
        $taoresource_entry->file         = $tredata['#']['FILE']['0']['#'];
        $taoresource_entry->url          = $tredata['#']['URL']['0']['#'];
        $taoresource_entry->lang         = $tredata['#']['LANG']['0']['#'];
        $taoresource_entry->description  = $tredata['#']['DESCRIPTION']['0']['#'];
        $taoresource_entry->keywords     = $tredata['#']['KEYWORDS']['0']['#'];
        $taoresource_entry->timemodified = $tredata['#']['TIMEMODIFIED']['0']['#'];
        $taoresource_entry = new taoresource_entry($taoresource_entry);
        
        // restore the metadata
        $metadata = $tredata['#']['METADATA']['0']['#']['ELEMENT'];
        foreach ($metadata as $element) {
            $entryid   = $element['#']['ENTRY_ID']['0']['#'];
            $el        = $element['#']['ELEMENT']['0']['#'];
            $namespace = $element['#']['NAMESPACE']['0']['#'];
            $value     = $element['#']['VALUE']['0']['#'];
            $taoresource_entry->add_element($el, $value, $namespace);
        }
        $taoresource_entry->add_instance();
    
    }    
    
    
    // Restore a physical file if one exists for the TAO Resource
    function taoresource_restore_files($oldid,$newid,$taoresource_entry,$restore) {
        global $CFG;

        // need a file to do anything ...
        if (empty($taoresource_entry->file)) {
            return true;
        }
        
        $status = true;
        // course directory exists
        $status = check_dir_exists($CFG->dataroot."/".$restore->course_id,true);
        //TAO Resource shared repository exists
        $status = $status && check_dir_exists($CFG->dataroot.TAORESOURCE_RESOURCEPATH,true);

        // we need to do anything referenced by $resource->reference and anything in moddata/resource/instance

        // do referenced files/dirs first.
        $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.TAORESOURCE_RESOURCEPATH.$taoresource_entry->file;
        
        if (file_exists($temp_path)) { // ok, it was backed up, restore it.
            $new_path = $CFG->dataroot.TAORESOURCE_RESOURCEPATH.$taoresource_entry->file;
            $status = $status && backup_copy_file($temp_path,$new_path);
        }

        return $status;
    }
    
    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //taoresource_decode_content_links_caller() function in each module
    //in the restore process
    function taoresource_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of taoresources
        $searchstring='/\$@(TAORESOURCEINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(TAORESOURCEINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if (!empty($rec->new_id)) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/taoresource/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/taoresource/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to taoresource view by moduleid
        $searchstring='/\$@(TAORESOURCEVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(TAORESOURCEVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if (!empty($rec->new_id)) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/taoresource/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/taoresource/view.php?id='.$old_id,$result);
                }
            }
        }

        //Link to taoresource view by taoresourceid
        $searchstring='/\$@(TAORESOURCEVIEWBYR)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (forum id)
                $rec = backup_getid($restore->backup_unique_code,"taoresource",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(TAORESOURCEVIEWBYR)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/taoresource/view.php?r='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/taoresource/view.php?r='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function taoresource_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($taoresources = get_records_sql ("SELECT r.id, r.alltext, r.description, r.identifier
                                   FROM {$CFG->prefix}taoresource r
                                   WHERE r.course = $restore->course_id")) {

            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($taoresources as $taoresource) {
                //Increment counter
                $i++;
                $content1 = $taoresource->alltext;
                $content2 = $taoresource->description;
                $result1 = restore_decode_content_links_worker($content1,$restore);
                $result2 = restore_decode_content_links_worker($content2,$restore);

                if ($result1 != $content1 || $result2 != $content2) {
                    //Update record
                    $taoresource->alltext = addslashes($result1);
                    $taoresource->description = addslashes($result2);
                    $status = update_record("taoresource",$taoresource);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content1).'<br />changed to<br />'.s($result1).'<hr /><br />';
                            echo '<br /><hr />'.s($content2).'<br />changed to<br />'.s($result2).'<hr /><br />';
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

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function taoresource_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert taoresource->alltext
        if ($records = get_records_sql ("SELECT r.id, r.alltext, r.options
                                         FROM {$CFG->prefix}taoresource r,
                                              {$CFG->prefix}backup_ids b
                                         WHERE r.course = $restore->course_id AND
                                               options = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'taoresource' AND
                                               b.new_id = r.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->alltext = restore_decode_wiki_content($record->alltext, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->alltext = $wtm->convert($record->alltext, $restore->course_id);
                $record->options = FORMAT_MARKDOWN;
                $status = update_record('taoresource', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
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
    function taoresource_restore_logs($restore,$log) {
                    
        $status = false;
                    
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
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
        case "update":
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
        case "view":
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
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
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