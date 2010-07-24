<?php

/**
 * @author Matt Clarkson <mattc@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Specialist Schools and Academies Trust SSO plugin
 *
 * Authenticate users against SSAT webservices.
 *
 * 2009-04-24  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');
require_once('ssatclient.class.php');



class auth_plugin_ssatsso extends auth_plugin_base {

    private $cacheduser;

    /**
     * Constructor.
     *
     * @return null
     */
    function auth_plugin_ssatsso() {
        $this->authtype = 'ssatsso';
        $this->config = get_config('auth/ssatsso');

        $this->wsdlurl    = isset($this->config->wsdlurl) ? $this->config->wsdlurl : '';
        $this->appkey     = isset($this->config->appkey) ? $this->config->appkey : '';
        $this->autologin  = isset($this->config->autologin) ? $this->config->autologin : false;
    }


    /**
     * Authenticate users credentials
     *
     * @param string $username
     * @param string $password
     * @return bool
     * @uses $CFG
     * @uses $SESSION
     */
    function user_login($username, $password) {
        global $CFG, $SESSION;

        $username = stripslashes($username);
        $password = stripslashes($password);

        try {
            $client = new SSATClient($this->wsdlurl, $this->appkey);

            if ($user = $client->validate_credentials($username, $password)) {
                $SESSION->ssat_user = $user;
                $SESSION->ssat_user_id = $user->user_id;

                return true; // Success - login the user
            } else {
                // Try again
            }

        } catch (Exception $e) {
            // Something went wrong with the webservice call - attempt to use cached auth details
            if ($user = get_record('user', 'username', addslashes($username), 'mnethostid', $CFG->mnet_localhost_id)) {
                if ($user->password = md5($password)) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Called before displaying the login page
     *
     * @return null
     * @uses $SESSION
     */
    function loginpage_hook() {
        global $SESSION;

        if (!empty($this->autologin)) {

            $token = optional_param('token', @$SESSION->token , PARAM_TEXT);

            if (!empty($token)) {
                $this->token_login($token);
            }
        }
    }


    /**
     * Get users details from external source
     *
     * @param string $username
     * @return object $user
     * @uses $SESSION
     */
     function get_userinfo($username) {
        global $SESSION;

        if (!isset($SESSION->ssat_user)) {
            return false;
        } else {
            $this->cacheduser = $SESSION->ssat_user;
            unset($SESSION->ssat_user);
        }
        $map = $this->get_ssat_fieldmap();

        foreach ($map as $local => $remote) {
            $user[$local] = $this->cacheduser->{$remote};
        }
        return $user;
    }


    /**
     * retuns user attribute mappings between moodle and SSAT
     *
     * @return array
     */
    function get_ssat_fieldmap() {
        $moodleattributes = array();
        foreach ($this->userfields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = $this->config->{"field_map_$field"};
            }
        }
        return $moodleattributes;
    }


    /**
     * Perform an action when a user updates their password
     *
     * @param object $user user object
     * @param object $newpassword user's new password
     * @return bool
     */
    function user_update_password($user, $newpassword) {
        return false;
    }


    /**
     * Is this an internal user?
     *
     * @return bool
     */
    function is_internal() {
        //we do not know if it was internal or external originally
        return false;
    }


    /**
     * Don't allow password changes
     *
     * @return bool
     */
    function can_change_password() {
        return false;
    }


    /**
     * Don't allow password reset
     *
     * @return bool
     */
    function can_reset_password() {
        return false;
    }


    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     *
     * @param object $form data from the submitted form
     * @param object $err any error information
     * @return null
     */
    function validate_form(&$form, &$err) {
        if (empty($form->appkey)) {
            $err['appkey'] = get_string('error_appkey', 'auth_ssatsso');
        }
        if (empty($form->wsdlurl)) {
            $err['wsdlurl'] = get_string('error_wsdlurl', 'auth_ssatsso');
        }

        // Attempt test connection to Webservice
        if (empty($err)) {
            try {
                @$ssat = new SSATClient($form->wsdlurl);
            } catch (Exception $e) {
                $err['connectiontest'] = get_string('error_connectiontest', 'auth_ssatsso', $e->getMessage());
            }
        }
    }


    /**
     * Config Form
     *
     * @param object $config config data
     * @param array $err errors keyed on field name
     * @param array $user_fields key value pairs of mapped fields
     * @return bool
     */
    function config_form($config, $err, $user_fields) {
        require('config.html');
        return true;
    }


    /**
     * Process Config
     *
     * @param object $config config form data
     *
     * @return bool
     */
    function process_config($config) {
        if (!isset($config->autologin)) {
            $config->autologin = 0;
        }
        set_config('appkey', $config->appkey, 'auth/ssatsso');
        set_config('wsdlurl', $config->wsdlurl, 'auth/ssatsso');
        set_config('autologin', $config->autologin, 'auth/ssatsso');
       return true;
    }

    /**
     * Authenticate a user via SSO token
     *
     * @param string $token
     * @return bool
     * @uses $CFG
     * @uses $USER
     * @uses $SESSION
     */
    function token_login($token) {
        global $CFG, $USER, $SESSION;

        try {
            $client = new SSATClient($this->wsdlurl, $this->appkey);
            if (!$ssatuser = $client->validate_token($token)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        $key = sesskey();

        if ($user = get_record('user', 'username', addslashes($ssatuser->user_name), 'mnethostid', $CFG->mnet_localhost_id)) {
            add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID,
                       $user->id, 0, $user->id);

            $SESSION->ssat_user_id = $ssatuser->user_id;
            $USER = complete_user_login($user);

            /// Redirection
            if (user_not_fully_set_up($USER)) {
                $urltogo = $CFG->wwwroot.'/user/edit.php';
                // We don't delete $SESSION->wantsurl yet, so we get there later
            } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
                unset($SESSION->wantsurl);
            } else {
                // no wantsurl stored or external - go to homepage
                $urltogo = $CFG->wwwroot.'/';
                unset($SESSION->wantsurl);
            }
            redirect($urltogo);
        }
        // Should never reach here.
        return false;
    }

    /**
     * Redirect to the requested url, ataching SSO token
     *
     * @param string $url URL to redirect to
     * @return null
     * @uses $SESSION
     */
    function dispatch($url) {
        global $SESSION;

        $redirect = $url;

        // Check for a ssat user id then get the current token
        if (isset($SESSION->ssat_user_id)) {
            try {
                $client = new SSATClient($this->wsdlurl, $this->appkey);
                if ($token = $client->get_token($SESSION->ssat_user_id)) {

                    // Rebuild the URL to include the token
                    $url_parts = parse_url($url);

                    if (isset($url_parts['query'])) {
                        $url_parts['query'] .= '&token='.$token;
                    } else {
                        $url_parts['query']  = 'token='.$token;
                    }

                    $redirect  = isset($url_parts['scheme']) ? $url_parts['scheme'].'://' : '';
                    $redirect .= isset($url_parts['user']) ? $url_parts['user'].($url_parts['pass']? ':'.$url_parts['pass']:'').'@':'';
                    $redirect .= isset($url_parts['host']) ? $url_parts['host'] : '';
                    $redirect .= isset($url_parts['port']) ? ':'.$url_parts['port'] : '';
                    $redirect .= isset($url_parts['path']) ? $url_parts['path'] : '';
                    $redirect .= isset($url_parts['query']) ? '?'.$url_parts['query'] : '';
                    $redirect .= isset($url_parts['fragment']) ? '#'.$url_parts['fragment'] : '';
                }
            } catch (Exception $e) {
                // Something went wrong, just redirect the user
            }
        }
        header("Location: $redirect", true, 307);
    }


}

?>
