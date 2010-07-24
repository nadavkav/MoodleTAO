<?php // $Id$ 

global $GLOBSTRING;

$string['autocreate'] = 'Les '.$GLOBSTRING['taolps'].' peuvent être créés automatiquement si des inscriptions ont lieu pour un '.$GLOBSTRING['taolp'].' qui n\'existe pas encore dans le Moodle.';
$string['category'] = 'Catégorie des '.$GLOBSTRING['taolps'].' créés automatiquement';
$string['course_fullname'] = 'Nom du champ dans lequel est stocké le nom du '.$GLOBSTRING['taolp'].'.';
$string['course_id'] = 'Nom du champ dans lequel est stocké l\'identifiant du '.$GLOBSTRING['taolp'].'. Les valeurs de ce champ sont utilisées pour effectuer la correspondance avec le champ « enrol_db_l_coursefield » de la table « course » de Moodle.';
$string['course_shortname'] = 'Nom du champ dans lequel est stocké le nom abrégé du '.$GLOBSTRING['taolp'].'.';
$string['course_table'] = 'Nom de la table où l\'on s\'attend à trouver la description du '.$GLOBSTRING['taolp'].' (nom, nom abrégé, identifiant, etc.)';
$string['description'] = 'Vous pouvez utiliser une base de données externe (de presque n\'importe quel type) pour contrôler les inscriptions. La base de données externe doit posséder un champ contenant l\'identifiant du '.$GLOBSTRING['taolp'].' et un champ contenant l\'identifiant de l\'utilisateur. Ces deux champs sont comparés aux champs que vous choisissez dans les tables locales des '.$GLOBSTRING['taolps'].' et des utilisateurs.';
$string['enrol_database_autocreation_settings'] = 'Création automatique des nouveaux '.$GLOBSTRING['taolps'];
$string['ignorehiddencourse'] = 'Si cette option est activée, les utilisateurs ne seront pas inscrits aux '.$GLOBSTRING['taolps'].' non disponibles pour les étudiants.';
$string['localcoursefield'] = 'Nom du champ de la table des '.$GLOBSTRING['taolps'].' du Moodle utilisé pour faire correspondre les '.$GLOBSTRING['taolps'].' avec la base de données distante (par exemple « idnumber »)';
$string['localrolefield'] = 'Nom du champ de la table des rôles utilisé pour faire correspondre les '.$GLOBSTRING['taolps'].' avec la base de données distante (par exemple « shortname »).';
$string['localuserfield'] = 'Nom du champ de la table des utilisateurs utilisé pour faire correspondre les '.$GLOBSTRING['taolps'].' avec la base de données distante (par exemple « idnumber »).';
$string['remotecoursefield'] = 'Nom du champ de la table distante utilisé pour faire la correspondance dans la table des '.$GLOBSTRING['taolps'].'.';
$string['student_coursefield'] = 'Nom du champ de la table d\'inscription des étudiants où trouver l\'identifiant du '.$GLOBSTRING['taolp'].' (course ID).';
$string['student_l_userfield'] = 'Nom du champ (de la table des utilisateurs du Moodle) utilisé pour faire correspondre les utilisateurs à un enregistrement de la BDD distante pour '.$GLOBSTRING['taopts'].' (par exemple « idnumber »).';
$string['student_r_userfield'] = 'Nom du champ (de la table d\'inscription des '.$GLOBSTRING['taopts'].' de la BDD externe) où trouver l\'identifiant de l\'utilisateur (user ID).';
$string['student_table'] = 'Nom de la table dans laquelle sont stockés les inscriptions des '.$GLOBSTRING['taopts'].'.';
$string['teacher_coursefield'] = 'Nom du champ de la table d\'inscription des enseignants où trouver l\'identifiant du '.$GLOBSTRING['taolp'].' (course ID).';
$string['teacher_l_userfield'] = 'Nom du champ (de la table des utilisateurs du Moodle) utilisé pour faire correspondre les utilisateurs à un enregistrement de la BDD distante pour les '.$GLOBSTRING['taopts']. (par exemple « idnumber »).';
$string['teacher_r_userfield'] = 'Nom du champ (de la table d\'inscription des enseignants de la BDD externe) où trouver l\'identifiant de l\'utilisateur (user ID).';
$string['teacher_table'] = 'Nom de la table dans laquelle sont stockés les inscriptions des '.$GLOBSTRING['taomt'].'.';
$string['template'] = 'Facultatif : les '.$GLOBSTRING['taolp'].' créés automatiquement peuvent hériter leurs réglages d\'un '.$GLOBSTRING['taolp'].' modèle';

?>