<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */


/**
* taoresource_plugin_base is the base class for taoresource plugins
*
* This class provides all the functionality for a taoresource plugin that does nothing :-)
* 
* The idea of the plugin is to give access to particular events in the cycle of creating new
* TAO Resources (new resources, NOT the attachment of resources to a course as a course module).
* 
* These events fall into three broad categories - creating a New Resource, updating a Resource,
* and searching for Resources that will be attached to a course as a course module.
* 
* Plugins are subclassed from this class, in a file called plugin.class.php, which must live in
* a directory named after the plugin, and follow a strict naming convention.  For example, the
* two standard plugins provided are, local, and solr.
* local: provides a search interface using the local resource table taoresource_entry.
* solr: provides a simple search interface to an Apache-Solr directory populated with data
* from the taoresource_entry table.
* 
* local lives in the mod/taoresource/plugins/local/plugin.class.php file with a class name of 
* taoresource_plugin_local.
* 
* All plugins are stacked, so you can create several specialised handlers, and have them run one 
* after the other.  If you want the processing of stacked handlers to finish at any stage then 
* return false from your handling method.
* 
* Plugins can be deactivated by system config eg. to deactivate the solr plugin, use:
* $CFG->taoresource_plugin_hide_solr  = 1;
* So it is taoresource_plugin_hide_<plugin name>.
* 
*/

class taoresource_plugin_base {

    /**
    * Constructor for the base taoresource_plugin class
    *
    * This is a stub providing hooks into the create, update
    * and search events on the life of a taoresource_entry
    * instance, and the associated taoresource_metadata instances.
    * taoresource_entry is the table that contains the basic
    * details of a TAO Resource, and the taoresource_metadata table
    * is a flexible structure to maintain an arbitrary set of metadata
    * attributes for a TAO Resource. 
    *
    */
    function taoresource_plugin_base() {

    }

    /**
     * Entry point to modify the search form - add/modify elements
     * here.
     *
     * @param mform   object, reference to Moodle Forms object
     * @return bool, return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function search_definition(&$mform) {

        return true;
    }
    
    /**
     * Entry point to facilitate the search based on search form
     * inputs submitted.  Using the form input values, populate 
     * the $results array with taoresource_entry objects corresponding
     * to what you want to give back to the user.
     *
     * @param fromform   object, reference to Moodle Forms populated
     * values.
     * @param result   array, reference to an array
     * @return bool, return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function search(&$fromform, &$result) {
        
        return true;
    }

    /**
     * Entry point to modify the taoresource_entry_form form - add/modify elements
     * here.
     *
     * @param mform   object, reference to Moodle Forms object
     * @return bool, return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function taoresource_entry_definition(&$mform) {

        return true;
    }

    
    /**
     * Entry point to validate the taoresource_entry_form form.
     * Add your errors to the $errors array, and use $mode to determine
     * if the taoresource_entry is being updated or added new (add == new).
     *
     * @param  data   object, reference to $data as per normal Moodle Forms validations
     * @param  files  object, reference to $files as per normal Moodle Forms validations
     * @param  errors object, reference to $errors as per normal Moodle Forms validations
     * @param  mode   add = new resource being created
     * @return bool,  return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function taoresource_entry_validation($data, $files, &$errors, $mode) {

        return true;
    }
    
    
    /**
     * If this is overriden to return true, then an extra page will appear
     * after the first page of data entry for a taoresource_entry.
     * 
     * You must implement taoresource_entry_extra_definition() to populate
     * this additional screen.
     *
     * @return bool, return true to activate extra screen
     *         false to finish here.
     */
    function taoresource_entry_extra_form_required() {

        return false;
    }

    
    /**
     * Entry point to modify the taoresource_entry_extra_form form - add/modify elements
     * here.
     * 
     * This form is used when it may make sense to have a second screen instead of lumping all data
     * onto one.
     *
     * @param mform   object, reference to Moodle Forms object
     * @return bool, return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function taoresource_entry_extra_definition(&$mform) {

        return true;
    }

    
    /**
     * Entry point to validate the taoresource_entry_extra_form form.
     * Add your errors to the $errors array, and use $mode to determine
     * if the taoresource_entry is being updated or added new (add == new).
     *
     * @param  data   object, reference to $data as per normal Moodle Forms validations
     * @param  files  object, reference to $files as per normal Moodle Forms validations
     * @param  errors object, reference to $errors as per normal Moodle Forms validations
     * @param  mode   add = new resource being created
     * @return bool,  return true to continue to the next handler
     *         false to stop the running of any subsequent plugin handlers.
     */
    function taoresource_entry_extra_validation($data, $files, &$errors, $mode) {

        return true;
    }
    
    
    /**
     * Access to the taoresource_entry object before a new object
     * is saved.  This is a good position to populate the remoteid
     * value after submitting the details to the external CNDP index.
     * 
     * @param taoresource_entry   object, reference to taoresource_entry object
     *        including metadata
     * @return bool, return true to continue to the next handler
     *        false to stop the running of any subsequent plugin handlers.
     */
    function before_save(&$taoresource_entry){
        
        return true;
    }
    
    /**
     * Access to the taoresource_entry object after a new object
     * is saved. 
     * 
     * @param taoresource_entry   object, reference to taoresource_entry object
     *        including metadata
     * @return bool, return true to continue to the next handler
     *        false to stop the running of any subsequent plugin handlers.
     */
    function after_save(&$taoresource_entry){
        
        return true;
    }
    
    /**
     * Access to the taoresource_entry object before an existing object
     * is updated. 
     * 
     * @param taoresource_entry   object, reference to taoresource_entry object
     *        including metadata
     * @return bool, return true to continue to the next handler
     *        false to stop the running of any subsequent plugin handlers.
     */
    function before_update(&$taoresource_entry){
        
        return true;
    }
    
    /**
     * Access to the taoresource_entry object after an existing object
     * is updated. 
     * 
     * @param taoresource_entry   object, reference to taoresource_entry object
     *        including metadata
     * @return bool, return true to continue to the next handler
     *        false to stop the running of any subsequent plugin handlers.
     */
    function after_update(&$taoresource_entry){
        
        return true;
    }
    
}
?>