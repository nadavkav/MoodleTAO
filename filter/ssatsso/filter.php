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


/**
 * Replace url's specified in config with SSO dispatcher url
 *
 * @param int $courseid
 * @param string $text text to filter
 * @return text
 * @uses $CFG
 */
function ssatsso_filter($courseid, $text) {
    global $CFG;

    static $externalurls;

    // Load list of urls to filter and cache it for the next call to this filter
    if (!isset($externalurls)) {
        $externalurls = explode("\n", get_config('filter_ssatsso', 'externalurls'));
        foreach ($externalurls as $key => $externalurl) {
            $externalurls[$key] = '/<a([^>]+)href=[\'"]{1,1}('.strtr(preg_quote($externalurl, '/'), array('\*' => '.*', "\r" => '')).')[\'"]{1,1}/iUse';
        }
    }

    // Replace with link to SSO dispatcher
    $dispatcher = $CFG->wwwroot.'/auth/ssatsso/dispatcher.php';
    $text = preg_replace($externalurls, "'<a'.'\\1'.'href=\'".$dispatcher."?url='.urlencode('\\2').'\''", $text);

    return $text;
}

?>
