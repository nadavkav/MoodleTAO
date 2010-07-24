<?php
/**
 * Functions for learning path format
 * 
 * @author David Drummond 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

function course_page_uri($page, $course) {
   global $CFG;
   return $CFG->wwwroot."/course/view.php?id=".$course->id."&page=".$page->id;
}

function course_enrol_uri($page, $course) {
   global $CFG;
   return $CFG->wwwroot."/course/enrol.php?id=".$course->id."&page=".$page->id;
}

function learning_update_course_status($status, $reason, $course) {
    global $CFG;

    // update the editing acccess
    if(!update_learning_path_editing_access($status, $course)){
	print_error('could not update learning path editing access');
        return false;
    }

    // update the course category - if necessary
    if (!empty($CFG->lpautomatedcategorisation)) {
        switch ($status) {
            case COURSE_STATUS_PUBLISHED:
                // move to the published category
                $newcat = $CFG->lppublishedcategory;   
                $course->guest = 1; // hack to ensure everyone can browse this course when it's published
                break;

            case COURSE_STATUS_SUSPENDEDDATE:
            case COURSE_STATUS_SUSPENDEDAUTHOR:
                // move to the suspended category 
                $newcat = $CFG->lpsuspendedcategory;   
                break;

            default:
                // move to the workshop category 
                $newcat = $CFG->lpdefaultcategory;   
                break;
        }

        // only update course if we need to change
        //if (!empty($newcat) && $newcat != $course->category) { // todo bug in this, category not always there
        if (!empty($newcat)) {

            if (!set_field('course', 'category', $newcat, 'id', $course->id)) {
                print_error('categoryupdateerror', 'local');
                return false;
            }

        }
    }

    return true;
}

/**
 * Check course status and update editing rights for learning path authors 
 *
 * @param integer $status  
 * @param object $course  
 *
 * @return bool
 */
function update_learning_path_editing_access($status, $course) {

    if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        print_error('nocontext');
    }

    // get a list of our authors
    $authors = tao_get_lpauthors($context);  

    if (!empty($authors)) {
        if ($course->approval_status_id == COURSE_STATUS_NOTSUBMITTED || $course->approval_status_id == COURSE_STATUS_NEEDSCHANGE) {
            // give editing rights to learning path authors
            foreach ($authors as $author) {
                tao_role_assign_by_shortname(ROLE_LPEDITOR, $author->id, $context->id);
            }
        } else {
            // remove editing rights to learning path authors 
            foreach ($authors as $author) {
                tao_role_unassign_by_shortname(ROLE_LPEDITOR, $author->id, $context->id);
            }
        }
    }

    // force accessinfo refresh for users visiting this context.
    mark_context_dirty($context->path);

    // success!
    return true;

}

?>
