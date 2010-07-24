<?php
/**
 * Admin setting class definition for displaying
 * the page format on the front page.
 *
 * @author Mark Nielsen
 * @version $Id$
 * @package format_page
 **/

/**
 * Parent class definition
 */
require_once($CFG->dirroot.'/lib/adminlib.php');

/**
 * New admin settings class definition
 *
 * This is a check box setting and it bases
 * it setting on whether the site course has
 * its format set to page or not.
 *
 * If format == page then it is checked
 * If format != page then it is not checked
 *
 * @package format_page
 **/
class admin_setting_special_pageformatonfrontpage extends admin_setting_configcheckbox {

    /**
     * Constructor - same as admin_setting_configcheckbox
     *
     * @return void
     **/
    function admin_setting_special_pageformatonfrontpage($name, $visiblename, $description, $defaultsetting) {
        parent::admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Check the site format setting
     *
     * @return mixed
     **/
    function get_setting() {
        $site = get_site();

        return ($site->format == 'page' ? 1 : NULL);
    }

    /**
     * Save the options to the site course's format field.
     *
     * Possible values: site and page
     *
     * @return string
     **/
    function write_setting($data) {
        if ($data == '1') {
            $format = 'page';
        } else {
            $format = 'site';
        }
        return (set_field('course', 'format', $format, 'id', SITEID) ? '' : get_string('errorsetting', 'admin') . $this->visiblename . '<br />');
    }
} // End admin_setting_special_pageformatonfrontpage

?>