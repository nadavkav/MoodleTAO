<?php 
$string['autocreate'] = 'Learning paths can be created automatically if there are enrolments to a learning path that doesn\'t yet exist in Moodle.';
$string['category'] = 'The category for auto-created learning paths.';
$string['course_fullname'] = 'The name of the field where the learning path fullname is stored.';
$string['course_id'] = 'The name of the field where the learning path ID is stored. The values of this field are used to match those in the \"enrol_db_l_coursefield\" field in Moodle\'s learning path table.';
$string['course_shortname'] = 'The name of the field where the learning path shortname is stored.';
$string['course_table'] = 'Then name of the table where we expect to find the learning path details in (short name, fullname, ID, etc.)';
$string['description'] = 'You can use a external database (of nearly any kind) to control your enrolments. It is assumed your external database contains a field containing a learning path ID, and a field containing a user ID. These are compared against fields that you choose in the local learning path and user tables.';
$string['enrol_database_autocreation_settings'] = 'Auto-creation of new learning paths';
$string['ignorehiddencourse'] = 'If set to yes users will not be enroled on learning paths that are set to be unavailable to students.';
$string['localcoursefield'] = 'The name of the field in the learning path table that we are using to match entries in the remote database (eg idnumber).';
$string['remotecoursefield'] = 'The name of the field in the remote table that we are using to match entries in the learning path table.';
$string['student_coursefield'] = 'The name of the field in the student enrolment table that we expect to find the learning path ID in.';
$string['teacher_coursefield'] = 'The name of the field in the teacher enrolment table that we expect to find the learning path ID in.';
$string['template'] = 'Optional: auto-created learning paths can copy their settings from a template learning path. Type here the shortname of the template learning path.';

?>