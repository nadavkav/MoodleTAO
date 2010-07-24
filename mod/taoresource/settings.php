<?php 
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require_once($CFG->dirroot.'/mod/taoresource/lib.php');

global $TAORESOURCE_WINDOW_OPTIONS; // make sure we have the pesky global

$checkedyesno = array(''=>get_string('no'), 'checked'=>get_string('yes')); // not nice at all

$settings->add(new admin_setting_configcheckbox('taoresource_backup_index', get_string('backup_index', 'taoresource'),
                   get_string('config_backup_index', 'taoresource'), '0'));

$settings->add(new admin_setting_configcheckbox('taoresource_restore_index', get_string('restore_index', 'taoresource'),
                   get_string('config_restore_index', 'taoresource'), '1'));
                   
$settings->add(new admin_setting_configtext('taoresource_framesize', get_string('framesize', 'taoresource'),
                   get_string('configframesize', 'taoresource'), 130, PARAM_INT));

$settings->add(new admin_setting_configtext('taoresource_defaulturl', get_string('resourcedefaulturl', 'taoresource'),
                   get_string('configdefaulturl', 'taoresource'), 'http://'));

$woptions = array('' => get_string('newwindow', 'taoresource'), 'checked' => get_string('pagewindow', 'taoresource'));
$settings->add(new admin_setting_configselect('taoresource_popup', get_string('display', 'taoresource'),
                   get_string('configpopup', 'taoresource'), '', $woptions));

foreach ($TAORESOURCE_WINDOW_OPTIONS as $optionname) {
    $popupoption = "taoresource_popup$optionname";
    if ($popupoption == 'taoresource_popupheight') {
        $settings->add(new admin_setting_configtext('taoresource_popupheight', get_string('newheight', 'taoresource'),
                           get_string('configpopupheight', 'taoresource'), 600, PARAM_INT));
    } else if ($popupoption == 'taoresource_popupwidth') {
        $settings->add(new admin_setting_configtext('taoresource_popupwidth', get_string('newwidth', 'taoresource'),
                           get_string('configpopupwidth', 'taoresource'), 800, PARAM_INT));
    } else {
        $settings->add(new admin_setting_configselect($popupoption, get_string('new'.$optionname, 'taoresource'),
                           get_string('configpopup'.$optionname, 'taoresource'), 'checked', $checkedyesno));
    }
}

?>