<?php
/**
 * Page Item Definition
 *
 * @author Mark Nielsen
 * @version $Id$
 * @package format_page
 **/

/**
 * Add content to a block instance. This
 * method should fail gracefully.  Do not
 * call something like error()
 *
 * @param object $block Passed by refernce: this is the block instance object
 *                      Course Module Record is $block->cm
 *                      Module Record is $block->module
 *                      Module Instance Record is $block->moduleinstance
 *                      Course Record is $block->course
 *
 * @return boolean If an error occures, just return false and 
 *                 optionally set error message to $block->content->text
 *                 Otherwise keep $block->content->text empty on errors
 **/
function pairandshare_set_instance(&$block) {
    global $CFG;

    require($CFG->dirroot.'/mod/pairandshare/lib.php');

    ob_start();
    pairandshare_print($block->moduleinstance, $block->course, false, $block->baseurl);
    $block->content->text = ob_get_contents();
    ob_end_clean();

    return true;
}

?>