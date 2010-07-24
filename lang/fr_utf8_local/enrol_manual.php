<?php // $Id: enrol_manual.php,v 1.5 2009/04/10 19:28:51 martignoni Exp $ 

global $GLOBSTRING;

$string['description'] = 'Contrôle des inscriptions par défaut. Un '.$GLOBSTRING['taopt'].' peut essentiellement être inscrit dans un '.$GLOBSTRING['taolp'].' de deux façons :
<ul>
<li>un '.$GLOBSTRING['taomt'].' ou l\'administrateur peut l\'inscrire manuellement en utilisant le lien disponible dans le menu d\'administration du '.$GLOBSTRING['taolp'].' ;</li>
<li>on peut définir dans un '.$GLOBSTRING['taolp'].' une « clef d\'inscription » (une sorte de mot de passe). Quiconque possède cette clef a la possibilité de s\'inscrire soi-même à ce '.$GLOBSTRING['taolp'].'.</li>
</ul>';
$string['enrol_manual_requirekey'] = 'Exiger une clef d\'inscription pour les nouveaux '.$GLOBSTRING['taolps'].' et empêcher la suppression des clefs existantes.';
$string['enrol_manual_usepasswordpolicy'] = 'Utiliser les règles de mot de passe actuelles pour les clefs d\'inscription aux '.$GLOBSTRING['taolps'].'.';
$string['keyholderrole' ] = 'Le rôle de l\'utilisateur détenant la clef d\'inscription d\'un '.$GLOBSTRING['taolp'].'. Affiché pour les étudiants tentant de s\'inscrire au '.$GLOBSTRING['taolp'].'.';

?>
