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
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * provide an admin UI to map urlish things to header images.
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/forms.php');

admin_externalpage_setup('imagemap');
admin_externalpage_print_header();

if ($default = optional_param('default', '', PARAM_FILE)) {
    set_config('defaultcustomheader', $default);
} else if (!isset($CFG->defaultcustomheader)) {
    set_config('defaultcustomheader', 'welcome.jpg');
}

$configure = optional_param('mappings', '', PARAM_FILE);
$basedir = '/theme/' . current_theme() . '/pix/headers';

// english will have all the available ones
$endir = $basedir . '/en_utf8/';
$images = get_directory_list($CFG->dirroot . $endir, '', false, false, true);
$images = array_flip($images);
foreach (array_keys($images) as $i) {
    $images[$i] = $i;
}

$thisurl = $CFG->wwwroot . '/local/admin/imagemap.php';

if (empty($configure)) {
    print_heading(get_string('imagemap', 'local'));
    echo '<p>' . get_string('imagemapdesc', 'local') . '</p>';

    foreach ($images as $image) {
        echo '<div class="imagemap"><a href="'.$thisurl.'?mappings='.$image.'"><b>' . $image
            . '</b><br /><img src="' . tao_header_image_location($image) . '" alt="' . $image . '" border="1" width="400" /></a><div class="imagemapdefault">';
        echo ($CFG->defaultcustomheader == $image ? ' (' . get_string('default') . ')</div>' : '<a href="'.$thisurl . '?default='.$image.'">'.get_string('setdefault','local').'</a></div>');
        echo '<div class="clearer"></div></div>' . "\n";
    }
    admin_externalpage_print_footer();
    exit;
}
echo ' ' . '<a href="' . $thisurl . '">' . get_string('gobacktolist', 'local') . '</a>';

print_heading(get_string('configuringmappingsfor', 'local', $configure));

echo '<div class="imagemap"><img src="' . $CFG->wwwroot . $endir . $configure . '" alt="' . $configure . '" border="1" width="400" /></div>' . "\n";

if ($configure == $CFG->defaultcustomheader) {
    notify(get_string('alreadydefault', 'local'));
}

echo '<p>' . get_string('mappinghelp', 'local') . '</p>';

if (!$mappings = get_records('header_image', 'image', $configure)) {
    notify(get_string('nomappings', 'local'));
    $mappings = array();
}
$mform = new tao_imagemapping_form('', array('mappings' => $mappings, 'image' => $configure));
if ($data = $mform->get_data()) {
    if (!empty($data->addurl)) {
        insert_record('header_image',
            (object)array(
                'url'         => $data->addurl,
                'description' => $data->adddesc,
                'image'       => $configure,
                'sortorder'   => $data->addsortorder,
                'courseid'    => $data->addcourseid,
            )
        );
    }
    foreach ($data as $key => $value) {
        if (!preg_match('/^url(\d+)$/', $key, $matches)) {
            continue;
        }
        $id = $matches[1];
        $key = 'delete' . $id;
        if (!empty($data->{$key})) {
            delete_records('header_image', 'id', $id);
            continue;
        }
        $url = $data->{'url' . $id};
        $courseid = $data->{'courseid' . $id};
        $desc = $data->{'desc' . $id};
        $order = $data->{'sortorder' . $id};
        update_record('header_image',
            (object)array(
                'url'         => $url,
                'description' => $desc,
                'image'       => $configure,
                'sortorder'   => $order,
                'courseid'    => $courseid,
                'id'          => $id,
            )
        );
    }
    redirect($thisurl . '?mappings=' . $configure, get_string('changessaved'), 3);
} else {
    $mform->display();
}

admin_externalpage_print_footer();

?>
