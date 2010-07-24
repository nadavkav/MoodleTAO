<?php

/**
 * @author Matt Clarkson <mattc@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle filter
 *
 * Text Filter: Specialist Schools and Academies Trust SSO link filter
 *
 * Re-write URL's to designated SSO sites to go via a dispatcher
 *
 * 2009-05-08  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$settings->add(new admin_setting_configtextarea('filter_ssatsso/externalurls', get_string('externalurls','filter_ssatsso'),
               get_string('instructions', 'filter_ssatsso'), ''));


?>
