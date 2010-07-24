<?php // $Id$ 

global $GLOBSTRING;

$string['description'] = 'Cette méthode permet une vérification systématique à partir d\'un fichier texte spécialement mis en forme disposé à un emplacement que vous choisissez. Le fichier est en format CSV (séparateurs virgules) avec 4 ou 6 champs par ligne, à savoir :
<pre>
*  opération, rôle, ID (utilisateur), ID (cours) [, début, fin]
où :
*  opération        = add | del
*  rôle             = student | teacher | teacheredit
*  ID (utilisateur) = champ idnumber de l\'utilisateur dans la table « user » (PAS le champ id)
*  ID ('.$GLOBSTRING['taolp'].')       = champ idnumber du '.$GLOBSTRING['taolp'].' dans la table « course » (PAS le champ id)
*  début            = date de début (en secondes depuis le 1.1.1970 à 0 h UTC) - facultatif
*  fin              = date de fin (en secondes depuis le 1.1.1970 à 0 h UTC) - facultatif
</pre>
Cela pourrait par exemple ressembler à ceci :
<pre>
    add, taopt, 5, CF101
    add, taomt, 6, CF101
    add, taost, 7, CF101
    del, taopt, 8, CF101
    del, taopt, 17, CF101
    add, taopt, 21, CF101, 1091115000, 1091215000
</pre>';

?>
