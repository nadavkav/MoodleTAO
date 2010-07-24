<?php 

/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage local
 * @author     David Drummond <david@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * Displays a participants learning information  
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot.'/local/lib/learning.php');
require_login();

$strheading = get_string('mylearning', 'local');

print_header($strheading, $strheading, build_navigation($strheading));

echo '<div id="browse_learning_paths">';
echo '<table id="browse_learning_paths_table"><tr>';
echo '  <td class="left">';
    tao_print_browse_learning_paths();
echo '  </td>';
echo '  <td class="right">';
tao_print_related_learning_paths();
echo '  </td>';
echo '</tr></table>';
echo '</div>';
echo '<div id="my_learning_paths">';
if (tao_rafl_mode_enabled()) {
    tao_print_my_learning_paths_raflmode();
} else {
    tao_print_my_learning_paths();
}
echo '</div>';
if (empty($CFG->taomylearningshowcert)) {
    echo '<div id="learning_path_certification">';
    tao_print_certification();
    echo '</div>';
}

print_footer();

?>
