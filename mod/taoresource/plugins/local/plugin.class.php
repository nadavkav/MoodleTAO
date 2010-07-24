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
class taoresource_plugin_local extends taoresource_plugin_base {

    function search_definition(&$mform) {
        //search text box
        $mform->addElement('text', 'search', get_string('searchfor', 'taoresource'), array('size'=>'35'));

        //checkboxes to choose search scope
        $searchin   = array();
        $searchin[] = &MoodleQuickForm::createElement('checkbox', 'title',          '', get_string('title', 'taoresource'));
        $searchin[] = &MoodleQuickForm::createElement('checkbox', 'keywords',       '', get_string('keywords', 'taoresource'));
        $searchin[] = &MoodleQuickForm::createElement('checkbox', 'description',    '', get_string('description', 'taoresource'));
        $mform->addGroup($searchin, 'searchin', get_string('searchin', 'taoresource'), array(' '), false);
        //set defaults
        $mform->setDefault('title',         1);
        $mform->setDefault('keywords',      1);
        $mform->setDefault('description',   1);
        
        return false;
    }
    
    
    function search(&$fromform, &$result) {
        global $CFG;
        $fromform->title = isset($fromform->title) ? true : false;
        $fromform->description = isset($fromform->description) ? true : false;
        $fromform->keywords = isset($fromform->keywords) ? true : false;
        
        // if the search criteria is left blank then this is a complete browse
        if ($fromform->search == '') {
            $fromform->search = '*';
        }
        if ($fromform->section == 'block') {
            $fromform->title = true;
            $fromform->description = true;
            $fromform->keywords = true;
        }
        
        $searchterms = explode(" ", $fromform->search);    // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            }
        }
        
        // no valid search terms so lets just open it up
        if (count($searchterms) == 0) {
            $searchterms[]= '%';
        }
        $search = trim(implode(" ", $searchterms));
        
        //to allow case-insensitive search for postgesql
        if ($CFG->dbfamily == 'postgres') {
            $LIKE = 'ILIKE';
            $NOTLIKE = 'NOT ILIKE';   // case-insensitive
            $REGEXP = '~*';
            $NOTREGEXP = '!~*';
        } else {
            $LIKE = 'LIKE';
            $NOTLIKE = 'NOT LIKE';
            $REGEXP = 'REGEXP';
            $NOTREGEXP = 'NOT REGEXP';
        }
        
        $titlesearch        = '';
        $descriptionsearch  = '';
        $keywordsearch      = '';
        
        foreach ($searchterms as $searchterm) {
            if ($titlesearch) {
                $titlesearch .= ' AND ';
            }
            if ($descriptionsearch) {
                $descriptionsearch .= ' AND ';
            }
            if ($keywordsearch) {
                $keywordsearch .= ' AND ';
            }
        
            if (substr($searchterm,0,1) == '+') {
                $searchterm          = substr($searchterm,1);
                $titlesearch        .= " title $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                $descriptionsearch  .= " description $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                $keywordsearch      .= " keywords $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            } else if (substr($searchterm,0,1) == "-") {
                $searchterm          = substr($searchterm,1);
                $titlesearch        .= " title $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                $descriptionsearch  .= " description $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                $keywordsearch      .= " keywords $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            } else {
                $titlesearch        .= ' title '.       $LIKE .' \'%'. $searchterm .'%\' ';
                $descriptionsearch  .= ' description '. $LIKE .' \'%'. $searchterm .'%\' ';
                $keywordsearch      .= ' keywords '.    $LIKE .' \'%'. $searchterm .'%\' ';
            }
        
        }
        
        $selectsql  = '';
        $selectsqlor  = '';
        $selectsql .= $CFG->prefix .'taoresource_entry WHERE (';
        $selectsqlor    = '';
        if($fromform->title && $search){
            $selectsql     .= $titlesearch;
            $selectsqlor    = ' OR ';
        }
        
        if($fromform->description && $search){
            $selectsql     .= $selectsqlor.$descriptionsearch;
            $selectsqlor    = ' OR ';
        }
        
        if ($fromform->keywords && $search){
            $selectsql     .= $selectsqlor.$keywordsearch;
        }
        
        $selectsql .= ')';
        
        $sort = "title ASC";
        $page = '';
        $recordsperpage = TAORESOURCE_SEARCH_LIMIT;
        
        if ($fromform->title || $fromform->description || $fromform->keywords) {
            // when given a complete wildcard, then this is browse mode
            if ($fromform->search == '*') {
                $resources =  get_records('taoresource_entry', '', '', $sort);
            }
            else {
                $resources = get_records_sql('SELECT * FROM '. $selectsql .' ORDER BY '. $sort, $page, $recordsperpage);
            }
        }
        // append the results
        if (!empty($resources)) {
            foreach ($resources as $resource) {
              $result []= new taoresource_entry($resource);
            }
        }
        return false;
    }
}

?>
