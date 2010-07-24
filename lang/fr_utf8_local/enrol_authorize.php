<?php // $Id$ 

global $GLOBSTRING;

$string['adminneworder'] = 'Cher administrateur,
  	 
Vous avez reçu une nouvelle commande en attente :

    No de commande : $a->orderid
    No de transaction : $a->transid
    Utilisateur : $a->user
    '.$GLOBSTRING['taoLp'].' : $a->course
    Montant : $a->amount

    SAISIE PROGRAMMÉE ACTIVE ? $a->acstatus

Si la saisie programmée est activée, il est prévu que les infos de carte de
crédit seront saisies le $a->captureon et que le '.$GLOBSTRING['taopt'].' sera inscrit au
'.$GLOBSTRING['taolp'].'. Dans le cas contraire, ces données arriveront à échéance le
$a->expireon et ne pourront plus être saisies après cette date.

Vous pouvez aussi accepter ou refuser le paiement afin d\'inscrire
immédiatement le '.$GLOBSTRING['taopt'].' en cliquant sur le lien ci-dessous.
$a->url';
$string['adminteachermanagepay'] = 'Les enseignants peuvent gérer les paiements du '.$GLOBSTRING['taolp'].'.';
$string['captureyes'] = 'Les données de la carte de crédit vont être saisies et l\'étudiant sera inscrit au '.$GLOBSTRING['taolp'].'. Voulez-vous continuer ?';
$string['choosemethod'] = 'Tapez la clef d\'inscription à ce cours.<br />Si vous n\'avez pas cette clef, ce '.$GLOBSTRING['taolp'].' vous sera accessible contre paiement.';
$string['costdefaultdesc'] = 'Pour utiliser ce prix par défaut, <strong>tapez -1 dans le champ du coût</strong> des paramètres du '.$GLOBSTRING['taolp'].'.';
$string['description'] = 'Le module Authorize.net permet de mettre en place des '.$GLOBSTRING['taolp'].' payants via des fournisseurs de paiement. Si le prix d\'un '.$GLOBSTRING['taolp'].' est de zéro, les '.$GLOBSTRING['taopts'].' peuvent s\'y inscrire sans payer. Le prix des '.$GLOBSTRING['taolps'].' peut être fixé de 2 manières. (1) Un prix défini globalement, que vous fixez ici, est le prix par défaut pour tous les '.$GLOBSTRING['taolps'].' du site. (2) Le prix de chaque '.$GLOBSTRING['taolp'].' peut être fixé individuellement. S\'il est défini, le prix spécifique d\'un cours remplace le prix par défaut.<br /><br /><b>Remarque :</b> si vous indiquez une clef d\'inscription dans les réglages du '.$GLOBSTRING['taolp'].', les '.$GLOBSTRING['taopts'].' auront également la possibilité de s\'y inscrire avec cette clef. Ceci est utile si vous avez un mélange de '.$GLOBSTRING['taopts'].' payant et non payant.';
$string['paymentpending'] = 'Votre paiement pour ce '.$GLOBSTRING['taolp'].' est en attente de traitement. Son numéro de commande est $a->orderid. Voir les <a href=\'$a->url\'>détails de la commande</a>.';
$string['pendingecheckemail'] = 'Cher administrateur,

Il y a actuellement $a->count eChecks en attente. Vous devez déposer un fichier CSV afin d\'inscrire les '.$GLOBSTRING['taopts'].'.

Veuillez cliquer sur le lien ci-dessous et lire le fichier d\'aide sur la page affichée :
$a->url';
$string['pendingechecksubject'] = '$a->course : eChecks en attente ($a->count)';
$string['pendingordersemail'] = 'Cher administrateur,
  	 
$a->pending transactions pour le '.$GLOBSTRING['taolp'].' $a->course arriveront à échéance
dans les $a->days jours, à moins que vous n\'acceptiez le paiement.

Ceci est un message d\'avertissement, car vous n\'avez pas activé
la saisie programmée. Vous devez donc accepter ou refuser les paiements
manuellement.
  	 
Pour accepter ou refuser les paiements en attente de traitement, veuillez
visiter la page

$a->url
  	 
Pour activer la saisie programmée, afin que vous ne receviez plus de
tels messages d\avertissement, veuillez visiter la page

$a->enrolurl';
$string['pendingordersemailteacher'] = 'Cher '.$GLOBSTRING['taost'].',
  	 
$a->pending transactions d\'un montant total de $a->currency $a->sumcost,
pour le '.$GLOBSTRING['taolp'].' $a->course, arriveront à échéance dans les $a->days jours,
à moins que vous n\'acceptiez le paiement.

Vous devez donc accepter ou refuser les paiements manuellement, car
l\'administrateur n\'a pas activé leur saisie programmée. 
  	 
Pour accepter ou refuser les paiements en attente de traitement, veuillez
visiter la page

$a->url';
$string['reviewday'] = 'Saisir les données de la carte de crédit automatiquement, à moins qu\'un '.$GLOBSTRING['taost'].' ou un administrateur ne contrôle la commande dans les <b>$a</b> jours. LE CRON DOIT ÊTRE ACTIF.<br />(0 jour signifie que la saisie programmée sera désactivée. Un contrôle par un enseignant ou administrateur est alors nécessaire. Dans ce cas, la transaction sera annulée si elle n\'est pas contrôlée dans les 30 jours)';
$string['unenrolstudent'] = 'Désinscrire le '.$GLOBSTRING['taopt'].' ?';
$string['welcometocoursesemail'] = 'Bonjour,

Nous vous remercions de votre paiement. Vous vous êtes inscrits aux '.$GLOBSTRING['taolps'].' suivants

$a->courses

Nous vous invitons à modifier votre profil :
$a->profileurl

Vous pouvez consulter les détails de votre paiement à l\'adresse
$a->paymenturl';

?>