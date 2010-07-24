<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth
 * @subpackage teosso
 *
 * Provide Single Sign On integration with Intel CA Federation Manager
 *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * TEOSSO authentication plugin.
**/
class auth_plugin_teosso extends auth_plugin_base {

    const ERRORS = true;

    /**
    * Constructor.
    */
    function auth_plugin_teosso() {
        auth_plugin_teosso::err('in auth_plugin_tesso init');

        if (!function_exists('simplexml_load_string')) {
            print_error('missingxmllib', 'auth_teosso');
        }
        $this->authtype = 'teosso';
        $this->config = get_config('auth/teosso');
    }

    /**
    * Returns true if the username and password work and false if they are
    * wrong or don't exist.
    *
    * @param string $username The username (with system magic quotes)
    * @param string $password The password (with system magic quotes)
    * @return bool Authentication success or failure.
    */
    function user_login($username, $password) {
        // if true, user_login was initiated by teosso/index.php
        auth_plugin_teosso::err('in user_login');
        if(isset($GLOBALS['teosso_login'])) {
            unset($GLOBALS['teosso_login']);
            return TRUE;
        }

        return FALSE;
    }


    /**
    * Returns the user information for 'external' users. In this case the
    * attributes provided by teosso
    *
    * @return array $result Associative array of user data
    */
    function get_userinfo($username) {
        if($login_attributes = $GLOBALS['teosso_login_attributes']) {
            $attributemap = auth_plugin_teosso::get_attributes();
            $result = array();

            foreach ($attributemap as $key => $value) {
                if(isset($login_attributes[$value]) && !empty($login_attributes[$value])) {
                    $result[$key] = $login_attributes[$value];
                }
            }

            unset($GLOBALS['teosso_login_attributes']);
            return $result;
        }

        return FALSE;
    }

    /*
    * Returns array containg attribute mappings between Moodle and teosso.
    */
    static function get_attributes() {
        // get the config again as this is a static call
        $pluginconfig = (array) get_config('auth/teosso');

        $fields = array("firstname", "lastname", "email", "phone1", "phone2",
            "department", "address", "city", "country", "description",
            "idnumber", "lang", "guid", "username");

        $moodleattributes = array();
        foreach ($fields as $field) {
            if (isset($pluginconfig["field_map_$field"])) {
                $moodleattributes[$field] = $pluginconfig["field_map_$field"];
            }
        }

        return $moodleattributes;
    }

    /**
    * Returns true if this authentication plugin is 'internal'.
    *
    * @return bool
    */
    function is_internal() {
        return false;
    }

    /**
    * Returns true if this authentication plugin can change the user's
    * password.
    *
    * @return bool
    */
    function can_change_password() {
        return false;
    }

    /*
    * Login page hook - we get to see everything before the page displays
    */
    function loginpage_hook() {
        // Prevent username from being shown on login page after logout
        global $CFG;
        auth_plugin_teosso::err('in loginpage_hook');

        $CFG->nolastloggedin = true;
        return;
    }

    /*
    * Logout page hook - we get to see everything before the page displays
    * send them back to the CA Federation Manager logout mechanism
    */
    function logoutpage_hook() {
        global $CFG, $USER;
        auth_plugin_teosso::err('in logoutpage_hook');

        if($USER->auth == 'teosso' && $this->config->dologout) {
            set_moodle_cookie('nobody');
            require_logout();
            redirect($this->config->signout_url);
        }
    }

    /**
    * Prints a form for configuring this authentication plugin.
    *
    * This function is called from admin/auth.php, and outputs a full page with
    * a form for configuring this plugin.
    *
    * @param array $page An object containing all the data for this page.
    */

    function config_form($config, $err, $user_fields) {
        global $CFG;

        // setup the defaults for field maps that are not catered for by
        // standard Moodle
        $config = $this->config;
        if (!isset ($config->field_map_username)) {
            $config->field_map_username = 'HTTP_LOGINID';
        }
        if (empty($config->field_map_firstname)) {
            set_config('field_map_firstname', 'HTTP_FIRSTNAME', 'auth/teosso');
            set_config('field_updatelocal_firstname', 'onlogin', 'auth/teosso');
            set_config('field_lock_firstname', 'locked', 'auth/teosso');
        }
        if (empty($config->field_map_lastname)) {
            set_config('field_map_lastname', 'HTTP_LASTNAME', 'auth/teosso');
            set_config('field_updatelocal_lastname', 'onlogin', 'auth/teosso');
            set_config('field_lock_lastname', 'locked', 'auth/teosso');
        }
        if (empty($config->field_map_email)) {
            set_config('field_map_email', 'HTTP_EMAIL', 'auth/teosso');
            set_config('field_updatelocal_email', 'onlogin', 'auth/teosso');
            set_config('field_lock_email', 'locked', 'auth/teosso');
        }
        if (empty($config->field_map_idnumber)) {
            set_config('field_map_idnumber', 'HTTP_EBUSAGENTID', 'auth/teosso');
            set_config('field_updatelocal_idnumber', 'oncreate', 'auth/teosso');
            set_config('field_lock_idnumber', 'locked', 'auth/teosso');
        }

        if (!isset ($config->signin_url)) {
            $config->signin_url = 'https://federation.intel.com/federation?target='.urlencode('http://teachonline.intel.com/auth/teosso/');
        }

        if (!isset ($config->signin_error_url)) {
            $config->signin_error_url = 'https://ssl.intel.com/EducationUser/login.aspx?Channel=en&ProgramID=TWS&Target=http%3a%2f%2feducate.intel.com%2fworkspace%2fauth%2fCheckStatus.aspx%3fLID%3den%26tid%3dsr&ERRORID=1001';
        }

        if (!isset ($config->dologout)) {
            $config->dologout = '';
        }
        if (!isset ($config->signout_url)) {
            $config->signout_url = 'https://federation.intel.com/logout?target='.urlencode($CFG->wwwroot);
        }
        if (!isset ($config->cpm_edit_url)) {
            $config->cpm_edit_url = 'https://ssl.intel.com/EducationUser/Registration.aspx?channel=en&ProgramID=TWS&mode=Edit';
        }
        if (!isset ($config->notshowusername)) {
            $config->notshowusername = '';
        }

        include "config.html";
    }


    /**
    * Processes and stores configuration data for this authentication plugin.
    *
    *
    * @param object $config Configuration object
    */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset ($config->dologout)) {
            $config->dologout = '';
        }
        if (!isset ($config->notshowusername)) {
            $config->notshowusername = '';
        }
        if (!isset ($config->signin_url)) {
            $config->signin_url = 'https://federation.intel.com/federation?target='.urlencode('http://teachonline.intel.com/auth/teosso/');
        }
        if (!isset ($config->signin_error_url)) {
            $config->signin_error_url = 'https://ssl.intel.com/EducationUser/login.aspx?Channel=en&ProgramID=TWS&Target=http%3a%2f%2feducate.intel.com%2fworkspace%2fauth%2fCheckStatus.aspx%3fLID%3den%26tid%3dsr&ERRORID=1001';
        }
        if (!isset ($config->signout_url)) {
            $config->signout_url = 'https://federation.intel.com/logout?target='.urlencode($CFG->wwwroot);
        }
        if (!isset ($config->cpm_edit_url)) {
            $config->cpm_edit_url = 'https://ssl.intel.com/EducationUser/Registration.aspx?channel=en&ProgramID=TWS&mode=Edit';
        }
        if (!isset ($config->field_map_username)) {
            $config->field_map_username = 'HTTP_LOGINID';
        }

        // save settings
        set_config('signin_url', $config->signin_url, 'auth/teosso');
        set_config('signin_error_url', $config->signin_error_url, 'auth/teosso');
        set_config('dologout', $config->dologout, 'auth/teosso');
        set_config('notshowusername', $config->notshowusername, 'auth/teosso');
        set_config('signout_url', $config->signout_url, 'auth/teosso');
        set_config('cpm_edit_url', $config->cpm_edit_url, 'auth/teosso');
        set_config('field_map_username', $config->field_map_username, 'auth/teosso');

        return true;
    }


    /*
    * Pull the list of attributes out of the XML stuffed in the header
    */
    static function get_sso_attributes() {
        $attribute_list = array('acct_id' => 'idnumber', 'acct_name' => 'username',
                                'email' => 'email', 'firstn' => 'firstname',
                                'lastn' => 'lastname');
        $attribute_list = array('field_map_username', 'field_map_firstname',
                                'field_map_lastname', 'field_map_email', 'field_map_idnumber');

        // get the config again as this is a static call
        $pluginconfig = get_config('auth/teosso');

        //retrieve the login data from the HTTP Headers
        $headers = apache_request_headers();
        foreach ($headers as $header => $value) {
            auth_plugin_teosso::err("$header: $value");
        }
        $attributes = array();
        foreach ($attribute_list as $attribute) {
            if (isset($headers[$pluginconfig->$attribute])) {
                $attributes[$pluginconfig->$attribute] = $headers[$pluginconfig->$attribute];
            }
        }
        foreach ($attributes as $key => $value) {
            auth_plugin_teosso::err("$key => $value");
        }
        return $attributes;
    }


    /**
    * Write an error message to stderr - I want this stuff to go the th apache log
    *
    * @param string $msg message to output
    */
    static function err($msg) {
        if (!auth_plugin_teosso::ERRORS) return;

        $stderr = fopen('php://stderr', 'w');
        fwrite($stderr,"auth_plugin_teosso: ". $msg . "\n");
        fclose($stderr);
    }
}

?>