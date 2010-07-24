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
    //resource mods

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

    //This function executes all the backup procedure about this mod
    function taoresource_backup_mods($bf,$preferences) {
        global $CFG;
        $status = true; 

        ////Iterate over taoresource table
        $taoresources = get_records ("taoresource","course",$preferences->backup_course,"id");
        if ($taoresources) {
            foreach ($taoresources as $taoresource) {
                if (backup_mod_selected($preferences,'taoresource',$taoresource->id)) {
                    $status = taoresource_backup_one_mod($bf,$preferences,$taoresource);
                }
            }
        }
        return $status;
    }

    
    function taoresource_backup_one_mod($bf,$preferences,$taoresource) {

        global $CFG;
        global $taoresource_entry_backedup;
        
        if (is_numeric($taoresource)) {
            $taoresource = get_record('taoresource','id',$taoresource);
        }
    
        $status = true;
        
        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$taoresource->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"taoresource"));
        fwrite ($bf,full_tag("NAME",4,false,$taoresource->name));
        fwrite ($bf,full_tag("TYPE",4,false,$taoresource->type));
        fwrite ($bf,full_tag("IDENTIFIER",4,false,$taoresource->identifier));
        fwrite ($bf,full_tag("DESCRIPTION",4,false,$taoresource->description));
        fwrite ($bf,full_tag("ALLTEXT",4,false,$taoresource->alltext));
        fwrite ($bf,full_tag("POPUP",4,false,$taoresource->popup));
        fwrite ($bf,full_tag("OPTIONS",4,false,$taoresource->options));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$taoresource->timemodified));
        
        if ($status && ($taoresource->type == 'file')) {
            
            //Start entries
            fwrite ($bf,start_tag("ENTRIES",4,true));
            
            // if this is a complete backup then all the entries are dumped first time round
            $entries = array();
            if ($CFG->taoresource_backup_index && !$taoresource_entry_backedup) {
                $entries = get_records('taoresource_entry', '', '', '', 'identifier');
            }
            if (!$CFG->taoresource_backup_index) {
                $entries[]= $taoresource;
            }
            
            $base = '/'.preg_quote($CFG->wwwroot,"/").'/';
            foreach ($entries as $entry) {
                // backup the taoresource_entry, and taoresource_metadata values
                $taoresource_entry = get_record('taoresource_entry','identifier',$entry->identifier);
                $taoresource_metadata = get_records('taoresource_metadata', 'entry_id', $taoresource_entry->id);
                
                // prepare the URL, so that it can be repointed on the restore
                $url = $taoresource_entry->url;
                if ($CFG->taoresource_backup_index && !empty($taoresource_entry->file)) {
                    $url = preg_replace($base,'$@TAORESOURCEINDEX@$', $url);
                }
                //Start entry
                fwrite ($bf,start_tag("ENTRY",5,true));
                // write out index entry data
                fwrite ($bf,full_tag("ID",6,false,$taoresource_entry->id));
                fwrite ($bf,full_tag("TITLE",6,false,$taoresource_entry->title));
                fwrite ($bf,full_tag("TYPE",6,false,$taoresource_entry->type));
                fwrite ($bf,full_tag("MIMETYPE",6,false,$taoresource_entry->mimetype));
                fwrite ($bf,full_tag("IDENTIFIER",6,false,$taoresource_entry->identifier));
                fwrite ($bf,full_tag("REMOTEID",6,false,$taoresource_entry->remoteid));
                fwrite ($bf,full_tag("FILE",6,false,$taoresource_entry->file));
                fwrite ($bf,full_tag("URL",6,false,$url));
                fwrite ($bf,full_tag("LANG",6,false,$taoresource_entry->lang));
                fwrite ($bf,full_tag("DESCRIPTION",6,false,$taoresource_entry->description));
                fwrite ($bf,full_tag("KEYWORDS",6,false,$taoresource_entry->keywords));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$taoresource_entry->timemodified));
                fwrite ($bf,start_tag("METADATA",6,true));
                if (!empty($taoresource_metadata)) {
                    foreach ($taoresource_metadata as $element) {
                        fwrite ($bf,start_tag("ELEMENT",7,true));
                        fwrite ($bf,full_tag("ID",8,false,$element->id));
                        fwrite ($bf,full_tag("ENTRY_ID",8,false,$element->entry_id));
                        fwrite ($bf,full_tag("ELEMENT",8,false,$element->element));
                        fwrite ($bf,full_tag("NAMESPACE",8,false,$element->namespace));
                        fwrite ($bf,full_tag("VALUE",8,false,$element->value));
                        $status = fwrite ($bf,end_tag("ELEMENT",7,true));
                    }
                }
                $status = fwrite ($bf,end_tag("METADATA",6,true));
                //End entry
                $status = fwrite ($bf,end_tag("ENTRY",5,true));
                
                // backup files for this taoresource.
                $status = taoresource_backup_files($bf,$preferences,$taoresource_entry);
                
            }
            //End entries
            $status = fwrite ($bf,end_tag("ENTRIES",4,true));
            $taoresource_entry_backedup = true;
        }
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));
        
        return $status;
    }

    
    function taoresource_backup_files($bf,$preferences,$taoresource_entry) {
        global $CFG;
        require_once("$CFG->dirroot/mod/taoresource/lib.php");
        $status = true;

        if (empty($taoresource_entry->file)) {
            return true;
        }
        
        $filename = $CFG->dataroot.TAORESOURCE_RESOURCEPATH.$taoresource_entry->file;
        if (!file_exists($filename)) {
            return true ; // doesn't exist but we don't want to halt the entire process so still return true.
        }
        
        $status = $status && check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code.TAORESOURCE_RESOURCEPATH,true);
        
        // if this is somewhere deeply nested we need to do all the structure stuff first.....
        $status = $status && backup_copy_file($filename,
                                              $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code.
                                              TAORESOURCE_RESOURCEPATH.$taoresource_entry->file);
        return $status;
    }

    
   ////Return an array of info (name,value)
   function taoresource_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
       if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += taoresource_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
       //First the course data
       $info[0][0] = get_string("modulenameplural","taoresource");
       if ($ids = taoresource_ids ($course)) {
           $info[0][1] = count($ids);
       } else {
           $info[0][1] = 0;
       }
       
       return $info;
   }

   ////Return an array of info (name,value)
   function taoresource_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function taoresource_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of taoresources
        $buscar="/(".$base."\/mod\/taoresource\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@TAORESOURCEINDEX*$2@$',$content);

        //Link to taoresource view by moduleid
        $buscar="/(".$base."\/mod\/taoresource\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@TAORESOURCEVIEWBYID*$2@$',$result);

        //Link to taoresource view by taoresourceid
        $buscar="/(".$base."\/mod\/taoresource\/view.php\?r\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@TAORESOURCEVIEWBYR*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of taoresources id
    function taoresource_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}taoresource a
                                 WHERE a.course = '$course'");
    }
   
?>
