<?php
/**
 * @author Matt Clarkson <mattc@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * SSO dispatcher: attach users sso token to requrested url
 *
 * 2009-05-12  File created.
 */

require_once("../../config.php");
require_once('auth.php');

$url = required_param('url', PARAM_URL);

require_login(0, false);

$auth_ssatsso = new auth_plugin_ssatsso;
$auth_ssatsso->dispatch($url);

?>
