<?php
/*
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
 * local db upgrades for TAO
 */
function xmldb_local_upgrade($oldversion) {
    global $CFG, $db;

    $result = true;
    $reassigncaps = false;
    $resetcustomroles = false;
    $resetstickyblocks = false;

// learning path approval status changes
    if ($result && $oldversion < 2008091803) {

        // fields added to the course table
        $table  = new XMLDBTable('course');
        $field  = new XMLDBField('approval_status_id');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, null, null);
    //function setAttributes($type, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $enum=null, $enumvalues=null, $default=null, $previous=null) {
        if (!field_exists($table, $field)) {
            /// Launch add field shortname
            $result = $result && add_field($table, $field);
        }

    }

    if ($oldversion < 2008092100) {
        // create the roles we need
        $roles = array(
            'superadmin' => array(
                'name'        => 'Super Admin',
                'description' => 'The highest level site administrator',
                'legacy'      => 'admin',
            ),
            'headteacher' => array(
                'name'        => 'Head Teacher',
                'description' => 'Oversees the professional development of the teachers at his/her school and other schools that s/he has been assigned to cover.'
            ),
            'headeditor' => array(
                'name'        => 'Head Editor',
                'description' => 'Approves Learning Plans',
            ),
            'seniorteacher' => array(
                'name'        => 'Senior Teacher',
                'description' => 'Has already achieved Participating Teacher and Master Teacher certification and is now pursuing Senior Teacher certification.',
            ),
            'masterteacher' => array(
                'name'        => 'Master Teacher',
                'description' => 'Has already achieved Participating Teacher certification and is now pursuing Master Teacher certification. Is assigned to an Senior Teacher.',
            ),
            'participatingteacher' => array(
                'name'        => 'Participating Teacher',
                'description' => 'Pursuing Participating Teacher certification. Is assigned to an Master Teacher.',
            ),
            'translator' => array(
                'name'        => 'Translator',
                'description' => 'Localizes User Interface',
            ),
        );
        foreach ($roles as $shortname => $roledata) {
            if (!array_key_exists('legacy', $roledata)) {
                $roledata['legacy']= '';
            }
            $roles[$shortname]['id'] = create_role($roledata['name'], $shortname, $roledata['description'], $roledata['legacy']);
        }

        // boostrap superadmin to the same as admin
        $admin   = get_record('role', 'shortname', 'admin');
        role_cap_duplicate($admin, $roles['superadmin']['id']);
    }

    // try to start over!!!
    if ($result && $oldversion < 2008092400) {

        $table = new XMLDBTable('course_status_history');
        if (table_exists($table)) {
            drop_table($table);
        }

        // fields added to the course table
        $table  = new XMLDBTable('course');
        $field  = new XMLDBField('approval_status_id');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, null, null);

        /// Launch add field approval_status_id
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }

        /// Define table mdl_course_approval_status_history to be created
        $table = new XMLDBTable('course_status_history');

        /// Adding fields to table mdl_course_approval_status
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('timestamp', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null );
        $table->addFieldInfo('approval_status_id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('reason', XMLDB_TYPE_TEXT, '1000', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null );

        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($oldversion < 2008092401) {
        $roles = get_records_select('role', "shortname IN ('headteacher', 'headeditor', 'seniorteacher', 'masterteacher', 'participatingteacher', 'translator')");
        set_config('messageenabledroles', implode(',', array_keys($roles)));
    }

    if ($oldversion < 2008092500) {
        // add course classification stuff
        $table = new XMLDBTable('classification_type');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, 100, null, XMLDB_NOTNULL);
        $table->addFieldInfo('type', XMLDB_TYPE_CHAR, 100, null, XMLDB_NOTNULL, false, true, array('filter', 'topcategory', 'secondcategory'), 'filter');
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

        $table = new XMLDBTable('classification_value');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('type', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_CHAR, 100, null, XMLDB_NOTNULL);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

        $table = new XMLDBTable('course_classification');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $result = $result && create_table($table);

    }

    if ($oldversion < 2008092501) {
        // bootstrap out classification system based on the TAO website.
        $types = array(
            array(
                'name' => 'Teaching strategies',
                'type' => 'filter',
                'values' => array(
                    'Connected Model',
                    'Constructivist Model',
                    'Integrated Model',
                    'Nested Model',
                    'Networked/Extended Model',
                    'Sequenced Model',
                    'Shared Model',
                ),
            ),
            array(
                'name' => 'Teaching methods',
                'type' => 'filter',
                'values' => array(
                    'Action-Oriented Learning',
                    'Active video work',
                    'Ball Bearings',
                    'Case Study',
                    'Creative writing',
                    'Discovery of learning',
                    'Excursion',
                    'Experiment',
                    'Free work',
                    'Group Puzzle',
                    'Learning Circle',
                    'Learning through teaching',
                    'Letter method',
                    'Mind mapping',
                    'Portfolio',
                    'Project Work',
                    'SOL method',
                    'Station Work',
                    'Traffic Lights',
                    'Using Tools and Resources',
                    'Web quest',
                    'Weekly Plan',
                    'Workshop',
                    'Other',
                ),
            ),
            array(
                'name' => 'Learning styles',
                'type' => 'filter',
                'values' => array(
                    'Visual/spatial',
                    'Verbal/linguistic',
                    'Logical/mathematical',
                    'Musical/rhythmic',
                    'Bodily/kinaesthetic',
                    'Interpersonal/social',
                    'Intrapersonal/introspective',
                    'Communication',
                    'Information',
                    'Simulation',
                    'Presentation',
                    'Production',
                    'Visualisation',
                ),
            ),
            array(
                'name' => 'Key Stages',
                'type' => 'topcategory',
                'values' => array(
                    '1 and 2',
                    '3 and 4',
                ),
            ),
            array(
                'name' => 'Subject',
                'type' => 'secondcategory',
                'values' => array(
                    'English',
                    'Mathematics',
                    'Science',
                    'Design and Technology',
                    'ICT',
                    'History',
                    'Geography',
                    'Art and Design',
                    'Music',
                    'Physical Education',
                ),
            ),
        );
        foreach ($types as $t) {
            $values = $t['values'];
            $newid = insert_record('classification_type', (object)$t);
            foreach  ($values as $v) {
                insert_record('classification_value', (object)array('type' => $newid, 'value' => $v));
            }
        }
    }


    if ($result && $oldversion <  2008100703) {
        // change course status values - seemingly have to drop and recreate the table to reset the serial with xmldb

        $table = new XMLDBTable('course_approval_status');
        if (table_exists($table)) {
            drop_table($table);
        }

        /// Define table mdl_course_approval_status to be created
        $table = new XMLDBTable('course_approval_status');

        /// Adding fields to table mdl_course_approval_status
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_TEXT, 15, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('displayname', XMLDB_TYPE_TEXT, '50', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, '100', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table mdl_course_approval_status
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Launch create table for mdl_course_approval_status
        $result = $result && create_table($table);

        // insert status records
        $record = new object();

        $record->id = 1;
        $record->shortname = 'notsubmitted';
        $record->displayname = 'Not Submitted';
        $record->description = 'Not submitted';
        insert_record('course_approval_status', $record);
        $record->id = 2;
        $record->shortname = 'submitted';
        $record->displayname = 'Submitted';
        $record->description = 'Submitted for approval';
        insert_record('course_approval_status', $record);
        $record->id = 3;
        $record->shortname = 'needschange';
        $record->displayname = 'Needs Changes';
        $record->description = 'Reviewed and needs changes';
        insert_record('course_approval_status', $record);
        $record->id = 4;
        $record->shortname = 'resubmitted';
        $record->displayname = 'Resubmitted';
        $record->description = 'Resubmitted for approval';
        insert_record('course_approval_status', $record);
        $record->id = 5;
        $record->shortname = 'approved';
        $record->displayname = 'Approved';
        $record->description = 'Reviewed and approved';
        insert_record('course_approval_status', $record);
        $record->id = 6;
        $record->shortname = 'published';
        $record->displayname = 'Published';
        $record->description = 'Published';
        insert_record('course_approval_status', $record);
        $record->id = 7;
        $record->shortname = 'suspendeddate';
        $record->displayname = 'Suspended - Out-Dated';
        $record->description = 'Suspended - out-dated';
        insert_record('course_approval_status', $record);
        $record->id = 8;
        $record->shortname = 'suspendedauthor';
        $record->displayname = 'Suspended - By Author ';
        $record->description = 'Suspended - by author request';
        insert_record('course_approval_status', $record);
    }

    if ($oldversion < 2008101300) {
        $table = new XMLDBTable('header_image');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('image', XMLDB_TYPE_TEXT, 'medium', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('url', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($oldversion < 2008101400) {
        set_config('defaultcustomheader', 'welcome.jpg');
    }

    // Upgrade of Learning Path Author roles
    if ($oldversion < 2008103000) {
        // add new roles
        //TODO: hardcoded Lang strings need to be put into lang!
        $roles = array(
            'lpcreator' => array(
                'name'        => 'LP Creator',
                'description' => 'Learning Path Creators can create new learning paths.',
                'legacy'      => 'coursecreator',
            ),
            'lpauthor' => array(
                'name'        => 'Learning Path Author',
                'description' => 'Develops new Learning Plans.'
            ),
            'lpeditor' => array(
                'name'        => 'Learning Path Editor',
                'description' => 'Allowed editing rights on a learning path'
            ),
        );

        foreach ($roles as $shortname => $roledata) {
            if (!array_key_exists('legacy', $roledata)) {
                $roledata['legacy']= '';
            }
            $roles[$shortname]['id'] = create_role($roledata['name'], $shortname, $roledata['description'], $roledata['legacy']);
        }

        // set default capabilities to each role
        $syscontext = get_context_instance(CONTEXT_SYSTEM);

        $lpc = get_record('role', 'shortname', 'lpcreator');
        $lpa = get_record('role', 'shortname', 'lpauthor');
        $lpe = get_record('role', 'shortname', 'lpeditor');

        // creator
        $allowedcaps = array(
            'moodle/local:islpcreator',
            'moodle/course:create',
        );
        foreach ($allowedcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $lpc->id, $syscontext->id, true);
        };

        // editor
        $allowedcaps = array(
            'moodle/local:islpeditor',
            'moodle/local:classifylearningpath',
            'moodle/role:assign',
            'format/page:addpages',
            'format/page:editpages',
            'format/page:managepages',
            'format/page:viewpagesettings',
            'moodle/site:manageblocks',
            'moodle/course:manageactivities',
        );
        foreach ($allowedcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $lpe->id, $syscontext->id, true);
        }

        // author
        $allowedcaps = array(
            'moodle/local:islpauthor',
        );
        foreach ($allowedcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $lpa->id, $syscontext->id, true);
        }

    }

    // assign new capability
    if ($oldversion < 2008110100) {

        $syscontext = get_context_instance(CONTEXT_SYSTEM);

        $role = get_record('role', 'shortname', 'lpauthor');
        assign_capability('moodle/local:viewunpublishedlearningpath', CAP_ALLOW, $role->id, $syscontext->id, true);
        $role = get_record('role', 'shortname', 'headeditor');
        assign_capability('moodle/local:viewunpublishedlearningpath', CAP_ALLOW, $role->id, $syscontext->id, true);

    }

    if ($oldversion < 2008110300) {
        set_config('lpautomatedcategorisation', 1);
    }

    if ($oldversion < 2008111300) {
        create_role('Certified Participating Teacher', 'certifiedpt', 'Role for a Participating Teacher once they have become certified', '');
    }

    if ($oldversion < 2008111800) {
        // make sure ST doesn't get can message own alumni now that we've split 9 & 10
        delete_records('role_capabilities',
            'capability', 'moodle/local:canmessageownalumni',
            'roleid', get_field('role', 'id', 'shortname', ROLE_ST));
    }

    if ($oldversion < 2008112100 && get_site()) {
        $b = (object)array(
            'blockid'  =>  get_field('block', 'id', 'name', 'tao_nav'),
            'pageid'   => SITEID,
            'pagetype' => 'course-view',
            'position' => 'l',
            'weight'   => 1,
            'visible'  => 1,
            'configdata' => '',
        );
        insert_record('block_instance', $b);
    }

    if ($oldversion < 2008112101)  {
        insert_record('header_image', (object)array('image' => 'header_login.jpg', 'url' => '/login/index.php', 'sortorder' => 1));
    }

    if ($oldversion < 2008112102)  {
        //first delete old custom user profile fields
        if ($custid = get_field('user_info_category', 'id', 'name', 'certification')) {
            delete_records('user_info_field', 'categoryid', $custid);
            delete_records('user_info_category', 'name', 'certification');
        }
    }

    if ($oldversion < 2008113000) {
        $table = new XMLDBTable('format_page_user_view');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('format_page_id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($oldversion < 2008120801) {
        // field added to the course_modules table
        $table  = new XMLDBTable('course_modules');
        $field  = new XMLDBField('trackprogress');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0, null);

        /// Launch add field shortname
        $result = $result && add_field($table, $field);
    }

    if ($oldversion < 2009022103) {
        // new group invites table
        $table  = new XMLDBTable('group_invites');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('fromuserid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('groupid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);   
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2009022104) {
       $table  = new XMLDBTable('groups');
       $field  = new XMLDBField('groupleader');
       $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);

       $result = $result && add_field($table, $field);
    }

    if ($oldversion < 2009023800) {
        $table = new XMLDBTable('user_friend');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('friendid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($oldversion < 2009031105) {
        $table = new XMLDBTable('certification_status');
        if (table_exists($table)) {
            drop_table($table);
        } 
    }
    
    if ($oldversion < 2009031107) {
        $headerimages = array();
        $headerimages[1]->image = 'header_login.jpg';
        $headerimages[1]->url = '/login/logout.php';
        $headerimages[1]->description = 'logout page';
        $headerimages[1]->sortorder = 1;

        $headerimages[2]->image = 'mycollaboration.jpg';
        $headerimages[2]->url = '/local/my/collaboration.php';
        $headerimages[2]->description = 'My Collaboration';
        $headerimages[2]->sortorder = 1;

        $headerimages[3]->image = 'mylearning.jpg';
        $headerimages[3]->url = '/local/lp/list.php';
        $headerimages[3]->description = 'Learning Paths';
        $headerimages[3]->sortorder = 1;

        $headerimages[4]->image = 'mylearning.jpg';
        $headerimages[4]->url = '/local/my/learning.php';
        $headerimages[4]->description = 'My Learning';
        $headerimages[4]->sortorder = 1;

        $headerimages[5]->image = 'mywork.jpg';
        $headerimages[5]->url = '/local/my/work.php';
        $headerimages[5]->description = 'My Work';
        $headerimages[5]->sortorder = 1;

        $headerimages[6]->image = 'header_login.jpg';
        $headerimages[6]->url = '/index.php';
        $headerimages[6]->description = 'homepage';
        $headerimages[6]->sortorder = 1;
    
        $headerimages[7]->image = 'header_login.jpg';
        $headerimages[7]->url = '/';
        $headerimages[7]->description = 'homepage';
        $headerimages[7]->sortorder = 1;

        foreach ($headerimages as $himg) {
            //first check to make sure this record doesn't already exist
            if (!record_exists('header_image', 'image', $himg->image, 'url', $himg->url)) {
                insert_record('header_image', $himg);
            }
        }
    }
    if ($oldversion < 2009031108) {
        set_config('defaultblocks_topics', 'tao_nav:course_list');
        set_config('defaultblocks_weeks', 'tao_nav:course_list');
        set_config('defaultblocks_social', 'tao_nav:course_list');
    }
    if ($oldversion < 2009031109) {  //add courseid to header_image table
        $table = new XMLDBTable('header_image');
        $field  = new XMLDBField('courseid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }
    
    }
    if ($oldversion < 2009031110) {  //modify url to allow nulls
        $table = new XMLDBTable('header_image');
        $field  = new XMLDBField('url');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null, null, null);
        $result = $result && change_field_notnull($table, $field);
    }

    if ($oldversion < 2009052201) { // add 'custom' flag to roles table 
        $table = new XMLDBTable('role');
        $field  = new XMLDBField('custom');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }

        // make the assumption that everything with an id over 7 (the standard Moodle installed roles) at this stage is a custom role
        execute_sql("UPDATE {$CFG->prefix}role SET custom = 1 WHERE id > 7");

    }

    if ($oldversion < 2009052501) {
       $table  = new XMLDBTable('user_friend');
       $field  = new XMLDBField('approved');
       $field->setAttributes(XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, null, null, null, null, null);

       if (!field_exists($table, $field)) {
           $result = $result && add_field($table, $field);
       }
    }

    if ($oldversion < 2009062401) {
         $hostid = get_field('mnet_host', 'id', 'name', 'localmahara');
         $host2service = new stdClass();
         $host2service->serviceid = get_field('mnet_service', 'id', 'name', 'local_mahara');
         if (!empty($host2service->serviceid)) {
             $host2service->publish = 0;
             $host2service->subscribe = 1;
             if ($hostrec = get_record('mnet_host2service', 'hostid', $hostid, 'serviceid', $host2service->serviceid)) {
                 $host2service->id = $hostrec->id;
                 update_record('mnet_host2service', $host2service);
             } else {
                 insert_record('mnet_host2service', $host2service);
             }
         }
    }

    if ($oldversion < 2009062800) {  
        $table = new XMLDBTable('learning_path_mode');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);

        $lpmodes = array();
        $lpmodes[0]->shortname = 'standard';
        $lpmodes[0]->name = 'Standard';

        $lpmodes[1]->shortname = 'rafl';
        $lpmodes[1]->name = 'RAFL';

        foreach ($lpmodes as $mode) {
            insert_record('learning_path_mode', $mode);
        }

     }

    if ($oldversion < 2009062800) { 
        $table = new XMLDBTable('course');
        $field  = new XMLDBField('learning_path_mode');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);

        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }

    }

    if ($oldversion < 2009062903) {
        $ptroleid = get_field('role', 'id', 'shortname', ROLE_PT);
        $lparoleid = get_field('role', 'id', 'shortname', ROLE_LPAUTHOR);
        set_config('defaultcourseroleid', $ptroleid);
        set_config('creatornewroleid', $lparoleid);
    }

    if ($oldversion < 2009070200) {
        // force setup of rafl item fields

        $table = new XMLDBTable('format_page');
        $field  = new XMLDBField('rafl_item');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }

        $table = new XMLDBTable('format_page_items');
        $field  = new XMLDBField('rafl_item');
        $field->setAttributes(XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, null, null, null, null, null);
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }
    }

    if ($result && $oldversion < 2009071803) {
        //delete old tao_certification table and insert it's rows into the new table
        $table = new XMLDBTable('tao_certification_status');
        if(table_exists($table)) {
            $certrecords = get_records('tao_certification_status');
            if (!empty($certrecords)) {
                foreach($certrecords as $cert) {
                    $cert->id = null;
                    insert_record('tao_user_certification_status', $cert);
                }
            }
            drop_table($table);
        }
        //now remove old blocks.
        $oldblocknames = array('authored_learning_paths', 'certification_path',
                               'course_status', 'learning_path_summary', 'lp_brief',
                               'lp_qa', 'team_groups');
        foreach($oldblocknames as $oldblockname) {
           if($oldblockid = get_field('block', 'id', 'name', $oldblockname)) {
               //first get new blocks id
               $newblockid  = get_field('block', 'id', 'name', 'tao_'.$oldblockname);
               //now update block instance table replacing oldid with new one
               $oldblocks = get_records('block_instance', 'blockid', $oldblockid);
               if (!empty($oldblocks)) {
                   foreach($oldblocks as $oldblock) {
                       $oldblock->blockid = $newblockid;
                       update_record('block_instance', $oldblock);
                   }
               }
               //now delete old record from block table.
               delete_records('block', 'name', $oldblockname);
           }
        }
    }

    if ($result && $oldversion < 2009072700) {
        //set up custom scale - in postinst so that the $USER is set.
        $scale = new stdclass();
        $scale->courseid = 0;
        $scale->userid = 0;
        $scale->name = 'TAO: Stars';
        $scale->scale = '★☆☆☆☆, ★★☆☆☆, ★★★☆☆, ★★★★☆, ★★★★★';
        $scale->description = '';
        $scale->timemodified = time();
        insert_record('scale', $scale);
    }
    if ($result && $oldversion < 2009072701) {
        //create new table for storing TAOview ratings
        $table = new XMLDBTable('taoview_ratings');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('artefactid', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('time', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('rating', XMLDB_TYPE_INTEGER, 4, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $result = $result && create_table($table);
    }

    if ($result && $oldversion < 2009072706) {
        //published course category is being moved.
        $cat = get_field('course_categories', 'id', 'name', get_string('taocatlp', 'local'));
        $pubcat = get_field('course_categories', 'id', 'name', 'Published');
        if (!empty($cat) && !empty($pubcat)) {
            //set default publish category to the main one.
            set_config('lppublishedcategory', $cat);
            //now find all the courses in the old published category and put them in the new one.
            $publishedcourses = get_records('course', 'category', $pubcat);
            if (!empty($publishedcourses)) {
                foreach ($publishedcourses as $pc) {
                    $pc->category = $cat;
                    update_record('course', $pc);
                }
            }
            //now remove old course category
            delete_records('course_categories', 'id', $pubcat);
        }
    }
    if ($result && $oldversion < 2009072708) {
        //now prevent the admin user from having the switchrole cap in learning path category
        $lpid = get_field('course_categories', 'id', 'name',get_string('taocatlp', 'local'));
        if (!empty($lpid)) { //this will only trigger on upgrades - not for fresh installs as the above category isn't created untill post_inst
            $catcontext = get_context_instance(CONTEXT_COURSECAT, $lpid);
            foreach (get_admin_roles() as $adminrole) {
                assign_capability('moodle/role:switchroles', CAP_PREVENT, $adminrole->id, $catcontext->id);
            }
        }
    }

    if ($result && $oldversion < 2009080300) {
        $reassigncaps = true;
        $resetcustomroles = true;
        $resetstickyblocks = true;
    }
    if ($result && $oldversion < 2009080301) {
        set_config('allowvisiblecoursesinhiddencategories', '1');
    }
    if ($result && $oldversion < 2009080302) {
        //delete old header image records
        delete_records('header_image', 'url', '/local/mahara/taoview.php?view=teaching');
        delete_records('header_image', 'url', '/local/mahara/taoview.php?view=tools');

        $headerimg = new stdclass();
        $headerimg->image = 'myteaching.jpg';
        $headerimg->url = '/local/mahara/taoviewtaoresources.php';
        $headerimg->description = get_string('myteaching','local');
        $headerimg->sortorder = 1;

        if (!record_exists('header_image', 'image', $headerimg->image, 'url', $headerimg->url)) {
            insert_record('header_image', $headerimg);
        }
        $headerimg->image = 'mytools.jpg';
        $headerimg->url = '/local/mahara/taoviewtaotools.php';
        $headerimg->description = get_string('mytools','local');

        if (!record_exists('header_image', 'image', $headerimg->image, 'url', $headerimg->url)) {
            insert_record('header_image', $headerimg);
        }

    }
    if ($result && $oldversion < 2009080303) {
        set_config('defaulthtmleditor', 'tinymce');
    }

    if ($result && $oldversion < 2009080309) {
        require_once($CFG->dirroot.'/mod/rafl/locallib.php');
        if (!check_rafl_table_encoding()) {
             notify("WARNING: The smartassess rafl tables are not utf8 based tables - they must be manually converted.");
             $result = false;
        }
    }

    if ($result && $oldversion < 2009101407) {
        $table = new XMLDBTable('rafl_webcells');
        if (table_exists($table)) {
            $webcelltext = 'How will the learning path be evaluated and developed?';
            $sql = "UPDATE {$CFG->prefix}rafl_webcells SET webcell_title = '" . $webcelltext . "' WHERE webcell_id =368657";
            if (!execute_sql($sql)) {
                 notify('rafl_webcells UPDATE failed');
                 $result = false;
            }
            //now update format page
            $fpitems = get_records('format_page', 'nameone', 'How will the learning path be evaluated and develo');
            foreach ($fpitems as $fpi) {
                $fpi->nameone = $webcelltext;
                $fpi->nametwo = $webcelltext;
                update_record('format_page', $fpi);
            }
        }
    }

    if ($result && $oldversion < 2009101410) {
        $reassigncaps = true;
        $resetcustomroles = true;
    }
    if ($result && $oldversion < 2009101413) {
        //MNET change update.
        //first create array of mappings
        $mnetfields = array();
        $mnetfields['address'] ='address';
        $mnetfields['aim'] ='aimscreenname';
        $mnetfields['city'] ='town';
        $mnetfields['country'] ='country';
        $mnetfields['deleted'] ='deleted';
        $mnetfields['description'] ='introduction';
        $mnetfields['firstname'] ='firstname';
        $mnetfields['htmleditor'] ='wysiwyg';
        $mnetfields['icq'] ='icqnumber';
        $mnetfields['idnumber'] ='studentid';
        $mnetfields['lang'] ='lang';
        $mnetfields['lastname'] ='lastname';
        $mnetfields['mns'] ='mnsnumber';
        $mnetfields['phone1'] ='businessnumber';
        $mnetfields['phone2'] ='homenumber';
        $mnetfields['skype'] ='skypeusername';
        $mnetfields['url'] ='officialwebsite';
        $mnetfields['yahoo'] ='yahoochat';
        //first find any active mnet entries.
        $hosts = get_records_select('mnet_host', 'deleted=0 AND applicationid=2');
        if (!empty($hosts)) {
            foreach ($hosts as $host) {
                foreach ($mnetfields as $name => $value) {
                    set_config($name, $value, 'mnet_userprofile_'.$host->id);
                }
            }
        }
        //now add mapping fields to db.
    }
// check whether we've been asked to reset anything
    if (!empty($reassigncaps)) {
        tao_reassign_capabilities();
    }

    if (!empty($resetcustomroles)) {
        if (empty($CFG->taomode)){ // only perform this in 'core' taomode
            tao_reset_custom_roles();
        } 
    }

    if (!empty($resetstickyblocks)) {
        if (empty($CFG->taomode)){ // only perform this in 'core' taomode
            tao_reset_stickyblocks(true);
        } 
    }

    return $result;
}
?>