<?php // $Id: role.php,v 1.117 2009/07/30 19:00:51 martignoni Exp $ 

global $GLOBSTRING;

$string['category:create'] = 'Créer les catégories de '.$GLOBSTRING['taolps'];
$string['category:delete'] = 'Supprimer les catégories de '.$GLOBSTRING['taolps'];
$string['category:update'] = 'Modifier les catégories de '.$GLOBSTRING['taolps'];
$string['course:changefullname'] = 'Modifier le nom du '.$GLOBSTRING['taolp'];
$string['course:changeidnumber'] = 'Modifier le no d\'identification du '.$GLOBSTRING['taolp'];
$string['course:changeshortname'] = 'Modifier le nom abrégé du '.$GLOBSTRING['taolp'];
$string['course:create'] = 'Créer des '.$GLOBSTRING['taolps'];
$string['course:delete'] = 'Supprimer des '.$GLOBSTRING['taolps'];
$string['course:managemetacourse'] = 'Gérer les méta-'.$GLOBSTRING['taolps'];
$string['course:request'] = 'Demander de nouveaux '.$GLOBSTRING['taolps'];
$string['course:reset'] = 'Réinitialiser les '.$GLOBSTRING['taolps'];
$string['course:update'] = 'Modifier les réglages des '.$GLOBSTRING['taolps'];
$string['course:view'] = 'Voir les '.$GLOBSTRING['taolps'];
$string['course:viewcoursegrades'] = 'Voir les notes du '.$GLOBSTRING['taolp']; // Legacy, to delete after 1.9 release
$string['course:viewhiddencourses'] = 'Voir les '.$GLOBSTRING['taolps'].' cachés';
$string['course:visibility'] = 'Cacher/afficher les '.$GLOBSTRING['taolps'];
$string['deletecourseoverrides'] = 'Supprimer toutes les dérogations du '.$GLOBSTRING['taolp'];
$string['globalroleswarning'] = 'ATTENTION ! Les rôles que vous attribuez sur cette page s\'appliqueront aux utilisateurs concernés pour l\'intégralité du système, y compris pour la page d\'accueil et pour tous les '.$GLOBSTRING['taolps'].'.';
$string['legacy:coursecreator'] = 'RÔLE HISTORIQUE : Responsable de '.$GLOBSTRING['taolp'];
$string['metaassignerror'] = 'Impossible d\'attribuer ce rôle à l\'utilisateur « {$a} », car la capacité « Gérer les méta-'.$GLOBSTRING['taolp'].' » est requise.';
$string['metaunassignerror'] = 'Le rôle de l\'utilisateur « {$a} » a été automatiquement réattribué. Si nécessaire, veuillez retirer ce rôle dans les '.$GLOBSTRING['taolps'].' dépendants.';
$string['site:approvecourse'] = 'Approuver la création de '.$GLOBSTRING['taolps'];
$string['site:backup'] = 'Sauvegarder les '.$GLOBSTRING['taolps'];
$string['site:import'] = 'Importer d\'autres '.$GLOBSTRING['taolps'].' dans un '.$GLOBSTRING['taolp'];
$string['site:restore'] = 'Restaurer les '.$GLOBSTRING['taolps'];
$string['unassignexplain'] = 'Le formulaire ci-dessus ne peut être utilisé que pour retirer des rôles qui ont été attribués manuellement. Vous ne pouvez pas modifier des attributions de rôles effectuées via CIRCE ou via des méta-'.$GLOBSTRING['taolps'].'.';
$string['userhashiddenassignments'] = 'Cet utilisateur a dans ce '.$GLOBSTRING['taolp'].' une ou plusieurs attributions de rôles cachés';

?>