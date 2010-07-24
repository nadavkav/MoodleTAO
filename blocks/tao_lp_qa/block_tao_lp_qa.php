<?PHP

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   blocks-lp_qa
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * this block is a slightly modified version of the html block
 */
 
class block_tao_lp_qa extends block_base {

    function init() {
        $this->title = get_string('blocktitle', 'block_tao_lp_qa');
        $this->version = 2009060500;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('newqa', 'block_tao_lp_qa'));
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!empty($this->instance->pinned) or $this->instance->pagetype === 'course-view') {
            // fancy html allowed only on course page and in pinned blocks for security reasons
            $filteropt = new stdClass;
            $filteropt->noclean = true;
        } else {
            $filteropt = null;
        }

        $this->content = new stdClass;
        $this->content->text = isset($this->config->text) ? format_text($this->config->text, FORMAT_HTML, $filteropt) : '';
        $this->content->footer = '';

        unset($filteropt); // memory footprint

        return $this->content;
    }

    /**
     * Will be called before an instance of this block is backed up, so that any links in
     * any links in any HTML fields on config can be encoded.
     * @return string
     */
    function get_backup_encoded_config() {
        /// Prevent clone for non configured block instance. Delegate to parent as fallback.
        if (empty($this->config)) {
            return parent::get_backup_encoded_config();
        }
        $data = clone($this->config);
        $data->text = backup_encode_absolute_links($data->text);
        return base64_encode(serialize($data));
    }

    /**
     * This function makes all the necessary calls to {@link restore_decode_content_links_worker()}
     * function in order to decode contents of this block from the backup 
     * format to destination site/course in order to mantain inter-activities 
     * working in the backup/restore process. 
     * 
     * This is called from {@link restore_decode_content_links()} function in the restore process.
     *
     * NOTE: There is no block instance when this method is called.
     *
     * @param object $restore Standard restore object
     * @return boolean
     **/
    function decode_content_links_caller($restore) {
        global $CFG;

        if ($restored_blocks = get_records_select("backup_ids","table_name = 'block_instance' AND backup_code = $restore->backup_unique_code AND new_id > 0", "", "new_id")) {
            $restored_blocks = implode(',', array_keys($restored_blocks));
            $sql = "SELECT bi.*
                      FROM {$CFG->prefix}block_instance bi
                           JOIN {$CFG->prefix}block b ON b.id = bi.blockid
                     WHERE b.name = 'tao_lp_qa' AND bi.id IN ($restored_blocks)"; 

            if ($instances = get_records_sql($sql)) {
                foreach ($instances as $instance) {
                    $blockobject = block_instance('tao_lp_qa', $instance);
                    $blockobject->config->text = restore_decode_absolute_links($blockobject->config->text);
                    $blockobject->config->text = restore_decode_content_links_worker($blockobject->config->text, $restore);
                    $blockobject->instance_config_commit($blockobject->pinned);
                }
            }
        }

        return true;
    }

    function instance_config_save($data, $pinned=false, $updaterafl=true) {
        global $CFG;

        // if in rafl mode then attempt to update the rafl tables
        if ($CFG->raflmodeenabled && $updaterafl) {

            $course = get_record('course', 'id', $this->instance->pageid);
       
            if ($course->learning_path_mode == LEARNING_PATH_MODE_RAFL) {
    
                // look up raflitem id
                if ($pageitem = get_record('format_page_items', 'blockinstance', $this->instance->id)) {
                    require_once($CFG->dirroot.'/mod/rafl/locallib.php');
                    $rafl = new localLibRafl();

                    $rafl->update_share_item($course->id, $pageitem->rafl_item, $data->text); 

                } else {
                    error_log('no matching rafl item could be found');
                }

            }

        }

        // And now forward to the default implementation defined in the parent class
        return parent::instance_config_save($data);
    }
}
?>
