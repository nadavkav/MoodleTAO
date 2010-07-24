<?php
//
// Edit menu form
// $Revision: 1.1 $
// $Author: julmis $
// $Date: 2006/06/02 06:31:49 $
//

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

    // Options for yes and no menus.
    $options[0] = get_string('no');
    $options[1] = get_string('yes');

?>
<form method="post" action="<?php print(basename($_SERVER['SCRIPT_NAME'])); ?>">
<input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
<input type="hidden" name="course" value="<?php p($course->id) ?>" />
<?php
    if (!empty($form->id)) {
        echo "<input type=\"hidden\" name=\"id\" value=\"$form->id\" />\n";
    }

?>
<table border="0" cellpadding="5" align="center">
<tr>
    <td align="right"><strong><?php print_string("title","cms"); ?>:</strong></td>
    <td><input type="text" name="name" size="45" value="<?php p($form->name); ?>" /></td>
</tr>
<tr>
    <td align="right" valign="top"><strong><?php print_string("intro","cms"); ?>:</strong></td>
    <td><textarea cols="50" rows="5" name="intro"><?php p($form->intro); ?></textarea></td>
</tr>
<tr>
    <td align="right"><strong><?php print_string('printdateonpage','cms'); ?>:</strong></td>
    <td><?php
    choose_from_menu($options, 'printdate', $form->printdate, '');
    ?></td>
</tr>
<tr>
    <td align="right"><strong><?php print_string('requirelogin','cms'); ?>:</strong></td>
    <td><?php
    choose_from_menu($options, 'requirelogin', $form->requirelogin, '', 'change_state();');
    ?></td>
</tr>
<tr>
    <td align="right"><strong><?php print_string('allowguest','cms'); ?>:</strong></td>
    <td><?php
    choose_from_menu($options, 'allowguest', $form->allowguest, '');
    ?></td>
</tr>
</table>
<p align="center"><input type="submit" value="<?php print_string("savechanges") ?>" /></p>
</form>

<script language="javascript" type="text/javascript">
<!--

function change_state () {

    var choice = document.forms[0].requirelogin;
    var rlogin = choice.options[choice.selectedIndex].value;

    if (rlogin != 1) {
        document.forms[0].allowguest.disabled = true;
    } else {
        document.forms[0].allowguest.disabled = false;
    }
}

document.onload = change_state();
-->
</script>