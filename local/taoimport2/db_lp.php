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
 * @subpackage 
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('taoimportlib.php');
require_once('db_lp_form.php');
require_once($CFG->dirroot.'/backup/lib.php');
require_once($CFG->dirroot.'/local/lib.php');
require_once($CFG->dirroot.'/backup/restorelib.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/course/format/learning/lib.php');
require_once($CFG->dirroot . '/course/format/page/lib.php');
require_once($CFG->dirroot . '/course/format/learning/pagelib.php');

require_capability('moodle/local:canimportlegacytao', get_context_instance(CONTEXT_SYSTEM));
$confirm = optional_param('confirm', '', PARAM_INT);

$strheading = get_string('legacytaoimport', 'local');
print_header($strheading, $strheading, build_navigation($strheading));



$importlang = 'text_en_US';
$dbh = taoimport_dbconnect();
if (!$dbh) {
    error("couldn't connect to db");
}
$strdbconfig = get_string('legacydbconfig', 'local');
$strdbusers = get_string('legacydbusers', 'local');
$strdblp = get_string('legacydblp', 'local');

$tabs[] = new tabobject('dbconfig', 'db_config.php', $strdbconfig, $strdbconfig, false);
$tabs[] = new tabobject('langfix', 'fixclassifylang.php', 'Fix Classification Lang', 'Fix Classification Lang', false);
$tabs[] = new tabobject('dbusers', 'db_users.php', $strdbusers, $strdbusers, false);
$tabs[] = new tabobject('dblp', 'db_lp.php', $strdblp, $strdblp, false);

print_tabs(array($tabs), 'dblp');
$mform = new taodb_lp_form('db_lp.php');
$schoolid = get_field('rafl_school', 'sc_id', 'sc_name', 'TAOC');
$errors = "";
$count = 0;
$countupdate = 0;
$debug = false; //easy way to set a debug val for a particular lp.

$sql = "SELECT pn.id, pn.title, pt.email, pn.method_id, pn.visible 
	    FROM presentation pn
	    JOIN participant pt ON pn.author = pt.id";
$sql .=	" ORDER BY pn.id";

 $rs = $dbh->Execute($sql);

 if ( $rs->RecordCount() ) {
     $mdata = $mform->get_data();
     if (empty($mdata)) {
         echo "<p>The following LP's will be imported:<br/>";
	 echo "<table>";
	 echo "<tr><td><b>Title</b></td><td><b>Author Email</b></td></tr>";
         $numimportlp = 0;
         while ($rec = $rs->FetchRow()) {
             if ($rec['visible'] <> 1) {
                 continue;
             }
             echo "<tr><td>" . $rec['title']."</td><td>".$rec['email']."</td><td>";
             //print_object(load_pages_from_legacy($dbh, $rec['id']));
             echo "</td></tr>";
             $numimportlp++;
         }
	 echo "</table>";
         echo "</p><p><strong>This script will try to import ".$numimportlp. " Learning paths - are you sure you want to do this?</strong></p>";
         //print form
         $mform->display();
         print_footer();
         exit();

     } else {

         while ($rec = $rs->FetchRow()) {
             if (empty($rec['title'])) { //sanity check
                 continue;
             }
             if ($rec['visible'] <> 1) {
                 continue;
             }
             //$debug = false;
             /*if ($rec['id'] == '139') {
                $debug = true;
             }*/
             if ($debug) {
                echo "<p>importing ID:".$rec['id']."</p>";
             }
             $author = get_author($dbh, $rec, $errors);
             if (empty($author)) {
                 $author = $USER;
             }
             $lp['legacyformat'] = 'STANDARD';
             $lp['content'] = load_pages_from_legacy($dbh, $rec['id']);
	         $lp['topics'] = load_topics_from_legacy($dbh, $rec['id']);

         if ($debug) {
              echo "<p>LP data!</p>";
              print_object($lp);
              print_object($share);
         }

         if (!record_exists('course', 'idnumber', $rec['id'])) { //check if already exists.
		 //echo '<p>' . $rec['id'] . ' not exists';
                 $data = new stdClass();
                 $data->course_template = $mdata->course_template;
                 $data->idnumber  = $rec['id'];
                 $data->fullname  = addslashes($rec['title']);
                 $data->shortname = addslashes($rec['title']);
                 $data->summary   = isset($lp['topics']['shortinfo']) ? addslashes($lp['topics']['shortinfo']):'';

                 $data->defaultrole = $CFG->defaultcourseroleid;
                 $data->format = 'learning';
                 $data->guest=1;
                 $data->groupmode=1;
                 $data->learning_path_mode = LEARNING_PATH_MODE_STANDARD;
                 $data->category = get_field('course_categories', 'id', 'name', 'Workshop');
                 $creatorroleid = get_field('role', 'id', 'shortname', ROLE_LPAUTHOR);
                 if ($debug) {
                     echo "<div>data:";
                     print_object($data);
                     echo "</div>";
                 }
                 $preferences = array();
                 $preferences['nopages'] = 1; //don't create pages - use the legacy data to do this.
                 tao_create_lp($data, $author, $creatorroleid, 0, $preferences);
                 echo ".";
                 flush();
                 $count++;
             } else {
                 echo '<p>' . $rec['id'] . ' exists';
                 $countupdate++;
             }

             $courseid = get_field('course', 'id', 'idnumber', $rec['id']);

             //first check if first "summary" page exists - if not, create it!
             $summarypageid = get_field('format_page','id', 'nameone', get_string('lpsummarypagetitle', 'local'), 'courseid', $courseid);
             $pageid = $courseid; // pageid is actually courseid in the blockinstance context.  i know!
             $pagetype = 'course-view';

             if (empty($summarypageid)) {
                 $page = new stdClass;
                 $page->nameone         = get_string('lpsummarypagetitle', 'local');
                 $page->nametwo         = get_string('lpsummarypagetitle', 'local');
                 $page->courseid        = $courseid;
                 $page->display         = 1;
                 $page->showbuttons     = 3;
                 $summarypageid = insert_record('format_page', $page);

                 // add the standard blocks for a summary page

                 $instanceid = tao_add_learningpath_block('tao_learning_path_summary', $pageid, $pagetype, "Learning Stations");
                 if (!empty($instanceid)) {
                     tao_add_learningpath_pageitem($summarypageid, $instanceid);
                 }
             }
             //now insert content.
             foreach ($lp['content'] as $pagenameid => $evds) {
                 //first check this page exists - if not, create it!
                 $pagenamemarker = 'legacyid'.$pagenameid;
                 $formatpageid = get_field('format_page', 'id', 'courseid', $courseid, 'nameone', $pagenamemarker);
                 if (empty($formatpageid)) {
                     // add the format page
                     $page = new stdClass;
                     $page->nameone         = $mdata->$pagenamemarker;
                     $page->nametwo         = $mdata->$pagenamemarker;
                     $page->courseid        = $courseid;
                     $page->display         = 1;
                     $page->showbuttons     = 3;
                     $page->parent          = $summarypageid;
                     $formatpageid = insert_record('format_page', $page);
                     // add the title block
                     $instanceid = tao_add_learningpath_block('html', $pageid, $pagetype, '', '<h1>' . $page->nameone . '</h1>');
                     if (!empty($instanceid)) {
                         tao_add_learningpath_pageitem($formatpageid, $instanceid);
                     }

       		         if (!empty($evds['questions'])) {
                         foreach ($evds['questions'] as $question => $qdata) {
						      // call the store_evidence routines
                              //use Textlib to convert/clean data
                             // $textlib = textlib_get_instance();
                              //$qdata['text'] = $textlib->convert($qdata['text'], strtolower(mb_detect_encoding($qdata['text'])));
						      $text = clean_text($qdata['text'], FORMAT_MOODLE);

                              // insert question as a block and format_page item on this page
                              $instanceid = tao_add_learningpath_block('tao_lp_qa', $pageid, $pagetype, $question, $text);

                              // create a new page item that links to the instance
                              if (!empty($instanceid)) {
                                  tao_add_learningpath_pageitem($formatpageid, $instanceid);
                              }
				    	}
				    }
			    }
             }

             //now do stuff with Teaching methods = presentation methods.
             $sql = "SELECT $importlang FROM language, presMethod WHERE ".
                    "language.id=presMethod.title_id AND presMethod.id=".$rec['method_id'];
             $rs2 = $dbh->Execute($sql);
             if (!empty($rs2)) {
                while($rec2 = $rs2->FetchRow()) {
                    $method = $rec2[$importlang];
                    $existingmethod = get_record('classification_value', 'value', $method);
                    if (!empty($existingmethod)) { //this is a valid method.
                        //now check to see if classification already set for this LP
                        if (!record_exists('course_classification', 'course', $courseid, 'value', $existingmethod->id)) {
                            //need to insert a classification and a tag.
                            insert_record('course_classification', (object)array('course' => $courseid, 'value' => $existingmethod->id));
                            tag_set_add('courseclassification', $courseid, strtolower($method));
                        }
                    }
                }
             }


             //now do stuff with Teaching strategies = presentation concepts.
             $sql = "SELECT $importlang FROM language, presConcept, pres_has_concept WHERE ".
                    "language.id=presConcept.language_id AND presConcept.id=pres_has_concept.c_id AND ".
                    "pres_has_concept.p_id=".$rec['id'];
             $rs2 = $dbh->Execute($sql);
             if (!empty($rs2)) {
                while($rec2 = $rs2->FetchRow()) {
                    $method = $rec2[$importlang];
                    $existingmethod = get_record('classification_value', 'value', $method);
                    if (!empty($existingmethod)) { //this is a valid method.
                        //now check to see if classification already set for this LP
                        if (!record_exists('course_classification', 'course', $courseid, 'value', $existingmethod->id)) {
                            //need to insert a classification and a tag.
                            insert_record('course_classification', (object)array('course' => $courseid, 'value' => $existingmethod->id));
                            tag_set_add('courseclassification', $courseid, strtolower($method));
                        }
                    }
                 }
             }
             //now do stuff with Categories/Learning Styles.
             $sql = "SELECT $importlang FROM language, category, pres_has_category WHERE ".
                    "language.id=category.language_id AND category.id=pres_has_category.c_id AND ".
                    "pres_has_category.p_id=".$rec['id'];
             $rs2 = $dbh->Execute($sql);
             if (!empty($rs2)) {
                while($rec2 = $rs2->FetchRow()) {
                    $method = $rec2[$importlang];
                    $existingmethod = get_record('classification_value', 'value', $method);
                    if (!empty($existingmethod)) { //this is a valid method.
                        //now check to see if classification already set for this LP
                        if (!record_exists('course_classification', 'course', $courseid, 'value', $existingmethod->id)) {
                            //need to insert a classification and a tag.
                            insert_record('course_classification', (object)array('course' => $courseid, 'value' => $existingmethod->id));
                            tag_set_add('courseclassification', $courseid, strtolower($method));
                        }
                    }
                }
             }

             //now do keystages and subject = ugly one needs to be seperated a bit.
             $sql = "SELECT $importlang FROM language, subject, pres_has_subject WHERE ".
                    "language.id=subject.language_id AND subject.id=pres_has_subject.s_id AND ".
                    "pres_has_subject.p_id=".$rec['id'];
             $rs2 = $dbh->Execute($sql);
             if (!empty($rs2)) {
                while($rec2 = $rs2->FetchRow()) {
                    $method = $rec2[$importlang];
                    //split out $method into keystage and subject
                    if (strpos($method, '(KS1/2)')) {
                        $method1 = "1 and 2";
                        $method2 = trim(str_replace('(KS1/2)', '', $method));
                    } elseif (strpos($method, '(KS3/4)')) {
                        $method1 = "3 and 4";
                        $method2 = trim(str_replace('(KS3/4)', '', $method));
                    }
                    if (!empty($method1)) {
                        $existingmethod = get_record('classification_value', 'value', $method1);
                        if (!empty($existingmethod)) { //this is a valid method.
                            //now check to see if classification already set for this LP
                            if (!record_exists('course_classification', 'course', $courseid, 'value', $existingmethod->id)) {
                                //need to insert a classification and a tag.
                                insert_record('course_classification', (object)array('course' => $courseid, 'value' => $existingmethod->id));
                                tag_set_add('courseclassification', $courseid, strtolower($method));
                            }
                        }
                    }
                    if (!empty($method2)) {
                        if ($method2 =='Sciences') {
                            $method2 = 'Science';
                        }
                        $existingmethod = get_record('classification_value', 'value', $method2);
                        if (empty($existingmethod)) { //create new classification if needed.
                            $newmeth = new stdclass();
                            $newmeth->type = get_field('classification_type', 'id', 'name', 'Subject');
                            $newmeth->value = $method2;
                            insert_record('classification_value', $newmeth);
                            $existingmethod = get_record('classification_value', 'value', $method2);
                        }
                        if (!empty($existingmethod)) { //this is a valid method.
                            //now check to see if classification already set for this LP
                            if (!record_exists('course_classification', 'course', $courseid, 'value', $existingmethod->id)) {
                                //need to insert a classification and a tag.
                                insert_record('course_classification', (object)array('course' => $courseid, 'value' => $existingmethod->id));
                                tag_set_add('courseclassification', $courseid, strtolower($method));
                            }
                        }
                    }
                 }
             }

             $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);

             //now do stuff with contributors
             if (!empty($lp['contributors'])) {
                $useridarray = array();
                $contribroleid = get_field('role', 'id', 'shortname', ROLE_LPCONTRIBUTOR);
                 foreach($lp['contributors'] as $conid) {
                     $user = get_record('user', 'username', $conid);
                     if (!empty($user)) {
                         role_assign($contribroleid, $user->id, 0, $coursecontext->id);
                         $useridarray[] = $user->id;
                     }
                 }
             }

             //now do stuff with Participants
             $ptroleid = get_field('role', 'id', 'shortname', ROLE_PT);
             
             $sql = "SELECT participant.id, participant.email FROM participant_has_presentation, participant WHERE ".
                    "participant_has_presentation.p_id=".$rec['id']. " AND ".
                    "participant.id=participant_has_presentation.u_id";
             $rs2 = $dbh->Execute($sql);
             if (!empty($rs2)) {
                while ($rec2 = $rs2->FetchRow()) {
                    $author = get_author($dbh, $rec2, $errors);
                    if (!empty($author)) {
                        role_assign($ptroleid, $author->id, 0, $coursecontext->id);
                    }
                }
             }

             //now change status to published if record is "visible"
             //change -1 states to "suspended"
             $course = get_record('course', 'id', $courseid);
             if ($rec['visible']==1) {
                 tao_update_course_status(COURSE_STATUS_PUBLISHED, 'Legacy Import', $course);
             } elseif ($rec['visible']==-1) {
                 tao_update_course_status(COURSE_STATUS_SUSPENDEDAUTHOR, 'Legacy Import', $course);
             }

         }
     }
 }
 if ($count) {
     notify("(".$count.") Learning Paths created successfully!",'notifysuccess');
 }
 if ($countupdate) {
     notify("(".$countupdate.") Learning Paths Updated successfully!",'notifysuccess');
 }
 if (!empty($errors)) {
     notify($errors);
 }
 print_footer();
function get_author($dbh, $rec, &$errors) {
    $author = get_record('user', 'email', $rec['email']);
    if (!empty($author)) {
        return $author;
    }
    //need to insert user record from TAO db for this user.
    $sql = "SELECT * from participant WHERE email='".$rec['email']."'";
    $rs = $dbh->Execute($sql);

    if (empty($rs)) {
         //$errors .= 'Learning Path created, but could not assign Author Learning path id: '. $rec['id']. ', '.$rec['title'] .' </br>';
        // debugging($sql,DEBUG_DEVELOPER);
    } else {
        $legacyauthor = $rs->FetchRow();
        //should use a global func for this!!!
         if (!record_exists('user', 'username', $legacyauthor['login'])) {
             $newuser = new stdclass();
             $newuser->username = addslashes($legacyauthor['login']);
             $newuser->email = $legacyauthor['email'];
             $newuser->firstname = addslashes($legacyauthor['firstname']);
             $newuser->lastname = addslashes($legacyauthor['name']);
             if (!empty($legacyauthor['phone'])) {
                 $newuser->phone1 = $legacyauthor['phone'];
             } else {
                 $newuser->phone1 = '';
             }
             if (!empty($legacyauthor['mobile'])) {
                 $newuser->phone2 = $legacyauthor['mobile'];
             } else {
                 $newuser->phone2 = '';
             }
             if (!empty($legacyauthor['show_name'])) {
                 $newuser->city = addslashes($legacyauthor['show_name']);
             }
             if (!empty($legacyauthor['name1'])) {
                 $newuser->institution = addslashes($legacyauthor['name1']);
             }
             if (!empty($legacyauthor['url'])) {

                 $newuser->url = addslashes($legacyauthor['url']);
             }
             $newuser->password = md5($legacyauthor['password']);
             $newuser->confirmed = 1;
             $newuser->policyagreed = 0;
             $newuser->deleted = 0;
             $newuser->mnethostid = 1;
             $newuser->emailstop = 0;
             $newuser->timezone = '99';
             $newuser->mailformat = 1;
             $newuser->maildigest = 0;
             $newuser->maildisplay = 2;
             $newuser->htmleditor = 1;
             $newuser->ajax = 1;
             $newuser->autosubscribe = 1;
             $newuser->timemodifed = time();
             if (!insert_record('user', $newuser)) {
                 $errors .= "insert failed for user with email:".$legacyauthor['email']."</br>";
             }
             $user = get_record('user', 'email', $legacyauthor['email']);
             // local_user_signup($user); //disable as we might not want to force a password change
             //do normal role ass as would be done in local_user_signup
             $ptrole = get_field('role', 'id', 'shortname', ROLE_PT);
             $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
             role_assign($ptrole, $user->id,0,$sitecontext->id);
         }
    }
    $author = get_record('user', 'email', $rec['email']);
    return $author;
}

function load_topics_from_legacy($dbh, $pres) { 

    $dbh = taoimport_dbconnect();

    $topics = array();

    $sql = "SELECT p.no, p.title, CAST(p.text AS TEXT) as text, t.name
             FROM pres_topic p
             JOIN pres_topic_type t on t.id = p.type
            WHERE p_id = $pres";

    $rs = $dbh->Execute($sql);
    if (!empty($rs)) {
        while ($res = $rs->FetchRow()) {
	        $topics[$res['name']] = $res['text'];
        }
    }
     
    return $topics;
}

//dbh == data connection
//pres == presentation id.
function load_pages_from_legacy($dbh, $pres) {

    $content = array();

    // define smarty template mappings
    $smarties = array('About the Learning Path' => 21,
                        'How will it be relevant to me?' => 26,
                        'How will it work in the classroom?' => 23,
                        'Resource Requirements' => 24,
                        'How will the learning path be evaluated and developed?' => 25);

    foreach($smarties as $pagetitle => $form) {

        $sql = "SELECT t.title AS question, t.text as answer
                  FROM pres_topic t
            INNER JOIN pres_topic_type y ON t.type = y.id
                 WHERE t.p_id = {$pres}
                   AND y.form = {$form}
              ORDER BY y.[no]";

        $evidences = $dbh->Execute($sql);
        if (!empty($evidences)) {
            while ($evidence = $evidences->FetchRow()) {
                if (!empty($evidence['question'])) {
                    if (!empty($evidence['answer'])) {
                        $content[$form]['questions'][$evidence['question']]['text'] = $evidence['answer'];
                    } else {
                        $content[$form]['questions'][$evidence['question']]['text'] = '';
                    }
                }
            }
        }
    }

    return $content;

}


?>