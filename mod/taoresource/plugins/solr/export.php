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
require_once("../../lib.php");

if ($entries = get_records("taoresource_entry")) {
    print "<?xml version='1.0'?>\n";
    print "<add>\n";
    foreach ($entries as $e) {
       $entry = new taoresource_entry($e);
       $issuedate = '0000-00-00T00:00:00.000Z';
       if ($entry->element('IssueDate')) {
       //    $issuedate = date("Y-m-d\TH:i:s.000\Z", $entry->element('IssueDate'));
           $issuedate = $entry->element('IssueDate');
       }

       print "    <doc>\n";
       print '        <field name="catalogentry">'.$entry->identifier.'</field>'."\n";
       print '        <field name="catalog">'.$entry->remoteid.'</field>'."\n";
       print '        <field name="entry">'.$entry->id.'</field>'."\n";
       print '        <field name="title_en">'.preg_replace('/\>/', '&gt;', preg_replace('/\</', '&lt;', preg_replace('/\&/', '&amp;', $entry->title))).'</field>'."\n";
       print '        <field name="language">'.$entry->lang.'</field>'."\n";
       print '        <field name="description_en">'.preg_replace('/\>/', '&gt;', preg_replace('/\</', '&lt;', preg_replace('/\&/', '&amp;', $entry->description))).'</field>'."\n";
       print '        <field name="keyword_en">'.$entry->keywords.'</field>'."\n";
       print '        <field name="contributor">'.($entry->element('Contributor') || '').'</field>'."\n";
       print '        <field name="issuedate">'.$issuedate.'</field>'."\n";
       print '        <field name="agefrom">10</field>'."\n";
       print '        <field name="ageto">80</field>'."\n";
       print '        <field name="format">'.$entry->mimetype.'</field>'."\n";
       print '        <field name="file">'.$entry->file.'</field>'."\n";
       print '        <field name="location">'.preg_replace('/\&/', '&amp;', $entry->url).'</field>'."\n";
       print '        <field name="learningresourcetype">LOMv1.0</field>'."\n";
       print '        <field name="rights">'.($entry->element('Rights') || '').'</field>'."\n";
       print '        <field name="rightsdescription">'.($entry->element('RightsDescription') || '').'</field>'."\n";
       print '        <field name="classificationpurpose">a-purpose</field>'."\n";
       print '        <field name="classificationtaxonpath">a-taxon-path</field>'."\n";
       print "    </doc>\n";
    }
    print "</add>\n";
}

exit(0);
?>
