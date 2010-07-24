<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require('../../config.php');
require_once('taoresource_entry_form.php');
require_once('taoresource_entry_extra_form.php');
require_once("lib.php");
require_once($CFG->libdir.'/filelib.php');

$ignore_list = array('mform_showadvanced_last', 'pagestep', 'MAX_FILE_SIZE', 'add', 'update', 'return', 'type', 'section', 'mode', 'course', 'submitbutton');

require_login();

$add           = optional_param('add', 0, PARAM_ALPHA);
$update        = optional_param('update', 0, PARAM_INT);
$return        = optional_param('return', 0, PARAM_BOOL); //return to course/view.php if false or mod/modname/view.php if true
$type          = optional_param('type', '', PARAM_ALPHANUM);
$section       = optional_param('section', 0, PARAM_INT);
$mode          = required_param('mode', PARAM_ALPHA);
$course        = required_param('course', PARAM_INT);
$pagestep      = optional_param('pagestep', 1, PARAM_INT);

if (! $course = get_record("course", "id", $course)) {
    error("This course doesn't exist");
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:manageactivities', $context);

$pagetitle = strip_tags($course->shortname);

// sort out how we should look depending on add or update
if ($mode == 'update') {
    $entry_id = required_param('entry_id', PARAM_INT);
    $taoresource_entry = taoresource_entry::get_by_id($entry_id);
    $strpreview = get_string('preview','taoresource');
    $taoresource_entry->url_display =  "<a href=\"$CFG->wwwroot/mod/taoresource/view.php?identifier={$taoresource_entry->identifier}&amp;inpopup=true\" "
      . "onclick=\"this.target='resource{$taoresource_entry->id}'; return openpopup('/mod/taoresource/view.php?inpopup=true&amp;identifier={$taoresource_entry->identifier}', "
      . "'resource{$taoresource_entry->id}','resizable=1,scrollbars=1,directories=1,location=0,menubar=0,toolbar=0,status=1,width=800,height=600');\">(".$strpreview.")</a>";
    $taoresource_entry->taoresourcefile = $taoresource_entry->file;
}
else {
    $mode = 'add';
    $taoresource_entry = new taoresource_entry();
}

// which form phase are we in - step 1 or step 2
$mform = false;
if ($pagestep == 1) {
    $mform = new mod_taoresource_taoresource_entry_form($mode);
    $mform->set_data($taoresource_entry);
}
else {
    $mform = new mod_taoresource_taoresource_entry_extra_form($mode);
    $mform->set_data($taoresource_entry);
}

if ( $mform->is_cancelled() ){
    //cancel - go back to course
    redirect($CFG->wwwroot."/course/view.php?id={$course->id}");
}

// is this a successful POST ?
if ($formdata = $mform->get_data()) {
    // check for hidden values
    if ($hidden = optional_param('taoresource_hidden', '', PARAM_CLEAN)) {
        $hidden = explode('|', $hidden);
        foreach ($hidden as $field) {
            $formdata->$field = taoresource_clean_field($field);
        }
    }

    // process the form contents
    // add form data to table object - skip the elements until we know what the identifier is
    foreach ($formdata as $key => $value) {
        if (in_array($key, $TAORESOURCE_CORE_ELEMENTS) && !empty($value)) {
            if ($key == 'url') {
                $taoresource_entry->add_element($key, clean_param($value, PARAM_URL));
            }
            else {
                $taoresource_entry->add_element($key, clean_param($value, PARAM_CLEAN));
            }
        }
    }

    $taoresource_entry->lang = $USER->lang;

    if ($mode == 'add') {
        // locally defined resource ie. we are the master
        $taoresource_entry->type = 'file';
        // page step 1
        if ($pagestep == 1) {
            // is this a local resource or a remote one?
            if (!empty($taoresource_entry->url)) {
                $taoresource_entry->identifier = sha1($taoresource_entry->url);
                $taoresource_entry->mimetype = mimeinfo("type", $taoresource_entry->url);
            }
            else {
                // if resource uploaded then move to temp area until user has
                // completed metadata
                if (!taoresource_check_and_create_moddata_temp_dir()) {
                    error("Error - can't create resources temp dir");
                }
                $tempfile = $_FILES['taoresourcefile']['tmp_name'];
                $taoresource_entry->uploadname = clean_param($_FILES['taoresourcefile']['name'], PARAM_PATH);
                $taoresource_entry->mimetype = clean_param($_FILES['taoresourcefile']['type'], PARAM_URL);

                if (empty($tempfile) || !$hash = taoresource_sha1file($tempfile)) {
                    error("Error - can't create hash of incoming resource file");
                }

                $taoresource_entry->identifier = $hash;
                $taoresource_entry->file = $hash.'-'.$taoresource_entry->uploadname;
                $taoresource_entry->tempfilename = $CFG->dataroot.TAORESOURCE_TEMPPATH.$taoresource_entry->file;


                $formdata->identifier = $taoresource_entry->identifier;
                $formdata->file = $taoresource_entry->file;
                $formdata->uploadname = $taoresource_entry->uploadname;
                $formdata->mimetype = $taoresource_entry->mimetype;

                if (!taoresource_copy_file($tempfile, $taoresource_entry->tempfilename, true)) {
                    error("Error - can't copy upload file to temp");
                }
            }
        } // page step 2 - get it from the hidden fields
        else {
            // is this a local resource or a remote one?
            if (!empty($formdata->url)) {
                $taoresource_entry->url = $formdata->url;
                $taoresource_entry->identifier = sha1($taoresource_entry->url);
                $taoresource_entry->mimetype = mimeinfo("type", $taoresource_entry->url);
            }
            else {
                // if these values are missing then we have a big problem - blowup appropriately
                if (empty($formdata->uploadname) || empty($formdata->mimetype) || empty($formdata->identifier) || empty($formdata->file)) {
                    // die a horrible death
                    error("Error - transition hiddne fields from step 1 missing in step 2");
                }
                $taoresource_entry->uploadname = $formdata->uploadname;
                $taoresource_entry->mimetype = $formdata->mimetype;
                $taoresource_entry->identifier = $formdata->identifier;
                $taoresource_entry->file = $formdata->file;
                $taoresource_entry->tempfilename = $CFG->dataroot.TAORESOURCE_TEMPPATH.$taoresource_entry->file;
            }
        }
    }

    // common update or add tasks
    // now that we know what the identifier will be - add the elements
    foreach ($formdata as $key => $value) {
        if (!in_array($key, $ignore_list) && !empty($value)) {
            $taoresource_entry->update_element($key, clean_param($value, PARAM_CLEAN));
        }
    }

    // if we need to do step 2 - defer the add/update, and load up the extra form
    // hide all values retrieved so far in hidden
    // put the file into temp for later
    if ($pagestep == 1 && taoresource_extra_resource_screen()) {
        // setup the new form, and hide away all the values we have so far
        $mform = new mod_taoresource_taoresource_entry_extra_form($mode);
        $mform->set_data($taoresource_entry);
        $hidden = array();
        foreach ($formdata as $key => $value) {
            if (!in_array($key, $ignore_list) && !empty($value)) {
                $mform->_form->addElement('hidden', $key, $value);
                $hidden[]= $key;
            }
        }
        $mform->_form->addElement('hidden', 'taoresource_hidden', join('|', $hidden));
    }
    else {
        if ($mode == 'add' && !$taoresource_entry->add_instance()) {
            error('Resource failed to save (add) to the DB');
        }
        else if ($mode != 'add' && !$taoresource_entry->update_instance()) {
            error('Resource failed to save (update) to the DB');
        }
        else {
            // everything saved OK - lets jump to the search
            $fullurl = $CFG->wwwroot."/mod/taoresource/search.php?id={$taoresource_entry->id}&course={$course->id}&section={$section}&type={$type}&add={$add}&return={$return}";
            redirect($fullurl);
        }
    }
}


// do we have hidden elements that we need to salvage
if ($hidden = optional_param('taoresource_hidden', '', PARAM_CLEAN)) {
    $hidden = explode('|', $hidden);
    foreach ($hidden as $field) {
        $mform->_form->addElement('hidden', $field, taoresource_clean_field($field));
//        $mform->_form->addElement('hidden', $field, optional_param($field, '', PARAM_RAW));
    }
    $mform->_form->addElement('hidden', 'taoresource_hidden', join('|', $hidden));
}

// build up navigation links
$navlinks = array();
$navlinks[] = array('name' => get_string("modulenameplural", 'taoresource'), 'link' => "$CFG->wwwroot/mod/taoresource/index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => get_string($mode.'taoresourcetypefile', 'taoresource'), 'link' => '', 'type' => 'title');
$navigation = build_navigation($navlinks);
print_header_simple($pagetitle, '', $navigation, "", "", false);
print_heading_with_help(get_string($mode.'taoresourcetypefile', 'taoresource'), 'addtaoresource', 'taoresource');

//// ensure static fields are not wrapped
//echo "<style>.fstatic { white-space: nowrap; }</style>";

//$mform->_form->_attributes['action'] = $CFG->wwwroot.'/mod/taoresource/edit.php?course='.$course->id.'&add='.$add.'&return='.$return.'&type='.$type.'&section='.$section.'&mode='.$mode;

$mform->_form->addElement('hidden', 'course', $course->id);
$mform->_form->addElement('hidden', 'add', $add);
$mform->_form->addElement('hidden', 'return', $return);
$mform->_form->addElement('hidden', 'type', $type);
$mform->_form->addElement('hidden', 'section', $section);
$mform->_form->addElement('hidden', 'mode', $mode);
if ($mode == 'update') {
    $mform->_form->addElement('hidden', 'entry_id', $entry_id);
}


// display whichever form
$mform->display();
print_footer($course);


// page local functions

// grab and clean form value
function taoresource_clean_field($field) {
    switch ($field) {
        case 'identifier' :
            $value = optional_param($field, '', PARAM_BASE64);
            break;
        case 'file' :
            $value = optional_param($field, '', PARAM_PATH);
            break;
        case 'uploadname' :
            $value = optional_param($field, '', PARAM_PATH);
            break;
        case 'mimetype' :
            $value = optional_param($field, '', PARAM_URL);
            break;
        default:
            $value = optional_param($field, '', PARAM_RAW);
            break;
    }
    return $value;
}
?>