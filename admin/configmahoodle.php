<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   admin
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * Disclaimer: This is an initial version with hardcoded lang strings/missing strings
 * it could do with a further tidy up.
 * at this stage, it only configures Moodle users to SSO into Mahara, it doesn't configure the reverse,
 */

 $moodledirroot = realpath(dirname(__FILE__).'/..');

 if (empty($_GET['step'])) { //display nice form to enter dirroot for Mahara
    require_once($moodledirroot.'/config.php');
    check_moodle(); //check to make sure Moodle is installed and can enable Networking and this user has access to do this.

    require_once($CFG->libdir . '/formslib.php');
//todo: dodgy class - should be in seperate file - keeping it here for install simplicity with the script using a single file.
class mahmoo_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'hdr', get_string('mahoodle', 'configmahoodle'));
        $mform->addElement('text', 'maharadir', get_string('maharadirroot', 'configmahoodle'), array('size'=>'40'));
        $strrequired = get_string('required');
        $mform->addRule('maharadir', 'required', 'required', null, 'client');
        $mform->addElement('static','description','',get_string('maharadirrootdesc','configmahoodle'));
        $this->add_action_buttons(false, get_string('execute', 'configmahoodle'));
    }
}
//end dodgy inline class.    
    $strheader = get_string('mahoodle', 'configmahoodle');
    $navlinks = array();
    $navlinks[] = array('name' => $strheader, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($strheader, $strheader, $navigation, "");
    $mahform = new mahmoo_form('');
    if ($mdata = $mahform->get_data()) {
        global $CFG;
        if (empty($mdata->maharadir) or !file_exists($mdata->maharadir.'/init.php')
                                     or !file_exists($mdata->maharadir.'/config.php')) {
            error('could not find Mahara dirroot');
        }

        //set Moodle Networking to be enabled!
        set_config('mnet_dispatcher_mode', 'strict');

        notify(get_string('step1', 'configmahoodle'),'notifysuccess');

        //redirect for step 2.
        redirect('configmahoodle.php?step=2&mad='.urlencode($mdata->maharadir));
    }
    $post->maharadir = '';
    if (file_exists($moodledirroot.'/mahara/config.php')) {
        $post->maharadir = $moodledirroot.'/mahara';
    } elseif (file_exists(realpath($moodledirroot.'../mahara/config.php'))) {
        $post->maharadir = realpath($moodledirroot.'../mahara/config.php');
    }
    echo "<p>".get_string('mahoodledesc', 'configmahoodle')."</p>";
    $mahform->set_data($post);
    $mahform->display();
    print_footer();
 }

 if (!empty($_GET['step'])) { //action time!
     $maharadirroot = strip_tags(urldecode($_GET['mad']));
     $moodlewebroot = parse_config($moodledirroot.'/config.php');
     $moodledataroot = parse_config($moodledirroot.'/config.php','dataroot');

     if (strpos($maharadirroot, '..') === 0) {
         //the dirroot cannot be relative.
         echo 'Invalid Maharadirroot';
         die;
     }

     if ($_GET['step']=='2') { //Enable Mahara networking
         check_mahara($maharadirroot, $moodledirroot); //check mahara is there, and that appropriate php stuff is set.
         require($maharadirroot.'/init.php');
         if (!set_config('enablenetworking','1')) {
             error('unable to enable Mahara Networking');
         }
         //now set up new insitution
         $newinstitution = new StdClass;
         $newinstitution->name = 'localmoodle';
         $newinstitution->displayname = 'Local Moodle';
         $newinstitution->authplugin = null;
         $newinstitution->registerallowed = 0;
         $newinstitution->theme = null;
         $newinstitution->defaultmembershipperiod = null;
         $newinstitution->maxuseraccounts = null;
         $currentinstitution = get_record('institution','name', $newinstitution->name);

         if (empty($currentinstitution)) { //new config so insert a new record,
             insert_record('institution', $newinstitution);
         } else { //current install, so update the record.
             //institution table doesn't have an id field, so normal update_record won't work!
             update_record('institution', $newinstitution, array('name' => $newinstitution->name));
         }
         //now configure XMLRPC
         $authinstance = new stdClass();
         // Get the auth instance with the highest priority number (which is
         $lastinstance = get_records_array('auth_instance', 'institution', $newinstitution->name, 'priority DESC', '*', '0', '1');
         if ($lastinstance == false) {
             $authinstance->priority = 0;
         } else {
             $authinstance->priority = $lastinstance[0]->priority + 1;
         }

         $authinstance->instancename = 'xmlrpc';
         $authinstance->institution  = 'localmoodle';
         $authinstance->authname     = 'xmlrpc';
         $currentauthins = get_record('auth_instance', 'institution', 'localmoodle','instancename', 'xmlrpc');
         if (empty($currentauthins)) {
            $authinstance->id = insert_record('auth_instance', $authinstance, 'id', true);
         } else {
            $authinstance->id = $currentauthins->id;
         }

         $auth_config = array(  'wwwroot'               => $moodlewebroot,
                                'updateuserinfoonlogin' => '1',
                                'weautocreateusers'     => '1',
                                'theyssoin'             => '1',
                                'weimportcontent'       => '1',
                                );
         foreach($auth_config as $field => $value) {
             $record = new stdClass();
             $record->instance = $authinstance->id;
             $record->field    = $field;
             $record->value    = $value;
             $currentrec = get_record('auth_instance_config', 'instance', $authinstance->id, 'field', $field);
             if (empty($currentrec)) {
                 insert_record('auth_instance_config', $record);
             } else {
                 update_record('auth_instance_config', $record, array('field' => $field,'instance' => $authinstance->id));
             }
         }

         //now save pubkey stuff
         require($maharadirroot.'/lib/peer.php');
         require($maharadirroot.'/api/xmlrpc/lib.php');
         //first check to see if a host exists that needs to be deleted.
         $existinghost = get_record('host', 'institution', $newinstitution->name);
         if (!empty($existinghost)) {
             delete_records('host', 'institution', $newinstitution->name);
         }
         $peer = new peer();
         $peer->bootstrap($moodlewebroot, null, 'moodle',$authinstance->institution);
         $peer->commit();

         //now write Mahara's Webroot to a file that we can read in step 3
         $mfile = $moodledataroot. "/maharaweb.txt";
         $fh = fopen($mfile, 'w') or die("can't open file");
         fwrite($fh, $CFG->wwwroot);
         fclose($fh);

         redirect($moodlewebroot.'/admin/configmahoodle.php?step=3&mad='.urlencode($maharadirroot));
     } elseif ($_GET['step']=='3') { //now configure Moodle to connect to the Mahara install.
         require_once($moodledirroot.'/config.php');
         check_moodle(); //check to make sure Moodle is installed and can enable Networking.
         include_once($moodledirroot.'/mnet/lib.php');

         $mfile = $CFG->dataroot. "/maharaweb.txt";
         if (!file_exists($mfile)) {
             error('cannot find maharawebroot from file');
         }
         $fh = fopen($mfile, 'r');
         $maharawebroot = fread($fh, filesize($mfile));
         fclose($fh);
         if (empty($maharawebroot)) {
             error('Step3 - could not find maharawebroot. Config failed.');
         }
         $strheader = get_string('mahoodle', 'configmahoodle');
         $navlinks = array();
         $navlinks[] = array('name' => $strheader, 'link' => null, 'type' => 'misc');
         $navigation = build_navigation($navlinks);
         print_header($strheader, $strheader, $navigation, "");

         //now set up pubkey stuff in Moodle
         $mnet_peer = new mnet_peer();
         $application = get_record('mnet_application', 'name', 'mahara');
         $mnet_peer->set_applicationid($application->id);
         $mnet_peer->bootstrap($maharawebroot, null, $application->name);
         $mnet_peer->set_name('localmahara');
         $mnet_peer->commit();

         //now configure Networking in Moodle.
         //first get hostid.
         $hostid = get_field('mnet_host', 'id', 'name', 'localmahara');
         $host2service = new stdClass();

         $host2service->hostid = $hostid;
         $host2service->serviceid = get_field('mnet_service', 'id', 'name', 'sso_idp');
         $host2service->publish = 1;
         $host2service->subscribe = 0;
         if ($hostrec = get_record('mnet_host2service', 'hostid', $hostid, 'serviceid', $host2service->serviceid)) {
             $host2service->id = $hostrec->id;
             update_record('mnet_host2service', $host2service);
         } else {
             insert_record('mnet_host2service', $host2service);
         }
         $host2service->serviceid = get_field('mnet_service', 'id', 'name', 'sso_sp');
         $host2service->publish = 0;
         $host2service->subscribe = 1;
         if ($hostrec = get_record('mnet_host2service', 'hostid', $hostid, 'serviceid', $host2service->serviceid)) {
             $host2service->id = $hostrec->id;
             update_record('mnet_host2service', $host2service);
         } else {
             insert_record('mnet_host2service', $host2service);
         }
         //custom TAO stuff
         $host2service->serviceid = get_field('mnet_service', 'id', 'name', 'local_mahara');
         if (!empty($host2service->serviceid)) {
             $host2service->publish = 0;
             $host2service->subscribe = 1;
             if ($hostrec = get_record('mnet_host2service', 'hostid', $hostid, 'serviceid', $host2service->serviceid)) {
                 $host2service->id = $hostrec->id;
                update_record('mnet_host2service', $host2service);
             } else {
                 insert_record('mnet_host2service', $host2service);
             }
         }
         //now enable Moodle Network Authentication
         if (empty($CFG->auth)) {
             set_config('auth', 'mnet');
         } else {
             $authsenabled = explode(',',$CFG->auth);
             $authsenabled[] = 'mnet';
             $authsenabled = array_unique($authsenabled);
             set_config('auth', implode(',', $authsenabled));
         }

         //now give users the capability to roam to other sites.
         $authroleid = get_field('role','id','shortname','user');
         if (empty($authroleid)) {
            notify("could not assign the capability to Roam to a remote Moodle to the Authenticated User - this must be done manually.");
         } else {
             $systemcontext = get_context_instance(CONTEXT_SYSTEM);
             assign_capability('moodle/site:mnetlogintoremote', CAP_ALLOW, $authroleid, $systemcontext->id);
         }

         //all done!
         notify(get_string('mahoodlecomplete', 'configmahoodle'),'notifysuccess');
         print_footer();
     }
 }
 function check_moodle() {
     if (!record_exists('course', 'id', SITEID)) {
        error("Moodle has not completed installation yet - please install Moodle first!");
     }
     
     //make sure this user can enable networking.
     require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
     
     //now enable Moodle networking.
     //first check to make sure all the good stuff is available.
     if (!extension_loaded('openssl')) {
         set_config('mnet_dispatcher_mode', 'off');
         print_error('requiresopenssl', 'mnet', '', NULL, true);
     }

     if (!$site = get_site()) {
         set_config('mnet_dispatcher_mode', 'off');
         print_error('nosite', '', '', NULL, true);
     }

     if (!function_exists('curl_init') ) {
        set_config('mnet_dispatcher_mode', 'off');
        print_error('nocurl', 'mnet', '', NULL, true);
     }
     if(!function_exists('xmlrpc_encode_request')) {
         trigger_error("You must have xml-rpc enabled in your PHP build to use this feature.");
         print_error('xmlrpc-missing', 'mnet','peers.php');
         exit;
     }
 }
 function check_mahara($maharadirroot, $moodledirroot) {
     //check directories exist where specified.
     //this check should happen as part of step 1, but check again here too.
     if (!file_exists($maharadirroot.'/config.php') || !file_exists($maharadirroot.'/init.php')) {
         echo "Mahara not found";
         die;
     }
     define('INTERNAL', 1);
     define('ADMIN', 1);
 }
 //this function is used to extract wwwroot and dirroot from mahara and moodle config files
 //don't pass this around as a post/get var to prevent xss
 function parse_config($pathtoconfig, $name='wwwroot') {

     if (!file_exists($pathtoconfig)) {
         echo "invalid path to find ".$name;
         die;
     }
     $fileasstring = file_get_contents($pathtoconfig);
     $num = preg_match_all('/\$[Cc][Ff][Gg]->'.$name.' *= * \'.*\'/', $fileasstring, &$matches);
     foreach($matches[0] as $mid => $match) {
         if (strpos($match, 'http://example.com/moodle') > 0 or
            (strpos($match, 'http://myhost.com/mahara/') > 0)) {
             unset($matches[0][$mid]);
         }
     }
     if (empty($matches[0])) {
         echo "<p>Error: ".$name." not found</p>";
         die;
     } elseif (count($matches[0]) > 1) {
         echo "<p>Error: more than one ".$name." found</p>";
         die;
     }
     foreach($matches[0] as $match) { //only one entry, but pulls it out.
         $match = trim(str_ireplace('$CFG->'.$name, '', $match));
         $match = trim(str_ireplace('=', '', $match));
         $match = trim(str_ireplace("'", '', $match));
         return $match;
     }
     print_object($matches);

 }
?>