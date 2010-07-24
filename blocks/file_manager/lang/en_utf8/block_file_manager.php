<?PHP // $Id: block_file_manager.php,v 1.3 2009/03/29 23:52:02 danmarsden Exp $ 
// Titles and Misc

$string['actionsname'] = "Actions";
$string['actualsize'] = "Actual Size";
$string['addfile'] = "Add File";
$string['addlink'] = "Add a File or a Link";
$string['addurl'] = "Add Url";
$string['adminsettings'] = "Admin Settings";
$string['allowsharing'] = "Allow user to share to other course members";
$string['categories'] = "Categories";
$string['catname'] = "Category";
$string['cbxoptall'] = "All";
$string['cbxoptnone'] = "None";
$string['changefilename'] = "Change File Name";
$string['compressedsize'] = "Comp. Size";
$string['confdelete'] = "Delete Confirmation";
$string['datename'] = "Date";
$string['descname'] = "Description";
$string['directorysize'] = 'Directory size';
$string['enablefmanager'] = "Enable the File Manager";
$string['file'] = "File";
$string['filemanager'] = "File Manager";
$string['filesizename'] = "Size";
$string['fmrootdir'] = "blocks/file_manager";
$string['foldername'] = "Folder Name";
$string['folders'] = "Folders";
$string['link'] = "link";
$string['maxdir'] = "Maximum Directory Size";
$string['maxup'] = "Maximum Upload Filesize";
$string['myfiles'] = "My Files";
$string['namename'] = "Name";
$string['newcategory'] = 'New category';
$string['othersharedfiles'] = "Others Shared Links";
$string['plural'] = "(s)";
$string['rename'] = "Rename";
$string['selectall'] = "Select All";
$string['selectallonthispage'] = "Select all on this Page";
$string['sharedcat'] = "Shared Category";
$string['sharedfiles'] = "Shared Links";
$string['sharedfold'] = "Shared Folder";
$string['sharetoany'] = "Share to anyone from the main page";
$string['showallshared'] = "Show all shared";
$string['showcourseonly'] = "Show course only";
$string['studentsshared'] = "Student's Shared Links";
$string['teachersshared'] = "Teacher's Shared Links";
$string['titlecats'] = "Manage Categories";
$string['userfirstname'] = "Firstname";
$string['userlastname'] = "Lastname";
$string['userrole'] = "Role";
$string['usersshared'] = "Users who shared something to you";
$string['zipfiles'] = "Zip Files";
$string['zipname'] = "Zipped Filename (No Extention)";
$string['showfileshelp'] = "the visualization of shared";
$string['menuhelp'] = "actions for shared";
$string['by'] = "by";
$string['shared'] = "shared file(s) or link";
$string['folder'] = "folder";
$string['course'] = "Course";
$string['unzipfiles'] = "Zip decompression";


// Button names
$string['btncancel'] = "Cancel";
$string['btnsubmit'] = "Submit";
$string['btnupdate'] = "Update";
$string['btnteachers'] = "Teachers";
$string['btnstudents'] = "Students";
$string['btnadmins'] = "Administrators";
$string['btndone'] = "Done";
$string['btncreate'] = "Create";
$string['btncreatenewcat'] = "Create New Category";
$string['btnchange'] = "Change";
$string['btnyes'] = "Yes";
$string['btnno'] = "No";
$string['btnnewlink'] = "New File/Link";
$string['btnaddlink'] = "Upload File/Link";
$string['btnnoassigncat'] = "No Category";
$string['btnlinkact'] = "With Chosen Link(s)...";
$string['btncatact'] = "With Chosen Category(s)...";
$string['btnmoveact'] = "Move to another folder";
$string['btndelact'] = "Delete";
$string['btnzipact'] = "Create Zip archive";
$string['btnassigncatact'] = "Assign a category";
$string['btnsharedact'] = "With Shared Link(s)...";
$string['btnfileuploads'] = "File Uploads";
$string['btnfilesharing'] = "File Sharing";
$string['btnsecurity'] = "Security";
$string['btnshare'] = "Share";
$string['btnnewfolder'] = "New Folder";
$string['btnassigncat'] = "Assign";
$string['btnmovehere'] = "Move Files Here";
$string['btnstandardzip'] = "Standard Zip";
$string['btnmoodlezip'] = "Moodle Zip";
$string['btnviewzip'] = "View Zipped Contents";
$string['btnunzip'] = "Unzip";

// Errors
$string['errrecordmod'] = "Could not \$a->errtype the \$a->forwho\ record.";
$string['errnoshared'] = "There are no files shared to you!";
$string['errcantfinduser'] = "Could not find the user.";
$string['errnosharedfound'] = "Could not find any shared files from the user!";
$string['errdontowncat'] = "You can't modify a category you don't own!";
$string['errnoupdate'] = "Couldn't update the record!";
$string['errnoinsert'] = "Couldn't insert the new record!";
$string['errnodelete'] = "Couldn't delete the record!";
$string['errnodeletefolder'] = "Could not delete the folder";
$string['errdontownlink'] = "You can't modify a link you don't own!";
$string['errwierdfilename'] = "This file \$a is invalid and couldnt be uploaded.";
$string['errnodir'] = "Couldn't make your personal directory space.";
$string['errserversave'] = "An error happened while saving the file on the server.";
$string['errnodeletefile'] = "Couldn't delete the file on the server!";
$string['errnoviewfile'] = "You cannot view this file!";
$string['errdontownfolder'] = "You don't own this folder!";
$string['errwrongparam'] = "Incorrect Parameters";
$string['errfmandisabled'] = "The Link Manager has been Disabled!";
$string['errcantviewshared'] = "You cannot share links \$a";
$string['errcantdelete'] = "You cannot delete this link; it is shared to everyone";
$string['errnocreatefold'] = "Couldn't create the folder.";
$string['errnoviewcat'] = "You cannot view this category!";
$string['errnoviewfold'] = "You cannot view this folder!";
$string['errcantmovefile'] = "Cannot move the file!";
$string['errfiletoolarge'] = "Filesize is too large!";
$string['errmaxdirexceeded'] = "Max Directory Space Exceeded!";
$string['errsamefolder'] = "Can't move folder '\$a' into itself!";
$string['errfileexists'] = "The file '\$a' exists in target directory. Please rename file or move into another folder";
$string['errnorename'] = "Couldn't rename the file!";
$string['errnozip'] = "Couldn't zip the files!";

// Messages and Warnings
$string['msgadminsettings'] = "Set various sitewide settings";  
$string['msgfilesshared'] = "Files shared to you";
$string['msgfilemanager'] = "Manage your files";
$string['msgfilemanagergroup'] = "Manage group's files";
$string['msgadminsetinstruct'] = "Edit File Management Settings";
$string['msgrecordsupdated'] = "Records Updated.";
$string['msgexplainingshared'] = "These people have shared some of their web links or files.";
$string['msgexplainingsharedind'] = "This person has shared these files to you.";
$string['msgexplainmanager'] = "You can manage your files/links/folders here.  If you are in a course, you can also
	share files to other course members as well as upload files to an assignment.";
$string['msgnolinks'] = "You have no uploaded links in this folder.";
$string['msgcatcreate'] = "Fill in this page to create a new category.";
$string['msgcatmodify'] = "You can rename your category here.";
$string['msgneedinputname'] = "You need to fill out the marked field(s).";
$string['msgduplicate'] = "This name is already in use, please try another.";
$string['msgconfdelete'] = "Are you sure you want to delete the \$a below?<br><br>";
$string['msgaddlink'] = "Fill in the form below to add a new file or web link";
$string['msgmodlink'] = "Change any of the fields and click 'Change' to save or 'Cancel' to cancel changes.";
$string['msgnonedefined'] = "None Defined";
$string['msgfileexists'] = "The file '\$a\' already exists.  Please upload another file, or upload<br> 
							 the same file and fill in the highlighted box to rename the file.";
$string['msgfilenotfound'] = "The file '\$a\' couldn't be found on the server.  Record deleted anyway.";
$string['msgopenlink'] = "Open the URL in a popup window!";
$string['msgopenfile'] = "Open '\$a' in a popup window!";
$string['msgcatinuse'] = "* (This category is being used! All links using this<br> category will be assigned 'No Category')";
$string['msglinkinuse'] = "*(This Link has been shared to others! All<br> shared links will be deleted!)";
$string['msgsharetoothers'] = "Share to others";
$string['msgshare'] = "Click on individual users, groups, or the <br>'select all' to share to those people.<br><b>Note:</b> Sharing to all will not show the New!!<br>flag to those users.";
$string['msgnocourseusers'] = "There isn't anyone in this course to share links to!";
$string['msgsharemulti'] = "<font color='red'>Warning:</font> Sharing multiple files to users will<br> remove all previous shares to those files.";
$string['msgnofilessel'] = "You have to select a link to be able to share it!";
$string['msgfoldmod'] = "You can type the new name of the folder here.";
$string['msgfoldcreate'] = "Type the name of the folder you want to create here.";
$string['msgfolder'] = "View \$a's Contents";
$string['msgrootdir'] = "Move up";
$string['msgopencat'] = "View Category Link(s)";
$string['msgexpsharedcat'] = "You can view all links this user has under their shared category.";
$string['msgexpsharedfold'] = "You can view all links this user has under their shared folder <br>(Use your browsers back/forward buttons to browse).";
$string['msgnosharedcatlink'] = "No Links with this Category!";
$string['msgopenfold'] = "Open Folder in New Window";
$string['msgnosharedfoldlink'] = "No Files/Links are shared";
$string['msgsublinksdeleted'] = "(All links/folders/shares under this will be deleted as well!!)";
$string['msgfolderinuse'] = "*(This Folder has been shared to others! All shared folders will be deleted!)<br>";
$string['msgmovetohere'] = " objects selected for moving. Now go to the destination and press 'Move Files Here'<br><br>";
$string['msgzipthese'] = " objects selected for zipping:";
$string['msgnotincludedzip'] = "(Will not be included in standard zip)";
$string['msgreadonly'] = "You have read-only access because you are not member of this group.";
$string['msgcancelok'] = "Your cancelation has been taken in account. You will be soon redirected to the folder from which you came from ...";
$string['msgmodificationok'] = "Modifications have been taken in account. You will be soon redirected to the folder from which you came from ...";
$string['msgcreationok'] = "Creation was successful. You will be soon redirected to the folder from which you came from ...";
$string['msgcatassigned'] = "The category was successfully assigned. You will be soon redirected to the folder from which you came from ...";
$string['msgcatassign'] = "Select the category that you want to assign to the ressource(s) you've selected.";
$string['msgshared'] = "Ressources you've selected were successfully shared. You will be soon redirected to the folder from which you came from ...";
$string['msgdeleteok'] = "The delete action was successfull. You will be soon redirected to the folder from which you came from ...";
$string['msgcatshared'] = "Be carefull, this category is currently shared. After deletion, it won't be shared anymore.";
$string['msgunzipinprogress'] = "Decompression in progress, please wait ...";
$string['file_manager:canmanagegroups'] = 'Can manage groups';
?>