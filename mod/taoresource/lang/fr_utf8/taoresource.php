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
$string['addtaoresourcetypefile'] = 'Ajouter une ressource mutualis�e';
$string['backup_index'] = 'Sauvegarder le r�f�rentiel des ressources';
$string['badcourseid'] = 'Identifiant de parcours invalide';
$string['cannotrestore'] = 'l\'entr�e du catalogue de ressources est manquante - probl�me de restauration : $a';
$string['choose'] = 'Choisir';
$string['classificationpurpose'] = 'Sujet de classification';
$string['classificationtaxonpath'] = 'Chemin taxinomique de classification';
$string['config_backup_index'] = 'Lors de la sauvegarde d\'un cours, sauvegarder TOUTES les entr�es de catalogue correspondantes (y compris les fichiers locaux) ?';
$string['config_freeze_index'] = 'Lors de la sauvegarde d\'un cours, ne sauvegarder aucun fichier physique du r�f�rentiel commun ?';
$string['config_restore_index'] = 'Lors d\'une restauration, restaurer TOUTES les entr�es de catalogue (y compris les fichiers locaux) ?  Ceci ne remplacera pas les entr�es et m�tadonn�es existantes.';
$string['configallowlocalfiles'] = 'Lors de la cr�ation d\'une nouvelle ressource de type fichier, permettre des liens vers les fichiers disponibles sur un syst�me de fichiers local, par exemple sur un CD ou sur un disque dur. Cela peut �tre utile dans une classe o� tous les �tudiants ont acc�s a un volume r�seau commun ou si des fichiers sur un CD sont n�cessaires. Il est possible que l\'utilisation de cette fonctionnalit� requi�re une modification des r�glages de s�curit� de votre navigateur.';
$string['configautofilerenamesettings'] = 'Mettre � jour automatiquement les r�f�rences vers d\'autres fichiers et dossiers lors d\'un changement de nom dans la gestion des fichiers.';
$string['configblockdeletingfilesettings'] = 'Emp�cher la suppression de fichiers et dossiers r�f�renc�s par des ressources. Veuillez remarquer que les images et autres fichiers r�f�renc�s dans le code HTML ne sont pas prot�g�s par ce r�glage.';
$string['configdefaulturl'] = 'Cette valeur est utilis�e pour pr�remplir l\'URL lors de la cr�ation d\'une nouvelle ressource point�e par URL.';
$string['configfilterexternalpages'] = 'L\'activation de ce r�glage permettra le filtrage des ressources externes (pages web, fichiers HTML d�pos�s) par les filtres d�finis dans le site (comme les liens des glossaires). Lorsque ce r�glage est actif, l\'affichage de vos pages sera ralenti de fa�on sensible. � utiliser avec pr�caution.';
$string['configforeignurlsheme'] = 'Forme g�n�rale de l\'Url. Utiliser \'&lt;%%%%ID%%%%&gt;\' comme emplacement de l\'Identifiant Unique de Ressource';
$string['configframesize'] = 'Quand une page web ou un fichier est affich� dans un cadre (frame), cette valeur indique (en pixels) la taille du cadre contenant la navigation (en haut de la fen�tre).';
$string['configparametersettings'] = 'D�termine si par d�faut la zone de configuration des param�tres est affich�e ou non, lors de l\'ajout de nouvelles ressources. Apr�s la premi�re utilisation, ce r�glage devient individuel.';
$string['configpopup'] = 'Lors de l\'ajout d\'une ressource pouvant �tre affich�e dans une fen�tre pop-up, cette option doit-elle �tre activ�e par d�faut ?';
$string['configpopupdirectories'] = 'Les fen�tres pop-up affichent le lien du dossier par d�faut';
$string['configpopupheight'] = 'Hauteur par d�faut des fen�tres pop-up';
$string['configpopuplocation'] = 'La barre de l\'URL est affich�e par d�faut dans les fen�tres pop-up';
$string['configpopupmenubar'] = 'La barre des menus est affich�e par d�faut dans les fen�tres pop-up';
$string['configpopupresizable'] = 'Les fen�tres pop-up sont redimensionnables par d�faut';
$string['configpopupscrollbars'] = 'Les barres de d�filement sont affich�es par d�faut dans les fen�tres pop-up';
$string['configpopupstatus'] = 'La barre d\'�tat est affich�e par d�faut dans les fen�tres pop-up';
$string['configpopuptoolbar'] = 'La barre des outils est affich�e par d�faut dans les fen�tres pop-up';
$string['configpopupwidth'] = 'Largeur par d�faut des fen�tres pop-up';
$string['configsecretphrase'] = 'Cette phrase secr�te est utilis�e pour g�n�rer le code crypt� pouvant �tre envoy� comme param�tre � certaines ressources. Ce code crypt� est fabriqu� en concat�nant une valeur md5 de l\'adresse IP du current_user et de cette phrase secr�te, par exemple : code = md5(IP.secretphrase). Ceci permet � la ressource recevant le param�tre de v�rifier la connexion pour plus de s�curit�.';
$string['configwebsearch'] = 'URL affich�e lors de l\'ajout d\'une page web ou d\'un lien, pour permettre � l\'utilisateur de rechercher l\'URL d�sir�e.';
$string['configwindowsettings'] = 'D�termine si, par d�faut, la zone de configuration des fen�tres est affich�e ou non, lors de l\'ajout de nouvelles ressources. Apr�s la premi�re utilisation, ce r�glage devient individuel.';
$string['contributor'] = 'Contributeur';
$string['conversioncancelled'] = 'Conversion annul�e. Vous allez �tre redirig�s vers la gestion des activit�s';
$string['convert'] = 'Convertir la s�lection';
$string['convertall'] = 'Mettre en commun et indexer les ressources';
$string['convertback'] = 'Rappatrier une ressouce commune';
$string['description'] = 'Description';
$string['directlink'] = 'Lien direct vers ce fichier';
$string['display'] = 'Fen�tre';
$string['filenotfound'] = 'D�sol�, le fichier demand� ne peut �tre trouv�';
$string['fileuploadfailed'] = 'Echec du t�l�chargement';
$string['framesize'] = 'Taille du cadre';
$string['freeze_index'] = 'Geler le r�f�rentiel de ressources';
$string['issuedate'] = 'Date de creation';
$string['keywords'] = 'Mots-clefs';
$string['learningresourcetype'] = 'Ressource mutualis�e';
$string['location'] = 'Emplacement de la ressource';
$string['metadata'] = 'M�tadonn�es';
$string['missingresource'] = 'choisir une URL ou un fichier';
$string['modulename'] = 'Ressource mutualis�e';
$string['modulenameplural'] = 'Ressources mutualis�es';
$string['name'] = 'Nom';
$string['newdirectories'] = 'Montrer les liens directs';
$string['newheight'] = 'Hauteur par d�faut (en pixels)';
$string['newlocation'] = 'Montrer la barre d\'adresse';
$string['newmenubar'] = 'Montrer la barre de menu';
$string['newresizable'] = 'Autoriser le redimensionnement';
$string['newscrollbars'] = 'Autoriser le d�filement';
$string['newstatus'] = 'Montrer la barre d\'�tat';
$string['newtoolbar'] = 'Montrer la barre d\'outils';
$string['newwidth'] = 'Largeur par d�faut (en pixels)';
$string['newwindow'] = 'Nouvelle fen�tre';
$string['noresourcesfound'] = 'Aucune ressource dans le catalogue';
$string['othersearch'] = 'Nouvelle recherche';
$string['pagewindow'] = 'M�me fen�tre';
$string['pan'] = 'Pan';
$string['pluginscontrol'] = 'Contr�le des plugins de m�tadonn�es';
$string['pluginscontrolinfo'] = 'Ces param�tres permettent de configurer les jeux de m�tadonn�es utilis�s lors de l\'indexation de la ressource';
$string['preview'] = 'Pr�visualiser';
$string['remotesearchquery'] = 'Recherche dans les r�f�rentiels de ressources';
$string['remotesearchresults'] = 'R�sultats de recherche ';
$string['remotesubmission'] = 'Soumission de ressource';
$string['repositorytoresource'] = 'R�f�rentiel commun vers '.$GLOBSTRING['taolp'];
$string['resourceaccessurlasforeign'] = 'Url d\'acc�s aux ressources';
$string['resourceconversion'] = 'Conversion de ressources';
$string['resourcedefaulturl'] = 'URL par d�faut';
$string['resourceexists'] = 'Il existe d�j� une ressource de m�me signature';
$string['resourcetorepository'] = $GLOBSTRING['taolp'].' vers r�f�rentiel commun';
$string['resourcetypefile'] = 'Identification de la ressource';
$string['restore_index'] = 'Restaurer le catalogue de ressources mutualis�es';
$string['rights'] = 'Droits d\'usage';
$string['rightsdescription'] = 'Description des droits';
$string['searchfor'] = 'Chercher';
$string['searchheader'] = 'Crit�res de recherche';
$string['searchin'] = 'Rechercher dans';
$string['searchtaoresource'] = 'Chercher une ressource mutualis�e';
$string['step2'] = 'Passer � l\'�tape 2';
$string['taoresourcetypefile'] = 'Ressource mutualis�e';
$string['title'] = 'Titre';
$string['typicalagerange'] = 'Tranche d\'age concern�e';
$string['updatetaoresource'] = 'Mettre � jour la ressource mutualis�e';
$string['updatetaoresourcetypefile'] = 'Mettre � jour la ressource mutualis�e';
$string['url'] = 'URL de la ressource mutualis�e';
$string['vol'] = 'Vol';
