<?php
/**
 * Page management
 * 
 * @author Jeff Graham, Mark Nielsen
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

$moving = optional_param('moving', 0, PARAM_INT);

require_capability('format/page:managepages', $context);

$PAGE->print_tabs('manage');

if ($pages = page_get_all_pages($course->id, 'flat')) {
    $table->head = array(get_string('pagename','format_page'),
                         get_string('pageoptions','format_page'),
                         get_string('displaytheme', 'format_page'),
                         get_string('displaymenu', 'format_page'),
                         get_string('publish', 'format_page'));
    $table->align       = array('left', 'center', 'center', 'center', 'center');
    $table->width       = '70%';
    $table->cellspacing = '0';
    $table->id          = 'editing-table';
    $table->class       = 'generaltable pageeditingtable';
    $table->data        = array();

    foreach ($pages as $page) {
        // Page link/name
        $name = page_pad_string('<a href="'.$PAGE->url_build('page', $page->id).'">'.format_string($page->nameone).'</a>', $page->depth);

        // Edit, move and delete widgets
        $widgets  = '<a href="'.$PAGE->url_build('page', $page->id, 'action', 'editpage', 'returnaction', 'manage').'" class="icon edit"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('editpage', 'format_page').'" /></a>&nbsp;';
        $widgets .= '<a href="'.$PAGE->url_build('action', 'moving', 'moving', $page->id, 'sesskey', sesskey()).'" class="icon move"><img src="'.$CFG->pixpath.'/t/move.gif" /></a>&nbsp;';
        $widgets .= '<a href="'.$PAGE->url_build('action', 'confirmdelete', 'page', $page->id, 'sesskey', sesskey()).'" class="icon delete"><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.get_string('deletepage', 'format_page').'" /></a>';

        // Theme, menu and publish widgets
        if ($page->parent == 0) {
            // Only master pages get this one
            $theme = page_manage_showhide($page, DISP_THEME);
        } else {
            $theme = '';
        }
        $menu    = page_manage_showhide($page, DISP_MENU);
        $publish = page_manage_showhide($page, DISP_PUBLISH);

        $table->data[] = array($name, $widgets, $theme, $menu, $publish);
    }

    print_table($table);
} else {
    error(get_string('nopages', 'format_page'), $PAGE->url_build('action', 'editpage'));
}

/**
 * Local methods to assist with generating output
 * that is specific to this page
 *
 */

/**
 * This function displays the hide/show icon & link page display settings
 *
 * @param object $page Page to show the widget for
 * @param int $type a display type to show
 * @uses $CFG
 */
function page_manage_showhide($page, $type) {
    global $CFG;

    if ($page->display & $type) {
        $showhide = 'showhide=0';
        $str = 'hide';
    } else {
        $showhide = 'showhide=1';
        $str = 'show';
    }

    $return = "<a href=\"$CFG->wwwroot/course/format/page/format.php?id=$page->courseid&amp;page=$page->id".
               "&amp;action=showhide&amp;display=$type&amp;$showhide&amp;sesskey=".sesskey().'">'.
               "<img src=\"$CFG->pixpath/i/$str.gif\" alt=\"".get_string($str).'" /></a>';

    return $return;
}

?>