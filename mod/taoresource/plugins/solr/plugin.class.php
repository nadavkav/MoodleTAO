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
* Extend the base resource class for file resources
*/
define('TAORESOURCE_PLUGIN_CURL_TIMEOUT', 5);
class taoresource_plugin_solr extends taoresource_plugin_base {
    

    function search_definition(&$mform) {
        //search text box
        $mform->addElement('text', 'search', get_string('searchfor', 'taoresource'), array('size'=>'35'));
        return false;
    }
    
    
    function search(&$fromform, &$result) {
        global $CFG, $USER;
        $terms = array('keyword', 'title', 'description');

        // Initialize with the target URL
        $search = trim($fromform->search);
        $lang = strtolower(preg_replace('/\_\w+$/','', $USER->lang));
        foreach ($terms as $term) {
            $search = preg_replace('/'.$term.':\s*/', $term.'_'.$lang.':', $search);
        }
        $url = 'http://localhost:7574/solr/select?q='.$search.'&wt=php';
        
        // is this a complete browse request?
        if ($fromform->search == '*') {
            $url = 'http://localhost:7574/solr/select?q='.urlencode('keyword_'.$lang.':[0 TO zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz]').'&wt=php';
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, TAORESOURCE_PLUGIN_CURL_TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle');
        $code = curl_exec($ch);
        if ($code === false) {
            $this->error[] = curl_errno($ch) .':'. curl_error($ch);
            return false;
        }
        curl_close($ch);
        
        $qr = false;
        if ($code) {
            $code = "\$qr = " . $code . ";";
            eval($code);
        }
        if ($qr && isset($qr['response']) && $qr['response']['numFound'] > 0) {
            $docs = $qr['response']['docs'];
            foreach($docs as $doc) {
                if ($taoresource_entry = taoresource_entry::get_by_id((int)$doc['entry'])) {
                    $result[] = $taoresource_entry;
                }
            }
        }
        return false;
    }
}

?>
