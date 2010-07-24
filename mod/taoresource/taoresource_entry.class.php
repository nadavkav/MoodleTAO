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
* taoresource_entry defines a taoresource including the metadata
*
* This class provides all the functionality for a taoresource
* defined locally or remotely
* 
* A locally defined resource is essentially one where the user has uploaded 
* a file, therefore this local moodle has to serve it.
* 
* A remote resource is one that has a fully qualified URI that does not rely
* on this local Moodle instance to serve the physical data eg. PDF, PNG etc.
* 
* mod/taoresource uses the presence of a $taoresource_entry->file attribute 
* to determine if this resource is hosted locally (the physical file must
* also exist in the course independent repository).
* 
* mod/taoresource uses a course independent file repository.  By default, this
* is located in $CFG->dataroot.TAORESOURCE_RESOURCEPATH where
* TAORESOURCE_RESOURCEPATH is '/taoresources/'.
* 
*/

class taoresource_entry {

    var $taoresource_entry;
    var $metadata_elements;
    var $file;
    var $id;

    
    /**
     * Internal method that processes the plugins for the search
     * interface.
     * 
     * @param criteria   object, reference to Moodle Forms populated
     *        values.
     * @return results, return an array of taoresource_entry objects that
     *         will be formated and displayed in the search results screen.
     */
    static function search (&$criteria) {
        // get the plugins
        $plugins = taoresource_get_plugins();
        $results = array();
        
        // process each plugins search function - there is a default called local
        foreach ($plugins as $plugin) {
            // if we get a positive return then we don't use any more plugins 
            // $results is passed by reference so plugins can doctor the incremental results
            $rc = $plugin->search($criteria, $results);
            if (!$rc) {
                break;
            }
        }
        return $results;
    }


    /**
     * Hydrate a taoresource_entry object reading by identifier
     * 
     * @param identifier   hash, sha1 hash identifier
     * @return taoresource_entry object
     */
    static function read($identifier) {
        global $CFG;
    
        if (! $taoresource_entry = get_record("taoresource_entry", "identifier", "$identifier")) {
            return false;
        }
    
        $taoresource_entry = new taoresource_entry($taoresource_entry);
        return $taoresource_entry;
    }

    /**
     * Hydrate a taoresource_entry object reading by id
     * 
     * @param id   int, internal id of taoresource_entry object
     * @return taoresource_entry object
     */
    static function read_by_id($entry_id) {
    
        if (! $taoresource_entry = get_record("taoresource_entry", "id", "$entry_id")) {
            return false;
        }
    
        $taoresource_entry = new taoresource_entry($taoresource_entry);
        return $taoresource_entry;
    }



    /**
     * Hydrate a taoresource_entry object reading by id
     * 
     * @param id   int, internal id of taoresource_entry object
     * @return taoresource_entry object
     */
    static function get_by_id($entry_id) {
    
        return taoresource_entry::read_by_id($entry_id);
    }


    /**
     * Hydrate a taoresource_entry object reading by identifier
     * 
     * @param identifier   hash, sha1 hash identifier
     * @return taoresource_entry object
     */
    static function get_by_identifier($identifier) {
    
        return $taoresource_entry = taoresource_entry::read($identifier);
    }
    
    
    /**
     * Internal method that processes the plugins for the before save
     * interface.
     * 
     * @return bool, returns true.
     */
    function before_save () {
        // get the plugins
        $plugins = taoresource_get_plugins();
        
        // process each plugins before_save function - there is a default called local
        foreach ($plugins as $plugin) {
            // if we get a positive return then we don't use any more plugins 
            $rc = $plugin->before_save($this);
            if (!$rc) {
                break;
            }
        }
        return true;
    }

    
    /**
     * Internal method that processes the plugins for the after save
     * interface.
     * 
     * @return bool, returns true.
     */
    function after_save () {
        // get the plugins
        $plugins = taoresource_get_plugins();
        
        // process each plugins before_save function - there is a default called local
        foreach ($plugins as $plugin) {
            // if we get a positive return then we don't use any more plugins 
            $rc = $plugin->after_save($this);
            if (!$rc) {
                break;
            }
        }
        return true;
    }

    
    /**
     * Internal method that processes the plugins for the before update
     * interface.
     * 
     * @return bool, returns true.
     */
    function before_update () {
        // get the plugins
        $plugins = taoresource_get_plugins();
        
        // process each plugins before_save function - there is a default called local
        foreach ($plugins as $plugin) {
            // if we get a positive return then we don't use any more plugins 
            $rc = $plugin->before_update($this);
            if (!$rc) {
                break;
            }
        }
        return true;
    }


   /**
     * Internal method that processes the plugins for the after update
     * interface.
     * 
     * @return bool, returns true.
     */
    function after_update () {
        // get the plugins
        $plugins = taoresource_get_plugins();
        
        // process each plugins before_save function - there is a default called local
        foreach ($plugins as $plugin) {
            // if we get a positive return then we don't use any more plugins 
            $rc = $plugin->after_update($this);
            if (!$rc) {
                break;
            }
        }
        return true;
    }
    
    
    /**
    * Constructor for the base taoresource class
    *
    * Constructor for the base taoresource class.
    * If cmid is set create the cm, course, taoresource objects.
    * and do some checks to make sure people can be here, and so on.
    *
    * @param taoresource_entry   object, taoresource_entry object, or table row
    * 
    */
    function taoresource_entry($taoresource_entry = false) {
        global $TAORESOURCE_CORE_ELEMENTS;
        $this->metadata_elements = array();
        if (is_object($taoresource_entry)) {
            foreach ($TAORESOURCE_CORE_ELEMENTS as $key) {
                $this->add_element($key, $taoresource_entry->$key);
            }
            if ($elements = get_records("taoresource_metadata", "entry_id", "$this->id")) {
                foreach ($elements as $element) {
                    $this->add_element($element->element, $element->value, $element->namespace);
                }
            }
        }
    }


    /**
     * set a core taoresource_entry attribute, or add a metadata element (allways appended)
     * 
     * @param element   string, name of taoresource_entry attribute or metadata element
     * @param value     string, value of taoresource_entry attribute or metadata element
     * @param namespace string, namespace of metadata element only
     */
    function add_element($element, $value, $namespace = '') {
        global $TAORESOURCE_CORE_ELEMENTS;
        // add the core ones to the main table entry - everything else goes in the metadata table
        if (in_array($element, $TAORESOURCE_CORE_ELEMENTS) && empty($namespace)) {
            $this->$element = addslashes($value);
        } 
        else {
            $this->metadata_elements []= new taoresource_metadata($this->id, $element, $value, $namespace);
        }
    }
    

    /**
     * access the value of a core taoresource_entry attribute or metadata element
     * 
     * @param element   string, name of taoresource_entry attribute or metadata element
     * @param namespace string, namespace of metadata element only
     * @return string, value of attribute or metadata element
     */
    function element($element, $namespace = '') {
        global $TAORESOURCE_CORE_ELEMENTS;
        if (in_array($element, $TAORESOURCE_CORE_ELEMENTS) && empty($namespace) && isset($this->$element)) {
            return $this->$element;
        } 
        else {
            foreach ($this->metadata_elements as $el) {
                if ($el->element == $element && $el->namespace == $namespace) {
                    return $el->value;
                }
            }
        }
        return false;
    }
    


    /**
     * amend a core taoresource_entry attribute, or metadata element - if metadata element
     * is not found then it is appended.
     * 
     * @param element   string, name of taoresource_entry attribute or metadata element
     * @param value     string, value of taoresource_entry attribute or metadata element
     * @param namespace string, namespace of metadata element only
     */
    function update_element($element, $value, $namespace = '') {
        global $TAORESOURCE_CORE_ELEMENTS;
        // add the core ones to the main table entry - everything else goes in the metadata table
        if (in_array($element, $TAORESOURCE_CORE_ELEMENTS) && empty($namespace) && !empty($value)) {
            $this->$element = addslashes($value);
        } 
        else {
            $location = false;
            foreach ($this->metadata_elements as $key => $el) {
                if ($el->element == $element && $el->namespace == $namespace) {
                    $location = $key;
                    break;
                }
            }
            if ($location !== false) {
                $this->metadata_elements[$location] = new taoresource_metadata($this->id, $element, $value, $namespace);
            }
            else {
                $this->metadata_elements []= new taoresource_metadata($this->id, $element, $value, $namespace);
            }
        }
    }
    

    /**
     * check if resource is local or not
     * 
     * @return bool, true = local
     */
    function is_local_resource() {
        global $CFG;

        if (isset($this->file) && $this->file) {
            $filename = $CFG->dataroot.TAORESOURCE_RESOURCEPATH.$this->file;
            if (is_file($filename)) {
                return true;
            }
        }
        return false;
    }

    
    /**
     * check if resource is remote or not
     * 
     * @return bool, true = remote
     */
    function is_remote_resource() {
        
        return ! $this->is_local_resource();
    }
    
    /**
     * Commit the new TAO resource to the database
     * 
     * @return bool, true = success
     */
    function add_instance() {
    // Given an object containing all the necessary data,
    // (defined by the form in mod.html) this function
    // will create a new instance and return the id number
    // of the new instance.
    
        global $CFG;

        // is this a local resource or a remote one?
        if (!empty($this->url) && empty($this->file)) {
            $this->identifier = sha1($this->url);
            $this->mimetype = mimeinfo("type", $this->url);
        }
        
        if ( isset($this->url) && $this->url && !$this->is_local_resource()) {
            $this->file = '';
        }
        else if (empty($this->url) && isset($this->file) && $this->file) {
            if (!taoresource_check_and_create_moddata_taoresource_dir()) {
                // error - can't create resources temp dir
                error("Error - can't create TAO resources dir");
            }
            
            $filename = $CFG->dataroot.TAORESOURCE_RESOURCEPATH.$this->file;
            if (!taoresource_copy_file($this->tempfilename, $filename, true)) {
                error("Error - can't copy temporary resource file ({$this->tempfilename}) to resource path ($filename)");
            }
            
            $this->url = $CFG->wwwroot.'/mod/taoresource/view.php?identifier='.$this->identifier;
            
            // tidy up temp file
            if (!taoresource_delete_file($this->tempfilename)) {
                error("Error - can't delete temporary resource file ({$this->tempfilename})");
            }
        }

        // one way or another we must have a URL by now
        if (!$this->url) {
            error("Tried to create a TAO Resource without a URL");
        }
        
        // trigger the before save plugins
        $this->before_save();
    
        $this->timemodified = time();
        if (!$id = insert_record('taoresource_entry', $this)) {
            return false;
        }
        $this->id = $id;
        
        // now do the LOM metadata elements
        foreach ($this->metadata_elements as $element) {
            $element->entry_id = $id;
            if (! $element->add_instance()) {
                return false;
            }
        }
        
        // trigger the after save plugins
        $this->after_save();
        
        return true;
    }


    /**
     * Commit the updated TAO resource to the database
     * 
     * @return bool, true = success
     */
    function update_instance() {
    // Given an object containing all the necessary data,
    // (defined by the form in mod.html) this function
    // will update an existing instance with new data.

        $this->timemodified = time();
        
        // trigger the before save plugins
        $this->before_update();
        
        // remove and recreate metadata records
        if (! delete_records('taoresource_metadata', 'entry_id', "$this->id")) {
            return false;
        }
        foreach ($this->metadata_elements as $element) {
            $element->add_instance();
        }
        if (! update_record('taoresource_entry', $this)) {
            return false;
        }
        
        // trigger the after save plugins
        $this->after_update();
        
        return true;
    }


    /**
     * delete the current TAO resource from the database, and
     * any locally attached files.
     * 
     * @return bool, true = success
     */
    function delete_instance() {
    // Given an object containing the taoresource data
    // this function will permanently delete the instance
    // and any data that depends on it, including local file.

        if (! delete_records('taoresource_metadata', 'entry_id', "$this->id")) {
            return false;
        }
        
        if ($this->is_local_resource()) {
            $filename = $CFG->dataroot.TAORESOURCE_RESOURCEPATH.$this->file;
            if (!taoresource_delete_file($filename)) {
                error("Error - can't delete resource file ({$filename})");
            }
        }
        
        if (! delete_records('taoresource_entry', 'id', "$this->id")) {
            return false;
        }
        return true;
    }
}
?>