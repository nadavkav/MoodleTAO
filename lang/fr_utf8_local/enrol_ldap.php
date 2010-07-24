<?php // $Id$ 

global $GLOBSTRING;

$string['description'] = '<p>Vous pouvez utiliser un serveur LDAP pour contrôler les inscriptions aux '.$GLOBSTRING['taolps'].'. On suppose que votre arbre LDAP contient des groupes correspondant aux '.$GLOBSTRING['taolps'].', et que chacun de ces groupes/'.$GLOBSTRING['taolp'].' contiendra les inscriptions à faire correspondre avec les '.$GLOBSTRING['taopts'].'.</p>
<p>On suppose que dans LDAP, les '.$GLOBSTRING['taolps'].' sont définis comme des groupes, et que chaque groupe comporte plusieurs champs indiquant l\'appartenance (<em>member</em> ou <em>memberUid</em>), contenant un identificateur unique de l\'utilisateur.</p>
<p>Pour pouvoir utiliser les inscriptions par LDAP, les utilisateurs <strong>doivent</strong> avoir un champ idnumber valide. Les groupes LDAP doivent comporter cet idnumber dans le champ définissant l\'appartenance afin que l\'utilisateur soit inscrit à ce '.$GLOBSTRING['taolp'].'. Cela fonctionne bien si vous utilisez déjà l\'authentification par LDAP.</p>
<p>Les inscriptions sont mises à jour lors de la connexion de l\'utilisateur. Il est aussi possible de lancer un script pour synchroniser les inscriptions. Voyez pour cela le fichier <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Cette extension peut également servir à la création automatique de nouveaux '.$GLOBSTRING['taolps'].' lorsque de nouveaux groupes apparaissent dans LDAP.</p>';
$string['enrolname'] = 'LDAP';
$string['enrol_ldap_autocreate'] = 'Des '.$GLOBSTRING['taolps'].' peuvent être créés automatiquement si des inscriptions existent pour un '.$GLOBSTRING['taolp'].' qui n\'existe pas encore dans Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Réglages de la création automatique de '.$GLOBSTRING['taolp'].'';
$string['enrol_ldap_category'] = 'Catégorie des '.$GLOBSTRING['taolps'].' créés automatiquement.';
$string['enrol_ldap_course_fullname']  = 'Facultatif : champ LDAP d\'où tirer le nom complet du '.$GLOBSTRING['taolp'].'.';
$string['enrol_ldap_course_idnumber'] = 'Champ correspondant avec l\'identificateur unique LDAP, D\'habitude <em>cn</em> ou <em>uid</em>. On recommande de verrouiller cette valeur lors de l\'utilisation de la création automatique de '.$GLOBSTRING['taolps'].'.';
$string['enrol_ldap_course_settings'] = 'Réglages de l\'inscription aux '.$GLOBSTRING['taolps'];
$string['enrol_ldap_course_shortname'] = 'Facultatif : champ LDAP d\'où tirer le nom abrégé du '.$GLOBSTRING['taolp'].'.';
$string['enrol_ldap_course_summary'] = 'Facultatif : champ LDAP d\'où tirer le résumé du '.$GLOBSTRING['taolp'].'.';                                                                                                                                                
$string['enrol_ldap_objectclass'] = 'Classe objectClass utilisée pour la recherche de '.$GLOBSTRING['taolps'].'. D\'habitude « posixGroup ».';
$string['enrol_ldap_student_contexts'] = 'Liste des contextes où sont placés les groupes contenant les inscriptions des étudiants. Séparez les différents contextes par des « ; ». Par exemple : « ou=courses,o=org; ou=others,o=org »';
$string['enrol_ldap_student_memberattribute'] = 'Nom de l\'attribut d\'appartenance (inscription) d\'un étudiant à un groupe (cours). D\'habitude « member » ou « memberUid ».';
$string['enrol_ldap_student_settings'] = 'Réglages pour l\'inscription des étudiants';
$string['enrol_ldap_teacher_contexts'] = 'Liste des contextes où sont placés les groupes contenant les inscriptions des enseignants. Séparez les différents contextes par des « ; ». Par exemple : « ou=courses,o=org; ou=others,o=org »';
$string['enrol_ldap_teacher_memberattribute'] = 'Nom de l\'attribut d\'appartenance (inscription) d\'un enseignant à un groupe (cours). D\'habitude « member » ou « memberUid ».';
$string['enrol_ldap_teacher_settings'] = 'Réglages pour l\'inscription des enseignants';
$string['enrol_ldap_template'] = 'Facultatif : les '.$GLOBSTRING['taolps'].' créés automatiquement peuvent copier leurs réglages sur un '.$GLOBSTRING['taolp'].' modèle.';

?>
