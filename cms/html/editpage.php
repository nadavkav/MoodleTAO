<?php
//
// Edit menu form
// $Revision: 1.6.10.1 $
// $Author: julmis $
// $Date: 2008/03/23 09:36:08 $
//

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

    if (empty($form->title)) {
        $form->title = '';
    }

    if (empty($form->body)) {
        $form->body = '';
    }

    if ( empty($form->pageid) ) {
        $form->pageid = '';
    }

    if ( empty($form->url) ) {
        $form->url = '';
    }

    if ( empty($form->target) ) {
        $form->target = '_top';
    }

    if ( empty($form->parentname) ) {
        $form->parentname = '';
    }

    if ( empty($form->publish) ) {
        $form->publish = 0;
    }

?>
<form name="cmsEditPage" action="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>" method="post">
<input type="hidden" name="sesskey" value="<?php p($USER->sesskey) ?>" />
<input type="hidden" name="course" value="<?php p($course->id) ?>" />
<?php

if (!empty($form->id)) {
    echo "<input type=\"hidden\" name=\"id\" value=\"$form->id\" />\n";
}

if (!empty($form->nid)) {
    echo "<input type=\"hidden\" name=\"nid\" value=\"$form->nid\" />\n";
}

if (!empty($form->pageid)) {
    echo "<input type=\"hidden\" name=\"pageid\" value=\"$form->pageid\" />\n";
}

if (! empty($form->parentid) ) {
    echo '<input type="hidden" name="parentid" value="'. $form->parentid .'" />' . "\n";
}

//if (empty($form->pagename) or is_numeric($form->pagename) or !empty($pagenameerror)) {
//    $readonly = '';
//} else { // once the pagename has been set to a non-integer it should not be changed again
//    $readonly = ' readonly="readonly"';
//}
$readonly = '';

?>
  <table border="0" cellpadding="4" align="center">
    <caption><strong><?php echo $strformtitle ?></strong></caption>
    <tbody>
      <tr>
        <td align="right"><strong><?php print_string("choosemenu","cms");?></strong>:</td>
        <td>

            <?php
            /*foreach ($form->menus as $menu) {
                echo "<option value=\"$menu->id\"";
                print (!empty($form->id) && $form->id == $menu->id) ? " selected=\"selected\"" : "";
                echo ">$menu->name</option>\n";
            }*/
            $options = array();
            foreach ( $form->menus as $menu ) {
                $options[$menu->id] = strip_tags($menu->name);
            }
            $active = (!empty($form->id) && $form->id == $menu->id) ? $menu->id : $form->id;
            choose_from_menu($options, "naviid", $active, "");
            ?>

        </td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("showinmenu","cms");?></strong>:</td>
        <td>
          <input type="hidden" name="showinmenu" value="0">
          <input type="checkbox" name="showinmenu"<?php print(!empty($form->showinmenu)) ? " checked=\"true\"" : "";?>>
        </td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("linkname","cms");?></strong>:</td>
        <td><input type="text" name="title" size="40" value="<?php p($form->title) ?>" /></td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("pagename","cms");?></strong>:</td>
        <td><input type="text" name="pagename" size="20" value="<?php p($form->pagename) ?>" <?php echo $readonly ?>/></td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("parentname","cms");?></strong>:</td>
        <td><input type="text" name="parentname" size="20" value="<?php p($form->parentname) ?>" /></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong><?php print_string("pagecontent","cms");?></strong>:</td>
        <td nowrap="nowrap">
        <script type="text/javascript">
        //<![CDATA[
            tp1 = new WebFXTabPane( document.getElementById( "tabPane1" ) );
        //]]>
        </script>
        <!-- build tabs -->
        <div class="tab-pane" id="tabPane1">
        <div class="tab-page" id="tabPage1">
        <p class="tab"><?php print_string('page','cms') ?></p>
        <script type="text/javascript">
        //<![CDATA[
        tp1.addTabPage( document.getElementById( "tabPage1" ) );
        //]]>
        </script>
        <?php print_textarea($usehtmleditor, "25", "60", "", "", "body", stripslashes($form->body), $course->id); ?>
        </div>
        <div class="tab-page" id="tabPage2">
        <p class="tab"><?php print_string('linkto','cms') ?></p>
        <script type="text/javascript">
        //<![CDATA[
        tp1.addTabPage( document.getElementById( "tabPage2" ) );
        //]]></script>
        <input type="text" name="url" size="50" value="<?php p($form->url); ?>" /> <?php
        $url = '/cms/activities.php?course='. $course->id .
               '&amp;sesskey='. $USER->sesskey;
        $stractivities = get_string('activities');

        button_to_popup_window($url, $stractivities, $stractivities, 400, 500, $stractivities, "none");
        ?>
        <br />
        <?php print_string('pagewindow', 'resource'); ?>
        <input type="radio" name="target" value="_top" <?php print($form->target == '_top' ? 'checked="checked" ':''); ?>/>
        <?php print_string('newwindow','resource'); ?>
        <input type="radio" name="target" value="_blank" <?php print($form->target == '_blank' ? 'checked="checked" ':''); ?>/>
        </div>
        </div>
        </td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("showblocks","cms");?></strong>:</td>
        <td>
          <input type="hidden" name="showblocks" value="0">
          <input type="checkbox" name="showblocks"<?php print(!empty($form->showblocks)) ? " checked=\"true\"" : "";?> />
        </td>
      </tr>
      <tr>
        <td align="right"><strong><?php print_string("publish","cms");?></strong>:</td>
        <td>
          <?php
          if ( has_capability('format/cms:publishpage', $context, $USER->id) ) {
              ?>
          <input type="checkbox" name="publish"<?php print(!empty($form->publish)) ? " checked=\"true\"" : "";?> />
          <?php
          } else {
              echo '<input type="hidden" name="publish" value="'. $form->publish .'" />'."\n";
          }
          ?>
        </td>
      </tr>
    </tbody>
  </table>
  <p align="center">
    <input type="submit" name="save" value="<?php print_string("savechanges"); ?>" />
    <input type="submit" name="preview" value="<?php print_string('preview', 'cms'); ?>" />
    <input type="submit" name="cancel" value="<?php print_string('cancel'); ?>" />
  </p>
</form>
