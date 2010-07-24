<?PHP
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

$string['addheader'] = 'Ajouter une nouvelle ressource';
$string['addtaoresource'] = 'Ajouter une ressource';
$string['addtaoresourcetypefile'] = 'Ajouter une ressource mutualisée';
$string['backup_index'] = 'Sauvegarder le référentiel des ressources';
$string['badcourseid'] = 'Identifiant de parcours invalide';
$string['cannotrestore'] = 'l\'entrée du catalogue de ressources est manquante - problème de restauration : $a';
$string['choose'] = 'Choisir';
$string['classificationpurpose'] = 'Sujet de classification';
$string['classificationtaxonpath'] = 'Chemin taxinomique de classification';
$string['config_backup_index'] = 'Lors de la sauvegarde d\'un cours, sauvegarder TOUTES les entrées de catalogue correspondantes (y compris les fichiers locaux) ?';
$string['config_freeze_index'] = 'Lors de la sauvegarde d\'un cours, ne sauvegarder aucun fichier physique du référentiel commun ?';
$string['config_restore_index'] = 'Lors d\'une restauration, restaurer TOUTES les entrées de catalogue (y compris les fichiers locaux) ?  Ceci ne remplacera pas les entrées et métadonnées existantes.';
$string['configallowlocalfiles'] = 'Lors de la création d\'une nouvelle ressource de type fichier, permettre des liens vers les fichiers disponibles sur un système de fichiers local, par exemple sur un CD ou sur un disque dur. Cela peut être utile dans une classe où tous les étudiants ont accès a un volume réseau commun ou si des fichiers sur un CD sont nécessaires. Il est possible que l\'utilisation de cette fonctionnalité requière une modification des réglages de sécurité de votre navigateur.';
$string['configautofilerenamesettings'] = 'Mettre à jour automatiquement les références vers d\'autres fichiers et dossiers lors d\'un changement de nom dans la gestion des fichiers.';
$string['configblockdeletingfilesettings'] = 'Empêcher la suppression de fichiers et dossiers référencés par des ressources. Veuillez remarquer que les images et autres fichiers référencés dans le code HTML ne sont pas protégés par ce réglage.';
$string['configdefaulturl'] = 'Cette valeur est utilisée pour préremplir l\'URL lors de la création d\'une nouvelle ressource pointée par URL.';
$string['configfilterexternalpages'] = 'L\'activation de ce réglage permettra le filtrage des ressources externes (pages web, fichiers HTML déposés) par les filtres définis dans le site (comme les liens des glossaires). Lorsque ce réglage est actif, l\'affichage de vos pages sera ralenti de façon sensible. À utiliser avec précaution.';
$string['configforeignurlsheme'] = 'Forme générale de l\'Url. Utiliser \'&lt;%%%%ID%%%%&gt;\' comme emplacement de l\'Identifiant Unique de Ressource';
$string['configframesize'] = 'Quand une page web ou un fichier est affiché dans un cadre (frame), cette valeur indique (en pixels) la taille du cadre contenant la navigation (en haut de la fenêtre).';
$string['configparametersettings'] = 'Détermine si par défaut la zone de configuration des paramètres est affichée ou non, lors de l\'ajout de nouvelles ressources. Après la première utilisation, ce réglage devient individuel.';
$string['configpopup'] = 'Lors de l\'ajout d\'une ressource pouvant être affichée dans une fenêtre pop-up, cette option doit-elle être activée par défaut ?';
$string['configpopupdirectories'] = 'Les fenêtres pop-up affichent le lien du dossier par défaut';
$string['configpopupheight'] = 'Hauteur par défaut des fenêtres pop-up';
$string['configpopuplocation'] = 'La barre de l\'URL est affichée par défaut dans les fenêtres pop-up';
$string['configpopupmenubar'] = 'La barre des menus est affichée par défaut dans les fenêtres pop-up';
$string['configpopupresizable'] = 'Les fenêtres pop-up sont redimensionnables par défaut';
$string['configpopupscrollbars'] = 'Les barres de défilement sont affichées par défaut dans les fenêtres pop-up';
$string['configpopupstatus'] = 'La barre d\'état est affichée par défaut dans les fenêtres pop-up';
$string['configpopuptoolbar'] = 'La barre des outils est affichée par défaut dans les fenêtres pop-up';
$string['configpopupwidth'] = 'Largeur par défaut des fenêtres pop-up';
$string['configsecretphrase'] = 'Cette phrase secrète est utilisée pour générer le code crypté pouvant être envoyé comme paramètre à certaines ressources. Ce code crypté est fabriqué en concaténant une valeur md5 de l\'adresse IP du current_user et de cette phrase secrète, par exemple : code = md5(IP.secretphrase). Ceci permet à la ressource recevant le paramètre de vérifier la connexion pour plus de sécurité.';
$string['configwebsearch'] = 'URL affichée lors de l\'ajout d\'une page web ou d\'un lien, pour permettre à l\'utilisateur de rechercher l\'URL désirée.';
$string['configwindowsettings'] = 'Détermine si, par défaut, la zone de configuration des fenêtres est affichée ou non, lors de l\'ajout de nouvelles ressources. Après la première utilisation, ce réglage devient individuel.';
$string['contributor'] = 'Contributeur';
$string['conversioncancelled'] = 'Conversion annulée. Vous allez être redirigés vers la gestion des activités';
$string['convert'] = 'Convertir la sélection';
$string['convertall'] = 'Mettre en commun et indexer les ressources';
$string['convertback'] = 'Rappatrier une ressouce commune';
$string['description'] = 'Description';
$string['directlink'] = 'Lien direct vers ce fichier';
$string['display'] = 'Fenêtre';
$string['filenotfound'] = 'Désolé, le fichier demandé ne peut être trouvé';
$string['fileuploadfailed'] = 'Echec du téléchargement';
$string['framesize'] = 'Taille du cadre';
$string['freeze_index'] = 'Geler le référentiel de ressources';
$string['issuedate'] = 'Date de creation';
$string['keywords'] = 'Mots-clefs';
$string['learningresourcetype'] = 'Ressource mutualisée';
$string['location'] = 'Emplacement de la ressource';
$string['metadata'] = 'Métadonnées';
$string['missingresource'] = 'choisir une URL ou un fichier';
$string['modulename'] = 'Ressource mutualisée';
$string['modulenameplural'] = 'Ressources mutualisées';
$string['name'] = 'Nom';
$string['newdirectories'] = 'Montrer les liens directs';
$string['newheight'] = 'Hauteur par défaut (en pixels)';
$string['newlocation'] = 'Montrer la barre d\'adresse';
$string['newmenubar'] = 'Montrer la barre de menu';
$string['newresizable'] = 'Autoriser le redimensionnement';
$string['newscrollbars'] = 'Autoriser le défilement';
$string['newstatus'] = 'Montrer la barre d\'état';
$string['newtoolbar'] = 'Montrer la barre d\'outils';
$string['newwidth'] = 'Largeur par défaut (en pixels)';
$string['newwindow'] = 'Nouvelle fenêtre';
$string['noresourcesfound'] = 'Aucune ressource dans le catalogue';
$string['othersearch'] = 'Nouvelle recherche';
$string['pagewindow'] = 'Même fenêtre';
$string['pan'] = 'Pan';
$string['pluginscontrol'] = 'Contrôle des plugins de métadonnées';
$string['pluginscontrolinfo'] = 'Ces paramètres permettent de configurer les jeux de métadonnées utilisés lors de l\'indexation de la ressource';
$string['preview'] = 'Prévisualiser';
$string['remotesearchquery'] = 'Recherche dans les référentiels de ressources';
$string['remotesearchresults'] = 'Résultats de recherche ';
$string['remotesubmission'] = 'Soumission de ressource';
$string['repositorytoresource'] = 'Référentiel commun vers '.$GLOBSTRING['taolp'];
$string['resourceaccessurlasforeign'] = 'Url d\'accès aux ressources';
$string['resourceconversion'] = 'Conversion de ressources';
$string['resourcedefaulturl'] = 'URL par défaut';
$string['resourceexists'] = 'Il existe déjà une ressource de même signature';
$string['resourcetorepository'] = $GLOBSTRING['taolp'].' vers référentiel commun';
$string['resourcetypefile'] = 'Identification de la ressource';
$string['restore_index'] = 'Restaurer le catalogue de ressources mutualisées';
$string['rights'] = 'Droits d\'usage';
$string['rightsdescription'] = 'Description des droits';
$string['searchfor'] = 'Chercher';
$string['searchheader'] = 'Critères de recherche';
$string['searchin'] = 'Rechercher dans';
$string['searchtaoresource'] = 'Chercher une ressource mutualisée';
$string['step2'] = 'Passer à l\'étape 2';
$string['taoresourcetypefile'] = 'Ressource mutualisée';
$string['title'] = 'Titre';
$string['typicalagerange'] = 'Tranche d\'age concernée';
$string['updatetaoresource'] = 'Mettre à jour la ressource mutualisée';
$string['updatetaoresourcetypefile'] = 'Mettre à jour la ressource mutualisée';
$string['url'] = 'URL de la ressource mutualisée';
$string['vol'] = 'Vol';
