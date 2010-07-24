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
 * 2009-04-13  File created.
 *
 * NOTE: This snippet of code is intended to be included in setup.php to intercept
 *       any requests containing a token.
 *
 */

if ($_SERVER['SCRIPT_FILENAME'] != $CFG->dirroot.'/login/index.php' && isset($_REQUEST['token']) && !isset($SESSION->ssat_user_id)) {
    $SESSION->token = $_REQUEST['token'];
    $SESSION->wantsurl = $FULLME;
    header('Location: '.$CFG->wwwroot.'/login/index.php');
    exit;
}

?>
