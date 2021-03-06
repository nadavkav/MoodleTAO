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
 * this file should be used for all the custom event definitions and handers.
 * event names should all start with tao_.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$handlers = array (
/*
 * send a message to every user with a particular role.
*/
    'tao_message_role' => array (
        'handlerfile'      => '/local/lib.php',
        'handlerfunction'  => 'tao_handle_message_role_event',    // argument to call_user_func(), could be an array
        'schedule'         => 'cron'
    ),
    'certification_updated' => array(
        'handlerfile'      => '/local/lib.php',
        'handlerfunction'  => 'tao_handle_certification_event',
        'schedule'         => 'cron',
    ),
    'learning_path_submitted' => array(
        'handlerfile'      => '/local/lib.php',
        'handlerfunction'  => 'tao_handle_learning_path_submission_event',
        'schedule'         => 'cron',
    ),
/* more go here */
);

?>
