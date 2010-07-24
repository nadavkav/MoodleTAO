<?php
//
// Edit menu form
// $Revision: 1.1 $
// $Author: julmis $
// $Date: 2006/06/02 06:31:49 $
//

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

?>
<div align="center">
<form method="get" action="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>">
<input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
<input type="hidden" name="course" value="<?php p($courseid) ?>" />
<?php print_string('choose') ?>: <select name="menuid" onchange="this.form.submit();">
<?php
if (is_array($menus)) {
    foreach ($menus as $menu) {
        echo "<option value=\"$menu->id\"";
        print (!empty($menuid) && $menuid == $menu->id) ? " selected=\"true\"" : "";
        echo ">$menu->name</option>\n";
    }
}
?>
</select>
</form>
</div>
<br />