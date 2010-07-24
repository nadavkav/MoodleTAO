<?php // $Id: enrol_mnet.php,v 1.6 2009/04/10 19:28:51 martignoni Exp $

global $GLOBSTRING;

$string['allow_allcourses'] = 'Permettre l\'inscription dans tous les '.$GLOBSTRING['taolps'].' distants.';
$string['allcourses'] = '$a '.$GLOBSTRING['taolps'].' potentiels';
$string['allowedcourses'] = '$a '.$GLOBSTRING['taolps'].' autorisés';
$string['nocoursesdefined'] = 'Aucun '.$GLOBSTRING['taolp'].' trouvé. Veuillez définir de nouveaux '.$GLOBSTRING['taolps'].' <a href=\"$a\">ici</a>.';
$string['allowedcourseslinktext'] = 'Modifier les '.$GLOBSTRING['taolps'].' et catégories autorisés';
$string['mnet_enrol_description'] = 'En publiant ce service, vous autorisez les administrateurs de $a à inscrire leurs '.$GLOBSTRING['taopts'].' à des '.$GLOBSTRING['taolps'].' sur votre Moodle.<br />
<ul>
<li><em>Dépendance</em> : vous devez également <strong>publier</strong> le service SSO (fournisseur de service) pour $a.</li>
<li><em>Dépendance</em> : vous devez également <strong>vous abonner</strong> au service SSO (fournisseur d\'identité) de $a.</li>
</ul><br />
En vous abonnant à ce service, vous pourrez inscrire vos '.$GLOBSTRING['taopts'].' aux '.$GLOBSTRING['taolps'].' sur $a.<br />
<ul>
<li><em>Dépendance</em> : vous devez également <strong>vous abonner</strong> au service SSO (fournisseur de service) de $a.</li>
<li><em>Dépendance</em> : vous devez également <strong>publier</strong> le service SSO (fournisseur d\'identité) pour $a.</li>
</ul><br />';
$string['mnetlocalforexternal'] = $GLOBSTRING['taoLps'].' locaux pour utilisateurs externes';

?>
