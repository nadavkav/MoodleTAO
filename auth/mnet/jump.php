<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Moodle Network Authentication
 *
 * Multiple host authentication support for Moodle Network.
 *
 * 2006-11-01  File created.
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
$setwantsurltome = optional_param('setwu', 1, PARAM_BOOL); // request cookie test

require_login(SITEID,false, null, $setwantsurltome);

if (!is_enabled_auth('mnet')) {
    error('mnet is disabled');
}

// grab the GET params - wantsurl could be anything - take it
// with PARAM_RAW
$hostid = optional_param('hostid', '0', PARAM_INT);
$hostwwwroot = optional_param('hostwwwroot', '', PARAM_URL);
$wantsurl = optional_param('wantsurl', '', PARAM_RAW);
$fromurl = optional_param('fromurl', '', PARAM_RAW);

// If hostid hasn't been specified, try getting it using wwwroot
if (!$hostid) {
    $hostid = get_field('mnet_host', 'id', 'wwwroot', $hostwwwroot);
}

//if fromurl hasn't been specified, try getting it from the refferrer
if (empty($fromurl) &&
    !empty($_SERVER['HTTP_REFERER']) &&
    strpos($_SERVER['HTTP_REFERER'], $CFG->wwwroot.'/') === 0) {

    $fromurl = str_replace($CFG->wwwroot.'/', '', $_SERVER['HTTP_REFERER']);
}

// start the mnet session and redirect browser to remote URL
$mnetauth = get_auth_plugin('mnet');
$url      = $mnetauth->start_jump_session($hostid, $wantsurl, $fromurl);

if (empty($url)) {
    error('DEBUG: Jump session was not started correctly or blank URL returned.'); // TODO: errors
}
redirect($url);

?>
