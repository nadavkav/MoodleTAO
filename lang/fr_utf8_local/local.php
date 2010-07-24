<?php 

// Core TAO Terminology

global $GLOBSTRING; 

$GLOBSTRING['taolp'] = 'thème';
$GLOBSTRING['taoLp'] = 'Thème';
$GLOBSTRING['taolps'] = 'thèmes';
$GLOBSTRING['taoLps'] = 'Thèmes';
$GLOBSTRING['taopt'] = 'participant';
$GLOBSTRING['taopts'] = 'participants';
$GLOBSTRING['taoPt'] = 'Participant';
$GLOBSTRING['taoPts'] = 'Participants';
$GLOBSTRING['taomt'] = 'mentor';
$GLOBSTRING['taomts'] = 'mentors';
$GLOBSTRING['taoMt'] = 'Mentor';
$GLOBSTRING['taoMts'] = 'Mentors';
$GLOBSTRING['taost'] = 'facilitateur';
$GLOBSTRING['taosts'] = 'facilitateurs';
$GLOBSTRING['taoSt'] = 'Facilitateur';
$GLOBSTRING['taoSts'] = 'Facilitateurs';
$GLOBSTRING['taoht'] = 'responsable';
$GLOBSTRING['taohts'] = 'responsables';
$GLOBSTRING['taoHt'] = 'Responsable';
$GLOBSTRING['taoHts'] = 'Responsables';
$GLOBSTRING['taolpeditor'] = 'responsable d\'édition';
$GLOBSTRING['taoLpeditor'] = 'Responsable d\'édition';
$GLOBSTRING['taohe'] = 'responsable de la publication';
$GLOBSTRING['taoHe'] = 'Responsable de la publication';
$GLOBSTRING['taote'] = 'auteur de gabarit';
$GLOBSTRING['taoTe'] = 'Auteur de gabarit';
$GLOBSTRING['alumnus'] = $GLOBSTRING['taopt'].' certifié';
$GLOBSTRING['Alumnus'] = $GLOBSTRING['taoPt'].' certifié';
$GLOBSTRING['alumni'] = $GLOBSTRING['taopts'].' certifiés';
$GLOBSTRING['Alumni'] = $GLOBSTRING['taoPts'].' certifiés';

// Placeholders

$GLOBSTRING['taomytools'] = 'Utilitaires TICE';
$GLOBSTRING['taomyteaching'] = 'Méthodes et pratiques TICE';
$GLOBSTRING['taomylearning'] = 'Mes thématiques';
$GLOBSTRING['taomycollaboration'] = 'Mes collaborations';
$GLOBSTRING['taomywork'] = 'Mes actions';

//

$string['configmysectioncategory'] = 'Cette catégorie contiendra les '.$GLOBSTRING['taolps'].' contenus dans les rubriques TAO (e.g., '.$GLOBSTRING['taomyteaching'].', '.$GLOBSTRING['taomycollaboration'].', and '.$GLOBSTRING['taomytools'].').';
$string['configmyteachingcourse'] = 'Le lien vers la rubrique \''.$GLOBSTRING['taomyteaching'].'\' du menu principal pointera vers ce '.$GLOBSTRING['taolp'].'.';
$string['choosemyteachingcourse'] = 'Choisir le '.$GLOBSTRING['taolp'].' associé au lien '.$GLOBSTRING['taomyteaching'];
$string['configmycollaborationcourse'] = 'Le lien vers la rubrique \''.$GLOBSTRING['taomycollaboration'].'\' du menu principal pointera vers ce '.$GLOBSTRING['taolp'].'.';
$string['choosemycollaborationcourse'] = 'Choisir le '.$GLOBSTRING['taolp'].' associé au lien '.$GLOBSTRING['taomycollaboration'];
$string['configmytoolscourse'] = 'Le lien vers la rubrique \''.$GLOBSTRING['taomytools'].'\' du menu principal pointera vers ce '.$GLOBSTRING['taolp'].'.';
$string['choosemytoolscourse'] = 'Choisir le '.$GLOBSTRING['taolp'].' associé au lien '.$GLOBSTRING['taomytools'];

// local capability strings //
$string['local:browselearningpaths'] = 'Utiliser la recherche de parcours par critère';
$string['local:canassignmt'] = 'Peut s\'attribuer un '.$GLOBSTRING['taomt'] .'(pour les '.$GLOBSTRING['taosts'].')';
$string['local:canassignpt'] = 'Peut s\'attribuer un '.$GLOBSTRING['taopt'] .'(pour les '.$GLOBSTRING['taomts'].')';
$string['local:cancreatelearningpaths'] = 'Peut créer des '.$GLOBSTRING['taolp'];
$string['local:cancreatetemplates'] = 'Peut créer des gabarits de '.$GLOBSTRING['taolps'];
$string['local:canmessageallalumni'] = 'Emettre un message vers tous les '.$GLOBSTRING['taopts'].' certifiés';
$string['local:canmessageallpts'] = 'Emettre un message vers tous les '.$GLOBSTRING['taopts'];
$string['local:canmessagealumnibylp'] = 'Emettre un message vers les '.$GLOBSTRING['taopts'].' d\'un '.$GLOBSTRING['taolp'];
$string['local:canmessageanyuser'] = 'Emettre un message vers n\'importe quel autre utilisateur';
$string['local:canmessagefellowadmins'] = 'Emetre un message aux autres administrateurs'; 
$string['local:canmessagefellowmts'] = 'Emettre un message vers les autres '.$GLOBSTRING['taomts'];
$string['local:canmessagefellowpts'] = 'Emettre un message vers les autres '.$GLOBSTRING['taopts'];
$string['local:canmessagefellowsts'] = 'Emettre un message vers les '.$GLOBSTRING['taosts'];
$string['local:canmessagehts'] = 'Emettre un message vers les '.$GLOBSTRING['taohts'];
$string['local:canmessagelumnibylp'] = 'Emettre un message vers les '.$GLOBSTRING['taopts'].' certifiés d\'un '.$GLOBSTRING['taolp'];
$string['local:canmessagemtsalumni'] = 'Emettre un message vers les '.$GLOBSTRING['taopts'].' certifiés de ses '.$GLOBSTRING['taomts'];
$string['local:canmessagemtspts'] = 'Emettre un message vers les '.$GLOBSTRING['taopts'].' de ses '.$GLOBSTRING['taomts'];
$string['local:canmessageownalumni'] = 'Emetre un message vers ses '.$GLOBSTRING['taopts'].' certifiés';
$string['local:canmessageownmts'] = 'Emettre un message vers ses '.$GLOBSTRING['taomts'].' (pour les '.$GLOBSTRING['taosts'].')';
$string['local:canmessageownpts'] = 'Emettre un message vers ses propres '.$GLOBSTRING['taopts'];
$string['local:cansearchforlptomessage'] = 'Peut rechercher un '.$GLOBSTRING['taolp'].' pour emettre un message aux participants';
$string['local:canselfassignheadeditor'] = 'Peut se désigner lui-même '.$GLOBSTRING['taoht'];
$string['local:classifylearningpath'] = 'Classifier les '.$GLOBSTRING['taolp'];
$string['local:hasdirectlprelationship'] = 'A une relation direct (et non indirecte) avec un '.$GLOBSTRING['taolp'];
$string['local:invitenewuser'] = 'Inviter un utilisateur';
$string['local:isassignablemt'] = 'Peut être désigé comme '.$GLOBSTRING['taomt'];
$string['local:isassignablept'] = 'Peut être désigné comme '.$GLOBSTRING['taopt'];
$string['local:islpauthor'] = 'Est auteur de '.$GLOBSTRING['taolp'];
$string['local:islpcreator'] = 'Est initiateur de '.$GLOBSTRING['taolp'].' (pas nécessairement rédacteur)';
$string['local:islpeditor'] = 'Est '.$GLOBSTRING['taolpeditor'].' de '.$GLOBSTRING['taolp'];
$string['local:ismt'] = 'Est '.$GLOBSTRING['taomt'];
$string['local:ispt'] = 'Est '.$GLOBSTRING['taopt'];
$string['local:isst'] = 'Est '.$GLOBSTRING['taost'];
$string['local:managepageactivities'] = 'Gérer les activités des pages';
$string['local:messagebyrole'] = 'Utiliser les rôles pour la messagerie';
$string['local:savelearningpathtemplate'] = 'Générer des archives de '.$GLOBSTRING['taolp'];
$string['local:updatecoursestatus'] = 'Modifier l\'état d\'un '.$GLOBSTRING['taolp'];
$string['local:viewcoursestatus'] = 'Voir le statut d\'un '.$GLOBSTRING['taolp'];
$string['local:viewresponsibleusers'] = 'Voir les personnes responsables d\'autres utilisateurs'; // clarify
$string['local:viewresponsibleusersbehalfof'] = 'Voir les personnes indirectement responsables d\'autres utilisateurs';
$string['local:viewunpublishedlearningpath'] = 'Voir le contenu des '.$GLOBSTRING['taolps'].' non publiés';


// roles and assignments between users
$string['assignrole'] = 'M\'attribuer la responsabilité de cet utilisateur comme $a';
$string['unassignrole'] = 'Me dégager de la responsabilité de cet utilisateur comme $a';
$string['notassignable'] = 'Désolé mais cet utilisateur ne peut vous être attribué comme $a';
$string['roleassigned'] = 'Cet utilisateur vous a été attribué comme $a';
$string['roleassignedshort'] = 'Rôle attribué';
$string['roleunassigned'] = 'Vous n\'êtes plus responsable de cet utilisateur comme $a';
$string['roleunassignedshort'] = 'Rôle supprimé';
$string['couldnotunassignrole'] = 'Une erreur sérieuse et non répertorié est apparue pendant l\'opération sur les rôlss';
$string['alreadyassigned'] = 'Cet utilisateur est déjà sous votre responsabilité comme $a';
$string['roleassignmentdidnotexist'] = 'La rezsponsabilité sur utilisateur comme $a ne peut pas être enevée : l\'assignation de rôle n\'existe pas';
$string['nosuchuser'] = 'Un utilisateur présentant de telles caractéristiques n\'existe pas';
$string['teacherid'] = 'ID';
$string['finduser'] = 'Chercher un utilisateur';
$string['nousers'] = 'Vous n\'êtes actuellement responsable de personne. Peut être voulez vous en chercher ?';
$string['responsiblefor'] = 'Les utilisateurs dont vous êtes responsable';
$string['responsibleforbehalfof'] = 'L\'utilisateur $a est responsable de';
$string['roletype'] = 'Type de relation';
$string['grandchildren'] = 'Utilisateurs hérités';
$string['searchtags'] = 'Chercher des tags';
$string['assignedheadeditorshort'] = 'Assigné comme '.$GLOBSTRING['taohe'];
$string['assignedheadeditor'] = 'Vous avez été désigné '.$GLOBSTRING['taohe'].' sur ce '.$GLOBSTRING['taolp'];
$string['alreadyassignedheadeditor'] = 'Vous êtes déjà '.$GLOBSTRING['taohe'].' de ce '.$GLOBSTRING['taolp'];

$string['assignedtemplateeditorshort'] = 'Assigné comme '.$GLOBSTRING['taote'];
$string['assignedtemplateeditor'] = 'Vous avez été désigné '.$GLOBSTRING['taote'].' sur ce '.$GLOBSTRING['taolp'];
$string['alreadyassignedtemplateeditor'] = 'Vous êtes déjà '.$GLOBSTRING['taote'].' de ce '.$GLOBSTRING['taolp'];

/* messaging related strings TODO remove these once we make sure they're not used anymore
$string['messagebyrole'] = 'Message by role';
$string['messageroles'] = 'Select roles to send messages to';
$string['messagerolesatleastone'] = 'You must select at least one role to message';
$string['sendtoroles'] = 'Send to role(s)';
$string['messagetargets'] = 'Enabled target roles';
$string['messagerolesenabled'] = 'Select roles to enable messaging to';
$string['messagenoroles'] = 'No roles available to select for messaging';
*/

// new ones
$string['messagebody'] = 'Corp de message';
$string['nomessagetargets'] = 'Désolé, vous n\'avez pas les droits suffisants pour envoyer des messages à des listes';
$string['messagenorecipients'] = 'Désolé, aucun utilisateur ne correspond à la liste choisie';
$string['sitelists'] = 'Listes de site';
$string['lplists'] = 'Liste du '.$GLOBSTRING['taolp'];
$string['messagesearchforlp'] = 'Rechercher un '.$GLOBSTRING['taolp'];
$string['messagebyrole'] = 'Par rôle';
$string['sendmessage'] = 'Envoyer!';
$string['sendingmessageto'] = 'Envoyer un message à $a->target ($a->count utilisateurs)';
$string['sendingmessagetocourse'] = 'Envoi du message $a->target du '.$GLOBSTRING['taolp'].' : $a->course ($a->count utilisateurs)';
$string['messagelistfooter'] = 'Ce message vous est envoyé au titre de membre de $a->target. Vous ne pouvez répondre directement qu\'à son émetteur, et non à la liste entière.';
$string['messagelistfootercourse'] = 'Ce message vous est envoyé au titre de membre de $a->target du '.$GLOBSTRING['taolp'].' : $a->course. Vous ne pouvez répondre directement qu\'à son émetteur, et non à la liste entière.';
$string['messagequeued'] = 'Votre message a été mis en file d\'attente. Il sera envoyé bientôt !';
// targets
$string['messagetargetallalumni'] = 'Tous les '.$GLOBSTRING['alumni'];
$string['messagetargetallalumnionlp'] = $GLOBSTRING['Alumni'].' par certification de '.$GLOBSTRING['taolp'];
$string['messagetargetallownpts'] = 'Mes '.$GLOBSTRING['taopts'];
$string['messagetargetalluncertifiedpts'] = 'Tous les '.$GLOBSTRING['taopts'].' sauf les '.$GLOBSTRING['alumni'];
$string['messagetargetanyotheruser'] = 'Tous les utilisateurs';
$string['messagetargethts'] = 'Les '.$GLOBSTRING['taoht'];
$string['messagetargetotheradmins'] = 'Les autres administrateurs';
$string['messagetargetothermts'] = 'Les autres '.$GLOBSTRING['taomts'];
$string['messagetargetotherptsonlp'] = 'Les autres '.$GLOBSTRING['taopts'];
$string['messagetargetothersts'] = 'Les autres '.$GLOBSTRING['taosts'];
$string['messagetargetownalumni'] = 'Mes '.$GLOBSTRING['alumni'];
$string['messagetargetmtsalumni'] = 'Les '.$GLOBSTRING['alumni'].' de mes '.$GLOBSTRING['taomts'];
$string['messagetargetownmts'] = 'Mes '.$GLOBSTRING['taomts'];
$string['messagetargetownptsonlp'] = 'Mes '.$GLOBSTRING['taopts'].' par '.$GLOBSTRING['taolp'];
$string['messagetargetownuncertifiedpts'] = 'Mes '.$GLOBSTRING['taopts'].' sauf les '.$GLOBSTRING['alumni'];
$string['messagetargetptsofmts'] = 'Les '.$GLOBSTRING['taopts'].' de mes '.$GLOBSTRING['taomts'];
$string['messagetargetptsonlp'] = 'Les '.$GLOBSTRING['taopts'].' par '.$GLOBSTRING['taolp'];
$string['messagetargetuncertifiedptsofmts'] = 'Les '.$GLOBSTRING['taopts'].' de mes '.$GLOBSTRING['taomts'].' sauf les '.$GLOBSTRING['alumni'];
$string['messagetargetheadeditors'] = 'Le comité éditorial';

// settings strings
$string['taosettings'] = 'Réglages TAO';
$string['chooseauthoringmode'] = 'Choisir le mode auteur';
$string['choosedefaultcategory'] = 'Choisir la catégorie par défaut';
$string['choosepublishedcategory'] = 'Choisir la catégorie pour les '.$GLOBSTRING['taolps'].' publiés';
$string['choosesuspendedcategory'] = 'Choisir la catégorie pour les '.$GLOBSTRING['taolps'].' suspendus';
$string['choosetemplatecategory'] = 'Choisir la catégorie pour les gabarits de '.$GLOBSTRING['taolps'];
$string['configdefaultcategory'] = 'Les '.$GLOBSTRING['taolps'].' nouvellement créés seront placés dans cette catégorie.';
$string['configlpautomatedcategorisation'] = 'Si activé, le système déplace automatiquement le '.$GLOBSTRING['taolp'].' à la catégorie adéquate lorsque l\'état du '.$GLOBSTRING['taolp'].' est mis à jour.';
$string['configpublishedcategory'] = 'Les '.$GLOBSTRING['taolps'].' publiés seront placés dans cette catégorie.';
$string['configsuspendedcategory'] = 'Les '.$GLOBSTRING['taolps'].' suspendus ou obsolètes seront placés dans cette catégorie.';
$string['configtemplatecategory'] = 'Cette catégorie sera utilisée pour choisir les gabarits de '.$GLOBSTRING['taolps'].'.';
$string['lpautomatedcategorisation'] = 'Catégorisation automatique des '.$GLOBSTRING['taolp'].' ?';

// learning path status related strings
$string['addnewlearningpath'] = 'Ajouter un nouveau '.$GLOBSTRING['taolp'];
$string['categoryupdateerror'] = 'La cétgorie n\'a pas pu être mise à jour';
$string['choosetemplate'] = 'Choisir un gabarit';
$string['courseisunpublished'] = 'Ce '.$GLOBSTRING['taolp'].' n\'est pas publié';
$string['historyheading'] = 'Historique des modifications de statut';
$string['lpsubmitted'] = 'Un '.$GLOBSTRING['taolp'].' a été soumis pour approbation et nécessite une relecture';
$string['missingstatusreason'] = 'Donnez une motivation au changement de statut';
$string['nostatusset'] = 'Pas de statut';
$string['notpermittedtoviewcourse'] = 'Vous n\'avez pas l\'autorisation de voir le contenu de ce '.$GLOBSTRING['taolp'];
$string['reason'] = 'Raison';
$string['statuschangeheading'] = 'Modifier le statut du '.$GLOBSTRING['taolp'];
$string['statuscustomhookerror'] = 'Could not execute custom hook';
$string['statushistoryupdateerror'] = 'Impossible d\'ajouter le changement le statut de ce '.$GLOBSTRING['taolp'].' à l\'historique';
$string['statusunchanged'] = 'Le statut du '.$GLOBSTRING['taolp'].' n\'a pas été modifié';
$string['statusupdated'] = 'Le statut du '.$GLOBSTRING['taolp'].' a été mis à jour';
$string['statusupdateerror'] = 'Impossible de modifier le statut du '.$GLOBSTRING['taolp'];
$string['submittedby'] = 'soumis par';

// learning path page strings
$string['classification'] = 'Classification';
$string['createtemplate'] = 'Créer un nouveau gabarit';
$string['defaultlearningpathfullname'] = 'Nouveau '.$GLOBSTRING['taolp'];
$string['defaultlearningpathshortname'] = 'LP101';
$string['lpsummarypagetitle'] = 'Page sommaire';
$string['makebackup'] = 'Générer une sauvegarde';

// Completion checklist popup
$string['cannotcompletelearningpath'] = 'Ces fonctions ne peuvent être choisies que pour des '.$GLOBSTRING['taolps'].' contenus dans vos '.$GLOBSTRING['taolps'].' personnels. Commencez par choisir un '.$GLOBSTRING['taolp'].' dans vos propres '.$GLOBSTRING['taolps'].'.';
$string['lpcompletionchecklist'] = 'Checklist d\'avancement';

// learning path template name - used by silent restore and initial courses
$string['learningpathtemplate'] = 'Gabarit de '.$GLOBSTRING['taolp'];
$string['learningpathtemplateshortname'] = 'LPTEMPLATE';

// learning path classification strings
$string['editlpclass'] = 'Edit classification type';
$string['lpclassification'] = 'Classification';
$string['lpclassificationheading'] = 'Learning Path Classification Administration';
$string['currentvalue'] = 'Current value';
$string['addclass'] = 'Add new value';
$string['classifylp'] = 'Classify your Learning Path';

//user Classification strings
$string['editmyclassifications'] = 'Modifier mes centres d\'intérêt';
$string['taotopicsinterest'] = 'Centres d\'intéret';

// my learning path page (replacement of http://aoc.ssatrust.org.uk/index?s=13)
$string['mylearningpaths'] = 'Mes '.$GLOBSTRING['taolps'];
$string['myownlearningpaths'] = 'Mes '.$GLOBSTRING['taolps'].' persos';
$string['mylearningpathsdescription'] = 'Cette page vous permet un accès rapide aux '.$GLOBSTRING['taolps'].' surlesquels vous travaillez ou auxquels vous voulez simplement accéder plus vite. Vous pouvez ajouter des '.$GLOBSTRING['taolps'].' à cette liste en rejoignant un gorupe constitué dans un '.$GLOBSTRING['taolp'].' ou en cliquant sur le lien \"Ajouter à mes '.$GLOBSTRING['taolps'].'\" dans les pages du '.$GLOBSTRING['taolp'].' lui-même.';
$string['learningpaths'] = $GLOBSTRING['taolps'];
$string['learningpath'] = $GLOBSTRING['taolp'];
$string['backtolist'] = 'Retour à la liste des '.$GLOBSTRING['taolps'];
$string['novisiblecourses'] = 'Aucun '.$GLOBSTRING['taolps'].' ne correspond à ce critère';
$string['nolearningpaths'] = 'Aucun';
$string['browselearningpaths'] = 'Naviguer dans les '.$GLOBSTRING['taolps'];
$string['browselearningpathsdescription'] = '<p>Un ensemble d\'espaces de réflexion et de connaissance sur les pratiques pédagogiques à base de TICE. Les exemples fournis sont essentiellement destinés à illustrer les méthodes de construction de ces pratiques et non comme leçons ou situations pédagogiques \"clef-en-main\". Vous et votre équipe pourrez examiner dans quelle mesure ces pratiques peuvent vous concerner ou rencontrer vos préoccupations ou besoins.
<p>Vous vous ferez votre propre opinion et développerez votre propre compétence pour exploiter ces méthodologies dans votre enseignement.';
$string['recommendedlearningpaths'] = $GLOBSTRING['taoLps'].' recommandés';
$string['recommendedlearningpathsdescription'] = 'Ces '.$GLOBSTRING['taolps'].' vous sont recommandés d\'après vos centres d\'intérêt ou les réglages de votre profil.';
$string['updateyourinterests'] = 'Mettre à jour mes centres d\'intérêt';
$string['nomatchinglearningpaths'] = 'Aucun '.$GLOBSTRING['taolp'].' dans vos centres d\'intérêt.';
$string['mycertification'] = 'Ma certification';
$string['reviewcertification'] = 'Visualiser ma progression';
$string['mycertificationdescription'] = '<p>Les participants peuvent recevoir un certificat enregistré sous form numérique dans vos données personnelles. La participation achevée à un '.$GLOBSTRING['taolp'].' vous donne accès à la certification.</p>
<p>Pour choisir le '.$GLOBSTRING['taolp'].' sur lequel vous voulez vous investir pour obtenir la certification, rejoignez un groupe de travail dans ce '.$GLOBSTRING['taolp'].'.</p>';
$string['mylearningpathcontributions'] = 'Les '.$GLOBSTRING['taolp'].' auxquels je participe';
$string['mylearningpathbookmarks'] = 'Les '.$GLOBSTRING['taolp'].' que je travaille';

// my work page
$string['myroles'] = 'Mes role(s)';
$string['myrolestext'] = 'Si vous pensez que vos attributions de rôle ne sont pas correctes, contactez un administrateur du site';
$string['nowork'] = 'Aucun travail en cours';
$string['noworktext'] = 'Vous n\'avez aucun travail associé à vos rôles actuels';
$string['authoredlearningpaths'] = 'Les '.$GLOBSTRING.' dont je suis auteur';
$string['messaging'] = 'Messagerie';
$string['messagebyrolelink'] = 'Envoyer des messages aux participants selon leur rôle';
$string['myediting'] = 'Mes ';
$string['learningpathsneededit'] = $GLOBSTRING['taoLps'].' nécessitant un superviseur d\'édition';
$string['learningpathsneedpublish'] = $GLOBSTRING['taoLps'].' non publiés';
$string['nolearningpaths'] = 'Aucun '.$GLOBSTRING['taolps'];
$string['editlearningpath'] = 'S\'auto-désigner superviseur d\'édition';
$string['myparticipants'] = 'Mes '.$GLOBSTRING['taopts'];
$string['lptemplates'] = 'Gabarits de '.$GLOBSTRING['taolps'];
$string['assignedtotemplate'] = 'Assigné au gabarit';
$string['createnewtemplate'] = 'Créer un gabarit';

// my collaboration page
$string['mygroups'] = 'Mes groupes';
$string['notinagroup'] = 'Vous ne faites partie d\'aucun groupe dans aucun '.$GLOBSTRING['taolp'].'.';
$string['messageall'] = 'message';
$string['invite'] = 'invité';
$string['members'] = 'Membres';
$string['noguest'] = 'La \'$a->page\' n\'est pas accessible aux invités.';

// certification path page (replacement of http://aoc.ssatrust.org.uk/index?s=8)
$string['certification'] = 'Certification';
$string['learningpathstatus'] = 'Statut d\'édition des '.$GLOBSTRING['taolps'];
$string['certifyparticipants'] = 'Certification des participants';
$string['noparticipantstocertify'] ='Aucun de vos '.$GLOBSTRING['taopts'].' n\'a demandé à être certifié.';

// learning path errors
$string['cannotselfassignedit'] = 'Vous n\'avez pas la permission de vous attribuer l\'édition';
$string['cannotselfassigntemplate'] = 'Vous n\'avez pas la permission de vous attribuer l\'édition des gabarits';

// map url to header
$string['imagemap'] = 'Images de l\'en-tête';
$string['imagemapdesc'] = 'Utilisez cette page pour personnaliser les images des en-têtes qui apparaissent dans les diverses sections du site, sur la base de la reconnaissance de schémas d\'URLs';
$string['setdefault'] = 'Choisir comme image par défaut';
$string['gobacktolist'] = 'Revenir à la liste';
$string['alreadydefault'] = 'Ceci est déjà l\'image par défaut, vous n\'avez donc pas à définir de règles explicite pour celle-ci.  Elle apparaîtra sur toutes les pages qui n\'ont pas une règle particulière.';
$string['configuringmappingsfor'] = 'Configuration d\'une règle pour $a';
$string['mappinghelp'] = 'Entrer les URLs et l\'ordre d\'évaluation des pages que vous voulez afficher avec cette image. Vous pouvez ajouter une description à chacune des entrées. Elle n\'est utilisée que comme aide mémoire, mais n\'a aucun autre usage dans el site. Lorsqu\'une page du site est demandée, la première régle accomplie (d\'ordre le plus faible) l\'emportera. Les URL doivent être écrites relativement à la racine loguqye de la plate-forme, ex: /course/index.php, et ne doivent pas inclure l\'URL de base. Des jokers (*) peuvent être utilisés de chaque côté de votre fragment.';
$string['nomappings'] = 'Aucune règle actuellement';
$string['addnewurlmap'] = 'Ajouter une nouvelle règle sur les URLs';
$string['currentmap'] = 'Modifier ou supprimer une règle existante';
$string['url'] = 'URL';

// nav strings
$string['myprofile'] = 'Mon compte';
$string['mylearning'] = 'Mes thèmes';
$string['mywork'] = 'Mes actions';
$string['myteaching'] = 'Méthodes et pratiques';
$string['mytools'] = 'Utilitaires et outils';
$string['mycollaboration'] = 'Mes collaborations';
$string['learningpathtasks'] = 'Tâches des '.$GLOBSTRING['taolps'];

//certificate module locking.
$string['namelockedwarning'] = 'Vous avez été certifié. Vos noms et prénoms ne peuvent être changés. Contactez un administrateur pour toute correction.'; 
$string['namelockedwarningadmin'] = 'Attention : cet utilisateur est certifié. Seuls les Administrateurs peuvent changer le nom mentionné';
$string['requiredcertification'] = 'Certification demandée';
$string['requiredcertificationdesc'] = 'Vous devez obtenir la Certification avant de pouvoir voir ce certificat.';
$string['setcertification'] = 'Certifier';

//Progress Indicator
$string['progressindicator'] = 'Indicateur de progression';
$string['trackprogress'] = 'Voir la progression';

// post install strings
$string['siteforumname'] = 'Forum du site';
$string['siteforumintro'] = 'Bienvenue au Forum global du site';

// default Learning Path Forum strings
$string['defaultforumname'] = 'Forum du '.$GLOBSTRING['taolp'];
$string['defaultforumintro'] = 'Bienvenue au Forum du '.$GLOBSTRING['taolp'];

// default Learning Path Wiki strings
$string['defaultwikiname'] = 'Wiki du '.$GLOBSTRING['taolp'];
$string['defaultwikisummary'] = 'Bienvenue au Wiki du '.$GLOBSTRING['taolp'];

// site wide FAQ
$string['defaultglossaryname'] = 'FAQ';
$string['defaultglossarydescription'] = 'Bienvenue dans le FAQ générale';

// friend strings
$string['requestfriend'] = 'Demande de mise en relation';
$string['arefriend'] = 'Vous êtes en relation avec $a->user';
$string['friendapprovalneeded'] = 'Vous avez demandé d\'entrer en relation avec $a->user, mais il n\'a pas encore accepté votre demande.';
$string['arealreadyfriend'] = 'Vous êtes déjà en relation avec cet utilisateur';
$string['notfriends'] = 'Vous n\'avez pas de relations avec cet utilisateur';
$string['removefriend'] = 'Enlever de mes relations';
$string['removefriendrequest'] = 'Demande de retrait d\'une relation';
$string['friended'] = 'Vous avez ajouté cet utilisateur à vos relations';
$string['userfriendrequest'] = 'Vous avez demandé d\'ajouter cet utilisateur dans vos relations';
$string['unfriended'] = 'Vous avez enlevé cet utilisateur de vos relations';
$string['couldnotaddasfriend'] = 'La relation n\'a pas pu être ajoutée';
$string['couldnotremoveasfriend'] = 'La mise en relation n\'a pas pu être annulée';
$string['friendpendingauthorisation'] = 'Autorisation en attente';
$string['myfriends'] = 'Mes relations';
$string['mypendingfriends'] = 'Les propositions de mise en relation en attente';
$string['relatedcourses'] = $GLOBSTRING['taolps'].' proches';
$string['friendrequests'] = 'Mes demandes de mise en relation en attente ';
$string['acceptfriendemailsubject'] = 'Mise en relation acceptée';
$string['acceptfriendemailbody'] = 'Bonjour $a->firstname,

$a->user a accepté votre demande de mise en relation
pour plus de détails, voir :
$a->url';
$string['declinefriendemailsubject'] = 'Mise en relation refusée';
$string['declinefriendemailbody'] = 'Bonjour $a->firstname,

$a->user a décliné votre demande de mise en relation
pour plus de détails, voir :
$a->url';
$string['requestfriendemailsubject'] = 'Demande de mise en relation';
$string['requestfriendemailbody'] = 'Bonjour $a->firstname,

$a->user vous a ajouté comme relation.
Pour approuver ou décliner cette invitation, voir :
$a->url';

// rafl strings
$string['standardview'] = 'Revenir à la vue standard';
$string['raflview'] = 'Revenir à la vue RAFL';
$string['lpcontributors'] = 'Contributeurs';
$string['lpleader'] = 'Leader';
$string['managelpcontributors'] = 'Gérer les contributeurs';
$string['nocontributors'] = 'Pas de contributeurs';
$string['raflmodeenabled'] = 'Activer le mode auteur RAFL';
$string['raflmodeenabledhelp'] = 'En cochant cette case, tous les participants pourront écrire des '.$GLOBSTRING['taolps'].' à l\'aide du module tiers-partie RAFL de SmartAssess';

//initial frontpage content:
$string['initialfrontpagecontent'] = '<h1>Intel Teach - Advanced Online &amp; Collaborations</h1>
<p style=\"margin-right: 0px\" dir=\"ltr\">Chez Intel, nous souhaitons partager notre passion pour la technologie, lorsqu\'elle constitue un outil de dialogue et d\'ouverture entre les personnes. Explorez nos communautés en ligne et le foisonnement de nouvelles idées et approches qu\'elles brassent, et participez à notre vision pour améliorer les différents points de friction entre notre monde actuel et les technologie.</p>
<div class=\"half\">
  <h2 class=\"half\"><a href=\"http://r1.tao.fr:8080/local/mahara/taoview.php?view=teaching\">Ma pédagogie</a> <b>›</b><br /></h2><!-- /section-title-linked --><img src=\"http://r1.tao.fr:8080/theme/intel/pix/path/teaching.jpg\" complete=\"true\" complete=\"true\" /> Un référentiel de ressources et d\'outil pour m\'aider dans mon enseignement. Téléchargez et partagez vos propres trouvailles avec la communauté. </div>
<div class=\"endfloat\"></div>
<div class=\"endfloat\"></div>
<div class=\"half half-last\">
  <h2><a href=\"http://r1.tao.fr:8080/local/my/learning.php\">Mes méthodes</a> <b>›</b><br /></h2><img src=\"http://r1.tao.fr:8080/theme/intel/pix/path/learning.jpg\" complete=\"true\" complete=\"true\" /> Exemple de parcours pédagogiques publiés montrant l\'usage de certaines méthodes et pratiques pédagogiques.</div>
<div class=\"endfloat\"></div><!-- /50-50-two-col-container -->
<div class=\"clearer\"></div><!-- 50-50-two-col-container -->
<div class=\"half\"><!-- section-title --><!--Optional \"btop\" snippet can be added here--><!-- section-title-linked -->
  <h2><a href=\"http://r1.tao.fr:8080/local/mahara/taoview.php?view=tools\">Mes outils</a> <b>›</b><br /></h2><!-- /section-title-linked --><img src=\"http://r1.tao.fr:8080/theme/intel/pix/path/tools.jpg\" complete=\"true\" complete=\"true\" /> Outils interactifs utiles pour l\'enseignenemt.</div>
<div class=\"half half-last\">
  <h2><a href=\"http://r1.tao.fr:8080/local/my/collaboration.php\">Mes collaborations</a> <b>›</b><br /></h2><img src=\"http://r1.tao.fr:8080/theme/intel/pix/path/collaboration.jpg\" complete=\"true\" complete=\"true\" /> Planifiez, partagez et échangez des idées à propos des parcours pédagogiques avec d\'autres membres de la communauté à l\'aide de ces services en ligne.</div>
<div class=\"endfloat\"></div><!-- /50-50-two-col-container -->
<div class=\"clearer\"></div><br />
<h2>Sessions introductives au programme</h2>
<p>Intel Teach - Advanced Online est un programme international de développement des pratiques professionnelles conçu pour collecter les résultats et productions générées par des activités de formation et de renforcement professionnel des enseignants. Ce vaste projet s\'appuie sur une méthodologie en cinq étapes, proposant pour chaque thème traité un canevas permettant de soumettre, débattre, commenter et évaluer des nouvelles idées, des nouvelles méthodologies ou pratiques, intégrant les innovations de la société de la connaissance et de l\'information.</p>
<p><b>Accès libre</b></p>
<p>Cette plate-forme propose un accès libre à l\'activité de la communauté, les ressources et débats qu\'elle produit et anime. Accès et participation sont libres, après participation à une session introductive d\'information et de formation à l\'utilisation.</p><br /><!-- 50-50-two-col-container --><!-- section-title --><!--Optional \"btop\" snippet can be added here--><!-- section-title-linked -->';

//Header langs
$string['myportfolio'] = 'Mon Portfolio';
$string['myhomepage'] = 'Ma page perso';

$string['viewmaharaprofile'] = 'View Portfolio';

$string['accept'] = 'Accepter';
$string['decline'] = 'Decliner';
$string['message']= 'Message';
$string['missingidnumber'] ='Identifiant manquant';
$string['inviteauser'] = 'Inviter un utilisateur';
$string['emailconfirmsent'] = 'Un courriel d\'invitation a été normalement envoyé à l\'adresse <b>$a</b>';
$string['certified_pt'] = $GLOBSTRING['taoPt'].' certifié';
$string['certified_mt'] = $GLOBSTRING['taoMt'].' certifié';
$string['certified_st'] = $GLOBSTRING['taoSt'].' certifié';

//taoview strings
$string['taoview'] = 'TAO View';
$string['taoresources'] = 'Ressources pédagogiques';
$string['taotools'] = 'Utilitaires';
$string['moreinfo'] = 'plus d\'infos';
$string['rating'] = 'Evaluation';
$string['rate'] = 'Evaluation';
$string['sendinratings'] = 'Envoyer aux évaluations';
$string['ratingssaved'] = 'Evaluations enregistrées';
$string['noartefactsfound'] = 'aucun artefact trouvé';
$string['toaddartefacts'] = 'Pour ajouter une ressource à cette page, allez dans <a href=\"$a->link\">votre portefolio</a>';
$string['filteredby'] = 'Filtré par';
$string['removefilters'] = 'Retirer les filtres';
$string['sortby'] = 'Trié par';

//TAO POSTinst strings - used to name courses/categories etc.
$string['taotrainingcourses'] = 'Bac à sable';
$string['taocatworkshop'] = 'Atelier';
$string['taocatsuspended'] = 'Suspendu';
$string['taocatlptemplates'] = 'Gabarits de '.$GLOBSTRING['taolps'];
$string['taocatlp'] = $GLOBSTRING['taolps'];

//custom tags string
$string['browsersearchtags'] = 'Chercher dans tous les centres d\'intérêt';

//edit lp settings page
$string['editlpsettings'] = 'Modifier les paramètres du '.$GLOBSTRING['taolp'];
$string['courseupdated'] = $GLOBSTRING['taoLp'].' mis à jour';

?>