<?php 
$string['description'] = '<p>You can use an LDAP server to control your enrolments.                            It is assumed your LDAP tree contains groups that map to 
                          the learning paths, and that each of thouse groups/courses will 
                          have membership entries to map to students.</p>
                          <p>It is assumed that learning paths are defined as groups in 
                          LDAP, with each group having multiple membership fields 
                          (<em>member</em> or <em>memberUid</em>) that contain a unique
                          identification of the user.</p>
                          <p>To use LDAP enrolment, your users <strong>must</strong> 
                          to have a valid  idnumber field. The LDAP groups must have 
                          that idnumber in the member fields for a user to be enrolled 
                          in the learning path.
                          This will usually work well if you are already using LDAP 
                          Authentication.</p>
                          <p>Enrolments will be updated when the user logs in. You
                           can also run a script to keep enrolments in synch. Look in 
                          <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
                          <p>This plugin can also be set to automatically create new 
                          learning paths when new groups appear in LDAP.</p>';

$string['enrol_ldap_autocreate'] = 'Learning paths can be created automatically if there are                                    enrolments to a learning path  that doesn\'t yet exist 
                                    in Moodle.';

$string['enrol_ldap_autocreation_settings'] = 'Automatic learning path creation settings';
$string['enrol_ldap_category'] = 'The category for auto-created learning paths.';
$string['enrol_ldap_course_idnumber'] = 'Map to the unique identifier in LDAP, usually                                         <em>cn</em> or <em>uid</em>. It is 
                                         recommended to lock the value if you are using 
                                         automatic learning path creation.';

$string['enrol_ldap_course_settings'] = 'Learning path enrolment settings';
$string['enrol_ldap_objectclass'] = 'objectClass used to search learning paths. Usually                                     \'posixGroup\'.';

$string['enrol_ldap_template'] = 'Optional: auto-created learning paths can copy                                   their settings from a template learning path.';


?>