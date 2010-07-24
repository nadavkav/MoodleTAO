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
 * local (tao) specific language strings.
 * these should be called like get_string('key', 'local');
 */

// local capability strings //
$string['local:messagebyrole'] = 'Message users by role';
$string['local:classifylearningpath'] = 'Classify Learning Path';
$string['local:viewunpublishedlearningpath'] = 'View Unpublished Learning Path';
$string['local:canassignmt'] = 'Can assign a Master Teacher to themselves (for Senior Teachers)';
$string['local:canassignpt'] = 'Can assign a Participating Teacher to themselves (for Master Teachers)';
$string['local:isassignablept'] = 'Is assignable as a Participating Teacher';
$string['local:isassignablemt'] = 'Is assignable as a Master Teacher';
$string['local:islpeditor'] = 'Is Learning Path Editor';
$string['local:islpauthor'] = 'Is Learning Path Author';
$string['local:islpcreator'] = 'Is Learning Path Creator';
$string['local:viewresponsibleusersbehalfof'] = 'View responsible-for users on behalf of another user';
$string['local:viewresponsibleusers'] = 'View own responsible-for users';
$string['local:canmessageownpts'] = 'Message own Participating Teachers';
$string['local:canmessagefellowmts'] = 'Message other Master Teachers';
$string['local:canmessageownmts'] = 'Message own Master Teachers';
$string['local:canmessagemtspts'] = 'Message Participating Teachers of own Master Teachers';
$string['local:canmessagefellowsts'] = 'Message other Senior Teachers';
$string['local:canmessagehts'] = 'Message Head Teachers';
$string['local:canmessageownalumni'] = 'Message own Alumni';
$string['local:canmessageallalumni'] = 'Message all Alumni';
$string['local:canmessagemtsalumni'] = 'Message Alumni of own Master Teachers';
$string['local:canmessagealumnibylp'] = 'Can message alumni of a specific LP';
$string['local:canmessageallpts'] = 'Message all Participating Teachers';
$string['local:canmessageanyuser'] = 'Message any other user';
$string['local:canmessagefellowadmins'] = 'Message other Admins'; 
$string['local:canmessagefellowpts'] = 'Message other Participating Teachers';
$string['local:canmessagelumnibylp'] = 'Message alumni by Learning Path certification';
$string['local:cansearchforlptomessage'] = 'Can search for LP to message users in';
$string['local:hasdirectlprelationship'] = 'Has a direct (not implied) relationship to an LP';
$string['local:ispt'] = 'Is Participating Teacher';
$string['local:ismt'] = 'Is Master Teacher';
$string['local:isst'] = 'Is Senior Teacher';
$string['local:invitenewuser'] = 'Invite new user';
$string['local:cancreatetemplates'] = 'Create Templates';
$string['local:cancreatelearningpaths'] = 'Create Learning Paths';
$string['local:bulkinvitenewuser'] = 'Bulk invite new user';
$string['local:canchangelpsettings'] ='Can change Learning Path Settings';
$string['local:createstandardlp'] ='Can Create standard Learning Path';
$string['local:managelpcontributors'] ='Manage Learning Path Contibutors';
$string['local:managemytasks'] ='Manage my tasks';
$string['local:viewlpcontributors'] ='view Learning Path Contributors';
$string['local:canselfassignheadeditor'] = 'can self assign as Head Editor';
$string['local:canselfassigntemplateeditor'] = 'can self assign as Template Editor';
$string['local:isheadeditor'] = 'is head editor';
$string['local:managepageactivities'] = 'Manage page activities';
$string['local:savelearningpathtemplate'] = 'can save learning path template';
$string['local:updatecoursestatus'] = 'update course status';
$string['local:viewcertificationblock'] = 'view certification block';
$string['local:viewcoursestatus'] = 'view course status';
$string['local:canimportlegacytao'] = 'can import legacy TAO learing paths';

// roles and assignments between users
$string['assignrole'] = 'Assign this user to me as a $a';
$string['unassignrole'] = 'Unassign this user from me as a $a';
$string['notassignable'] = 'Sorry, but that user is not assignable to you as a $a';
$string['roleassigned'] = 'Successfully assigned that user to you as a $a';
$string['roleassignedshort'] = 'Role assigned';
$string['roleunassigned'] = 'Successfully unassigned that user from you as a $a';
$string['roleunassignedshort'] = 'Role unassigned';
$string['couldnotunassignrole'] = 'A serious but unspecified error occurred while trying to unassign a role from you';
$string['alreadyassigned'] = 'That user is already assigned to you as a $a';
$string['roleassignmentdidnotexist'] = 'Could not unassign that user from you as a $a, the role assignment did not exist';
$string['nosuchuser'] = 'Could not find any user with those details who is able to be assigned to you';
$string['teacherid'] = 'Teacher ID';
$string['finduser'] = 'Search for a user';
$string['nousers'] = 'There are no users you are currently responsible for. Perhaps try searching for some';
$string['responsiblefor'] = 'Users you are responsible for';
$string['responsibleforbehalfof'] = 'Users $a is responsible for';
$string['roletype'] = 'Relationship type';
$string['grandchildren'] = 'Inherited users';

$string['assignedheadeditorshort'] = 'Assigned Head Editor';
$string['assignedheadeditor'] = 'You have been assigned as a Head Edtitor of this Learning Path';
$string['alreadyassignedheadeditor'] = 'You are already assigned as Head Editor of this Learning Path';

$string['assignedtemplateeditorshort'] = 'Assigned Template Editor';
$string['assignedtemplateeditor'] = 'You have been assigned as a Template Editor or this Learning Path';
$string['alreadyassignedtemplateeditor'] = 'You are already assigned as Template Editor of this Learning Path';

/* messaging related strings TODO remove these once we make sure they're not used anymore
$string['messagebyrole'] = 'Message by role';
$string['messageroles'] = 'Select roles to send messages to';
$string['messagerolesatleastone'] = 'You must select at least one role to message';
$string['sendtoroles'] = 'Send to role(s)';
$string['messagetargets'] = 'Enabled target roles';
$string['messagerolesenabled'] = 'Select roles to enable messaging to';
$string['messagenoroles'] = 'No roles available to select for messaging';
*/

// new ones
$string['messagebody'] = 'Message body';
$string['nomessagetargets'] = 'Sorry, but you do not have the relevant permissions to message lists of users';
$string['messagenorecipients'] = 'Sorry, but there were no users to message matching the selected list';
$string['sitelists'] = 'Site Lists';
$string['lplists'] = 'Learning Path Lists';
$string['messagesearchforlp'] = 'Search for a Learning Path';
$string['messagebyrole'] = 'By Role';
$string['sendmessage'] = 'Send!';
$string['sendingmessageto'] = 'Sending message to $a->target ($a->count users)';
$string['sendingmessagetocourse'] = 'Sending message to $a->target of $a->course ($a->count users)';
$string['messagelistfooter'] = 'This message was sent to you as a member of $a->target.  Replying to this message will just reply to the sender, not the group.';
$string['messagelistfootercourse'] = 'This message was sent to you as a member of $a->target of $a->course.  Replying to this message will just reply to the sender, not the group.';
$string['messagequeued'] = 'Queued your message for sending. It will be sent soon!';
// targets
$string['messagetargetallalumni'] = 'All alumni';
$string['messagetargetallalumnionlp'] = 'Alumni by LP certification';
$string['messagetargetallownpts'] = 'My Participating Teachers';
$string['messagetargetalluncertifiedpts'] = 'All uncertified Participating Teachers';
$string['messagetargetanyotheruser'] = 'Any user';
$string['messagetargethts'] = 'Head Teachers';
$string['messagetargetotheradmins'] = 'All other Administrators';
$string['messagetargetothermts'] = 'All other Master Teachers';
$string['messagetargetotherptsonlp'] = 'Other Participating Teachers';
$string['messagetargetothersts'] = 'Other Senior Teachers';
$string['messagetargetownalumni'] = 'My alumni';
$string['messagetargetmtsalumni'] = 'Alumni of my Master Teachers';
$string['messagetargetownmts'] = 'My Master Teachers';
$string['messagetargetownptsonlp'] = 'My Participating Teachers by LP';
$string['messagetargetownuncertifiedpts'] = 'My uncertified Participating Teachers';
$string['messagetargetptsofmts'] = 'Participating Teachers of my Master Teachers';
$string['messagetargetptsonlp'] = 'Participating Teachers by LP';
$string['messagetargetuncertifiedptsofmts'] = 'Uncertified Participating Teachers of my Master Teachers';
$string['messagetargetheadeditors'] = 'Editorial Board';

// settings strings
$string['taosettings'] = 'TAO Settings';
$string['configtemplatecategory'] = 'This category will be used for creating and selecting learning path templates.';
$string['chooseauthoringmode'] = 'Choose the Authoring Mode';
$string['choosetemplatecategory'] = 'Choose the Template Category';
$string['configdefaultcategory'] = 'This category will be used as the default workspace for creating new learning paths.';
$string['choosedefaultcategory'] = 'Choose the Default Category';
$string['configlpautomatedcategorisation'] = 'If enabled the system will automatically move a learning path to the correct category when the status is updated.';
$string['lpautomatedcategorisation'] = 'Automated Categorisation of Learning Paths?';
$string['configpublishedcategory'] = 'This category will be used for holiding published learning paths.';
$string['choosepublishedcategory'] = 'Choose the Published Learning Path Category';
$string['configsuspendedcategory'] = 'This category will be used for holding suspended learning paths.';
$string['choosesuspendedcategory'] = 'Choose the Suspended Learning Path Category';

// learning path status related strings
$string['addnewlearningpath'] = 'Add a new learning path';
$string['choosetemplate'] = 'Choose a template';
$string['reason'] = 'Reason';
$string['nostatusset'] = 'No Status Set';
$string['statuschangeheading'] = 'Change Learning Path Status';
$string['historyheading'] = 'Learning Path Status Change History';
$string['statusunchanged'] = 'The learning path status was not changed';
$string['statusupdated'] = 'The learning path status was updated';
$string['missingstatusreason'] = 'Please give a reason for the status change';
$string['lpsubmitted'] = 'A learning path has been submitted for approval and requires review';
$string['submittedby'] = 'submitted by';
$string['categoryupdateerror'] = 'Could not update category';
$string['statusupdateerror'] = 'Could not update status';
$string['statushistoryupdateerror'] = 'Could not update status history';
$string['statuscustomhookerror'] = 'Could not execute custom hook';
$string['courseisunpublished'] = 'This learning path is not published';
$string['notpermittedtoviewcourse'] = 'You are not permitted to view this course';

// learning path page strings
$string['defaultlearningpathfullname'] = 'Learning Path Fullname 101';
$string['defaultlearningpathshortname'] = 'LP101';
$string['classification'] = 'Classification';
$string['makebackup'] = 'Save Template';
$string['lpsummarypagetitle'] = 'Summary Page';
$string['createtemplate'] = 'Create a new Template';

// Completion checklist popup
$string['cannotcompletelearningpath'] = 'These function can only be selected for learning paths contained in your list of personal learning paths. Please start by choosing a learning path from your list of personal learning paths.';
$string['lpcompletionchecklist'] = 'Completion Checklist';

// learning path template name - used by silent restore and initial courses
$string['learningpathtemplate'] = 'Learning Path Template';
$string['learningpathtemplateshortname'] = 'LPTEMPLATE';

// learning path classification strings
$string['editlpclass'] = 'Edit classification type';
$string['lpclassification'] = 'Classification';
$string['lpclassificationheading'] = 'Learning Path Classification Administration';
$string['currentvalue'] = 'Current value';
$string['addclass'] = 'Add new value';
$string['classifylp'] = 'Classify your Learning Path';

//user Classification strings
$string['editmyclassifications'] = 'Edit my TAO topics interest';
$string['taotopicsinterest'] = 'TAO Topics interest';

// my learning path page (replacement of http://aoc.ssatrust.org.uk/index?s=13)
$string['mylearningpaths'] = 'My Learning Paths';
$string['myownlearningpaths'] = 'My Own Learning Paths';
$string['mylearningpathsdescription'] = 'This area provides quick access to the Learning Paths you are currently working on or simply want to retain a quick link to.  You can add learning paths to this list by joining a group on the learning path or selecting \"Add to my learning paths\" when viewing the learning path.';
$string['learningpaths'] = 'Learning Paths';
$string['learningpath'] = 'Learning Path';
$string['backtolist'] = 'Back to Learning Paths';
$string['novisiblecourses'] = 'No Learning Paths matched the selected criteria';
$string['nolearningpaths'] = 'None';
$string['browselearningpaths'] = 'Browse Learning Paths';
$string['browselearningpathsdescription'] = '<p>Published lesson examples showing recommended teaching methods and related activities. The examples are meant as an explanation of the principles and ideas and not to serve as blueprint for your own lessons. You and your team can determine whether the methods meet your expectations and/or needs.

<p>You can make use of your own experience and expand the variety of methods to suit the learning process during your lessons.';
$string['recommendedlearningpaths'] = 'Recommended Learning Paths';
$string['recommendedlearningpathsdescription'] = 'Learning Paths are recommended based on the interests marked down in your user profile.';
$string['updateyourinterests'] = 'Update your interests';
$string['nomatchinglearningpaths'] = 'No learning paths match your interests';
$string['mycertification'] = 'My Certification';
$string['reviewcertification'] = 'Review certification progress';
$string['mycertificationdescription'] = '<p>Participants will receive a certificate that can be digitally stored in your CPD record. Completion of the certification tasks of at least one learning path is required to obtain a certificate.</p>

<p>To select a learning path to pursue certification against join a group on the learning path.</p>';
$string['mylearningpathcontributions'] = 'Paths I am contributing to';
$string['mylearningpathbookmarks'] = 'Paths I am working on';

// my work page
$string['myroles'] = 'My Role(s)';
$string['myrolestext'] = 'If you believe these are not correct or you are missing roles please contact your site administrator';
$string['nowork'] = 'No Work';
$string['noworktext'] = 'You have no work assigned for your roles';
$string['authoredlearningpaths'] = 'My Authored Learning Paths';
$string['messaging'] = 'Messaging';
$string['messagebyrolelink'] = 'Message Other Users By Role';
$string['myediting'] = 'My Editing';
$string['learningpathsneededit'] = 'Learning Paths Requiring an Editor';
$string['learningpathsneedpublish'] = 'Unpublished Learning Paths';
$string['nolearningpaths'] = "No Learning Paths";
$string['editlearningpath'] = "Assign self as editor";
$string['myparticipants'] = "My Participants";
$string['lptemplates'] = "Learning Path Templates";
$string['assignedtotemplate'] = "Assigned to template";
$string['createnewtemplate'] = "Create new template";

// my collaboration page
$string['mygroups'] = "My Groups";
$string['notinagroup'] = "You are not in any groups on a learning path.";
$string['messageall'] = "message";
$string['invite'] = "invite";
$string['members'] = "Members";
$string['noguest'] = 'The \'$a->page\' is not available to guests.';

// certification path page (replacement of http://aoc.ssatrust.org.uk/index?s=8)
$string['certification'] = 'Certification';
$string['learningpathstatus'] = 'Status of your learning paths';
$string['certifyparticipants'] = 'Certify participants';
$string['noparticipantstocertify'] ='None of your participants has requested to be certified.';
$string['moredetail'] = 'More Detail';
$string['lessdetail'] = 'Less Detail';

// learning path errors
$string['cannotselfassignedit'] = 'You are not permitted to self assign editing rights';
$string['cannotselfassigntemplate'] = 'You are not permitted to self assign template editing rights';

// map url to header
$string['imagemap'] = 'Header images';
$string['imagemapdesc'] = 'Use this page to configure custom header images to show up on various pages in the system based on urls';
$string['setdefault'] = 'Set as default image';
$string['gobacktolist'] = 'Go back to list';
$string['alreadydefault'] = 'Note that this is already the default image, so you don\'t need to explicitly set mappings for it.  It will show on all pages with no other mappings';
$string['configuringmappingsfor'] = 'Configuring URL mappings for $a';
$string['mappinghelp'] = 'Enter the URLs and sortorder here of the pages you wish to map to this image. If you like, you can enter an optional description for each entry. This is not used for anything else than a reminder for you.   When a page in the system is requested, the first matching result (with the lowest sortorder) will be used.  Each URL should be relative to the system, eg /course/index.php, and not include the URL root at all.  Wildcards are used on either side of your URL fragment.';
$string['nomappings'] = 'No mappings yet';
$string['addnewurlmap'] = 'Add a new url mapping';
$string['currentmap'] = 'Edit or delete existing mapping';
$string['url'] = 'URL';

// nav strings
$string['myprofile'] = 'My Profile';
$string['mylearning'] = 'My Learning';
$string['mywork'] = 'My Tasks';
$string['myteaching'] = 'My Teaching';
$string['mytools'] = 'My Tools';
$string['mycollaboration'] = 'My Collaboration';
$string['learningpathtasks'] = 'Learning Path Tasks';

//certificate module locking.
$string['namelockedwarning'] = 'Your First Name and Last Name cannot be changed as you have been issued a certificate. Please contact an Administrator if you would like to change your name'; 
$string['namelockedwarningadmin'] = 'Warning: this user has been issued a certificate. Only Administrators can change this persons name';
$string['requiredcertification'] = 'Required Certification';
$string['requiredcertificationdesc'] = 'You must obtain Certification before being able to view this certificate.';
$string['setcertification'] = 'Set Certification';

//Progress Indicator
$string['progressindicator'] = 'Progress Indicator';
$string['trackprogress'] = 'Track Progress with Progress Indicator';

// post install strings
$string['siteforumname'] = 'TAO Site Forum';
$string['siteforumintro'] = 'Welcome to the TAO Site Forum';

// default Learning Path Forum strings
$string['defaultforumname'] = 'Learning Path Forum';
$string['defaultforumintro'] = 'Welcome to the Learning Path Forum';

// default Learning Path Wiki strings
$string['defaultwikiname'] = 'Learning Path Wiki';
$string['defaultwikisummary'] = 'Welcome to the Learning Path Wiki';

// site wide FAQ
$string['defaultglossaryname'] = 'FAQ';
$string['defaultglossarydescription'] = 'Welcome to the global FAQ';

// friend strings
$string['requestfriend'] = 'Request Friend';
$string['arefriend'] = 'You are friends with $a->user';
$string['friendapprovalneeded'] = 'You have requested to add $a->user as a friend, but they have not yet authorised your request.';
$string['arealreadyfriend'] = 'You are already friends with this user';
$string['notfriends'] = 'You are not friends with this user';
$string['removefriend'] = 'Remove as a friend';
$string['removefriendrequest'] = 'Remove friend request';
$string['friended'] = 'You have added this user as a friend';
$string['userfriendrequest'] = 'You have requested to add this user as a friend';
$string['unfriended'] = 'You have removed this user from your friends';
$string['couldnotaddasfriend'] = 'Could not add as friend';
$string['couldnotremoveasfriend'] = 'Could not remove as friend';
$string['friendpendingauthorisation'] = 'Pending Authorisation';
$string['myfriends'] = 'My Friends';
$string['mypendingfriends'] = 'Friend Request(s) Awaiting Approval';
$string['relatedcourses'] = 'Learning Paths with this tag';
$string['friendrequests'] = 'Request(s) to add me as a friend';
$string['acceptfriendemailsubject'] = 'Friend request accepted';
$string['acceptfriendemailbody'] = 'Hi $a->firstname,

$a->user has accepted your Friend Request
for more details, see:
$a->url';
$string['declinefriendemailsubject'] = 'Friend request declined';
$string['declinefriendemailbody'] = 'Hi $a->firstname,

$a->user has declined your Friend Request
for more details, see:
$a->url';
$string['requestfriendemailsubject'] = 'Friend request';
$string['requestfriendemailbody'] = 'Hi $a->firstname,

$a->user has added you as a friend.
To approve or decline this request, see:
$a->url';

// rafl strings
$string['standardview'] = 'Switch to Standard View';
$string['raflview'] = 'Switch to RAFL View';
$string['lpcontributors'] = 'Contributors';
$string['lpleader'] = 'Leader';
$string['managelpcontributors'] = 'Manage contributors';
$string['nocontributors'] = 'No contributors';
$string['raflmodeenabled'] = 'Enable the RAFL Mode authoring functionality';
$string['raflmodeenabledhelp'] = 'Checking this will all participants to create learning paths using the 3rd-Party RAFL Module from SmartAssess';

//initial frontpage content:
$string['initialfrontpagecontent'] = '<h1>Intel Teach - Advanced Online &amp; Collaborative</h1>
<p>At Intel, we believe in sharing our passion for technology in order to create a transparent and open dialogue with people in our industry. Explore our communities and join the conversation—share thoughts and ideas, or get an insider\'s view on how Intel approaches many of the issues that affect technology today.<span style=\"font-weight: bold;\"></span></p>
<div id=\"main\"><!-- 50-50-two-col-container -->
  <div class=\"half\"> <!-- section-title --> <!--Optional \"btop\" snippet can be added here--> <!-- section-title-linked -->
    <h2><a href=\"$a->myteachinglink\">My Teaching</a> <b>›</b><br /></h2><!-- /section-title-linked --> <img src=\"$a->myteachingimg\" /> A repository of resources to assist you with your teaching. Upload and share your own resources with others.    <div class=\"endfloat\"></div>
    <div class=\"endfloat\"></div> </div>
  <div class=\"half half-last\">
    <h2><a href=\"$a->mylearninglink\">My Learning</a> <b>›</b><br /></h2> <img src=\"$a->mylearningimg\" /> Published lesson examples showing recommended teaching methods and related activities.</div>
  <div class=\"endfloat\"></div> <!-- /50-50-two-col-container --> </div>
<div class=\"clearer\"></div> <!-- 50-50-two-col-container -->
<div class=\"half\"> <!-- section-title --> <!--Optional \"btop\" snippet can be added here--> <!-- section-title-linked -->
  <h2><a href=\"$a->mytoolslink\">My Tools</a> <b>›</b><br /></h2><!-- /section-title-linked --> <img src=\"$a->mytoolsimg\" /> Interactive tools designed to help with your teaching.</div>
<div class=\"half half-last\">
  <h2><a href=\"$a->mycollablink\">My Collaboration</a> <b>›</b><br /></h2> <img src=\"$a->mycollabimg\" /> Plan, share and exchange ideas on learning paths with other team members using these networking tools.</div>
<div class=\"endfloat\"></div> <!-- /50-50-two-col-container -->
<div class=\"clearer\"></div> <br />
<h2>Programme Introductory Sessions</h2>
<p>Intel Teach - Advanced Online is an international CPD programme designed to capture the CPD evidence generated during curriculum re-development and the creation of related resources. It does this through the use of a five-step Learning Path. This five step approach provides a framework in which new ideas can be planned, developed, used and evaluated.</p>
<p><b>Free Registration</b></p>
<p>This website provides free access to online support, training resources and collaborative tools. Registration, participation and use of the online platform are free, subject to completion of initial introductory training by at least one member of the school curriculum, CPD or management team.</p><br /> ';

//Header langs
$string['myportfolio'] = 'My Portfolio';
$string['myhomepage'] = 'My Homepage';

$string['viewmaharaprofile'] = 'View Portfolio';

$string['accept'] = 'Accept';
$string['decline'] = 'Decline';
$string['message']= 'Message';
$string['missingidnumber'] ='Missing Idnumber';
$string['inviteauser'] = 'Invite a user';
$string['emailconfirmsent'] = 'An invitation email should have been sent to the address at <b>$a</b>';
$string['certified_pt'] = 'Certified PT';
$string['certified_mt'] = 'Certified MT';
$string['certified_st'] = 'Certified ST';

//taoview strings
$string['taoview'] = 'TAO View';
$string['taoresources'] = 'Teaching Resources';
$string['taotools'] = 'Teaching Tools';
$string['moreinfo'] = 'more info';
$string['rating'] = 'Rating';
$string['rate'] = 'Rate';
$string['sendinratings'] = 'Send in Ratings';
$string['ratingssaved'] = 'Ratings Saved';
$string['noartefactsfound'] = 'no artefacts found';
$string['toaddartefacts'] = 'To add a resource to this page, go to <a href=\"$a->link\">your portfolio</a>';
$string['filteredby'] = 'Filtered by';
$string['removefilters'] = 'Remove filters';
$string['sortby'] = 'Sort by';

//TAO POSTinst strings - used to name courses/categories etc.
$string['taotrainingcourses'] = 'TAO Training Courses';
$string['taocatworkshop'] = 'Workshop';
$string['taocatsuspended'] = 'Suspended';
$string['taocatlptemplates'] = 'Learning Path Templates';
$string['taocatlp'] = 'Learning Paths';

//custom tags string
$string['browsersearchtags'] = 'browse/search all tags';

//edit lp settings page
$string['editlpsettings'] = 'Edit learning path settings';
$string['courseupdated'] = 'Course updated';

//legacy tao import script
$string['legacytaoimport'] = 'Legacy TAO Import';
$string['legacydbconfig'] = 'DB Config';
$string['legacydbusers'] = 'Import Users';
$string['legacydblp'] = 'Import Learning Paths';

//strings used in the intel theme
$string['faq'] = 'FAQ';
$string['intelheadergraphic'] = 'Intel Header Graphic';
$string['intellogo'] = 'Logo - Intel';

//strings used by SSAT to place extra content on the taoview pages
$string['taotoolsdesc'] = '';
$string['taoresourcesdesc'] = '';
?>