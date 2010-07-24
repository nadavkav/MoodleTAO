/* // $Id: styles.php,v 1.1.1.1 2006/10/13 02:55:43 mark-nielsen Exp $ */
/**
 * Style Sheet for task list
 * 
 * @author Mark Nielsen
 * @version $Id: styles.php,v 1.1.1.1 2006/10/13 02:55:43 mark-nielsen Exp $
 * @package block_task_list
 **/

/* Add task drop-down */
.block_task_list .addtaskitem {
    text-align: center;
    padding: 10px;
}

/* When viewing tasks */
.block_task_list .tasklayout {
    width: 100%;
}

.block_task_list .savechanges {
    text-align: left;
}

.block_task_list .taskheading {
    font-weight: normal;
    margin: 0px;
}

.block_task_list .tasklistheading {
    margin: 3px 0px 3px 0px;
}

.block_task_list .taskinstructions {
    margin: 10px 0px 10px 0px;
}

.block_task_list td.displaytasklist {
    vertical-align: top;
    text-align: left;
}

/* Editing a task */
.block_task_list .taskedit h2,
.block_task_list .taskedit p {
    text-align: center;
}

/* Navigation */
.block_task_list .navtitle {
    white-space: nowrap;
    font-weight: bold;
}
.block_task_list td.tasknav {
    vertical-align: top;
    text-align: left;
}

/* Moving a task */
.block_task_list .moving {
    text-align: center;
    font-weight: bold;
    font-size: 1.2em;
    margin-bottom: 1em;
}