<?php
$string['auth_teossotitle']         = 'TEO SSO Authentication';
$string['auth_teossodescription']   = 'Authentication with SSO using CA Federation Manager data';

$string['auth_teosso_signin_url'] = 'TEO Signin URL';
$string['auth_teosso_signin_url_description'] = 'full URL (including query string) for site to redirect to when a User does not have a valid session eg. https://federation.intel.com/federation?target=<moodle root>
.';

$string['auth_teosso_signin_error_url'] = 'TEO Signin Error URL';
$string['auth_teosso_signin_error_url_description'] = 'full URL (including query string) for site to redirect to when there is an error in the login process eg. bad XML, incorrect values.';

$string['auth_teosso_field_map_username'] = 'Account User Name Field Mapping';
$string['auth_teosso_field_map_username_description'] = 'Map the Account User Name from the XML Header Insert (SM_USER Header) to Moodle Account Name';

$string['auth_teosso_dologout'] = 'Log out from CA Federation Manager';
$string['auth_teosso_dologout_description'] = 'Check to have the module log out from CA Federation Manager when user log out from Moodle';

$string['auth_teosso_signout_url'] = 'TEO Sign Out URL';
$string['auth_teosso_signout_url_description'] = 'full URL (including query string) for site to redirect to when a User is to be completely logged out eg. https://federation.intel.com/federation?target=<moodle root>
.';

$string['auth_teosso_cpm_edit_url'] = 'CPM Edit user URL';
$string['auth_teosso_cpm_edit_url_description'] = 'full URL for jump to eidt CPM user profile';

$string['auth_teosso_http_header'] = 'HTTP Header name passed from CA FM';
$string['auth_teosso_http_header_description'] = 'Name of the HTTP Header that CA federation Manager uses to pass the User attribute declartions of a logged in user.';

$string['auth_teosso_notshowusername'] = 'Do not show username';
$string['auth_teosso_notshowusername_description'] = 'Check to have Moodle not show the username for users logging in by TEO SSO';

$string['missingidnumber'] = 'The configuration for the teosso idnumber field mapping is missing';

$string['missingxmllib'] = 'missing PHP module SimpleXML - cant call simplexml_load_string()';

$string['idnumber_error'] = 'idnumber attribute not found in Federation Manager HTTP Header $a';

$string['username_error'] = 'username attribute not found in Federation Manager HTTP Header $a';

$string['user_auth_type_error'] = 'User does not use teosso auth type $a';

$string['profile'] = 'Jump to CPM';
$string['profileedit'] = 'SSO User Profile';
?>