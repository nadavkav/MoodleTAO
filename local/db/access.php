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
 * Capability definitions for the lplan module.
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
*/

$local_capabilities = array(

    // send messages to targetted groups (by role)
    'moodle/local:messagebyrole' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),

    'moodle/local:classifylearningpath' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:viewunpublishedlearningpath' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:canselfassignheadeditor' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:canselfassigntemplateeditor' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:cancreatelearningpaths' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:cancreatetemplates' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:managepageactivities' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:savelearningpathtemplate' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:viewcoursestatus' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:viewcertificationblock' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),

    'moodle/local:updatecoursestatus' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),


    // assigning roles on users
    'moodle/local:canassignmt' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:isassignablemt' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:canassignpt' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:isassignablept' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:viewresponsibleusers' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:viewresponsibleusersbehalfof' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_USER,
    ),

    // identifying capabilities for roles
    'moodle/local:isheadeditor' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:islpauthor' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:islpeditor' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:islpcreator' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:ispt' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:ismt' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:isst' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:hasdirectlprelationship' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    // giant list of message target capabilities
    'moodle/local:canmessageownpts'       => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagefellowmts'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessageownmts'       => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagemtspts'       => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagefellowsts'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagehts'          => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessageownalumni'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ),
    'moodle/local:canmessageallalumni'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ),
    'moodle/local:canmessagemtsalumni'       => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessageallpts'       => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessageanyuser'      => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagefellowadmins' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canmessagefellowpts'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),
    'moodle/local:canmessagealumnibylp'    => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
    ),

    // extra caps related to messaging
    'moodle/local:cansearchforlptomessage' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:invitenewuser' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),
    'moodle/local:bulkinvitenewuser' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),

    /*****/

    'moodle/local:viewlpcontributors' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),
    'moodle/local:managelpcontributors' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),
    'moodle/local:createstandardlp' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'riskbitmask'  => RISK_SPAM,
    ),
    'moodle/local:canchangelpsettings' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:managemytasks' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canimportlegacytao' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canviewraflmod' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
    'moodle/local:canassignselftorafl' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
);

?>
