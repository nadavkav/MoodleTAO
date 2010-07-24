TEOSSO Authentication for Moodle
-------------------------------------------------------------------------------
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

Changes:
- 2008-12    : Created by Piers Harding, Catalyst IT

Requirements:
- SimpleXML


Install instructions:
- 1 If you only want TEOSSO as login option, change login page to point to auth/teosso/index.php
- 2 Configure and enable TEOSSO plugin


This corresponds to auth module config in Moodle for 
TEOSSO:

TEO Signin URL: https://seagull.local.net/login
        URL for site to redirect to when a User does not have a valid session
        eg. https://federation.intel.com/federation . This will have the
        target querystring parameter appended to it eg: https://federation.intel.com?target=http://teachonline.intel.com/auth/teosso/.
Log out from CA Federation Manager: X
        Check to have the module log out from CA Federation Manager when user
        log out from Moodle
TEO Sign Out URL: https://seagull.local.net/logout
       URL for site to redirect to when a User is to be completely logged out 
       eg. https://federation.intel.com/federation . This will have the RESUMEPATH
       querystring parameter appended to it which is always wwwroot of the 
       current Moodle.
Do not show username: X
       Check to have Moodle not show the username for users logging in by TEO SSO
HTTP Header name passed from CA FM: SM_USER
       Name of the HTTP Header that CA federation Manager uses to pass the User
       attribute declartions of a logged in user.

