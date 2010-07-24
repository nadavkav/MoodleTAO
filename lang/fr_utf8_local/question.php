<?php // $Id: question.php,v 1.47 2009/06/28 18:36:33 martignoni Exp $

global $GLOBSTRING;

$string['changepublishstatuscat'] = 'La <a href=\"$a->caturl\">catégorie « {$a->name} »</a> du '.$GLOBSTRING['taolp'].' « {$a->coursename} » verra son état modifié de <strong>$a->changefrom à $a->changeto</strong>.';
$string['filesareacourse'] = 'la zone des fichiers du '.$GLOBSTRING['taolp'];
$string['missingcourseorcmid'] = 'Vous devez fournir l\'identifiant de '.$GLOBSTRING['taolp'].' ou le numéro de '.$GLOBSTRING['taolp'].' pour imprimer la question.';
$string['missingcourseorcmidtolink'] = 'Vous devez fournir l\'identifiant de '.$GLOBSTRING['taolp'].' ou le numéro de '.$GLOBSTRING['taolp'].' à get_question_edit_link.';
$string['questionaffected'] = '<a href=\"$a->qurl\">La question « {$a->name} » ($a->qtype)</a> est dans cette catégorie, mais est aussi utilisée dans le <a href=\"$a->qurl\">test « {$a->quizname} »</a> dans le '.$GLOBSTRING['taolp'].' « {$a->coursename} ».';
$string['questionsmovedto'] = 'Les questions encore utilisées ont été déplacées vers « {$a} » dans la catégorie de '.$GLOBSTRING['taolp'].' mère.';

?>
