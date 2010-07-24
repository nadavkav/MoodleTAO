<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require_once('../../config.php');
require_once('search_form.php');
require_once("lib.php");

$course  = required_param('course', PARAM_INT);
$add     = optional_param('add', 0, PARAM_ALPHA);
$return  = optional_param('return', 0, PARAM_BOOL); //return to course/view.php if false or mod/modname/view.php if true
$type    = optional_param('type', '', PARAM_ALPHANUM);
$section = optional_param('section', 0, PARAM_ALPHANUM);
$id      = optional_param('id', false, PARAM_INT);
$page    = optional_param('page', false, PARAM_INT);

// query string parameters to ignore
$exclude_inputs = array('course', 'section', 'add', 'update', 'return', 'type', 'id', 'page', 'MAX_FILE_SIZE', 'submitbutton');

if (! $course = get_record("course", "id", $course)) {
    error("This course doesn't exist");
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:manageactivities', $context);

//process search form
$mform = new mod_taoresource_search_form();
if ( $mform->is_cancelled() ){
    //cancel - go back to course    
    redirect($CFG->wwwroot."/course/view.php?id={$course->id}");
}

// build up navigation links
$navlinks = array();
$navlinks[] = array('name' => get_string("modulenameplural", 'taoresource'), 'link' => "$CFG->wwwroot/mod/taoresource/index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => get_string('searchtaoresource', 'taoresource'), 'link' => '', 'type' => 'title');
$navigation = build_navigation($navlinks);

print_header_simple(get_string('searchtaoresource', 'taoresource'), '', $navigation, "", "", false);
print_heading_with_help(get_string('searchtaoresource', 'taoresource'), 'searchtaoresource', 'taoresource');

//get language strings
$strrepository = get_string('repository','taoresource');
$strpreview = get_string('preview','taoresource');
$strchoose = get_string('choose','taoresource');
$stredit = get_string('edit','taoresource');

// add in hidden navigational elements
$mform->_form->addElement('hidden', 'course', $course->id);
$mform->_form->addElement('hidden', 'add', $add);
$mform->_form->addElement('hidden', 'return', $return);
$mform->_form->addElement('hidden', 'type', $type);
$mform->_form->addElement('hidden', 'section', $section);

// handle a search query
// this can be either:
//    a browse request from the block
//    a paging request from the paginator
//    a search POST event
if ( ($fromform = $mform->get_data()) || $id || $section == "block" || $page !== false){
    if ($page !== false) {
        // deserialise the search query
        $search_parameters = base64_decode(optional_param('search', '', PARAM_ALPHANUM));
        if ($search_parameters) {
            // grab each parameter + value and populate the form data
            $search_parameters = explode('&', $search_parameters);
            foreach ($search_parameters as $search_parameter) {
                $parts = explode('=', $search_parameter, 2);
                if (!empty($parts[0]) && !empty($parts[1])) {
                    $fromform->$parts[0] = $parts[1];
                }
            }
        }
    }
    $mform->set_data($fromform);
    $mform->display();
    $fromform->search = isset($fromform->search) ? clean_param($fromform->search, PARAM_CLEAN) : clean_param(optional_param('search', '', PARAM_RAW), PARAM_CLEAN);
    $fromform->section = $section;
    
    $resources = array();
    // if we have an id then we must have come here from the add page
    if (!empty($fromform->id)) {
        $resources []= taoresource_entry::get_by_id($fromform->id);
    }
    else if ($id) {
        $resources []= taoresource_entry::get_by_id($id);
    }
    else {
        $resources = taoresource_entry::search($fromform);
    }
	//output results in same format as ims finder.php
	if ($resources) {
        $totalcount = count($resources);
        $baseurl = $CFG->wwwroot.
        "/mod/taoresource/search.php?course={$course->id}&section={$section}&type={$type}&add=taoresource&return={$return}&";
        // serialise the search query and append to the paging URI
        $search_parameters = '';
        foreach ($fromform as $fld => $val) {
            if (!in_array($fld, $exclude_inputs)) {
                if ($val === true) {
                    $val = 'true';
                }
                else if ($val === false) {
                    $val = 'false';
                }
                $search_parameters .= "{$fld}={$val}&";
            }
        }
        if ($search_parameters) {
            $baseurl .= 'search='.base64_encode($search_parameters).'&';
        }
        
        // Grab this pages worth
        if ($totalcount > TAORESOURCE_RESULTS_PER_PAGE) {
            $startpos = $page * TAORESOURCE_RESULTS_PER_PAGE;
            $resources_subset = array_slice($resources, $startpos, TAORESOURCE_RESULTS_PER_PAGE, true);
        }
        else {
            $resources_subset = $resources;
        }
	    
	    echo '<div id="taoresource-results" class="generalbox mform">';
	    print_paging_bar($totalcount, $page, TAORESOURCE_RESULTS_PER_PAGE, $baseurl, $pagevar='page');
    	echo '<ul>'; 
		foreach($resources_subset as $resource) {
			echo "<li> " .stripslashes_safe($resource->title);
            echo "&nbsp;";
			if ($section != 'block') {
    			echo '<a href=\''.$CFG->wwwroot.
                     "/course/modedit.php?course={$course->id}&section={$section}&type={$type}&add=taoresource&return={$return}&entry_id={$resource->id}".'\'>('.get_string("choose").')</a>';
			}
            echo "&nbsp;";
            echo  "<a href=\"$CFG->wwwroot/mod/taoresource/view.php?identifier={$resource->identifier}&amp;inpopup=true\" "
                  . "onclick=\"this.target='resource{$resource->id}'; return openpopup('/mod/taoresource/view.php?inpopup=true&amp;identifier={$resource->identifier}', "
                  . "'resource{$resource->id}','resizable=1,scrollbars=1,directories=1,location=0,menubar=0,toolbar=0,status=1,width=800,height=600');\">(".$strpreview.")</a>";
                  
            if (has_capability('moodle/course:manageactivities', $context)) {
                echo "&nbsp;";
                echo '<a href=\''.$CFG->wwwroot.
                     "/mod/taoresource/edit.php?course={$course->id}&section={$section}&type={$type}&add=taoresource&return={$return}&mode=update&entry_id={$resource->id}".'\'>('.get_string("update").')</a>';
            }
			echo "</li>\n";
		}
		echo '</ul>';
		print_paging_bar($totalcount, $page, TAORESOURCE_RESULTS_PER_PAGE, $baseurl, $pagevar='page');
        echo '</div>';
	} else {
		echo get_string('noresourcesfound','taoresource');
	}
//render form
} else {
	$toform = new object();
    $mform->set_data($toform);
    $mform->display();
}

print_footer($course);
?>