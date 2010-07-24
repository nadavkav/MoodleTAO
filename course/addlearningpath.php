<?php
/*
   // select a learning path course backup as a 'template' for a new course

   Page doubles as "add a learning path" and "create a template".  Only real differnce is that 
   the labels change and the course is saved in a different category.

*/
    require_once('../config.php');
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    require_once('lib.php');
    require_once($CFG->dirroot.'/backup/lib.php');
    require_once('addlearningpath_form.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');
    require_once($CFG->dirroot.'/lib/xmlize.php');
    require_once($CFG->dirroot.'/local/lib.php');
    require_once($CFG->dirroot.'/local/tao.php');

    require_login();

    $categoryid       = optional_param('category', 0, PARAM_INT); // course category - can be changed in edit form
    $createtemplate   = optional_param('ct', 0, PARAM_INT); // create template flag

    $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);

    $creatorroleid = $CFG->creatornewroleid;

    if ($categoryid) {
        if (!$category = get_record('course_categories', 'id', $categoryid)) {
            error('Category ID was incorrect');
        }
        // for the type of learning path we are creating:
        //    check we have the appropriate capability and that the category provided matches the config setting 
        //     it is thus up to the calling page (most likey 'my work') to ensure the correct category is provided.
        if ($createtemplate) {
             require_capability('moodle/local:cancreatetemplates', $sitecontext);
             if($categoryid != $CFG->lptemplatescategory) {
                 error('Invalid category provided for creating templates');
             }
             $creatorroleid = get_field('role', 'id', 'shortname', ROLE_TEMPLATEEDITOR);
        } else {
             require_capability('moodle/local:cancreatelearningpaths', $sitecontext);
             if($categoryid != $CFG->lpdefaultcategory) {
                 error('Invalid category provided for creating learning paths');
             }
             $creatorroleid = get_field('role', 'id', 'shortname', ROLE_LPAUTHOR);
        }
    } else {
        error('Category must be specified');
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    $mform = new addlearningpath_form('addlearningpath.php', compact('createtemplate', 'category'));

    // processing section
    if ($mform->is_cancelled()){
        redirect($CFG->wwwroot .'/local/my/work.php');
    } else if (($data = $mform->get_data())) { 
        $newcourseid = tao_create_lp($data, $USER, $creatorroleid, $createtemplate);
        redirect($CFG->wwwroot."/course/view.php?id=$newcourseid");
    }

    // display section
    if ($createtemplate) {
        $title = get_string('createtemplate', 'local'); 
        $strtitle = get_string('createtemplate', 'local');
        $straddnewcourse = get_string("createtemplate", "local");
    } else {
        $title = get_string('addnewlearningpath', 'local'); 
        $strtitle = get_string('addnewlearningpath', 'local');
        $straddnewcourse = get_string("addnewlearningpath", "local");
    }

    $fullname = $site->fullname;

    $streditcoursesettings = get_string("editcoursesettings");
    $stradministration = get_string("administration");
    $strcourses = get_string("courses");
    $navlinks = array();

    $navlinks[] = array('name' => $stradministration,
                        'link' => "$CFG->wwwroot/$CFG->admin/index.php",
                        'type' => 'misc');
    $navlinks[] = array('name' => $strcourses,
                        'link' => "$CFG->wwwroot/course/index.php",
                        'type' => 'misc');
    $navlinks[] = array('name' => $straddnewcourse,
                        'link' => null,
                        'type' => 'misc');
    $title = "$site->shortname: $straddnewcourse";
    $fullname = $site->fullname;

    $navigation = build_navigation($navlinks);

    print_header($title, $fullname, $navigation, $mform->focus());
    print_heading($strtitle);
    //print_simple_box_start("center");

    $mform->display();

    //print_simple_box_end("center");
    print_footer();

?>