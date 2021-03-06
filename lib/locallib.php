<?php
/**
 * This file provides hooks from moodle core into custom code.
 *
 * Important note
 * --------------
 *
 * If at all possible, the facilities provided here should not be used.
 * Wherever possible, customisations should be written using one of the
 * standard plug-in points like modules, blocks, auth plugins, themes, ...
 *
 * However, sometimes that is just not possible, because of the nature
 * of the change you want to make. In which case the second best plan is
 * to implement your feature in a generally useful way, which can
 * be contributed back to the moodle project so that everyone benefits.
 *
 * But supposing you are forced to implement some nasty hack that only
 * you will ever want, then the local folder is for you. The idea is that
 * instead of scattering your changes throughout the code base, you
 * put them all in a folder called 'local'. Then you won't have to
 * deal with merging problems when you upgrade the rest of your moodle
 * installation.
 *
 *
 * Available hooks
 * ===============
 *
 * These are similar to the module interface, however, not all the the
 * facilities that are available to modules are available to local code (yet).
 * See also http://docs.moodle.org/en/Development:Local_customisation for more
 * information.
 *
 *
 * Local database customisations
 * -----------------------------
 *
 * If your local customisations require changes to the database, use the files:
 *
 * local/version.php
 * local/db/upgrade.php
 *
 * In the file version.php, set the variable $local_version to a versionstamp
 * value like 2006030300 (a concatenation of year, month, day, serial).
 *
 * In the file upgrade.php, implement the
 * function xmldb_local_upgrade($oldversion) to make the database changes.
 *
 * Note that you don't need to have an install.xml file. Instead,
 * when your moodle instance is first installed, xmldb_local_upgrade() will be called
 * with $oldversion set to 0, so that all the updates run.
 *
 * Local capabilities
 * ------------------
 *
 * If your local customisations require their own capabilities, use
 * 
 * local/db/access.php
 *
 * You should create an array called $local_capabilities, which looks like:
 * 
 * $local_capabilities = array(
 *         'moodle/local:capability' => array(
 *         'captype' => 'read',
 *         'contextlevel' => CONTEXT_SYSTEM,
 *      ),
 * );
 *
 * Note that for all local capabilities you add, you'll need to add language strings.
 * Moodle will expect to find them in local/lang/en_utf8/local.php (eg for English)
 * with a key (following the above example) of local:capability
 * See the next section for local language support.
 *
 *
 * Local language support
 * ----------------------
 *
 * Moodle already supports local overriding of any language strings, or the
 * creation of new strings. Just create a folder lang/XX_utf8_local where XX is
 * a language code. Any language files you put in there will be used before
 * the standard files. So, for example, can can create a file
 * lang/en_utf8_local/moodle.php containing
 *   $strings['login'] = 'Sign in';
 * and that will change the string 'Login' to 'Sign in'. (See also
 * http://docs.moodle.org/en/Language_editing for another way to achieve this.)
 *
 * This mechanism can also be used to create completely new language files. For
 * example, suppose you have created some code in local/myfeature.php that needs
 * some language strings. You can put those strings in the file
 * lang/en_utf8_local/local_myfeature.php, and then access then using
 * get_string('mystring', 'local_myfeature'). (Note that you do not have to call
 * the file local_myfeature.php, you can call it anything you like, however,
 * the convention of calling lang files for local/ code local_somthing is recommended.)
 *
 * In addition, there is one other mechanism that is available. Strings from the
 * 'local' langauge file (for example get_string('mystring', 'local') will be
 * found if the string is found in the file local/lang/en_utf8/local.php. Since
 * the lang file 'local' is used for a lot of things like capability names, you
 * can use this file for things like that.
 *
 *
 * Local admin menu items
 * ----------------------
 *
 * It is possible to add new items to the admin_tree block.  
 * To do this, create a file, local/settings.php
 * which can access the $ADMIN variable directly and add things to it.
 * You might do something like:
 * $ADMIN->add('root', new admin_category($name, $title);
 * $ADMIN->add('foo', new admin_externalpage($name, $title, $url, $cap);
 *
 *
 * Course deletion
 * ---------------
 *
 * To have your local customisations notified when a course is deleted,
 * make a file called
 *
 * local/lib.php
 *
 * In there, implement the function local_delete_course($courseid). This
 * function will then be called whenever the functions remove_course_contents()
 * or delete_course() from moodlelib are called.
 *
 * Course backup and restore
 * -------------------------
 *
 * If you've added some extra course-level information,
 * you can add hooks to run local code during both backup & restore.
 * To have this code run, create the file called local/lib.php
 * Inside, implement these three functions:
 *
 * --- begin function skeleton --
 * implement the local hook to run some custom code at course backup time.
 * see lib/locallib.php
 *
 * @param filehandle $bf open file handle for writing to
 * @param stdclass object $course course we're backing up
 * @param integer $startlevel level we're currently using
 *
 * @return boolean
 * function local_course_backup($bf, $course, $startlevel) {
 *     // write out any extra XML you want using the standard start_tag, full_tag and end_tag methods.
 * }
 * --- end function skeleton --
 *
 * --- begin function skeleton --
 * implement the local hook to run some custom code at course restore time.
 * see lib/locallib.php
 *
 * @param object $parser      moodleparser object, passed by reference
 * @param string $tagname     current tag being read
 * @param int    $level       current depth of XML where LOCAL tag is found +1
 * @param string $errorstring string to fill with errors
 *
 * @return boolean
 * function local_course_restore_getdata(&$parser, $tagname, $level, &$errorstr) {
 *     // using the parser methods, cache in the $parser->info object somewhere the result of reading each tag.
 *     // you may need to employ dirty hack methods to keep track of where you are.
 * }
 * --- end function skeleton --
 *
 * --- begin function skeleton --
 * implement the local hook to run some custom code at course restore time.
 * see lib/locallib.php
 *
 * @param int $courseid the id of the course to restore into
 * @param stdclass $data the data the parser has created
 *
 * @return boolean
 * function local_course_restore_createdata($courseid, $data) {
 *     // insert data to the database.
 * }
 * --- end function skeleton --
 *
 */

/**
 * This function checks to see whether local database customisations are up-to-date
 * by comparing $CFG->local_version to the variable $local_version defined in
 * local/version.php. If not, it looks for a function called 'xmldb_local_upgrade'
 * in a file called 'local/db/upgrade.php', and if it's there calls it with the
 * appropiate $oldversion parameter. Then it updates $CFG->local_version.
 * On success it prints a continue link. On failure it prints an error.
 *
 * @uses $CFG
 * @uses $db to do something really evil with the debug setting that should probably be eliminated. TODO!
 * @param string $continueto a URL passed to print_continue() if the local upgrades succeed.
 */
function upgrade_local_dbs($continueto) {

    global $CFG, $db;

    $path = '/local';
    $pat = 'local';
    $status = true;
    $changed = false;
    $firstloop = true;

    while(is_dir($CFG->dirroot.$path)){
        // if we don't have code version or a db upgrade file, check lower
        if (!file_exists($CFG->dirroot."{$path}/version.php") || !file_exists($CFG->dirroot."{$path}/db/upgrade.php")){
            $path .= '/local';
            $pat .= 'local';
            continue;
        }

        require_once ($CFG->dirroot ."{$path}/version.php");  // Get code versions

        $cfgvarname = "{$pat}_version";

        if (empty($CFG->{$cfgvarname})) { // normally we'd install, but just replay all the upgrades.
            $CFG->{$cfgvarname} = 0;
        }

        $localversionvar = "{$pat}_version";

        // echo "($localversionvar) ".$$localversionvar." > ($cfgvarname) ".$CFG->{$cfgvarname}."<br/>";

        if ($$localversionvar > $CFG->{$cfgvarname}) { // something upgrades!

            upgrade_log_start();
            /// Capabilities
            /// do this first *instead of* last , so that the installer has the chance to locally assign caps
            if (!update_capabilities($pat)) {
                error('Could not set up the capabilities for ' . $pat . '!');
            }

            if ($firstloop){
                $strdatabaseupgrades = get_string('databaseupgrades');
                print_header($strdatabaseupgrades, $strdatabaseupgrades,
                    build_navigation(array(array('name' => $strdatabaseupgrades, 'link' => null, 'type' => 'misc'))), '', upgrade_get_javascript());
                $firstloop = false;
            }
            $changed = true;

            require_once ($CFG->dirroot ."{$path}/db/upgrade.php");

            $db->debug = true;
            $upgradefunc = "xmldb_{$pat}_upgrade";
            if ($upgradefunc($CFG->{$cfgvarname})) {
                $db->debug = false;
                if (set_config($localversionvar, $$localversionvar)) {
                    notify(get_string('databasesuccess'), 'notifysuccess');
                    notify(get_string('databaseupgradelocal', '', $path.' >> '.$$localversionvar), 'notifysuccess');
                } else {
                    $status = false;
                    error('Upgrade of local database customisations failed in $path! (Could not update version in config table)');
                }
            } else {
                $db->debug = false;
                error("Upgrade failed!  See {$path}/version.php");
            }

            if (!events_update_definition($pat)) {
                error('Could not set up the events definitions for ' . $pat . '!');
            }
            upgrade_log_finish();

        } else if ($$localversionvar < $CFG->{$cfgvarname}) {
            notify("WARNING!!!  The local version you are using in $path is OLDER than the version that made these databases!");
        }


        $path .= '/local';
        $pat .= 'local';
    }

    if ($changed){
        print_continue($continueto);
        print_footer('none');
        exit;
    }
}

/**
 * Notify local code that a course is being deleted.
 * Look for a function local_delete_course() in a file called
 * local/lib.php andn call it if it is there.
 *
 * @param int $courseid the course that is being deleted.
 * @param bool $showfeedback Whether to display notifications on success.
 * @return false if local_delete_course failed, or true if
 *          there was noting to do or local_delete_course succeeded.
 */
function notify_local_delete_course($courseid, $showfeedback) {
    global $CFG;
    $localfile = $CFG->dirroot .'/local/lib.php';
    if (file_exists($localfile)) {
        require_once($localfile);
        if (function_exists('local_delete_course')) {
            if (local_delete_course($courseid)) {
                if ($showfeedback) {
                    notify(get_string('deleted') . ' local data');
                }
            } else {
                return false;
            }
        }
    }
    return true;
}
?>
