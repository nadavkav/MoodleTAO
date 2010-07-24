# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                 localhost
# Database:             realsmart
# Server version:       5.0.51a-community-nt
# Server OS:            Win32
# Target-Compatibility: MySQL 5.0
# max_allowed_packet:   1048576
# HeidiSQL version:     3.2 Revision: 1129
# --------------------------------------------------------

/*!40100 SET CHARACTER SET utf8*/;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0*/;

#
# Table structure for table 'mdl_rafl_cohorts'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_cohorts` (
  `cohort_id` int(11) NOT NULL auto_increment,
  `cohort_name` text NOT NULL,
  `cohort_school` int(11) NOT NULL default '0',
  `cohort_lepp_id` int(11) default NULL,
  PRIMARY KEY  (`cohort_id`),
  KEY `cohort_school` (`cohort_school`),
  KEY `cohort_name` (`cohort_name`(10),`cohort_school`),
  CONSTRAINT `mdl_rafl_cohorts_fk` FOREIGN KEY (`cohort_school`) REFERENCES `mdl_rafl_school` (`sc_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43568 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_cohorts'
#

# (No data found.)


#
# Table structure for table 'mdl_rafl_comments'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `comment_item` int(11) NOT NULL default '0',
  `comment_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `comment_report` int(11) default '0',
  `comment_rate` float default NULL,
  `comment_votes` int(11) default NULL,
  `comment_share` int(11) NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `comment_item` (`comment_item`)
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_comments'
#

# (No data found.)



#
# Table structure for table 'mdl_rafl_item_rating'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_item_rating` (
  `ir_id` int(10) unsigned NOT NULL auto_increment,
  `ir_itemid` int(10) unsigned NOT NULL default '0',
  `ir_memberid` int(10) NOT NULL default '0',
  PRIMARY KEY  (`ir_id`),
  KEY `ir_itemid` (`ir_itemid`),
  KEY `ir_memberid` (`ir_memberid`),
  CONSTRAINT `mdl_rafl_item_rating_fk` FOREIGN KEY (`ir_itemid`) REFERENCES `mdl_rafl_items` (`item_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_rating_fk1` FOREIGN KEY (`ir_memberid`) REFERENCES `mdl_rafl_members` (`mb_id`) ON DELETE CASCADE
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_item_rating'
#

# (No data found.)



#
# Table structure for table 'mdl_rafl_item_reports'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_item_reports` (
  `ir_id` int(10) unsigned NOT NULL auto_increment,
  `ir_itemid` int(10) unsigned NOT NULL default '0',
  `ir_memberid` int(10) NOT NULL default '0',
  `ir_mentorid` int(11) NOT NULL COMMENT 'Mentor ID flagged to review this report',
  `ir_shareid` int(11) NOT NULL COMMENT 'Share ID for this context',
  `ir_date_added` datetime NOT NULL COMMENT 'Date and time the report was filed',
  `ir_status` enum('pending','accepted','denied') NOT NULL default 'pending' COMMENT 'Status of this report',
  `ir_reportingip` varchar(15) NOT NULL COMMENT 'IP address of reporter',
  PRIMARY KEY  (`ir_id`),
  KEY `ir_itemid` (`ir_itemid`),
  KEY `ir_memberid` (`ir_memberid`),
  KEY `ir_status` (`ir_status`),
  KEY `ir_shareid` (`ir_shareid`),
  CONSTRAINT `mdl_rafl_item_reports_fk` FOREIGN KEY (`ir_itemid`) REFERENCES `mdl_rafl_items` (`item_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_reports_fk1` FOREIGN KEY (`ir_memberid`) REFERENCES `mdl_rafl_members` (`mb_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_reports_fk2` FOREIGN KEY (`ir_shareid`) REFERENCES `mdl_rafl_share` (`share_id`) ON DELETE CASCADE
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_item_reports'
#

# (No data found.)



#
# Table structure for table 'mdl_rafl_item_type'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_item_type` (
  `item_type_id` tinyint(4) unsigned NOT NULL auto_increment,
  `item_type_name` tinytext NOT NULL,
  PRIMARY KEY  (`item_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'item_type'
#

LOCK TABLES `mdl_rafl_item_type` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_item_type` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_item_type` (`item_type_id`, `item_type_name`) VALUES
	(1,'rafl'),
	(2,'rweb'),
	(3,'rcast'),
	(4,'rmap'),
	(5,'rchat'),
	(6,'rafl_result'),
	(7,'rplan'),
	(8,'rplan_lesson'),
	(9,'comment'),
	(10,'message');
/*!40000 ALTER TABLE `mdl_rafl_item_type` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_item_view_status'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_item_view_status` (
  `item_view_status_id` bigint(11) unsigned NOT NULL auto_increment COMMENT 'Primary key',
  `item_type_id` tinyint(4) unsigned NOT NULL COMMENT 'The item type of this comment/evidence',
  `mb_id_writer` int(11) NOT NULL COMMENT 'The member who has written the comment/evidence',
  `mb_id_viewer` int(11) NOT NULL COMMENT 'The member who is to view the comment/evidence',
  `item_id_comment_evidence` int(11) unsigned NOT NULL COMMENT 'The item id for the comment OR evidence affected',
  `item_id` int(11) unsigned NOT NULL COMMENT 'The item id of the unit, task and success criteria of the affected comment OR evidence',
  `share_id` int(11) default NULL COMMENT 'The share id for this comment/evidence',
  `date_created` date NOT NULL,
  PRIMARY KEY  (`item_view_status_id`),
  KEY `item_view_status_id_fk` (`mb_id_writer`),
  KEY `item_view_status_id_fk1` (`mb_id_viewer`),
  KEY `item_view_status_id_fk2` (`item_id`),
  KEY `item_view_status_id_fk3` (`share_id`),
  KEY `item_view_status_id_fk4` (`item_type_id`),
  CONSTRAINT `mdl_rafl_item_view_status_id_fk` FOREIGN KEY (`mb_id_writer`) REFERENCES `mdl_rafl_members` (`mb_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_view_status_id_fk1` FOREIGN KEY (`mb_id_viewer`) REFERENCES `mdl_rafl_members` (`mb_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_view_status_id_fk2` FOREIGN KEY (`item_id`) REFERENCES `mdl_rafl_items` (`item_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_view_status_id_fk3` FOREIGN KEY (`share_id`) REFERENCES `mdl_rafl_share` (`share_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_item_view_status_id_fk4` FOREIGN KEY (`item_type_id`) REFERENCES `mdl_rafl_item_type` (`item_type_id`) ON DELETE CASCADE
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_item_view_status'
#

# (No data found.)



#
# Table structure for table 'mdl_rafl_items'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_items` (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `item_webcell` int(11) NOT NULL default '0',
  `item_parent_item` int(11) unsigned zerofill default '00000000000',
  `item_school` int(11) NOT NULL default '0',
  `item_default_type` tinyint(4) unsigned NOT NULL default '0',
  `item_access` tinyint(4) unsigned default NULL,
  PRIMARY KEY  (`item_id`),
  KEY `item_webcell` (`item_webcell`),
  KEY `item_school` (`item_school`),
  KEY `item_access` (`item_access`),
  KEY `item_default_type` (`item_default_type`),
  KEY `item_parent_item` (`item_parent_item`),
  CONSTRAINT `mdl_rafl_items_fk` FOREIGN KEY (`item_webcell`) REFERENCES `mdl_rafl_webcells` (`webcell_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_items_fk1` FOREIGN KEY (`item_school`) REFERENCES `mdl_rafl_school` (`sc_id`),
  CONSTRAINT `mdl_rafl_items_fk2` FOREIGN KEY (`item_access`) REFERENCES `mdl_rafl_item_access` (`item_access_id`) ON DELETE SET NULL,
  CONSTRAINT `mdl_rafl_items_fk3` FOREIGN KEY (`item_default_type`) REFERENCES `mdl_rafl_item_type` (`item_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=402518 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_items'
#

LOCK TABLES `mdl_rafl_items` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_items` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_items` (`item_id`, `item_webcell`, `item_parent_item`, `item_school`, `item_default_type`, `item_access`) VALUES
	('368597',368647,'0',287,1,3),
	('368607',368657,'368597',287,1,3),
	('368617',368667,'368597',287,1,3),
	('368627',368677,'368597',287,1,3),
	('368637',368687,'368597',287,1,3),
	('368647',368697,'368597',287,1,3),
	('402267',402327,'368647',287,1,3),
	('402277',402337,'368647',287,1,3),
	('402287',402347,'368647',287,1,3),
	('402297',402357,'368647',287,1,3),
	('402307',402367,'368647',287,1,3),
	('402317',402377,'368637',287,1,3),
	('402327',402387,'368637',287,1,3),
	('402337',402397,'368637',287,1,3),
	('402347',402407,'368637',287,1,3),
	('402357',402417,'368637',287,1,3),
	('402367',402427,'368637',287,1,3),
	('402377',402437,'368627',287,1,3),
	('402387',402447,'368627',287,1,3),
	('402397',402457,'368627',287,1,3),
	('402407',402467,'368627',287,1,3),
	('402417',402477,'368627',287,1,3),
	('402427',402487,'368627',287,1,3),
	('402467',402527,'368607',287,1,3),
	('402477',402537,'368607',287,1,3),
	('402487',402547,'368607',287,1,3),
	('402497',402557,'368607',287,1,3),
	('402507',402567,'368617',287,1,3),
	('402517',402577,'368617',287,1,3);
/*!40000 ALTER TABLE `mdl_rafl_items` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_members'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_members` (
  `mb_id` int(11) NOT NULL,
  `mb_firstname` tinytext,
  `mb_surmame` tinytext,
  `mb_username` tinytext NOT NULL,
  `mb_password` tinytext NOT NULL,
  `mb_school` int(11) NOT NULL default '0',
  `mb_pic` tinytext,
  `mb_theme` int(11) default '1',
  `mb_sound` int(11) default NULL,
  `mb_school_id` text,
  `mb_ratetype` int(11) default NULL,
  `mb_type` tinytext NOT NULL,
  `mb_icon` varchar(40) default 'smiley',
  `mb_colour` varchar(40) default 'gradient',
  `mb_date_added` datetime default NULL COMMENT 'Date and time the member was added',
  PRIMARY KEY  (`mb_id`),
  UNIQUE KEY `school_usernames` (`mb_username`(40),`mb_school`),
  KEY `mb_school_id` (`mb_school_id`(10)),
  KEY `mb_school` (`mb_school`),
  CONSTRAINT `mdl_rafl_members_fk` FOREIGN KEY (`mb_school`) REFERENCES `mdl_rafl_school` (`sc_id`)
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_members'
#

LOCK TABLES `mdl_rafl_members` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_members` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_members` (`mb_id`, `mb_firstname`, `mb_surmame`, `mb_username`, `mb_password`, `mb_school`, `mb_pic`, `mb_theme`, `mb_sound`, `mb_school_id`, `mb_ratetype`, `mb_type`, `mb_icon`, `mb_colour`, `mb_date_added`) VALUES
	(97007,NULL,NULL,97007,97007,287,NULL,1,NULL,'1',NULL,'learner','smiley','gradient',NULL),
	(97017,NULL,NULL,97017,97017,287,NULL,1,NULL,'3',NULL,'learner','smiley','gradient',NULL);
/*!40000 ALTER TABLE `mdl_rafl_members` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_rafl'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_rafl` (
  `rafl_id` int(11) NOT NULL auto_increment,
  `rafl_item` int(11) NOT NULL default '0',
  `rafl_level` tinytext,
  `rafl_version` tinytext,
  `rafl_desc` text,
  `rafl_collective` int(11) default '0',
  `rafl_order` int(11) NOT NULL default '0',
  `rafl_lock` int(11) NOT NULL default '0',
  `rafl_success_obj` tinytext,
  `rafl_success_evid_req` int(11) default NULL,
  `rafl_rtool_id` int(11) default NULL,
  `rafl_type` text,
  `rafl_map_id` int(11) NOT NULL,
  PRIMARY KEY  (`rafl_id`),
  KEY `rafl_item` (`rafl_item`),
  KEY `rafl_map_id` (`rafl_map_id`),
  KEY `rafl_rtool_id` (`rafl_rtool_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50238 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_rafl'
#

LOCK TABLES `mdl_rafl_rafl` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_rafl` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_rafl` (`rafl_id`, `rafl_item`, `rafl_level`, `rafl_version`, `rafl_desc`, `rafl_collective`, `rafl_order`, `rafl_lock`, `rafl_success_obj`, `rafl_success_evid_req`, `rafl_rtool_id`, `rafl_type`, `rafl_map_id`) VALUES
	(37607,368637,NULL,NULL,NULL,0,1,0,NULL,NULL,3,'task',368597),
	(37617,368647,NULL,NULL,NULL,0,0,0,NULL,NULL,2,'task',368597),
	(37587,368617,NULL,NULL,NULL,0,3,0,NULL,NULL,5,'task',368597),
	(37597,368627,NULL,NULL,NULL,0,2,0,NULL,NULL,4,'task',368597),
	(37567,368597,NULL,NULL,'\r',NULL,0,0,NULL,NULL,1,'unit',368597),
	(37577,368607,NULL,NULL,NULL,0,4,0,NULL,NULL,6,'task',368597),
	(50017,402267,NULL,NULL,NULL,0,4,0,NULL,1,11,'criteria',368597),
	(50027,402277,NULL,NULL,NULL,0,3,0,NULL,1,10,'criteria',368597),
	(50037,402287,NULL,NULL,NULL,0,2,0,NULL,1,9,'criteria',368597),
	(50047,402297,NULL,NULL,NULL,0,1,0,NULL,1,8,'criteria',368597),
	(50057,402307,NULL,NULL,NULL,0,0,0,'Understand the learning path',1,7,'criteria',368597),
	(50067,402317,NULL,NULL,NULL,0,5,0,NULL,1,17,'criteria',368597),
	(50077,402327,NULL,NULL,NULL,0,4,0,NULL,1,16,'criteria',368597),
	(50087,402337,NULL,NULL,NULL,0,3,0,NULL,1,15,'criteria',368597),
	(50097,402347,NULL,NULL,NULL,0,2,0,NULL,1,14,'criteria',368597),
	(50107,402357,NULL,NULL,NULL,0,1,0,NULL,1,13,'criteria',368597),
	(50117,402367,NULL,NULL,NULL,0,0,0,'Establish how the learning path will be relevant to me, and learn:',1,12,'criteria',368597),
	(50127,402377,NULL,NULL,NULL,0,5,0,NULL,1,23,'criteria',368597),
	(50137,402387,NULL,NULL,NULL,0,4,0,NULL,1,22,'criteria',368597),
	(50147,402397,NULL,NULL,NULL,0,3,0,NULL,1,21,'criteria',368597),
	(50157,402407,NULL,NULL,NULL,0,2,0,NULL,1,20,'criteria',368597),
	(50167,402417,NULL,NULL,NULL,0,1,0,NULL,1,19,'criteria',368597),
	(50177,402427,NULL,NULL,NULL,0,0,0,'Use learning paths in the classroom',1,18,'criteria',368597),
	(50187,402467,NULL,NULL,NULL,0,3,0,NULL,1,32,'criteria',368597),
	(50197,402477,NULL,NULL,NULL,0,2,0,NULL,1,31,'criteria',368597),
	(50207,402487,NULL,NULL,NULL,0,1,0,NULL,1,30,'criteria',368597),
	(50217,402497,NULL,NULL,NULL,0,0,0,'Evaluate and develop the learning path',1,29,'criteria',368597),
	(50227,402507,NULL,NULL,NULL,0,1,0,NULL,1,25,'criteria',368597),
	(50237,402517,NULL,NULL,NULL,0,0,0,'Require resources',1,24,'criteria',368597);
/*!40000 ALTER TABLE `mdl_rafl_rafl` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_rafl_res'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_rafl_res` (
  `rafl_res_id` int(11) NOT NULL auto_increment,
  `rafl_res_item` int(11) NOT NULL default '0',
  `rafl_res_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `rafl_res_rate` int(11) default NULL,
  `rafl_res_share` int(11) NOT NULL,
  PRIMARY KEY  (`rafl_res_id`),
  KEY `rafl_res_item` (`rafl_res_item`)
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_rafl_res'
#

# (No data found.)


#
# Table structure for table 'mdl_rafl_school'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_school` (
  `sc_id` int(11) NOT NULL auto_increment,
  `sc_name` text NOT NULL,
  `sc_logo` text,
  `sc_user` text NOT NULL,
  `sc_password` text NOT NULL,
  `sc_department` text,
  `sc_uploadsize` int(11) default NULL,
  `sc_primary` text,
  `sc_primary_email` text,
  `sc_champ` text,
  `sc_champ_email` text,
  `sc_admin` text,
  `sc_admin_email` text,
  `sc_realsmart` tinyint(4) NOT NULL default '1',
  `sc_alite` tinyint(4) NOT NULL default '0',
  `sc_modwrite` tinytext NOT NULL,
  `sc_disklimit_soft` mediumint(9) unsigned NOT NULL default '10000' COMMENT 'Soft limit for disk space in MB',
  `sc_disklimit_hard` mediumint(9) unsigned NOT NULL default '25000' COMMENT 'Hard limit for disk space in MB',
  `sc_north` int(11) default NULL,
  `sc_gate` int(11) default NULL,
  `sc_terms` char(50) NOT NULL default 'N',
  `sc_rafl` tinyint(1) default '1',
  `sc_rcast` tinyint(1) default '1',
  `sc_rmap` tinyint(1) default '1',
  `sc_rplan` tinyint(1) default '1',
  `sc_rweb` tinyint(1) default '1',
  `connum` int(11) default NULL,
  PRIMARY KEY  (`sc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=288 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_school'
#

LOCK TABLES `mdl_rafl_school` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_school` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_school` (`sc_id`, `sc_name`, `sc_logo`, `sc_user`, `sc_password`, `sc_department`, `sc_uploadsize`, `sc_primary`, `sc_primary_email`, `sc_champ`, `sc_champ_email`, `sc_admin`, `sc_admin_email`, `sc_realsmart`, `sc_alite`, `sc_modwrite`, `sc_disklimit_soft`, `sc_disklimit_hard`, `sc_north`, `sc_gate`, `sc_terms`, `sc_rafl`, `sc_rcast`, `sc_rmap`, `sc_rplan`, `sc_rweb`, `connum`) VALUES
	(287,'TAOC ','TAOC.jpg','school@smartassess.com','smartpass',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,'taoc',10000,25000,NULL,NULL,'N',1,1,1,0,1,NULL);
/*!40000 ALTER TABLE `mdl_rafl_school` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_share'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share` (
  `share_id` int(11) NOT NULL,
  `share_item` int(11) NOT NULL default '0',
  `share_subject` int(11) NOT NULL default '0',
  `share_name` varchar(1000) default NULL,
  `share_public` int(11) default NULL,
  `share_type` int(11) NOT NULL default '4',
  `share_school` int(11) NOT NULL default '0',
  `share_active` tinytext NOT NULL,
  `share_permission` int(11) default NULL,
  `share_member` int(11) NOT NULL default '0' COMMENT 'Corresponding member who has shared this item (eg. mentor)',
  `share_status` char(3) NOT NULL default 'NEW',
  PRIMARY KEY  (`share_id`),
  KEY `share_item` (`share_item`),
  KEY `share_member` (`share_member`),
  KEY `share_subject` (`share_subject`),
  KEY `share_permission` (`share_permission`),
  KEY `share_active` (`share_active`(3))
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_share'
#

LOCK TABLES `mdl_rafl_share` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_share` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_share` (`share_id`, `share_item`, `share_subject`, `share_name`, `share_public`, `share_type`, `share_school`, `share_active`, `share_permission`, `share_member`, `share_status`) VALUES
	(5555555,368597,12707,'Class',0,3,287,'ACCEPTED',NULL,97007,'OLD');
/*!40000 ALTER TABLE `mdl_rafl_share` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_share_cohort'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share_cohort` (
  `share_cohort_id` int(11) NOT NULL auto_increment,
  `share_cohort_share` int(11) NOT NULL default '0',
  `share_cohort_cohort` int(11) NOT NULL default '0',
  `share_cohort_member` int(11) NOT NULL default '0',
  PRIMARY KEY  (`share_cohort_id`),
  KEY `share_cohort_share` (`share_cohort_share`),
  KEY `share_cohort_member` (`share_cohort_member`)
) ENGINE=InnoDB AUTO_INCREMENT=10974 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_share_cohort'
#

# (No data found.)


#
# Table structure for table 'mdl_rafl_share_cohort_members'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share_cohort_members` (
  `s_c_m_id` int(11) NOT NULL auto_increment,
  `s_c_m_share` int(11) NOT NULL default '0',
  `s_c_m_cohort` int(11) default NULL,
  `s_c_m_member` int(11) NOT NULL default '0',
  `s_c_m_status` varchar(255) character set utf8 NOT NULL default 'NEW',
  `s_c_m_sharer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`s_c_m_id`),
  KEY `s_c_m_cohort` (`s_c_m_cohort`),
  KEY `s_c_m_share` (`s_c_m_share`),
  KEY `s_c_m_member_combo` (`s_c_m_member`,`s_c_m_cohort`),
  CONSTRAINT `mdl_rafl_share_cohort_members_fk` FOREIGN KEY (`s_c_m_member`) REFERENCES `mdl_rafl_members` (`mb_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_share_cohort_members_fk1` FOREIGN KEY (`s_c_m_cohort`) REFERENCES `mdl_rafl_cohorts` (`cohort_id`) ON DELETE CASCADE,
  CONSTRAINT `mdl_rafl_share_cohort_members_fk2` FOREIGN KEY (`s_c_m_share`) REFERENCES `mdl_rafl_share` (`share_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=389284 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_share_cohort_members'
#

LOCK TABLES `mdl_rafl_share_cohort_members` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_share_cohort_members` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_share_cohort_members` (`s_c_m_id`, `s_c_m_share`, `s_c_m_cohort`, `s_c_m_member`, `s_c_m_status`, `s_c_m_sharer`) VALUES
	(389243,7,NULL,97017,'OLD',97007);
/*!40000 ALTER TABLE `mdl_rafl_share_cohort_members` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_share_indiv'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share_indiv` (
  `share_indiv_id` int(11) NOT NULL auto_increment,
  `share_indiv_share` int(11) NOT NULL default '0',
  `share_indiv_indiv` int(11) NOT NULL default '0',
  `share_indiv_member` int(11) NOT NULL default '0',
  `share_indiv_status` varchar(255) character set utf8 NOT NULL default 'NEW',
  PRIMARY KEY  (`share_indiv_id`),
  KEY `share_indiv_share` (`share_indiv_share`),
  KEY `share_indiv_member` (`share_indiv_member`)
) ENGINE=InnoDB AUTO_INCREMENT=2 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_share_indiv'
#

LOCK TABLES `mdl_rafl_share_indiv` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_share_indiv` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_share_indiv` (`share_indiv_id`, `share_indiv_share`, `share_indiv_indiv`, `share_indiv_member`, `share_indiv_status`) VALUES
	(1,7,97017,97007,'OLD');
/*!40000 ALTER TABLE `mdl_rafl_share_indiv` ENABLE KEYS*/;
UNLOCK TABLES;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS*/;



#
# Table structure for table 'mdl_rafl_share_mentor'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share_mentor` (
  `share_mentor_id` int(11) NOT NULL auto_increment,
  `share_mentor_share` int(11) NOT NULL default '0',
  `share_mentor_mentor` int(11) NOT NULL default '0',
  `share_mentor_member` int(11) NOT NULL default '0',
  `share_mentor_status` varchar(255) character set utf8 NOT NULL default 'NEW',
  PRIMARY KEY  (`share_mentor_id`),
  KEY `share_mentor_share` (`share_mentor_share`),
  KEY `share_mentor_mentor` (`share_mentor_mentor`)
) ENGINE=InnoDB /*!40100 DEFAULT CHARSET=utf8*/;




#
# Dumping data for table 'mdl_rafl_share_mentor'
#

LOCK TABLES `mdl_rafl_share_mentor` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_share_mentor` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_share_mentor` (`share_mentor_id`, `share_mentor_share`, `share_mentor_mentor`, `share_mentor_member`, `share_mentor_status`) VALUES
	(1,5555555,97017,97007,'OLD');
/*!40000 ALTER TABLE `mdl_rafl_share_mentor` ENABLE KEYS*/;
UNLOCK TABLES;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS*/;



#
# Table structure for table 'mdl_rafl_share_type'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_share_type` (
  `invite_type_id` int(11) NOT NULL auto_increment,
  `invite_type_name` tinytext NOT NULL,
  PRIMARY KEY  (`invite_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_share_type'
#

LOCK TABLES `mdl_rafl_share_type` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_share_type` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_share_type` (`invite_type_id`, `invite_type_name`) VALUES
	(3,'contributor');
/*!40000 ALTER TABLE `mdl_rafl_share_type` ENABLE KEYS*/;
UNLOCK TABLES;



#
# Table structure for table 'mdl_rafl_subjects'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_subjects` (
  `subject_id` int(11) NOT NULL auto_increment,
  `subject_name` tinytext NOT NULL,
  `subject_member` int(11) NOT NULL default '0',
  `subject_school` int(11) NOT NULL default '0',
  `subject_lepp_id` varchar(12) default NULL,
  PRIMARY KEY  (`subject_id`),
  KEY `subject_member` (`subject_member`),
  KEY `subject_school` (`subject_school`)
) ENGINE=InnoDB AUTO_INCREMENT=12708 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_subjects'
#

LOCK TABLES `mdl_rafl_subjects` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_subjects` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_subjects` (`subject_id`, `subject_name`, `subject_member`, `subject_school`, `subject_lepp_id`) VALUES
	(12707,'The New Learning Path',97007,287,NULL);
/*!40000 ALTER TABLE `mdl_rafl_subjects` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'mdl_rafl_webcells'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ `mdl_rafl_webcells` (
  `webcell_id` int(11) NOT NULL auto_increment,
  `webcell_title` tinytext NOT NULL,
  `webcell_text` text,
  `webcell_member` int(11) NOT NULL default '0',
  `webcell_school` int(11) NOT NULL default '0',
  PRIMARY KEY  (`webcell_id`),
  KEY `webcell_member` (`webcell_member`),
  KEY `webcell_school` (`webcell_school`)
) ENGINE=InnoDB AUTO_INCREMENT=402578 /*!40100 DEFAULT CHARSET=utf8*/;



#
# Dumping data for table 'mdl_rafl_webcells'
#

LOCK TABLES `mdl_rafl_webcells` WRITE;
/*!40000 ALTER TABLE `mdl_rafl_webcells` DISABLE KEYS*/;
INSERT INTO `mdl_rafl_webcells` (`webcell_id`, `webcell_title`, `webcell_text`, `webcell_member`, `webcell_school`) VALUES
	(368647,'What are learning paths?',/*!40100 _utf8*/ 0x3C703E3C666F6E7420666163653D2247656F726769612C2054696D6573204E657720526F6D616E2C2054696D65732C207365726966222073697A653D2233223E3C7374726F6E673E416476616E636564204F6E6C696E6520616E6420436F6C6C61626F726174697665202E206120677569646520746F20746865204C6561726E696E672050617468733C62723E54686520416476616E636564204F6E6C696E6520616E6420436F6C6C61626F7261746976652043504420706C6174666F726D2069732064657369676E656420746F20656E636F7572616765206F6E676F696E6720436F6E74696E756F75732050726F66657373696F6E616C20446576656C6F706D656E74206F6E206120E2809C50756C6CE2809D206F722064656D616E64206C65642062617369732E2054686520706C6174666F726D207573657320612072616E6765206F662061636365737320706F696E747320666F72207061727469636970616E74732C2065616368206F662077686963682070726F76696465732061206C696E6B20746F20746865206B65792070726F66657373696F6E616C20646576656C6F706D656E7420726F757465732077697468696E2074686520706C6174666F726D2C207768696368206172652064657363726962656420617320E2809C4C6561726E696E67205061746873E2809D2E3C62723E3C7374726F6E673E3C62723E5768617420617265204C6561726E696E672050617468733F203C2F7374726F6E673E3C62723E3C62723E41204C6561726E696E67205061746820697320726F757465207468726F75676820776869636820612054656163686572206F722054656163686572732063616E20646576656C6F702061206E657720736574206F66206D6574686F646F6C6F677920666F722064656C69766572696E6720616E2061726561206F72206172656173206F662074686520637572726963756C756D2E2041732070617274206F66207468652070726F636573732C207061727469636970616E747320706C616E20616E6420646576656C6F70206C6573736F6E20706C616E732C206E6577206163746976697469657320616E64207265736F75726365732C20776869636820617265206576616C756174656420696E2075736520746F20656E61626C6520636F6E74696E756F757320696D70726F76656D656E74207468726F756768207265666C6563746976652070726163746963652E3C62723E3C62723E447572696E672074686520646576656C6F706D656E7420616E642064656C6976657279206F6620746865204C6561726E696E672050617468732C207061727469636970616E74732077696C6C20656E636F756E74657220616E64207072616374696365206E6577207465616368696E67206D6574686F646F6C6F6769657320616E64206E6577206D6574686F6473206F66207265736F75726365206372656174696F6E20616E64207468726F7567682074686520757365206F66207468657365206E657720746F6F6C732077696C6C20656E68616E63652074686569722070726163746963652E3C62723E3C62723E436F6C6C61626F726174696F6E206265747765656E207061727469636970616E7473206973206B657920746F20746865207375636365737366756C20646576656C6F706D656E74206F662061206C6561726E696E67207061746877617920616E642061206E756D626572206F6620726F7574657320616E6420746F6F6C73206172652070726F7669646564206F6E2074686520416476616E636564204F6E6C696E652026616D703B20436F6C6C61626F72617469766520706C6174666F726D20746F20656E61626C6520616E6420656E636F7572616765206C6F63616C2C20726567696F6E616C2C206E6174696F6E616C20616E6420696E7465726E6174696F6E616C20636F6C6C61626F726174696F6E2E203C62723E3C62723E5375636820636F6C6C61626F726174696F6E206E656564206E6F74206265207265737472696374656420746F206F6E6520706172746963756C617220637572726963756C756D207375626A65637420617265612C266E6273703B206B6579207374616765206F722070686173652C2073696E63652074686520646576656C6F706D656E74206F66206E65772069646561732C20636F6E636570747320616E64206D6574686F646F6C6F6769657320746861742063616E2062652061646F70746564206163726F73732074686520637572726963756C756D2069732074686520636F726520666F6375732E203C62723E266E6273703B3C2F666F6E743E3C2F703E,97007,287),
	(368657,'How will the learning path be evaluated and developed?',NULL,97007,287),
	(368667,'Resource requirements',NULL,97007,287),
	(368677,'How will it work in the classroom?',NULL,97007,287),
	(368687,'How will it be relevant to me? - What will I learn',NULL,97007,287),
	(368697,'About the learning path','<p>Guidance</p><p>Text&nbsp;</p><p>Media&nbsp;</p><p>Files&nbsp;</p><p><br></p>',97007,287),
	(402327,'Analysed what opportunities for collaboration there will be','<p>Guidance</p>\n<p>Text&nbsp;</p>\n<p>Media&nbsp;</p>\n<p>Files&nbsp;</p>\n<p>&nbsp;</p>',97007,287),
	(402337,'Decided which lesson topic would be most suited to this methodology','<p>Guidance</p>\n<p>Text&nbsp;</p>\n<p>Media&nbsp;</p>\n<p>Files&nbsp;</p>\n<p>&nbsp;</p>',97007,287),
	(402347,'Proposed a teaching methodology','<p>Guidance</p>\n<p>Text&nbsp;</p>\n<p>Media&nbsp;</p>\n<p>Files&nbsp;</p>\n<p>&nbsp;</p>',97007,287),
	(402357,'Determined what the added value to the learner experience will be','<p>Guidance</p>\n<p>Text&nbsp;</p>\n<p>Media&nbsp;</p>\n<p>Files&nbsp;</p>\n<p>&nbsp;</p>',97007,287),
	(402367,'Planned ideas for the potential use of the learning path','<p>Guidance</p>\n<p>Text&nbsp;</p>\n<p>Media&nbsp;</p>\n<p>Files&nbsp;</p>\n<p>&nbsp;</p>',97007,287),
	(402377,'What additional support I will require',NULL,97007,287),
	(402387,'What opportunities for collaboration there will be',NULL,97007,287),
	(402397,'What steps I will required to reach the CPD outcomes',NULL,97007,287),
	(402407,'What skills will I develop as part of this process',NULL,97007,287),
	(402417,'What the CPD outcomes are that are being sought',NULL,97007,287),
	(402427,'What teacher skills will be required to implement the Learning Path',NULL,97007,287),
	(402437,'Explained what opportunities for collaboration there will be',NULL,97007,287),
	(402447,'Determined what pupil skills will be required to implement the Learning Path',NULL,97007,287),
	(402457,'Outlined the steps that will be required to reach the learning outcomes',NULL,97007,287),
	(402467,'Categorised how the learning will be assessed',NULL,97007,287),
	(402477,'Listed what learning outcomes will be sought in the classroom',NULL,97007,287),
	(402487,'Linked learning paths to curricula and subject plans',NULL,97007,287),
	(402527,'Planned how participants will re-purpose and redevelop the learning pathway for its next use as the process goes full circle',NULL,97007,287),
	(402537,'Identified areas for improvement in future implementations',NULL,97007,287),
	(402547,'Identified areas of good practice and success.',NULL,97007,287),
	(402557,'Assessed and evaluateed each lesson using a combination of individual and group reflections.',NULL,97007,287),
	(402567,'Determined what additional support will be required in the classroom',NULL,97007,287),
	(402577,'Established what technical and resource requirements there will be',NULL,97007,287);
/*!40000 ALTER TABLE `mdl_rafl_webcells` ENABLE KEYS*/;

UNLOCK TABLES;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS*/;

