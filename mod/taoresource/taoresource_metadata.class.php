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
* taoresource_metadata defines a taoresource_metadata element
*
* This class provides all the functionality for a taoresource_metadata
* You dont really need to be here, as this is managed through the 
* taoresource_entry object.
*/
class taoresource_metadata {

    var $element;
    var $namespace;
    var $value;
    var $entry_id;

    /**
    * Constructor for the taoresource_metadata class
    */
    function taoresource_metadata($entry_id, $element, $value, $namespace = '') {
        $this->entry_id = $entry_id;
        $this->element = $element;
        $this->namespace = $namespace;
        $this->value = addslashes($value);
    }


    function add_instance() {
        return insert_record('taoresource_metadata', $this);
    }

}
?>