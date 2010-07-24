<?php // $Id: pagediff.php,v 1.2.10.1 2008/03/23 09:36:06 julmis Exp $

    /*
     * TODO:
     * This page is still heavily under construction and needs more
     * testing, alot of testing....
     */

    require('../config.php');
    require('cmslocallib.php');

    $versionid   = required_param('id',  PARAM_INT);
    $courseid = required_param('course',  PARAM_INT);

    if ( !$course = get_record("course", "id", $courseid) ) {
        error("Invalid course id!!!");
    }

    require_login($course->id);

    if ( !confirm_sesskey() ) {
        error("Session key error!!!");
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    require_capability('format/cms:editpage', $context);

    print_header();

    if ( $selected = get_record("cmspages_history", "id", $versionid) ) {
        $preversion = floatval($selected->version) - 0.1;
        if ( $preversion == 1  ) {
            $preversion = '1.0';
        }

        if ( $previous = get_record("cmspages_history", "pageid", $selected->pageid,
                                    "version", "$preversion") ) {
            $diff = PHPDiff($previous->content, $selected->content);
        }
    }

    $strversion = get_string('version');

    if ( !empty($diff) ) {
        error_reporting(0);
        $oldfile = explode("\n", cms_format_html($previous->content, false));
        $newfile = explode("\n", cms_format_html($selected->content, false));

        $difflines = explode("\n", $diff);

        echo '<table border="1" cellpadding="4" width="100%">';
        echo '<tr><td width="50%">'. $strversion . ': '. $preversion .'</td>';
        echo '<td width="50%">'. $strversion .': '. $selected->version .'</td></tr>';

        foreach ( $difflines as $line ) {
            preg_match("/^([0-9\,]+)([a|c|d])([0-9\,]+)$/i", $line, $match);
            $out = $match[1];
            $status = $match[2];
            $in = $match[3];

            switch ( $status ) {
                case 'a': // Added lines
                    echo '<tr valign="top">';
                    echo '<td width="50%" style="background-color: #f5f5f5; font-family: monospace;">';

                    list($oldstart, $oldend) = split(",", $out);
                    list($newstart, $newend) = split(",", $in);
                    if ( !empty($oldend) ) { // It's a range of lines
                        $start = $oldstart - 1;
                        $end   = $oldend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($oldfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else { // single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $oldstart .'.</td>';
                        echo '<td>'. htmlentities($oldfile[$oldstart]) .'</td>';
                        echo '</tr></table>';
                    }

                    echo '</td>';
                    echo '<td width="50%" style="background-color: #009933; font-family: monospace;">';

                    if ( !empty($newend) ) { // It's range of lines.
                        $start = $newstart - 1;
                        $end = $newend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($newfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';

                    } else { // Single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $newstart .'.</td>';
                        echo '<td>'. htmlentities($newfile[($newstart - 1)]) .'</td>';
                        echo '</tr></table>';
                    }
                    echo '</td>';
                    echo '</tr>';
                break;
                case 'c': // Changed lines
                    echo '<tr valign="top">';
                    echo '<td width="50%" style="background-color: #f5f5f5; font-family: monospace;">';

                    list($oldstart, $oldend) = split(",", $out);
                    list($newstart, $newend) = split(",", $in);
                    if ( !empty($oldend) ) { // It's a range of lines
                        $start = $oldstart - 1;
                        $end   = $oldend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($oldfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else { // single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $oldstart .'.</td>';
                        echo '<td>'. htmlentities($oldfile[($oldstart - 1)]) .'</td>';
                        echo '</tr></table>';
                    }

                    echo '</td>';
                    echo '<td width="50%" style="background-color: #fff999; font-family: monospace;">';

                    if ( !empty($newend) ) { // It's range of lines.
                        $start = $newstart - 1;
                        $end = $newend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($newfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';

                    } else { // Single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $newstart .'.</td>';
                        echo '<td>'. htmlentities($newfile[($newstart - 1)]) .'</td>';
                        echo '</tr></table>';
                    }
                    echo '</td>';
                    echo '</tr>';
                break;
                case 'd': // Deleted lines
                    echo '<tr valign="top">';
                    echo '<td width="50%" style="background-color: #990033; font-family: monospace;">';

                    list($oldstart, $oldend) = split(",", $out);
                    list($newstart, $newend) = split(",", $in);

                    if ( !empty($oldend) ) { // It's a range of lines
                        $start = $oldstart - 1;
                        $end   = $oldend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($oldfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    } else { // single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $oldstart .'.</td>';
                        echo '<td>'. htmlentities($oldfile[($oldstart - 1)]) .'</td>';
                        echo '</tr></table>';
                    }

                    echo '</td>';
                    echo '<td width="50%" style="background-color: #f5f5f5; font-family: monospace;">';

                    if ( !empty($newend) ) { // It's range of lines.
                        $start = $newstart - 1;
                        $end = $newend - 1;
                        echo '<table border="0">';
                        for ( $i = $start; $i <= $end; $i++ ) {
                            echo '<tr valign="top">';
                            echo '<td>'. ($i + 1) .'.</td>';
                            echo '<td>'. htmlentities($newfile[$i]) .'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';

                    } else { // Single line.
                        echo '<table border="0">';
                        echo '<tr valign="top"><td>'. $newstart .'.</td>';
                        echo '<td>&nbsp;</td>';
                        echo '</tr></table>';
                    }
                    echo '</td>';
                    echo '</tr>';
                break;
            }
        }

        echo '</table>';
    }

    print_footer($course);

//////////////////////////////// Supporting functions ////////////////////////////////

/*
    Copyright 2003,2004 Nils Knappmeier (nk@knappi.org)
    Copyright 2004 Patrick R. Michaud (pmichaud@pobox.com)

    This file is part of PmWiki; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  See pmwiki.php for full details.

    This file implements a diff function in native PHP.  It is based
    upon the PHPDiffEngine code written by Nils Knappmeier, who in turn
    had used Daniel Unterberger's diff
    (http://www.holomind.de/phpnet/diff.php) as the basis for his code.

    Pm's revision of Nils' code simply attempts to streamline it
    for speed (eliminate function calls and unnecessary string ops)
    and place everything into a single file.

*/

// PHPDiff returns the differences between $old and $new, formatted
// in the standard diff(1) output format.
function PHPDiff($old,$new) {

    // Clean input a bit
    $old = cms_format_html($old);
    $new = cms_format_html($new);

    // split the source text into arrays of lines
    $t1 = explode("\n",$old);
    $x = array_pop($t1);
    if ($x > '') {
        $t1[]="$x\n\\ No newline at end of file";
    }
    $t2 = explode("\n",$new);
    $x = array_pop($t2);
    if ($x > '') {
        $t2[]="$x\n\\ No newline at end of file";
    }

    // build a reverse-index array using the line as key and line number as value
    // don't store blank lines, so they won't be targets of the shortest distance
    // search
    foreach($t1 as $i=>$x) {
        if ($x > '') {
            $r1[$x][]=$i;
        }
    }
    foreach($t2 as $i=>$x) {
        if ($x > '') {
            $r2[$x][]=$i;
        }
    }

    $a1 = 0;
    $a2 = 0;   // start at beginning of each list
    $actions = array();

    // walk this loop until we reach the end of one of the lists
    while ($a1<count($t1) && $a2<count($t2)) {
        // if we have a common element, save it and go to the next
        if ($t1[$a1]==$t2[$a2]) {
            $actions[]=4;
            $a1++;
            $a2++;
            continue;
        }

        // otherwise, find the shortest move (Manhattan-distance) from the
        // current location
        $best1 = count($t1);
        $best2 = count($t2);
        $s1 = $a1;
        $s2 = $a2;
        while(($s1+$s2-$a1-$a2) < ($best1+$best2-$a1-$a2)) {
            $d=-1;
            foreach((array)@$r1[$t2[$s2]] as $n) {
                if ($n>=$s1) {
                    $d = $n;
                    break;
                }
            }
            if ($d>=$s1 && ($d+$s2-$a1-$a2)<($best1+$best2-$a1-$a2)) {
                $best1 = $d;
                $best2 = $s2;
            }
            $d = -1;
            foreach((array)@$r2[$t1[$s1]] as $n) {
                if ($n>=$s2) {
                    $d = $n;
                    break;
                }
            }
            if ($d>=$s2 && ($s1+$d-$a1-$a2)<($best1+$best2-$a1-$a2)) {
                $best1 = $s1;
                $best2 = $d;
            }
            $s1++;
            $s2++;
        }
        while ($a1<$best1) {
            $actions[] = 1;
            $a1++;
        }  // deleted elements
        while ($a2<$best2) {
            $actions[] = 2;
            $a2++;
        }  // added elements
    }

    // we've reached the end of one list, now walk to the end of the other
    while($a1 < count($t1)) {
        $actions[] = 1;
        $a1++;
    }  // deleted elements
    while($a2 < count($t2)) {
        $actions[] = 2;
        $a2++;
    }  // added elements

    // and this marks our ending point
    $actions[] = 8;

    // now, let's follow the path we just took and report the added/deleted
    // elements into $out.
    $op = 0;
    $x0 = $x1 = 0;
    $y0 = $y1 = 0;
    $out = array();
    foreach($actions as $act) {
        if ($act==1) {
            $op|=$act;
            $x1++;
            continue;
        }
        if ($act==2) {
            $op|=$act;
            $y1++;
            continue;
        }
        if ($op>0) {
            $xstr = ($x1==($x0+1)) ? $x1 : ($x0+1).",$x1";
            $ystr = ($y1==($y0+1)) ? $y1 : ($y0+1).",$y1";
            if ($op==1) {
                $out[] = "{$xstr}d{$y1}";
            } else if ($op==3) {
                $out[] = "{$xstr}c{$ystr}";
            }
            while ($x0<$x1) {
                $out[] = '< '.$t1[$x0];
                $x0++;
            }   // deleted elems
            if ($op==2) {
                $out[] = "{$x1}a{$ystr}";
            } elseif ($op==3) {
                $out[] = '---';
            }
            while ($y0<$y1) {
                $out[] = '> '.$t2[$y0];
                $y0++;
            }   // added elems
        }
        $x1++;
        $x0 = $x1;
        $y1++;
        $y0 = $y1;
        $op = 0;
    }
    $out[] = '';
    return join("\n",$out);
}

function cms_format_html ( $strhtml, $toentities=true ) {

    $blockelems = "/(<\/(html|head|body|title|h1|h2|h3|h4|h5|h6|" .
                  "address|blockquote|del|ins|div|fieldset|form|" .
                  "hr|noscript|p|pre|script|table|tr|td|dl|ol|ul|" .
                  "dt|dd|li)+>)/i";
    $brhr = "/(<(br|hr) ?\/?>)/i";

    // Make one line.
    $strhtml = preg_replace("/\n/", "", $strhtml);
    $strhtml = preg_replace("/\r/", "", $strhtml);
    $strhtml = preg_replace("/<br \/><\/p>/i", "</p>", $strhtml);
    // Add returns.
    $strhtml = preg_replace($blockelems, "$0\r\n", $strhtml);
    $strhtml = preg_replace($brhr, "$0\r\n", $strhtml);

    if ( $toentities ) {
        return htmlentities($strhtml) . "\n";
    } else {
        return $strhtml . "\n";
    }

}