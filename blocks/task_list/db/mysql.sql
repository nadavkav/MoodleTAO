# $Id: mysql.sql,v 1.1.1.1 2006/10/13 02:55:43 mark-nielsen Exp $

# task list block database tables

#
# Table structure for table `assess`.  Stores tasks for a task list instance.
#

CREATE TABLE `prefix_block_task_list` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `instanceid` int(10) unsigned NOT NULL default '0',
    `type` varchar(10) NOT NULL default '',
    `name` text NOT NULL default '',    
    `checked` int(4) unsigned NOT NULL default '0',
    `info` text NOT NULL default '',
    `timemodified` int(10) unsigned NOT NULL default '0',
    PRIMARY KEY  (`id`),
    KEY `instanceid` (`instanceid`)
) TYPE=MyISAM COMMENT='Stores tasks for a task list instance';