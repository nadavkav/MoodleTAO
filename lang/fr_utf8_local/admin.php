<?php // $Id$

global $GLOBSTRING;

$string['allowcategorythemes'] = 'Autoriser les chartes graphiques de catégories';
$string['allowcoursethemes'] = 'Autoriser les chartes graphiques de '.$GLOBSTRING['taolp'];
$string['allowvisiblecoursesinhiddencategories'] = 'Autoriser la visibilité des '.$GLOBSTRING['taolps'].' dans les catégories cachées';
$string['configallowcategorythemes'] = 'Si vous activez ce réglage, les chartes pourront être définies au niveau des catégories. La charte définie pour la catégorie sera héritée par toutes les sous-catégories et tous les '.$GLOBSTRING['taolp'].' qui s\'y trouvent, à moins qu\'un thème propre n\'y soit défini spécifiquement. ATTENTION ! L\'activation de cette option pourrait affecter la performance de votre site.';
$string['configallowcoursethemes'] = 'Si vous activez ce réglage, les chartes pourront être définies au niveau des '.$GLOBSTRING['taolp'].'. Les chartes de '.$GLOBSTRING['taolp'].' auront priorité sur tous les autres réglages de charte (charte du site, de la catégorie, de l\'utilisateur ou de la session).';
$string['configallowunenroll'] = 'Si vous choisissez « Oui », les '.$GLOBSTRING['taopts'].' pourront se désinscrire eux-mêmes des '.$GLOBSTRING['taolps'].'. Dans le cas contraire, ils ne le peuvent pas, et ce processus sera contrôlé par les '.$GLOBSTRING['taomts'].' et les administrateurs.';
$string['configallowuserthemes'] = 'Si vous activez ce réglage, les utilisateurs pourront choisir leur propre charte. La charte choisie par l\'utilisateur aura priorité sur celle du site (mais pas sur les chartes des '.$GLOBSTRING['taolps'].').';
$string['configallusersaresitestudents'] = 'Pour les activités affichées sur la page d\'accueil du site, TOUS les utilisateurs doivent-ils être considérés comme des '.$GLOBSTRING['taopts'].' ? Si vous choisissez « Oui », tout utilisateur possédant un compte confirmé pourra participer à ces activités en tant que '.$GLOBSTRING['taopt'].'. Si vous choisissez « Non », seuls les participants d\'au moins un '.$GLOBSTRING['taolp'].' pourront accéder aux activités de la page d\'accueil.';
$string['configautologinguests'] = 'Connecter automatiquement en tant qu\'invité les utilisateurs accédant à un '.$GLOBSTRING['taolp'].' ouvert aux invités.';
$string['configcoursemanager'] = 'Ce réglage permet de choisir les utilisateurs apparaissant dans la description du '.$GLOBSTRING['taolp'].'. Pour être affichés dans la description d\'un '.$GLOBSTRING['taolp'].', les utilisateurs doivent avoir au moins l\'un de ces rôles dans ce '.$GLOBSTRING['taolp'].'.';
$string['configcourserequestnotify'] = 'Indiquez le nom d\'utilisateur de la personne devant être informée lors de la demande de nouveaux '.$GLOBSTRING['taolps'].'.';
$string['configcourserequestnotify2'] = 'Les utilisateurs qui seront avertis lors d\'une demande de '.$GLOBSTRING['taolp'].'. Seuls les utilisateurs ayant le droit d\'approuver des demandes de '.$GLOBSTRING['taolp'].' sont affichés.';
$string['configcoursesperpage'] = 'Saisissez un nombre de '.$GLOBSTRING['taolps'].' à afficher par page dans la liste des '.$GLOBSTRING['taolps'].'.';
$string['configcreatornewroleid'] = 'Ce rôle est attribué automatiquement aux auteurs dans les '.$GLOBSTRING['taolps'].' qu\'ils créent. Ce rôle ne leur est pas attribué s\'ils possèdent déjà les capacités nécessaires dans le contexte immédiatement supérieur.';
$string['configdefaultallowedmodules'] = 'Pour les '.$GLOBSTRING['taolps'].' de la catégorie ci-dessus, quels modules voulez-vous autoriser <b>lorsque le '.$GLOBSTRING['taolp'].' est créé</b> ?';
$string['configdefaultcourseroleid'] = 'Ce rôle est attribué automatiquement aux utilisateurs s\'inscrivant dans un '.$GLOBSTRING['taolp'].'.';
$string['configdefaultrequestcategory'] = 'Les '.$GLOBSTRING['taolps'].' demandés par les utilisateurs seront automatiquement classés dans cette catégorie.';
$string['configdefaultrequestedcategory'] = 'Catégorie par défaut où placer les '.$GLOBSTRING['taolps'].' dont la demande a été approuvée.';
$string['configdefaultuserroleid'] = 'Tous les utilisateurs connectés auront les capacités du rôle spécifié ici, au niveau du site, EN PLUS de celles de tous les autres rôles qu\'ils ont déjà. Par défaut, il s\'agit du rôle « Utilisateur authentifié » (anciennement du rôle « Invité »). Il est à remarquer que cela n\'entrera pas en conflit avec les autres rôles disponibles : cela permet simplement de s\'assurer que tous les utilisateurs possèdent les capacités qui ne peuvent être attribuées au niveau des '.$GLOBSTRING['taolp'].' (par exemple écrire des articles de blog, gérer son calendrier, etc.).';
$string['configdisablecourseajax'] = 'Ne pas utiliser AJAX lors de la modification des pages de '.$GLOBSTRING['taolp'].'.';
$string['configenablecourserequests'] = 'Vous permettrez ainsi à tous les utilisateurs de faire des demandes de création de '.$GLOBSTRING['taolp'].'.';
$string['configenablestats'] = 'Si vous activez ce réglage, le cron automatique de Moodle traitera les historiques et produira quelques statistiques. Suivant la quantité de trafic sur votre site, le traitement peut prendre du temps. Les statistiques vous fourniront d\'intéressants graphiques et des statistiques sur chaque '.$GLOBSTRING['taolp'].' ou sur la totalité du site.';
$string['configenrolmentplugins'] = 'Veuillez choisir les méthodes d\'inscription que vous voulez utiliser. N\'oubliez pas de configurer correctement les réglages.<br /><br />Vous devez indiquer quelles méthodes sont disponibles, et <strong>une</strong> méthode peut être définie comme méthode d\'inscription <em>interactive</em> par défaut. Si vous ne désirez pas utiliser l\'inscription interactive aux '.$GLOBSTRING['taolp'].', veuillez désactiver l\'option « '.$GLOBSTRING['taoLp'].' disponible pour inscription » dans les '.$GLOBSTRING['taolps'].' en question.';
$string['configforcelogin'] = 'Habituellement, la page d\'accueil du site et la liste des '.$GLOBSTRING['taolps'].' (mais pas les '.$GLOBSTRING['taolps'].' eux-mêmes) peuvent être consultés sans se connecter au site. Si vous désirez forcer les visiteurs à se connecter avant de faire QUOI QUE CE SOIT dans le site, veuillez activer cette option.';
$string['configgradebookroles'] = 'Ce réglage permet de configurer les rôles apparaissant dans le carnet d\'évaluaton. Pour être mentionnés dans le carnet d\'évaluations d\'un '.$GLOBSTRING['taolp'].', les utilisateurs doivent avoir au moins l\'un de ces rôles dans ce '.$GLOBSTRING['taolp'].'.';
$string['configgradeexport'] = 'Sélectionnez les formats d\'exportation privilégiés du carnet d\'évaluations. Les sélections mettront en place et utiliseront ensuite un champ « dernière exportation » pour chaque évaluation. Par exemple, cela permettra d\'identifier des évaluations exportées comme « nouvelles » ou « modifiées ». Si vous n\'êtes pas sûr à ce sujet, ne cochez rien.';
$string['configguestroleid'] = 'Ce rôle est attribué automatiquement aux utilisateurs invités. Il est aussi attribué temporairement aux utilisateurs non inscrits à un '.$GLOBSTRING['taolp'].' permettant l\'accès aux invités, lorsqu\'ils y entrent sans la clef. Veuillez vérifier que ce rôle possède bien les capacités moodle/legacy:guest et moodle/course:view.';
$string['confighiddenuserfields'] = 'Veuillez sélectionner quelles informations vous désirez cacher aux autres utilisateurs du '.$GLOBSTRING['taolp'].' que les administrateurs/'.$GLOBSTRING['taomts'].'. Vous pourrez ainsi améliorer la protection des données des '.$GLOBSTRING['taopts'].'. Il est possible de sélectionner plusieurs champs.';
$string['configlongtimenosee'] = 'Si les étudiants ne se connectent pas au serveur durant ce laps de temps, leur inscription aux '.$GLOBSTRING['taolp'].' est automatiquement annulée.';
$string['configmaxbytes'] = 'Taille maximale des fichiers déposés dans le site, en octets. Cette valeur est limitée par les réglages PHP post_max_size et upload_max_filesize, ainsi que par le réglage Apache LimitRequestBody. Le réglage maxbytes limite quant à lui la taille au niveau des '.$GLOBSTRING['taolp'].' ou des chartes. Si vous choisissez « Limite serveur », le maximum alloué par le serveur sera utilisé.';
$string['confignodefaultuserrolelists'] = 'Ce réglage permet d\'éviter de présenter dans la page d\'accueil la liste de tous les utilisateurs, quand le rôle par défaut possède cette capacité, en raison d\'appels obsolètes aux fonctions get_course_user, etc. Cochez cette option si votre serveur souffre d\'une baisse de performance.';
$string['confignonmetacoursesyncroleids'] = 'Par défaut, toutes les attributions de rôles des '.$GLOBSTRING['taolps'].' enfants sont synchronisées dans les méta-'.$GLOBSTRING['taolps'].'. Les rôles désignés ici ne seront pas inclus dans le processus de synchronisation. Il est possible de sélectionner plusieurs champs.';
$string['configopentogoogle'] = 'Si vous activez cette option, Google pourra entrer dans votre site en tant qu\'utilisateur invité. En outre, les internautes arrivant sur votre site depuis une recherche Google seront automatiquement connectés en tant qu\'utilisateur invité. Cela ne permet cependant un accès transparent qu\'aux '.$GLOBSTRING['taolp'].' déjà ouverts aux invités.';
$string['configprofilesforenrolledusersonly'] = 'Afin de prévenir les abus de spammeurs, le profil des utilisateurs qui ne sont inscrits à aucun '.$GLOBSTRING['taolps'].' sont cachés. Les nouveaux utilisateurs doivent s\'inscrire au moins à un '.$GLOBSTRING['taolp'].' pour pouvoir ajouter une description à leur profil.';
$string['configrequestedstudentname'] = 'Terme utilisé pour « étudiant » dans les '.$GLOBSTRING['taolps'].' demandés';
$string['configrequestedstudentsname'] = 'Terme utilisé pour « étudiants » dans les '.$GLOBSTRING['taolps'].' demandés';
$string['configrequestedteachername'] = 'Terme utilisé pour « enseignant » dans les '.$GLOBSTRING['taolps'].' demandés';
$string['configrequestedteachersname'] = 'Terme utilisé pour « enseignants » dans les '.$GLOBSTRING['taolps'].' demandés';
$string['configrestrictbydefault'] = 'Les '.$GLOBSTRING['taolps'].' créés dans cette catégorie doivent-ils avoir par défaut des restrictions sur les modules ?';
$string['configrestrictmodulesfor'] = 'Quels '.$GLOBSTRING['taolps'].' doivent avoir <b>le réglage</b> permettant de désactiver des modules d\'activité ? Ce réglage ne s\'applique qu\'aux auteurs. Les administrateurs pourront toujours ajouter n\'importe quelle activité à des '.$GLOBSTRING['taolps'].'.';
$string['configsectionrequestedcourse'] = 'Demande de '.$GLOBSTRING['taolps'];
$string['configsendcoursewelcomemessage'] = 'Si ce réglage est activé, les utilisateurs recevront un message de bienvenue par courriel après qu\'ils se sont inscrits dans un '.$GLOBSTRING['taolp'].'.';
$string['configshowsiteparticipantslist'] = 'Tous les étudiants et les enseignants de la page d\'accueil de ce site seront affichés dans la liste des participants du site. Qui doit avoir l\'autorisation de voir cette liste des participants de la page d\'accueil ?'; // Legacy, to delete for 1.7
$string['configstatscatdepth'] = 'Le code des statistiques utilise une logique simplifiée pour les inscriptions aux '.$GLOBSTRING['taolps'].'. Les dérogations sont ignorées et le nombre de catégories mères vérifiées est limité. Le nombre 0 signifie une détection directe des attributions de rôles au niveau du site et des '.$GLOBSTRING['taolps'].'. Le nombre 1 indique de détecter également les attributions de rôles dans la catégorie parente du '.$GLOBSTRING['taolp'].', etc. Des nombres plus élevés génèrent une charge plus élevée de la base de données lors du calcul des statistiques.';
$string['configstatsuserthreshold'] = 'Si vous tapez ici une valeur numérique supérieure à zéro pour ordonner les '.$GLOBSTRING['taolps'].', les cours comportant un nombre inférieur d\'utilisateurs inscrits (tous les rôles) seront ignorés';
$string['configteacherassignteachers'] = 'Les enseignants peuvent-ils choisir d\'autres enseignants pour les '.$GLOBSTRING['taolps'].' qu\'ils donnent ? Si « Non », seuls les responsables de cours et les administrateurs peuvent choisir les enseignants.';
$string['configvisiblecourses'] = 'Afficher normalement les '.$GLOBSTRING['taolps'].' placés dans des catégories cachées';
$string['coursemanager'] = 'Gestionnaires de '.$GLOBSTRING['taolps'];
$string['coursemgmt'] = 'Gestion des '.$GLOBSTRING['taolps'];
$string['courseoverview'] = 'Vue d\'ensemble du '.$GLOBSTRING['taolp'];
$string['courserequestnotify'] = 'Notification des demandes de '.$GLOBSTRING['taolp'];
$string['courserequestnotifyemail'] = 'L\'utilisateur $a->user a demandé un nouveau '.$GLOBSTRING['taolp'].' sur $a->link';
$string['courserequests'] = 'Demandes de '.$GLOBSTRING['taolp'];
$string['courserequestspending'] = 'Demandes de '.$GLOBSTRING['taolp'].' en attente';
$string['courses'] = $GLOBSTRING['taoLps'];
$string['coursesperpage'] = $GLOBSTRING['taoLps'].' par page';
$string['creatornewroleid'] = 'Rôle des auteurs dans les nouveaux '.$GLOBSTRING['taolps'];
$string['defaultcourseroleid'] = 'Rôle par défaut des utilisateurs d\'un '.$GLOBSTRING['taolp'];
$string['defaultrequestcategory'] = 'Catégorie par défaut des demandes de '.$GLOBSTRING['taolp'];
$string['disablecourseajax'] = 'Désactiver l\'édition AJAX dans les '.$GLOBSTRING['taolps'];
$string['enablecourserequests'] = 'Activer les demandes de '.$GLOBSTRING['taolp'];
$string['longtimenosee'] = 'Désinscrire les utilisateurs des '.$GLOBSTRING['taolps'].' après';
$string['mnetrestore_extusers_noadmin'] = '<strong>Remarque :</strong> ce fichier de sauvegarde semble provenir d\'une installation de Moodle différente et contient des comptes utilisateurs distants du Réseau Moodle. Vous n\'êtes pas autorisé à effectuer ce type de restauration. Veuillez contacter l\'administrateur du site ou restaurer le '.$GLOBSTRING['taolp'].' sans les informations des utilisateurs (modules, fichiers, etc.)';
$string['sendcoursewelcomemessage'] = 'Envoyer un message de bienvenue aux '.$GLOBSTRING['taolps'];
$string['stickyblockscourseview'] = 'Page de '.$GLOBSTRING['taolp'];
$string['uucoursedefaultrole'] = 'Rôle par défaut dans les '.$GLOBSTRING['taolps'];

?>