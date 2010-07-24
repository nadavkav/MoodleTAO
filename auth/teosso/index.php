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

session_write_close();

require_once('../../config.php');
require_once('auth.php');

global $CFG, $USER;

$logout = optional_param('logout', false, PARAM_INT);

$config = get_config('auth/teosso');

if ($logout) {
    // log the session out - redirect to special Intel logout URL
    auth_plugin_teosso::err('in logout sequence');
    require_logout();
    redirect($config->signout_url);
}
elseif(!isloggedin() && isguestuser()) {
    // not sure if we need this one... guest user to be treated differently?
    auth_plugin_teosso::err('in guest user login sequence');
}
elseif(!isloggedin()) {
    // initiate login sequence - redirect to special login link for Intel
    auth_plugin_teosso::err('in login sequence');
    teosso_authenticate_user();
}
else {
    // is logged in - go back to the main site page
    auth_plugin_teosso::err('allready logged in');
    header('Location: '.$CFG->wwwroot);
}



// parse out the TEO SSO authentication header, and process the login
//SM_USER = <cpm><acct_id>123456</acct_id><acct_name>hoang</acct_name><email>hoang.m.nguyen@intel.com</email>
//          <firstn>Hoang</firstn><lastn>Nguyen</lastn></cpm>
function teosso_authenticate_user() {
    global $CFG, $USER, $SESSION;

    $pluginconfig = get_config('auth/teosso');

    // retrieve the login data from the HTTP Headers
    $attributes = auth_plugin_teosso::get_sso_attributes();

    // check to see if we got any authentication data
    if (empty($attributes)) {
        redirect($pluginconfig->signin_url);
    }

    // get the http headers for error reporting
    $headers = apache_request_headers();
    $attr_hdrs = array();
    foreach ($headers as $key => $value) {
        if (preg_match('/^HTTP_/', $key)) {
            $attr_hdrs[]= $key .': ' . $value;
        }
    }
    $headers = implode(' | ', $attr_hdrs);

    // FIND THE VALIDIDTY OF THE HTTP HEADER
    $attrmap = auth_plugin_teosso::get_attributes();
    if (empty($attrmap['idnumber'])) {
        // serious misdemeanour
        print_error('missingidnumber', 'auth_teosso');
    }
    if (empty($attributes[$attrmap['idnumber']])) { #
        // not valid session. Ship user off to Federation Manager
        add_to_log(0, 'login', 'error', '/auth/teosso/index.php', get_string('idnumber_error', 'auth_teosso', $headers));
        redirect($pluginconfig->signin_error_url);
    } else {
        // in theory we only need acct_id at this point - we should retrieve the user record to get the username via idnumber
        if (! $user = get_record('user', 'idnumber', $attributes[$attrmap['idnumber']])) {
            // must be a new user
            if (!empty($attributes[$attrmap['username']])) {
                $attributes['username'] = $attributes[$attrmap['username']];
            }
            else {
                add_to_log(0, 'login', 'error', '/auth/teosso/index.php', get_string('username_error', 'auth_teosso', $headers));
                redirect($pluginconfig->signin_error_url);
            }
        }
        else {
            // user must use the auth type teosso or authenticate_user_login() will fail
            if ($user->auth != 'teosso') {
                add_to_log(0, 'login', 'error', '/auth/teosso/index.php', get_string('user_auth_type_error', 'auth_teosso', $headers));
                redirect($pluginconfig->signin_error_url);
            }
            // because we want to retain acct_id as the master ID
            // we need to modify idnumber on mdl_user NOW - so it all lines up later
            if (isset($attributes[$attrmap['username']]) && $user->username != $attributes[$attrmap['username']]) {
                if (!set_field('user', 'username', $attributes[$attrmap['username']], 'id', $user->id)) {
                    print_error('usernameupdatefailed', 'auth_teosso');
                }
                $attributes['username'] = $attributes[$attrmap['username']];
            } else {
                $attributes['username'] = $user->username;
            }
        }

        // Valid session. Register or update user in Moodle, log him on, and redirect to Moodle front
        // we require the plugin to know that we are now doing a teosso login in hook puser_login
        $GLOBALS['teosso_login'] = TRUE;

        // make variables accessible to teosso->get_userinfo. Information will be requested from authenticate_user_login -> create_user_record / update_user_record
        $GLOBALS['teosso_login_attributes'] = $attributes;

        // just passes time as a password. User will never log in directly to moodle with this password anyway or so we hope?
        $USER = authenticate_user_login($attributes['username'], time());
        $USER->loggedin = true;
        $USER->site     = $CFG->wwwroot;

        update_user_login_times();

        if($pluginconfig->notshowusername) {
            // Don't show username on login page
            set_moodle_cookie('nobody');
        }

        set_login_session_preferences();
        add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);
        check_enrolment_plugins($USER);
        load_all_capabilities();

        // just fast copied this from some other module - might not work...
        if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
            $urltogo = $SESSION->wantsurl;
        } else {
            $urltogo = $CFG->wwwroot.'/';
        }
        unset($SESSION->wantsurl);
        redirect($urltogo);
    }
}

?>