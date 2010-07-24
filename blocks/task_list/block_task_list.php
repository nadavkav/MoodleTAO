<?php // $Id: block_task_list.php,v 1.5 2006/11/08 01:03:00 mark-nielsen Exp $
/**
 * Task list block
 *
 * @author Mark Nielsen
 * @version $Id: block_task_list.php,v 1.5 2006/11/08 01:03:00 mark-nielsen Exp $
 * @package block_task_list
 **/

/**
 * Class list class definition
 *
 * @package block_task_list
 * @todo Create a class 'task' for managing different task types
 **/
class block_task_list extends block_base {
    /**
     * Sets default title and version.
     *
     * @return void
     **/
    function init() {
        $this->title = get_string('blockname', 'block_task_list') ;
        $this->version = 2007011505;
    }

    /**
     * We only want this block used in course-view
     * @return array
     */
    function applicable_formats() {
        return array('all' => false, 'course-view' => true);
    }

    /**
     * Make sure that several settings are set
     * for the rest of the class to use.
     *
     * Items that are set:
     *   course
     *   title
     *   config->taskorder
     *   config->display
     *   baseurl
     *   tasks
     *
     * @uses $CFG
     * @return void
     **/
    function specialization() {
        global $CFG, $COURSE;

        // Set the course
        $this->course = $COURSE;

        // Set title
        if (isset($this->config->title)) {
            $this->title = format_text($this->config->title, FORMAT_HTML);
        } else if (isset($CFG->block_task_list_title)) {
            $this->title = format_text($CFG->block_task_list_title, FORMAT_HTML);
        }

        if (!isset($this->config->taskorder)) {
            $this->config->taskorder = '';
        }

        if (!isset($this->config->categorywordwrap)) {
            $this->config->categorywordwrap = 45;
        }

        if (!isset($this->config->display) or $this->course->format != 'page') {
            $this->config->display = 'normal';
        }

        // Get the tasks
        if (!$this->tasks = get_records('block_task_list', 'instanceid', $this->instance->id)) {
            $this->tasks = array();
        }
    }

    /**
     * Creates the content of the block when
     * viewing in a course.
     *
     * @uses $CFG
     * @return object
     * @todo Idea: display X uncheck tasks organized by category and allow user to check them off
     **/
    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance) or !($this->can_checkoff() or $this->can_view())) {
            return $this->content;
        }
        $this->set_baseurl(true);

        switch ($this->config->display) {
            case 'expanded':
                $this->content->text = format_text($this->make_task_view(),FORMAT_HTML);
                break;

            case 'normal':
            default:
                $this->content->text = "<ul class=\"list\"><li><a href=\"$CFG->wwwroot/blocks/task_list/view.php?instanceid={$this->instance->id}\">".get_string('managetasklist', 'block_task_list').'</a></li></ul>';
                break;
        }

        return $this->content;
    }

    /**
     * Has instance config
     *
     * @return boolean
     **/
    function instance_allow_config() {
        return true;
    }

    /**
     * Has global config
     *
     * @return boolean
     **/
    function has_config() {
        return true;
    }

    /**
     * Default return is false - header will be shown
     *
     * @return boolean
     */
    function hide_header() {
        if ($this->config->display == 'extended') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Clean up tables when deleted
     *
     * @return boolean
     **/
    function instance_delete() {
        return delete_records('block_task_list', 'instanceid', $this->instance->id);
    }

    /**
     * Checks to see if the current user can
     * check-off items in the task list
     *
     * @return boolean
     **/
    function can_checkoff() {
        return has_capability('block/task_list:checkofftasks', get_context_instance(CONTEXT_BLOCK, $this->instance->id));
    }

    /**
     * Checks to see if the current user can
     * view items in the task list
     *
     * @return boolean
     **/
    function can_view() {
        return has_capability('block/task_list:viewtasks', get_context_instance(CONTEXT_BLOCK, $this->instance->id));
    }

    /**
     * Sets the baseurl
     *
     * @param boolean $incourse If we are printing the ouput in a course or not
     * @return void
     **/
    function set_baseurl($incourse = false) {
        global $CFG;

        if ($this->config->display == 'expanded' and $incourse) {
            if ($pageid = optional_param('page', 0, PARAM_INT)) {
                $pageid = '&amp;page='.$pageid;
            } else {
                $pageid = '';
            }
            $this->baseurl = "$CFG->wwwroot/course/view.php?id={$this->course->id}$pageid";
        } else {
            $this->baseurl = $CFG->wwwroot.'/blocks/task_list/view.php?instanceid='.$this->instance->id;
        }
    }

    /**
     * Returns the output of task management screens.
     *
     * Screens (AKA: taskmode):
     *   edit - Task editing controls
     *   view - Task display (can check off items and such)
     *
     * @return string
     **/
    function make_task_view() {
        $taskmode = optional_param('taskmode', '', PARAM_ALPHA);

    /// Handle the mode
        switch ($taskmode) {
            case 'move':
                $selected = 'edit';
                $taskmodeoutput = $this->make_move();
                break;
            case 'edit':
                $selected = 'edit';
                if ($this->can_checkoff()) {
                    $taskmodeoutput = $this->make_edit();
                } else {
                    $taskmodeoutput = $this->make_view();
                }
                break;
            case 'view':
            default:
                $selected = 'view';
                $taskmodeoutput = $this->make_view();
                break;
        }

    /// Build up the output: tabs, messages and taskmode output
        $output  = $this->make_tabs($selected);
        $output .= $this->print_messages(true);
        $output .= $taskmodeoutput;

        return $output;
    }

    /**
     * Creates the screen for viewing tasks.
     *
     * If category type tasks are present, then
     * a left menu is created for navigating
     * through the tasks based on category
     *
     * @return string
     **/
    function make_view() {
        $categoryid = optional_param('categoryid', 0, PARAM_INT);

        if (!$this->can_view()) {
            error('You are not authorized to view this page');
        }

        if (empty($this->tasks)) {
            // Cannot do anything yet
            $this->set_message(get_string('notasksfound', 'block_task_list'));
            return '';
        }

    /// Handle any submitted data (checked tasks)
        if ($data = data_submitted() and $this->can_checkoff() and confirm_sesskey()) {
            // Set and clean task IDs
            if (isset($data->taskids)) {
                $checkedtaskids = clean_param($data->taskids, PARAM_INT);
            } else {
                $checkedtaskids = array();
            }
            // If category ID, then start after we find category,
            // otherwise start right away.
            if ($categoryid) {
                $start = false;
            } else {
                $start = true;
            }

            // Run through all the tasks
            $taskids = explode(',', $this->config->taskorder);
            foreach ($taskids as $taskid) {
                $task = $this->tasks[$taskid];

                if ($start and $task->type == 'category') {
                    // Done processing
                    break;

                } else if ($task->type == 'category' and $task->id == $categoryid) {
                    // We can start processing items
                    $start = true;

                } else if ($start) {
                    // Processing a task - determine if it is checked
                    if (in_array($task->id, $checkedtaskids)) {
                        $checked = 1;
                    } else {
                        $checked = 0;
                    }
                    if ($task->checked != $checked) {
                        // Changed so update
                        if (set_field('block_task_list', 'checked', $checked, 'id', $task->id)) {
                            // Success - update our local object
                            $this->tasks[$task->id]->checked = $checked;
                        }
                    }
                }
            }

            $this->set_message(get_string('changessaved'), 'notifysuccess');
        }

    /// Start building the output

        // Layout Table - two columns
        $output = '<table class="tasklayout" align="center" border="0" cellpadding="10px" cellspacing="0"><tr>';

        // Fist, build the navigation (if it exists)
        $firstcat = true;
        $catlinks = array();
        $taskids  = explode(',', $this->config->taskorder);
        foreach ($taskids as $taskid) {
            $task = $this->tasks[$taskid];

            if ($task->type != 'category') {
                // Only category tasks are added
                continue;
            }

            $taskname = format_string($task->name);
            if (($firstcat and !$categoryid) or $task->id == $categoryid) {
                // None linked item (current location)
                $catlinks[] = '<li class="active">'.wordwrap($taskname,$this->config->categorywordwrap,'<br>').'</li>';
                $firstcat = false;
            /// Make sure it is set to an actual number now - Used later!
                $categoryid = $task->id;
            } else {
                // Linked item
                $catlinks[] = "<li><a href=\"$this->baseurl&amp;taskmode=view&amp;categoryid=$task->id\" title=\"$taskname\">".wordwrap($taskname,$this->config->categorywordwrap,'<br />')."</a></li>";
            }
        }

        if (!empty($catlinks)) {
            $output .= '<td class="tasknav"><div class="navtitle">'.wordwrap($this->title,$this->config->categorywordwrap,'<br />').'</div><ul>'.implode("\n", $catlinks).'</ul></td>';
        }

    /// Now the task list

        // Set up the table
        $table->align       = array('center', 'left');
        $table->size        = array('', '100%');
        $table->width       = '100%';
        $table->tablealign  = 'center';
        $table->cellpadding = '5px';
        $table->cellspacing = '0';
        $table->data        = array();

        if ($categoryid) {
            $startcollecting = false;
        } else {
            $startcollecting = true;
        }

        if (!$this->can_checkoff()) {
            $disabled = 'disabled = "disabled"';
        } else {
            $disabled = '';
        }

        foreach ($taskids as $taskid) {
            $task = $this->tasks[$taskid];
            if ($startcollecting and $task->type == 'category') {
                // We have been collecting and we ran into another category.  Stop!
                break;

            } else if ($task->type == 'category' and $task->id == $categoryid) {
                // Found a place to start
                $startcollecting = true;

            } else if ($startcollecting) {
                // Add task
                if ($task->checked) {
                    $checked = 'checked="checked" ';
                } else {
                    $checked = '';
                }

                $table->data[] = array("<input type=\"checkbox\" id=\"$task->id\" name=\"taskids[]\" value=\"$task->id\" $checked $disabled/>",
                                       "<label for=\"$task->id\">".$this->print_name($task, true).'</label>');
            }
        }

        // Continue the layout table
        $output .= '<td class="displaytasklist">';

        if (empty($table->data)) {
            $output .= '<h3 class="notasktodsiplay">'.get_string('notaskstodisplay', 'block_task_list').'</h2>';
        } else {
            if ($categoryid) {
                $a = new stdClass;
                $a->category = format_string($this->tasks[$categoryid]->name);
                $a->title = format_string($this->title);
                $output .= '<h2 class="taskheading">'.get_string('taskheading', 'block_task_list', $a).'</h2>';
            }
            $output .= '<h3 class="tasklistheading">'.get_string('blockname', 'block_task_list').'</h3>';
            $output .= '<div class="taskinstructions">'.get_string('taskinstructions', 'block_task_list').'</div>';
            $output .= "<form method=\"post\" action=\"$this->baseurl\">
                        <input type=\"hidden\" name=\"taskmode\" value=\"view\" />
                        <input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />
                        <input type=\"hidden\" name=\"categoryid\" value=\"$categoryid\" />";
            $output .= $this->print_table($table, true);
            if ($this->can_checkoff()) {
                $output .= '<p class="savechanges"><span class="button"><input type="submit" value="'.get_string('savechanges').'" /></span></p>';
            }
            $output .= '</form>';
        }

        // End the layout table
        $output .= '</td></tr></table>';

        return $output;
    }

    /**
     * Creates the screen for editing tasks.
     *
     * @uses $CFG
     * @return string
     **/
    function make_edit() {
        global $CFG;

        $type       = optional_param('type', '', PARAM_ALPHA);
        $taskaction = optional_param('taskaction', '', PARAM_ALPHA);
        $taskid     = optional_param('taskid', 0, PARAM_INT);
        $output     = '';

        if (!$this->can_edit()) {
            error('You are not authorized to view this page');
        }

    /// Actions that handle Processing
        switch ($taskaction) {
            case 'confirmed':  // Confirmation of deletion
                if (!confirm_sesskey()) {
                    error(get_string('confirmsesskeybad', 'error'));
                }

                if (delete_records('block_task_list', 'id', $taskid)) {

                    // This replaces the ID:
                    //  Anywhere in the list (except the first spot)
                    //  OR at the beginning of the list
                    //  OR the ID is the list and replaces it
                    $this->config->taskorder = preg_replace("/,$taskid\b|\b$taskid,|\b$taskid\b/", '', $this->config->taskorder);
                    $this->instance_config_commit();

                    // Remove from out task list
                    unset($this->tasks[$taskid]);

                    $this->set_message(get_string('taskdeleted', 'block_task_list'), 'notifysuccess');
                } else {
                    $this->set_message(get_string('taskdeletefailed', 'block_task_list'));
                }
                break;

            case 'save':  // Saving an add or edit form
                if (confirm_sesskey() and data_submitted()) {
                    $task = new stdClass;
                    $task->instanceid   = $this->instance->id;

                    $task->type         = required_param('type', PARAM_ALPHA);
                    if ($task->type == 'category') {
                        $task->name = required_param('taskname', PARAM_TEXT);
                    } else {
                        $task->name = required_param('taskname', PARAM_RAW);
                    }
                    $task->format       = required_param('format', PARAM_INT);
                    $task->timemodified = time();

                    if ($id = optional_param('taskid', 0, PARAM_INT)) {
                        $task->id = $id;
                        $result = update_record('block_task_list', $task);
                    } else {
                        $result = $task->id = insert_record('block_task_list', $task);

                        if ($task->id) {
                            if (empty($this->config->taskorder)) {
                                $this->config->taskorder = $task->id;
                            } else {
                                $this->config->taskorder .= ",$task->id";
                            }

                            // Save the order
                            $this->instance_config_commit();
                        }
                    }

                    if ($result) {
                        // Update the tasks object
                        $this->tasks[$task->id] = $task;
                        $this->set_message(get_string('taskitemsaved', 'block_task_list'), 'notifysuccess');
                    } else {
                        $this->set_message(get_string('taskitemnotsaved', 'block_task_list'));
                    }
                }
                break;
        }

    /// Actions that handle display
        switch ($taskaction) {
            case 'edit':  // Edit button was pushed
                if (!confirm_sesskey()) {
                    error(get_string('confirmsesskeybad', 'error'));
                }
                $name = get_field('block_task_list', 'name', 'id', $taskid);

            case 'add':  // An option in the add task menu was selected
                if (empty($name)) {
                    $name = '';
                }
                if ($usehtmleditor = can_use_html_editor()) {
                    $format = FORMAT_HTML;
                    $formatstr = get_string('formathtml');
                } else {
                    $format = FORMAT_MOODLE;
                    $formatstr = get_string('formattext');
                }

                $addform = '<h2>'.get_string('editingtask', 'block_task_list', get_string($type, 'block_task_list'))."</h2>
                            <form action=\"$this->baseurl\" method=\"post\" accept-charset=\"utf-8\">
                            <input type=\"hidden\" name=\"taskmode\" value=\"edit\" />
                            <input type=\"hidden\" name=\"taskaction\" value=\"save\" />
                            <input type=\"hidden\" name=\"taskid\" value=\"$taskid\" />
                            <input type=\"hidden\" name=\"type\" value=\"$type\" />
                            <input type=\"hidden\" name=\"format\" value=\"$format\" />
                            <input type=\"hidden\" name=\"sesskey\" value=\"".sesskey().'" />';
                if ($type == 'category') {
                    $addform .= '<strong>'.get_string('name', 'block_task_list') .':&nbsp;</strong>'.
                                 print_textfield('taskname', s($name), get_string('name', 'block_task_list'), 50, 0, true);
                } else {
                    $addform .= print_textarea($usehtmleditor, 10, 65, 0, 0, 'taskname', $name, $this->course->id, true).
                               "<br />$formatstr ".helpbutton('textformat', get_string('helpformatting'), 'moodle', true, false, '', true);
                }
                $addform .= '<p><span class="button"><input type="submit" value="'.get_string('savechanges').'" /></span></p>
                             </form>';
                $output .= print_box($addform, 'generalbox boxaligncenter taskedit', '', true);

                // A little hack to make the HTML editor work
                if ($usehtmleditor and $type != 'category') {
                    ob_start();
                    use_html_editor('taskname');
                    $output .= ob_get_contents();
                    ob_end_clean();
                }
                break;

            case 'delete':  // Delete button pushed
                if (!confirm_sesskey()) {
                    error(get_string('confirmsesskeybad', 'error'));
                }
                // Don't like it, but it works...
                ob_start();
                notice_yesno(get_string('confirmdeletetask', 'block_task_list', $this->print_name($this->tasks[$taskid], true)),
                             $this->baseurl.'&amp;taskmode=edit&amp;taskaction=confirmed&amp;taskid='.$taskid.'&amp;sesskey='.sesskey(),
                             $this->baseurl.'&amp;taskmode=edit');
                $output .= ob_get_contents();
                ob_end_clean();
                break;

            default:
                if (empty($this->tasks)) {
                    $this->set_message(get_string('notasksfound', 'block_task_list'));

                } else {
                    // Table setup
                    $table->head        = array(get_string('taskitem', 'block_task_list'), get_string('type', 'block_task_list'), get_string('action', 'block_task_list'));
                    $table->align       = array('left', 'left', 'center');
                    $table->width       = '70%';
                    $table->tablealign  = 'center';
                    $table->cellpadding = '5px';
                    $table->cellspacing = '0';
                    $table->data        = array();

                    // Get task IDs
                    $taskids = explode(',', $this->config->taskorder);
                    foreach ($taskids as $taskid) {
                        $task = $this->tasks[$taskid];

                        $url = "$this->baseurl&amp;taskmode=edit&amp;taskid=$taskid&amp;sesskey=".sesskey().'&amp;';

                        $buttons = '<a href="'.$url.'taskaction=edit&amp;type='.$task->type.'">'.
                                   '<img src="'.$CFG->pixpath.'/t/edit.gif" height="11" width="11" border="0" alt="'.get_string('edit').'" /></a>'.
                                   '&nbsp;'.
                                   '<a href="'.$url.'taskaction=delete">'.
                                   '<img src="'.$CFG->pixpath.'/t/delete.gif" height="11" width="11" border="0" alt="'.get_string('delete').'" /></a>'.
                                   '&nbsp;'.
                                   '<a href="'.$this->baseurl.'&amp;taskmode=move&amp;moving='.$taskid.'">'.
                                   '<img src="'.$CFG->pixpath.'/t/move.gif" height="11" width="11" border="0" alt="'.get_string('move').'" /></a>';

                        $table->data[] = array($this->print_name($task, true), get_string($task->type, 'block_task_list'), $buttons);
                    }

                    $output .= $this->print_table($table, true);
                }
                // Add task item drop-down
                $output .= '<div class="addtaskitem">'.
                            get_string('addtaskitem', 'block_task_list').': '.
                            popup_form($this->baseurl.'&amp;taskmode=edit&amp;taskaction=add&amp;type=', $this->get_task_menu(), 'addtaskitem', '', 'choose', '', '', true).
                           '</div>';
                break;

        }

        return $output;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    function make_move() {
        global $CFG;

        $moving = required_param('moving', PARAM_INT);
        $after  = optional_param('after', -1, PARAM_INT);

        $output = print_box(get_string('movingtask', 'block_task_list', $this->print_name($this->tasks[$moving], true)), 'boxaligncenter moving', 'headingmoving', true);

        if (empty($this->tasks)) {
            $this->set_message(get_string('notasksfound', 'block_task_list'));
        } else if ($after != -1 and confirm_sesskey()) {
            $order = explode(',', $this->config->taskorder);

            $neworder = array();
            foreach ($order as $pos => $taskid) {
                // Adding to the top
                if ($after == 0 and $pos == 0) {
                    $neworder[] = $moving;
                }
                if ($taskid == $moving) {
                    // Moving this one, will be added elsewhere
                    continue;
                }
                $neworder[] = $taskid;

                // Adding after a task
                if ($taskid == $after) {
                    $neworder[] = $moving;
                }
            }

            // Implode then update
            $this->config->taskorder = implode(',', $neworder);

            if ($this->instance_config_commit()) {
                $this->set_message(get_string('taskmoved', 'block_task_list'), 'notifysuccess');
            } else {
                $this->set_message(get_string('taskmovefailed', 'block_task_list'));
            }

            $output = $this->make_edit();
        } else {
            // Table setup
            $table->head        = array(get_string('taskitem', 'block_task_list'), get_string('type', 'block_task_list'));
            $table->align       = array('left', 'left');
            $table->width       = '70%';
            $table->tablealign  = 'center';
            $table->cellpadding = '5px';
            $table->cellspacing = '0';
            $table->data        = array();

            $movehere = '<a href="'.$this->baseurl.'&amp;taskmode=move&amp;sesskey='.sesskey().'&amp;moving='.$moving.'&amp;after=%s" title="'.get_string('movehere').'">'.
                        '<img src="'.$CFG->pixpath.'/movehere.gif" height="16" width="80" border="0" alt="'.get_string('movehere').'" /></a>';

            $table->data[] = array(sprintf($movehere, 0), '');

            // Get task IDs
            $taskids = explode(',', $this->config->taskorder);
            foreach ($taskids as $taskid) {
                if ($moving == $taskid) {
                    // Moving this one
                    continue;
                }
                $task = $this->tasks[$taskid];

                $table->data[] = array($this->print_name($task, true), get_string($task->type, 'block_task_list'));
                $table->data[] = array(sprintf($movehere, $taskid), '');
            }

            $output .= $this->print_table($table, true);
        }
        return $output;
    }

    /**
     * Makes the tabs View and Edit
     *
     * @param string $selected The current tab
     * @return string
     **/
    function make_tabs($selected = '') {
        if (!$this->can_edit() && !$this->can_view()) {
            return '';
        }

        if (empty($selected)) {
            // Attempt to set it
            $selected = optional_param('taskmode', '', PARAM_ALPHA);
        }

        $rows = array();
        $row  = array();

        if ($this->can_view()) {
            $row[] = new tabobject('view', "$this->baseurl&amp;taskmode=view", get_string('view', 'block_task_list'));
        }
        if ($this->can_edit()) {
            $row[] = new tabobject('edit', "$this->baseurl&amp;taskmode=edit", get_string('edit', 'block_task_list'));
        }

        $rows[] = $row;

        return print_tabs($rows, $selected, NULL, NULL, true);
    }

    /**
     * Determines if the current user can edit the task list.
     *
     * @uses $USER
     * @param int $userid The ID of the user in question if different from current user
     * @return boolean
     **/
    function can_edit($userid = 0) {
        global $USER;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        return has_capability('block/task_list:manage', get_context_instance(CONTEXT_BLOCK, $this->instance->id), $userid);
    }

    /**
     * Creates a menu array with all the task types
     *
     * @return array array('type' => 'Type name')
     **/
    function get_task_menu() {
        $types = $this->get_task_types();

        $options = array();
        foreach ($types as $type) {
            $options[$type] = get_string($type, 'block_task_list');
        }

        return $options;
    }

    /**
     * Gets the task types
     *
     * @return array
     **/
    function get_task_types() {
        return array('category', 'task');
    }

    /**
     * Sets a message to be printed.  Messages are printed
     * by calling {@link print_messages()}.
     *
     * @uses $SESSION
     * @param string $message The message to be printed
     * @param string $class Usually notifyproblem or notifysuccess.
     * @param string $align Alignment of the message
     * @return boolean
     **/
    function set_message($message, $class="notifyproblem", $align='center') {
        global $SESSION;

        if (empty($SESSION->block_task_list_messages) or !is_array($SESSION->block_task_list_messages)) {
            $SESSION->block_task_list_messages = array();
        }

        $SESSION->block_task_list_messages[] = array($message, $class, $align);

        return true;
    }

    /**
     * Print all set messages.
     *
     * See {@link set_message()} for setting messages.
     *
     * @uses $SESSION
     * @param boolean $return Return output or not
     * @return boolean
     **/
    function print_messages($return = false) {
        global $SESSION;

        if (empty($SESSION->block_task_list_messages)) {
            // No messages to print
            return '';
        }

        $output = '';
        foreach($SESSION->block_task_list_messages as $message) {
            $output .= '<div class="'.$message[1].'" align="'. $message[2] .'">'. clean_text($message[0]) .'</div>'."<br />\n";
        }

        // Reset
        unset($SESSION->block_task_list_messages);

        if ($return) {
            return $output;
        } else {
            echo $output;
            return true;
        }
    }

    /**
     * Runs a task through format_text
     *
     * @param object $task Full task object
     * @param boolean $return Return or print output
     * @return mixed
     **/
    function print_name($task, $return = false) {
        $options = new stdClass;
        $options->para = false;
        $options->noclean = true;
        $output = format_text($task->name, $task->format, $options, $this->course->id);

        if ($return) {
            return $output;
        } else {
            echo $output;
            return true;
        }
    }

    /**
     * Print the table with Intel themeing
     *
     * @param object $table Table object used in print_table()
     * @param boolean $return TRUE = return output; FALSE = print output
     * @return mixed string HTML of the table or true
     **/
    function print_table($table, $return=false) {
        $output = "<div class=\"rounded rounded-generaltable boxalign$table->tablealign\" style=\"width: $table->width;\">
                       <div class=\"hd hd-generaltable\"><div class=\"c\"></div></div>";

        // Save - will restore later
        $align = $table->tablealign;
        $width = $table->width;

        // Want the table to render like so
        $table->tablealign = 'center';
        $table->width      = '100%';

        $output .= print_table($table, true);
        $output .= "    <div class=\"ft ft-generaltable\"><div class=\"c\"></div></div>
                    </div>\n";

        // Restore original settings (PHP 5)
        $table->tablealign = $align;
        $table->width      = $width;

        if ($return) {
            return $output;
        } else {
            echo $output;
            return true;
        }
    }

    /**
     * Enable the block for backup and restore.
     *
     * @return boolean
     **/
    function backuprestore_instancedata_used() {
        return true;
    }

    /**
     * Backup tasks
     *
     * @param resource $bf Backup File
     * @param object $preferences Backup preferences
     * @return boolean
     **/
    function instance_backup($bf, $preferences) {
        $status = true;

        fwrite ($bf,start_tag('TASKS',5,true));

        // Write in all of the tasks
        foreach ($this->tasks as $task) {
            fwrite ($bf,start_tag('TASK',6,true));
            fwrite ($bf,full_tag('ID',7,false,$task->id));
            fwrite ($bf,full_tag('TYPE',7,false,$task->type));
            fwrite ($bf,full_tag('NAME',7,false,$task->name));
            fwrite ($bf,full_tag('CHECKED',7,false,$task->checked));
            fwrite ($bf,full_tag('INFO',7,false,$task->info));
            fwrite ($bf,full_tag('TIMEMODIFIED',7,false,$task->timemodified));
            fwrite ($bf,end_tag('TASK',6,true));
        }

        $status = fwrite ($bf,end_tag('TASKS',5,true));

        return $status;
    }

    /**
     * Allows the block class to restore its backup routine.
     *
     * Should not return false if data (see example below) is empty
     * because old backups would not contain block instance backup data.
     *
     * @param object $restore Standard restore object
     * @param object $instance A block_instance record along with oldid set to the old block_instance record ID.
     * @return boolean
     **/
    function instance_restore($restore, $data) {
        $status = true;

        if (!empty($data->info) and !empty($data->info['TASKS']['0']['#']['TASK'])) {
            $info = $data->info;

            //traverse_xmlize($info);                                   //Debug
            //print_object ($GLOBALS['traverse_array']);                //Debug
            //$GLOBALS['traverse_array']='';                            //Debug

            // Start restore routines here using $info to get backedup data

        /// Get all the tasks and restore them all
            $tasks = $info['TASKS']['0']['#']['TASK'];
            for($i = 0; $i < count($tasks); $i++) {
                $taskinfo = $tasks[$i];

                $task = new stdClass;
                $task->instanceid   = $this->instance->id;
                $task->type         = backup_todb($taskinfo['#']['TYPE']['0']['#']);
                $task->name         = backup_todb($taskinfo['#']['NAME']['0']['#']);
                $task->checked      = backup_todb($taskinfo['#']['CHECKED']['0']['#']);
                $task->info         = backup_todb($taskinfo['#']['INFO']['0']['#']);
                $task->timemodified = backup_todb($taskinfo['#']['TIMEMODIFIED']['0']['#']);

                $oldid = backup_todb($taskinfo['#']['ID']['0']['#']);

                if ($newid = insert_record('block_task_list', $task)) {
                    backup_putid($restore->backup_unique_code, 'block_task_list', $oldid, $newid);
                } else {
                    $status = false;
                    break;
                }
            }

        /// Update taskorder so it references the new task IDs

            $newtaskids = array();
            $oldtaskids = explode(',', $this->config->taskorder);
            foreach ($oldtaskids as $oldtaskid) {
                if ($newtaskid = backup_getid($restore->backup_unique_code, 'block_task_list', $oldtaskid)) {
                    $newtaskids[] = $newtaskid->new_id;
                } else {
                    $status = false;
                    break;
                }
            }

            // Prep for storage and save
            $this->config->taskorder = implode(',', $newtaskids);
            $status = $this->instance_config_commit();
        } else {
            // Nothing to save, so make sure taskorder is blank
            if (!empty($this->config->taskorder)) {
                $this->config->taskorder = '';
                $status = $this->instance_config_commit();
            }
        }

        return $status;
    }

    /**
     * This function makes all the necessary calls to {@link restore_decode_content_links_worker()}
     * function inorder to decode contents of this block from the backup
     * format to destination site/course in order to mantain inter-activities
     * working in the backup/restore process.
     *
     * This is called from {@link restore_decode_content_links()}
     * function in the restore process.  This function is called regarless of
     * the return value from {@link backuprestore_instancedata_used()}.
     *
     * @param object $restore Standard restore object
     * @return boolean
     **/
    function decode_content_links_caller($restore) {
        global $CFG;

        $status = true;

        $sql = "SELECT bi.*
                  FROM {$CFG->prefix}block_instance bi,
                       {$CFG->prefix}block b,
                       {$CFG->prefix}backup_ids ids
                 WHERE b.id = bi.blockid
                   AND b.name = 'task_list'
                   AND ids.new_id = bi.id
                   AND ids.table_name = 'block_instance'
                   AND ids.backup_code = $restore->backup_unique_code";

        if ($instances = get_records_sql($sql)) {
            foreach ($instances as $instance) {
                $block  = block_instance('task_list', $instance);
                $status = $status and $block->decode_content_links_worker($restore);
            }
        }

        return $status;
    }

    /**
     * Assistant to decode_content_links_caller
     *
     * @return boolean
     **/
    function decode_content_links_worker($restore) {
        global $CFG;

        $status = true;

        $i = 0;   // Counter to send some output to the browser to avoid timeouts
        foreach ($this->tasks as $task) {
            //Increment counter
            $i++;
            $content = $task->name;
            $result = restore_decode_content_links_worker($content,$restore);
            if ($result != $content) {
                // Changed, so update record
                $update = new stdClass;
                $update->id   = $task->id;
                $update->name = addslashes($result);
                $status = update_record('block_task_list',$update);
                if (debugging()) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                    }
                }
            }
            //Do some output
            if (($i+1) % 5 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 100 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        }

        return $status;
    }

    /**
     * Return a content encoded to support interactivities linking. This function is
     * called automatically from the backup procedure by {@link backup_encode_absolute_links()}.
     *
     * @param string $content Content to be encoded
     * @param object $restore Restore preferences object
     * @return string The encoded content
     **/
    function encode_content_links($content, $restore) {
        global $CFG;

        $base     = preg_quote($CFG->wwwroot,"/");
        $patterns = array();
        $replaces = array();

        // Links to view.php
        $patterns[] = "/(".$base."\/blocks\/task_list\/view.php\?id\=)([0-9]+)/";
        $replaces[] = '$@BLOCKTASKLISTVIEW*$2@$';

        return preg_replace($patterns, $replaces, $content);
    }

    /**
     * Return content decoded to support interactivities linking.
     * This is called automatically from
     * {@link restore_decode_content_links_worker()} function
     * in the restore process.
     *
     * @param string $content Content to be dencoded
     * @param object $restore Restore preferences object
     * @return string The dencoded content
     **/
    function decode_content_links($content, $restore) {
        global $CFG;

        $result = $content;  // Yes, it is silly

        $searchstring = '/\$@(BLOCKTASKLISTVIEW)\*([0-9]+)@\$/';
        $foundset     = array();

        preg_match_all($searchstring, $result, $foundset);
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            foreach($foundset[2] as $oldcid) {
                // Get new IDs
                $c = backup_getid($restore->backup_unique_code, 'course', $oldcid);

                // Update the searchstring
                $searchstring='/\$@(BLOCKTASKLISTVIEW)\*('.$oldcid.')@\$/';

                if (!empty($c->new_id)) {
                    // It is a link to this course, update the link to its new location
                    $result = preg_replace($searchstring, "$CFG->wwwroot/blocks/task_list/view.php?id=$c->new_id", $result);
                } else {
                    // It's a foreign link so leave it as original
                    $result = preg_replace($searchstring, "$restore->original_wwwroot/blocks/task_list/view.php?id=$oldcid", $result);
                }
            }
        }

        return $result;
    }
}

?>