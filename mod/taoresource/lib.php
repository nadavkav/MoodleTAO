<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

define('TAORESOURCE_LOCALPATH', 'LOCALPATH');
define('TAORESOURCE_TEMPPATH', '/temp/taoresources/');
define('TAORESOURCE_RESOURCEPATH', '/taoresources/');
define('TAORESOURCE_SEARCH_LIMIT', '200');
define('TAORESOURCE_RESULTS_PER_PAGE', '20');


global $TAORESOURCE_WINDOW_OPTIONS, $TAORESOURCE_CORE_ELEMENTS, $TAORESOURCE_METADATA_ELEMENTS; // must be global because it might be included from a function!
$TAORESOURCE_WINDOW_OPTIONS = array('resizable', 'scrollbars', 'directories', 'location',
                                 'menubar', 'toolbar', 'status', 'width', 'height');

$TAORESOURCE_CORE_ELEMENTS = array('id', 'identifier', 'title', 'description', 'keywords',
                                   'url', 'file', 'type', 'remoteid', 'mimetype',
                                   'lang', 'timemodified');
$TAORESOURCE_METADATA_ELEMENTS = array('Contributor', 'IssueDate', 'TypicalAgeRange',
                                     'LearningResourceType', 'Rights', 'RightsDescription',
                                     'ClassificationPurpose', 'ClassificationTaxonPath');

if (!isset($CFG->taoresource_hide_repository)) {
    set_config("taoresource_hide_repository", "1");
}

require_once('taoresource_base.class.php');

require_once('taoresource_plugin_base.class.php');

require_once('taoresource_entry.class.php');

require_once('taoresource_metadata.class.php');

    
/**
* Find active plugins, load the class files, and instantiate
* the appropriate plugin object.
*/
function taoresource_get_plugins() {
    global $CFG;
    
    $plugins = array();
    $taoentryplugins = get_list_of_plugins('mod/taoresource/plugins');
    foreach ($taoentryplugins as $taoentryplugin) {
        if (!empty($CFG->{'taoresource_plugin_hide_'.$taoentryplugin})) {  // Not wanted
            continue;
        }
        require_once("$CFG->dirroot/mod/taoresource/plugins/$taoentryplugin/plugin.class.php");
        $taoresourceclass = "taoresource_plugin_$taoentryplugin";
        $plugin = new $taoresourceclass();
        $plugins[] = $plugin;
    }
    return $plugins;
}

/**
* Check all the plugins to see if they specify the extra screen
*/
function taoresource_extra_resource_screen() {
    $plugins = taoresource_get_plugins();
    $extra = false;
    foreach ($plugins as $plugin) {
        $extra = $plugin->taoresource_entry_extra_form_required();
        if ($extra) {
            break;
        }
    }
    return $extra;
}


/**
* callback method from modedit.php for adding a new taoresource instance
*/
function taoresource_add_instance($taoresource) {
    global $CFG;

    $taoresource->type = clean_param($taoresource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/taoresource/type/$taoresource->type/resource.class.php");
    $taoresourceclass = "taoresource_$taoresource->type";
    $res = new $taoresourceclass();

    return $res->add_instance($taoresource);
}


/**
* callback method from modedit.php for updating a taoresource instance
*/
function taoresource_update_instance($taoresource) {
    global $CFG;

    $taoresource->type = clean_param($taoresource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/taoresource/type/$taoresource->type/resource.class.php");
    $taoresourceclass = "taoresource_$taoresource->type";
    $res = new $taoresourceclass();

    return $res->update_instance($taoresource);
}


/**
* callback method from modedit.php for deleting a taoresource instance
*/
function taoresource_delete_instance($id) {
    global $CFG;

    if (! $taoresource = get_record("taoresource", "id", "$id")) {
        return false;
    }

    $taoresource->type = clean_param($taoresource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/taoresource/type/$taoresource->type/resource.class.php");
    $taoresourceclass = "taoresource_$taoresource->type";
    $res = new $taoresourceclass();

    return $res->delete_instance($taoresource);
}

/**
 * What does this do?
 */
function taoresource_user_outline($course, $user, $mod, $taoresource) {
    if ($logs = get_records_select("log", "userid='$user->id' AND module='taoresource'
                                           AND action='view' AND info='$taoresource->id'", "time ASC")) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new object();
        $result->info = get_string("numviews", "", $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}


/**
 * What does this do?
 */
function taoresource_user_complete($course, $user, $mod, $taoresource) {
    global $CFG;

    if ($logs = get_records_select("log", "userid='$user->id' AND module='taoresource'
                                           AND action='view' AND info='$taoresource->id'", "time ASC")) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string("mostrecently");
        $strnumviews = get_string("numviews", "", $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string("neverseen", "taoresource");
    }
}

/**
 * What does this do?
 */
function taoresource_get_participants($taoresourceid) {
//Returns the users with data in one taoresource
//(NONE, byt must exists on EVERY mod !!)

    return false;
}

/**
 * This constructs the course module information the gets used
 * in the course module list.
 */
function taoresource_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///

   global $CFG;

   $info = NULL;

   if ($taoresource = get_record("taoresource", "id", $coursemodule->instance, '', '', '', '', 'id, popup, identifier, type, name')) {
       $taoresource_entry = taoresource_entry::read($taoresource->identifier);
       $info = new object();
       $info->name = $taoresource->name;
       if (!empty($taoresource->popup)) {
           $info->extra =  urlencode("onclick=\"this.target='taoresource$taoresource->id'; return ".
                            "openpopup('/mod/taoresource/view.php?inpopup=true&amp;id=".
                            $coursemodule->id.
                            "','taoresource$taoresource->id','$taoresource->popup');\"");
       }

       require_once($CFG->libdir.'/filelib.php');

       if ($taoresource->type == 'file') {
           if (!$taoresource_entry) {
               $icon = 'unknown.gif';
           }
           else {
               $icon = mimeinfo("icon", $taoresource_entry->url);
           }
           if ($icon != 'unknown.gif') {
               $info->icon ="f/$icon";
           } else {
               $info->icon ="f/web.gif";
           }
       } else if ($taoresource->type == 'directory') {
           $info->icon ="f/folder.gif";
       }
   }

   return $info;
}

function taoresource_fetch_remote_file ($cm, $url, $headers = "" ) {
/// Snoopy is an HTTP client in PHP

    global $CFG;

    require_once("$CFG->libdir/snoopy/Snoopy.class.inc");

    $client = new Snoopy();
    $ua = 'Moodle/'. $CFG->release . ' (+http://moodle.org';
    if ( $CFG->taoresource_usecache ) {
        $ua = $ua . ')';
    } else {
        $ua = $ua . '; No cache)';
    }
    $client->agent = $ua;
    $client->read_timeout = 5;
    $client->use_gzip = true;
    if (is_array($headers) ) {
        $client->rawheaders = $headers;
    }

    @$client->fetch($url);
    if ( $client->status >= 200 && $client->status < 300 ) {
        $tags = array("A"      => "href=",
                      "IMG"    => "src=",
                      "LINK"   => "href=",
                      "AREA"   => "href=",
                      "FRAME"  => "src=",
                      "IFRAME" => "src=",
                      "FORM"   => "action=");

        foreach ($tags as $tag => $key) {
            $prefix = "fetch.php?id=$cm->id&amp;url=";
            if ( $tag == "IMG" or $tag == "LINK" or $tag == "FORM") {
                $prefix = "";
            }
            $client->results = taoresource_redirect_tags($client->results, $url, $tag, $key,$prefix);
        }
    } else {
        if ( $client->status >= 400 && $client->status < 500) {
            $client->results = get_string("fetchclienterror","taoresource");  // Client error
        } elseif ( $client->status >= 500 && $client->status < 600) {
            $client->results = get_string("fetchservererror","taoresource");  // Server error
        } else {
            $client->results = get_string("fetcherror","taoresource");     // Redirection? HEAD? Unknown error.
        }
    }
    return $client;
}

function taoresource_redirect_tags($text, $url, $tagtoparse, $keytoparse,$prefix = "" ) {
    $valid = 1;
    if ( strpos($url,"?") == FALSE ) {
        $valid = 1;
    }
    if ( $valid ) {
        $lastpoint = strrpos($url,".");
        $lastslash = strrpos($url,"/");
        if ( $lastpoint > $lastslash ) {
            $root = substr($url,0,$lastslash+1);
        } else {
            $root = $url;
        }
        if ( $root == "http://" or
             $root == "https://") {
            $root = $url;
        }
        if ( substr($root,strlen($root)-1) == '/' ) {
            $root = substr($root,0,-1);
        }

        $mainroot = $root;
        $lastslash = strrpos($mainroot,"/");
        while ( $lastslash > 9) {
            $mainroot = substr($mainroot,0,$lastslash);

            $lastslash = strrpos($mainroot,"/");
        }

        $regex = "/<$tagtoparse (.+?)>/is";
        $count = preg_match_all($regex, $text, $hrefs);
        for ( $i = 0; $i < $count; $i++) {
            $tag = $hrefs[1][$i];

            $poshref = strpos(strtolower($tag),strtolower($keytoparse));
            $start = $poshref + strlen($keytoparse);
            $left = substr($tag,0,$start);
            if ( $tag[$start] == '"' ) {
                $left .= '"';
                $start++;
            }
            $posspace   = strpos($tag," ", $start+1);
            $right = "";
            if ( $posspace != FALSE) {
                $right = substr($tag, $posspace);
            }
            $end = strlen($tag)-1;
            if ( $tag[$end] == '"' ) {
                $right = '"' . $right;
            }
            $finalurl = substr($tag,$start,$end-$start+$diff);
            // Here, we could have these possible values for $finalurl:
            //     file.ext                             Add current root dir
            //     http://(domain)                      don't care
            //     http://(domain)/                     don't care
            //     http://(domain)/folder               don't care
            //     http://(domain)/folder/              don't care
            //     http://(domain)/folder/file.ext      don't care
            //     folder/                              Add current root dir
            //     folder/file.ext                      Add current root dir
            //     /folder/                             Add main root dir
            //     /folder/file.ext                     Add main root dir

            // Special case: If finalurl contains a ?, it won't be parsed
            $valid = 1;

            if ( strpos($finalurl,"?") == FALSE ) {
                $valid = 1;
            }
            if ( $valid ) {
                if ( $finalurl[0] == "/" ) {
                    $finalurl = $mainroot . $finalurl;
                } elseif ( strtolower(substr($finalurl,0,7)) != "http://" and
                           strtolower(substr($finalurl,0,8)) != "https://") {
                     if ( $finalurl[0] == "/") {
                        $finalurl = $mainroot . $finalurl;
                     } else {
                        $finalurl = "$root/$finalurl";
                     }
                }

                $text = str_replace($tag,"$left$prefix$finalurl$right",$text);
            }
        }
    }
    return $text;
}

/**
 * Check to see if a given URI is a URL.
 * 
 * @param $path  string, URI.
 * 
 * @return bool, true = is URL
 */
function taoresource_is_url($path) {
    if (strpos($path, '://')) {     // eg http:// https:// ftp://  etc
        return true;
    }
    if (strpos($path, '/') === 0) { // Starts with slash
        return true;
    }
    return false;
}

/**
 * Get the list of supported types compatible with mod/resource.
 * 
 * @return array, resource type objects
 */
function taoresource_get_types() {
    global $CFG;

    $types = array();

    $standardtaoresources = array('file');
    foreach ($standardtaoresources as $taoresourcetype) {
        $type = new object();
        $type->modclass = MOD_CLASS_RESOURCE;
        $type->name = $taoresourcetype;
        $type->type = "taoresource&amp;type=$taoresourcetype";
        $type->typestr = get_string("taoresourcetype$taoresourcetype", 'taoresource');
        $types[] = $type;
    }

    /// Drop-in extra taoresource types
    $taoresourcetypes = get_list_of_plugins('mod/taoresource/type');
    foreach ($taoresourcetypes as $taoresourcetype) {
        if (!empty($CFG->{'taoresource_hide_'.$taoresourcetype})) {  // Not wanted
            continue;
        }
        if (!in_array($taoresourcetype, $standardtaoresources)) {
            $type = new object();
            $type->modclass = MOD_CLASS_RESOURCE;
            $type->name = $taoresourcetype;
            $type->type = "taoresource&amp;type=$taoresourcetype&amp;identifier=abc";
            $type->typestr = get_string("taoresourcetype$taoresourcetype", 'taoresource');
            $types[] = $type;
        }
    }

    return $types;
}

function taoresource_get_view_actions() {
    return array('view','view all');
}

function taoresource_get_post_actions() {
    return array();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * 
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function taoresource_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 * 
 * @return array, of capabilities
 */ 
function taoresource_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * Function to check and create the needed moddata dir to
 * save all the mod backup files. We always name it moddata
 * to be able to restore it, but in restore we check for
 * $CFG->moddata !!
 * 
 * @return bool, true = dir exists
 */
function taoresource_check_and_create_moddata_temp_dir() {

    global $CFG;
    $status = check_dir_exists($CFG->dataroot.TAORESOURCE_TEMPPATH,true);
    return $status;
}

/**
 * Function to check and create the needed moddata dir to
 * save all the mod backup files. We always name it moddata
 * to be able to restore it, but in restore we check for
 * $CFG->moddata !!
 * 
 * @return bool, true = dir exists
 */
function taoresource_check_and_create_moddata_taoresource_dir() {

    global $CFG;
    $status = check_dir_exists($CFG->dataroot.TAORESOURCE_RESOURCEPATH,true);
    return $status;
}

/**
 *  copy file - most likely tmp file to temp, while resource details are 
 *  sorted out
 * 
 * @param $from_file string, source file for copy
 * @param $to_file string, destination location for file
 * @param $log_clam bool, shall we log this?
 * @return bool, true = success
 */
function taoresource_copy_file($from_file,$to_file,$log_clam=false) {

    global $CFG;

    if (is_file($from_file)) {
        //echo "<br />Copying ".$from_file." to ".$to_file;              //Debug
        //$perms=fileperms($from_file);
        //return copy($from_file,$to_file) && chmod($to_file,$perms);
        umask(0000);
        if (copy($from_file,$to_file)) {
            chmod($to_file,$CFG->directorypermissions);
            if (!empty($log_clam)) {
                clam_log_upload($to_file,null,true);
            }
            return true;
        }
        return false;
    } 
    else {
        //echo "<br />Error: not file or dir ".$from_file;               //Debug
        return false;
    }
}


/**
 *  delete file - most likely removing temp file
 * 
 * @param $file string, location of file to delete
 * @return bool, true = succesful delete
 */
function taoresource_delete_file($file) {

    if (is_file($file)) {
        chmod($file, 0777);
        if (((unlink($file))) == FALSE) {
            return false;
        }
        return true;
    }
    else {
        return false;
    }
}

/**
 * generate key/unique name of file
 * 
 * @param $file string, location of file
 * @return string, sha1 hash of file contents  
 */
function taoresource_sha1file($file) {
     return sha1(file_get_contents($file));
}

/**
 * format the URL correctly for local files    
 * 
 * @param $path string, the physical resource location
 * @param $options array, query string parameters to be passed along
 * @return string, formated URL.
 */
function taoresource_get_file_url($path, $options=null) {
    global $CFG, $HTTPSPAGEREQUIRED;

    $path = str_replace('//', '/', $path);  
    $path = trim($path, '/'); // no leading and trailing slashes

    $url = $CFG->wwwroot."/mod/taoresource/file.php";
    if ($CFG->slasharguments) {
        $parts = explode('/', $path);
        $parts = array_map('rawurlencode', $parts);
        $path  = implode('/', $parts);
        $ffurl = $url.'/'.$path;
        $separator = '?';
    } else {
        $path = rawurlencode('/'.$path);
        $ffurl = $url.'?file='.$path;
        $separator = '&amp;';
    }

    if ($options) {
        foreach ($options as $name=>$value) {
            $ffurl = $ffurl.$separator.$name.'='.$value;
            $separator = '&amp;';
        }
    }

    return $ffurl;
}


/**
 * return a 404 if a TAO Resource is not found
 * 
 * @param courseid int, the current context course    
 */
function taoresource_not_found($courseid=0) {
    global $CFG;
    header('HTTP/1.0 404 not found');
    $url = $CFG->wwwroot;
    if ($courseid != 0) {
        $url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
    }
    error('filenotfound', 'taoresource', $url); //this is not displayed on IIS??
}


/**
 * on the install we still need to index the text description
 * which the install.xml syntax does not let us do in a database
 * dependent fashion
 */
function taoresource_install() {
    global $CFG;

    $result = true;
    
    if (preg_match('/^postgres/', $CFG->dbtype)) {
        $idx_field = 'description';
    }
    else {
        $idx_field = 'description(1000)';
    }
    $table = new XMLDBTable('taoresource_entry');
    $index = new XMLDBIndex('description');
    $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array($idx_field));
    if (!index_exists($table, $index)) {
        $result = add_index($table, $index, false, false);
    }
    return $result;
}

?>