<?php  // $Id: report_security.php,v 1.13 2009/04/10 19:28:53 martignoni Exp $

global $GLOBSTRING;

$string['check_courserole_details'] = '<p>Chaque '.$GLOBSTRING['taolp'].' possède son propre rôle par défaut attribué aux nouveaux participants inscrits. Veuillez vous assurer qu\'aucune capacité comportant des risques n\'est autorisée pour ce rôle.</p><p>Le seul type de rôle historique supporté pour un tel rôle est le rôle <em>Étudiant</em>.</p>';
$string['check_courserole_error'] = 'Rôle par défaut des '.$GLOBSTRING['taolps'].' défini de façon incorrecte !';
$string['check_courserole_name'] = 'Rôles par défaut ('.$GLOBSTRING['taolp'].')';
$string['check_courserole_notyet'] = 'Seul le rôle par défaut des '.$GLOBSTRING['taolps'].' est utilisé.';
$string['check_courserole_ok'] = 'Définition correcte des rôles par défaut des '.$GLOBSTRING['taolps'].'.';

$string['check_defaultcourserole_details'] = '<p>Le rôle de participant par défaut pour l\'inscription aux '.$GLOBSTRING['taolp'].' indique le rôle attribué par défaut lors de l\'inscription. Veuillez vous assurer qu\'aucune capacité comportant des risques n\'est autorisée pour ce rôle.</p><p>Le seul type de rôle historique supporté pour un tel rôle est le rôle <em>Étudiant</em>.</p>';
$string['check_defaultcourserole_error'] = 'Rôle par défaut du '.$GLOBSTRING['taolp'].' « {$a} » incorrectement défini !';
$string['check_defaultcourserole_name'] = 'Rôle par défaut des '.$GLOBSTRING['taolps'].' (global)';

$string['check_defaultuserrole_details'] = '<p>Tous les utilisateurs connectés possèdent les capacités du rôle par défaut. Veuillez vous assurer qu\'aucune capacité comportant des risques n\'est autorisée pour ce rôle.</p><p>Le seul type de rôle historique supporté pour un tel rôle est le rôle <em>Utilisateur authentifié</em>. La capacité de voir les '.$GLOBSTRING['taolps'].' ne doit pas être autorisée.</p>';

$string['check_google_details'] = '<p>L\'activation du réglage « Ouvert à Google » autorise les moteurs de recherche à accéder aux '.$GLOBSTRING['taolps'].' en tant qu\'invités. Il n\'y a aucune raison d\'activer ce réglage si l\'accès aux invités n\'est pas autorisé.</p>';
$string['check_guestrole_details'] = '<p>Le rôle invité est utilisé pour l\'accès aux '.$GLOBSTRING['taolps'].' temporaire d\'utilisateurs non connectés. Veuillez vous assurer qu\'aucune capacité comportant des risques n\'est autorisée pour ce rôle.</p><p>Le seul type de rôle historique supporté pour un tel rôle est le rôle <em>Invité</em>.</p>';

?>