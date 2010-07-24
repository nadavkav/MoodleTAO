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
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once($CFG->dirroot . '/mnet/xmlrpc/client.php'); //mnet client library
require_once($CFG->dirroot . '/local/tao.php');
require_once($CFG->dirroot . '/local/mahara/taoviewlib.php');

require_login();
$tagfilter = optional_param('tag', '', PARAM_ALPHANUM);
$userfilter = optional_param('filteruser', '', PARAM_ALPHANUM);
$sort = optional_param('sort', '', PARAM_ALPHANUM);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 12, PARAM_INT);        // how many per page

///// Add ajax-related libs
    require_js(array('yui_yahoo', 'yui_event', 'yui_dom', 'yui_connection', 'yui_json'));
    require_js($CFG->wwwroot . '/local/mahara/rate_ajax.js');


    $headerstr = get_string('taoview', 'local');
    print_header($headerstr, $headerstr, build_navigation($headerstr));
    
    print_heading(get_string('taoresources', 'local'));
    $viewtype = 'taoresources';
    print_string('taoresourcesdesc', 'local');

    taoview_print_view($viewtype, $tagfilter, $userfilter, $sort, $page, $perpage);

    print_footer();

?>