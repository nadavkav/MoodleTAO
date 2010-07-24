<?php 
$string['allowcoursethemes'] = 'Allow learning path themes';
$string['allowvisiblecoursesinhiddencategories'] = 'Allow visible learning paths in hidden categories';
$string['configallowcategorythemes'] = 'If you enable this, then themes can be set at the category level. This will affect all child categories and learning paths unless they have specifically set their own theme. WARNING: Enabling category themes may affect performance.';
$string['configallowcoursethemes'] = 'If you enable this, then learning paths will be allowed to set their own themes.  Learning path themes override all other theme choices (site, user, or session themes)';
$string['configallowunenroll'] = 'If this is set \'Yes\', then students are allowed to unenrol themselves from learning paths whenever they like. Otherwise they are not allowed, and this process will be solely controlled by the teachers and administrators.';
$string['configallowuserthemes'] = 'If you enable this, then users will be allowed to set their own themes.  User themes override site themes (but not learning path themes)';
$string['configallusersaresitestudents'] = 'For activities on the front page of the site, should ALL users be considered as students?  If you answer \"Yes\", then any confirmed user account will be allowed to participate as a student in those activities.  If you answer \"No\", then only users who are already a participant in at least one learning path will be able to take part in those front page activities. Only admins and specially assigned teachers can act as teachers for these front page activities.';
$string['configvisiblecourses'] = 'Display learning paths in hidden categories normally';
$string['configautologinguests'] = 'Should visitors be logged in as guests automatically when entering learning paths with guest access?';
$string['configcoursemanager'] = 'This setting allows you to control who appears on the learning path description. Users need to have at least one of these roles in a learning path to be shown on the learning path description for that learning path.';
$string['configcourserequestnotify'] = 'Type username of user to be notified when new learning path requested.';
$string['configcourserequestnotify2'] = 'Users who will be notified when a learning path is requested. Only users who can approve learning path requests are listed here.';
$string['configcoursesperpage'] = 'Enter the number of learning paths to be display per page in a learning path listing.';
$string['configcreatornewroleid'] = 'This role is automatically assigned to creators in new learning paths they created. This role is not assigned if creator already has needed capabilities in parent context.';
$string['configdefaultallowedmodules'] = 'For the learning paths which fall into the above category, which modules do you want to allow by default <b>when the learning path is created</b>?';
$string['configdefaultcourseroleid'] = 'Users who enrol in a learning path will be automatically assigned this role.';
$string['configdefaultrequestcategory'] = 'Learning paths requested by users will be automatically placed in this category.';
$string['configdefaultrequestedcategory'] = 'Default category to put learning paths that were requested into, if they\'re approved.';
$string['configdefaultuserroleid'] = 'All logged in users will be given the capabilities of the role you specify here, at the site level, in ADDITION to any other roles they may have been given.  The default is the Authenticated user role (or Guest role in older versions).  Note that this will not conflict with other roles they have, it just ensures that all users have capabilities that are not assignable at the learning path level (eg post blog entries, manage own calendar, etc).';
$string['configdisablecourseajax'] = 'Do not use AJAX when editing main learning path pages.';
$string['configenablecourserequests'] = 'This will allow any user to request a learning path be created.';
$string['configenablestats'] = 'If you choose \'yes\' here, Moodle\'s cronjob will process the logs and gather some statistics.  Depending on the amount of traffic on your site, this can take awhile. If you enable this, you will be able to see some interesting graphs and statistics about each of your learning paths, or on a sitewide basis.';
$string['configenrolmentplugins'] = 'Please choose the enrolment plugins you wish to use. Don\'t forget to configure the settings properly.<br /><br />You have to indicate which plugins are enabled, and <strong>one</strong> plugin can be set as the default plugin for <em>interactive</em> enrolment. To disable interactive enrolment, set \"enrollable\" to \"No\" in required learning paths.';
$string['configforcelogin'] = 'Normally, the front page of the site and the learning path listings (but not learning paths) can be read by people without logging in to the site.  If you want to force people to log in before they do ANYTHING on the site, then you should enable this setting.';
$string['configgradebookroles'] = 'This setting allows you to control who appears on the gradebook.  Users need to have at least one of these roles in a learning path to be shown in the gradebook for that learning path.';
$string['configguestroleid'] = 'This role is automatically assigned to the guest user. It is also temporarily assigned to not enrolled users when they enter learning path that allows guests without password. Please verify that the role has moodle/legacy:guest and moodle/course:view capability.';
$string['confighiddenuserfields'] = 'Select which user information fields you wish to hide from other users other than learning path teachers/admins. This will increase student privacy. Hold CTRL key to select multiple fields.';
$string['configlongtimenosee'] = 'If students haven\'t logged in for a very long time, then they are automatically unsubscribed from learning paths.  This parameter specifies that time limit.';
$string['configmaxbytes'] = 'This specifies a maximum size that uploaded files can be throughout the whole site. This setting is limited by the PHP settings post_max_size and upload_max_filesize, as well as the Apache setting LimitRequestBody. In turn, maxbytes limits the range of sizes that can be chosen at learning path level or module level. If \'Server Limit\' is chosen, the server maximum allowed by the server will be used.';
$string['confignodefaultuserrolelists'] = 'This setting prevents all users from being returned from the database from deprecated calls of get_course_user, etc., for the site learning path if the default role provides that access. Check this, if you suffer a performance hit.';
$string['confignonmetacoursesyncroleids'] = 'By default all role assignments from child learning paths are synchronised to metacourses. Roles that are selected here will not be included in the synchronisation process.';
$string['configopentogoogle'] = 'If you enable this setting, then Google will be allowed to enter your site as a Guest.  In addition, people coming in to your site via a Google search will automatically be logged in as a Guest.  Note that this only provides transparent access to learning paths that already allow guest access.';
$string['configprofilesforenrolledusersonly'] = 'To prevent misuse by spammers, profile descriptions of users who are not yet enrolled in any learning path are hidden. New users must enrol in at least one learning path before they can add a profile description.';
$string['configrequestedstudentname'] = 'Word for student used in requested learning paths';
$string['configrequestedstudentsname'] = 'Word for students used in requested learning paths';
$string['configrequestedteachername'] = 'Word for teacher used in requested learning paths';
$string['configrequestedteachersname'] = 'Word for teachers used in requested learning paths';
$string['configrestrictbydefault'] = 'Should new learning paths that are created that fall into the above category have their modules restricted by default?';
$string['configrestrictmodulesfor'] = 'Which learning paths should have <b>the setting</b> for disabling some activity modules?  Note that this setting only applies to teachers, administrators will still be able to add any activity to a learning path.';
$string['configsectionrequestedcourse'] = 'Learning path requests';
$string['configsendcoursewelcomemessage'] = 'If enabled, users receive a welcome message via email when they self-enrol in a learning path.';
$string['configstatscatdepth'] = 'Statistics code uses simplified learning path enrolment logic, overrides are ignored and there is a maximum number of verified parent learning path categories. Number 0 means detect only direct role assignments on site and learning path level, 1 means detect also role assignments in parent category of learning path, etc. Higher numbers result in much higher database server load during stats processing.';
$string['configstatsuserthreshold'] = 'If you enter a non-zero,  non numeric value here, for ranking learning paths, learning paths with less than this number of enrolled users (all roles) will be ignored';
$string['configteacherassignteachers'] = 'Should ordinary teachers be allowed to assign other teachers within learning paths they teach?  If \'No\', then only learning path creators and admins can assign teachers.';
$string['coursemanager'] = 'Learning path managers';
$string['coursemgmt'] = 'Add/edit learning paths';
$string['courseoverview'] = 'Learning path overview';
$string['courserequests'] = 'Learning path Requests';
$string['courserequestnotify'] = 'Learning path request notification';
$string['courserequestnotifyemail'] = 'User $a->user requested a new learning path at $a->link';
$string['courserequestspending'] = 'Pending learning path requests';
$string['courses'] = 'Learning paths';
$string['coursesperpage'] = 'Learning paths per page';
$string['creatornewroleid'] = 'Creators\' role in new learning paths';
$string['defaultcourseroleid'] = 'Default role for users in a learning path';
$string['defaultrequestcategory'] = 'Default category for learning path requests';
$string['disablecourseajax'] = 'Disable AJAX learning path editing';
$string['enablecourserequests'] = 'Enable learning path requests';
$string['longtimenosee'] = 'Unsubscribe users from learning paths after';
$string['mnetrestore_extusers_noadmin'] = '<strong>Note:</strong> This backup file seems to come from a different Moodle installation and contains remote Moodle Network user accounts. You are not allowed to execute this type of restore. Contact the administrator of the site or, alternatively, restore this learning path without any user information (modules, files...)';
$string['sendcoursewelcomemessage'] = 'Send learning path welcome message';
$string['stickyblockscourseview'] = 'Learning path page';
$string['uucoursedefaultrole'] = 'Default learning path role';

?>