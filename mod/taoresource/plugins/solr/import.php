<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

require('../../../../config.php');
require("../../lib.php");
require($CFG->libdir.'/filelib.php');

include ("Console/Getopt.php"); 
$cg = new Console_Getopt();
$allowedShortOptions = "l:f:";
$args = $cg->readPHPArgv();
$ret = $cg->getopt($args, $allowedShortOptions);
if (PEAR::isError($ret)) {
    die ("Error in command line: " . $ret->getMessage() . "\n otpions are: -f<filename> -l<lang>");
}

// set the defaults
$lang = 'en';
$file = "/home/piers/code/lucene/apache-solr-1.3.0/imsrepo/exampledocs/tao.xml";

// now parse the options array
$opts = $ret[0];
if (sizeof($opts) > 0) {
    foreach ($opts as $o) {
        switch ($o[0]) {
            case 'l':
                $lang = $o[1];
                break;
            case 'f':
                $file = $o[1];
                break;
        }
    }
}

$elements = array("title_{$lang}" => 'title', 
       'catalogentry' => 'identifier',
       'catalog' => 'remoteid',
       'language' => 'lang',
       "description_{$lang}" => 'description',
       "keyword_{$lang}" => 'keywords',
       'contributor' => 'Contributor',
       'issuedate' => 'IssueDate',
       'agefrom' => 'AgeFrom',
       'ageto' => 'AgeTo',
       'format' => 'mimetype',
       'location' => 'url',
       'file' => 'file',
       'learningresourcetype' => 'LearningResourceType',
       'rights' => 'Rights',
       'rightsdescription' => 'RightsDescription',
       'classificationpurpose' => 'ClassificationPurpose',
       'classificationtaxonpath' => 'ClassificationTaxonPath' );


$xml = simplexml_load_file($file);

if ($result = $xml->xpath("doc")) {
    foreach ($result as $doc) {
        // check if the entry exists
        $exists = false;
        if ($el = $doc->xpath("field[@name='catalogentry']/text()")) {
            $identifier = $el[0];
        }
        if (!$entry = taoresource_entry::get_by_identifier($identifier)) {
            $entry = new taoresource_entry();
        } 
        else { 
            $entry->metadata_elements = array();
            $exists = true;
        }
        $entry->add_element('type', 'file');
        foreach ($elements as $key => $element) {
            if ($el = $doc->xpath("field[@name='{$key}']/text()")) {
                $entry->add_element($element, $el[0]);
            }
        }
        if ($exists) {
            $entry->update_instance();
        }
        else {
            $entry->add_instance();
        }
    }
}

exit(0);
?>
