<?php // $Id$ 

global $GLOBSTRING;

$string['addingquestions'] = 'Vous gérez votre banque de questions dans cette partie de la page. Les questions sont réparties en catégories, afin de les organiser. Elles peuvent être utilisées dans n\'importe lequel de vos '.$GLOBSTRING['taolps'].', ou même dans d\'autres '.$GLOBSTRING['taolps'].' si vous les « publiez ».<br /><br />Créez d\'abord une catégorie. Vous pourrez ensuite créer ou modifier des questions. Vous pouvez choisir une de ces questions pour l\'ajouter à votre test dans l\'autre partie de la page.';
$string['affectedstudents'] = $GLOBSTRING['taoPts'].' concernés';
$string['attemptsonly'] = 'N\'afficher que les '.$GLOBSTRING['taopts'].' ayant déjà effectué le test';
$string['bothattempts'] = 'Afficher aussi les '.$GLOBSTRING['taopts'].' n\'ayant pas fait le test';
$string['downloadextra'] = '(le fichier est aussi déposé dans les fichiers du '.$GLOBSTRING['taolp'].', dans le dossier /backupdata/quiz)';
$string['emailconfirmbody'] = 'Bonjour,

Vous avez envoyé vos réponses au test « $a->quizname » du '.$GLOBSTRING['taolp'].'
« $a->coursename » à $a->submissiontime.

Ce message confirme que nous avons reçu correctement vos réponses.

Vous pouvez accéder à ce test en suivant le lien $a->quizurl.';
$string['emailnotifybody'] = 'Bonjour,

Le participant $a->studentname a effectué le test « $a->quizname » ($a->quizurl)
du '.$GLOBSTRING['taolp'].' « $a->coursename ».

Vous pouvez voir cette tentative en suivant le lien $a->quizreviewurl.';
$string['filloutnumericalanswer'] = 'Vous devez fournir au moins une réponse possible et sa tolérance. La première réponse correspondant sera utilisée pour déterminer le score et le feedback. Si vous fournissez un feedback sans réponse à la fin, celui-ci sera présenté aux '.$GLOBSTRING['taopts'].' dont la réponse ne correspond à aucune des autres solutions.';
$string['importfilearea'] = 'Importer à partir d\'un fichier du '.$GLOBSTRING['taolp'].'...';
$string['notavailabletostudents'] = 'Ce test n\'est actuellement pas disponible pour vos '.$GLOBSTRING['taopts'];
$string['popupnotice'] = 'Pour les '.$GLOBSTRING['taopts'].', l\'affichage de ce test se fera dans une fenêtre « sécurisée »';
$string['publishedit'] = 'Pour ajouter ou modifier une question de cette catégorie, vous devez avoir ces permissions dans le '.$GLOBSTRING['taolp'].' publiant cette catégorie.';
$string['questiondeleted'] = 'Cette question a été supprimée. Veuillez contacter votre '.$GLOBSTRING['taomt'];
$string['reportmulti_q_x_student'] = 'Choix des '.$GLOBSTRING['taolp'];
$string['reviewoptions'] = 'Les '.$GLOBSTRING['taopts'].' peuvent relire';
$string['savedfromdeletedcourse'] = 'Récupérées du '.$GLOBSTRING['taolp'].' supprimé « {$a} »';
$string['shownoattempts'] = 'Afficher les '.$GLOBSTRING['taopts'].' sans tentative';
$string['shownoattemptsonly'] = 'N\'afficher que les '.$GLOBSTRING['taopts'].' sans tentative';
$string['unusedcategorydeleted'] = 'Cette catégorie à été supprimée, car après la suppression du '.$GLOBSTRING['taolp'].', les questions qui y étaient classées ne sont plus utilisées nulle part.';
$string['youneedtoenrol'] = 'Vous devez vous inscrire à ce '.$GLOBSTRING['taolp'].' avant d\'effectuer ce test';

?>
