<?php
//
// Edit menu form
// $Revision: 1.1 $
// $Author: julmis $
// $Date: 2006/06/02 06:31:49 $
//

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

?>
<form method="post" action="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>">
<input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
<input type="hidden" name="course" value="<?php p($course->id) ?>" />
<?php
if (!empty($form->id)) {
    echo "<input type=\"hidden\" name=\"id\" value=\"$form->id\" />\n";
}

// Needed when deleting page
if (!empty($form->nid)) {
    echo "<input type=\"hidden\" name=\"nid\" value=\"$form->nid\" />\n";
}

// Needed when deleting page
if (!empty($form->naviid)) {
    echo "<input type=\"hidden\" name=\"naviid\" value=\"$form->naviid\" />\n";
}

?>
<div align="center">
<p><?php echo $deletemessage ?></p>
<p><input type="submit" value="<?php print_string("yes"); ?>" />&nbsp;
<input type="submit" name="cancel" value="<?php print_string("no"); ?>" /></p>
</div>
</form>