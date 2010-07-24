<?PHP // $Id$

// Allows the admin to configure other Moodle hosts info

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
include_once($CFG->dirroot.'/mnet/lib.php');
require_once($CFG->dirroot . '/mnet/xmlrpc/client.php'); //mnet client library
require_once('mnet_userprofile_form.php');

require_login();
admin_externalpage_setup('mnetpeers');

$context = get_context_instance(CONTEXT_SYSTEM);

require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

/// Initialize variables.
$hostid = required_param('hostid', PARAM_INT);

$strmnetuserprofile = get_string('mnetuserprofile', 'mnet');

admin_externalpage_print_header();

$mnet_peer->id = $hostid; //hack to make tabs.php behave.
$mnet_peer->application->name = '';//hack to make tabs.php behave.

$currenttab = 'mnetuserprofile';
require_once($CFG->dirroot .'/admin/mnet/tabs.php');

$userfields = mnet_get_user_fields();
$externalfields = mnet_get_externalprofilefields($hostid);
if (empty($externalfields)) {
    //todo - should use a default set of $externalfields as none have been returned from the external system
    error("Mnet returned no externalfields - please upgrade your external system");
}

$mform = new mnetuserpofile_form('', array('hostid' => $hostid, 'externalvalues' => $externalfields, 'internalvalues' => $userfields));

$mnetconfig = get_records_menu('config_plugins', 'plugin', 'mnet_userprofile_'.$hostid, '', 'UPPER(name), UPPER(value)');

$mform->set_data($mnetconfig);

/// If data submitted, process and store
if (($form = data_submitted()) && confirm_sesskey()) {
    foreach($userfields as $field => $value) {
        set_config($value, strtolower($form->$field), 'mnet_userprofile_'.$hostid);
    }

}
$mform->display();
admin_externalpage_print_footer();

function mnet_get_externalprofilefields($hostid) {
    /// Setup MNET environment
    global $MNET,$CFG;
    if (empty($MNET)) {
        $MNET = new mnet_environment();
        $MNET->init();
    }

/// Setup the server
    $host = get_record('mnet_host','id', $hostid); //we retrieve the server(host) from the 'mnet_host' table
    if (empty($host)) {
        error('Invalid Hostid');
    }
    $mnet_peer = new mnet_peer();                          //we create a new mnet_peer (server/host)
    $mnet_peer->set_wwwroot($host->wwwroot);               //we set this mnet_peer with the host http address

    $client = new mnet_xmlrpc_client();        //create a new client
    $client->set_method('auth/mnet/auth.php/get_user_profile_fields'); //tell it which method we're going to call
    $client->send($mnet_peer);                 //Call the server
    if (!empty($client->response['faultString'])) {
        error("Mnet error:".$client->response['faultString']);
    }

    return $client->response;
}
?>