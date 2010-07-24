<?php // $Id: frontpage.php,v 1.2 2006/10/01 09:27:05 gustav_delius Exp $

    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

        ob_start();

        if (!empty($pagedata)) {

            if ( !empty($pagedata->requirelogin) ) {

                require_login();

            }

            if (! empty($pagedata->requirelogin) && (isguest() && !$pagedata->allowguest)) {
                    print_string('pageviewdenied','cms');
            } else {

                $modified = userdate($pagedata->modified);
                $toolbar = '';
                if (isadmin() || isteacheredit($courseid)) {

                    $stredit   = get_string('edit');
                    $stradd    = get_string('addchild', 'cms');
                    $strhistory    = get_string('pagehistory', 'cms');
                    $strdelete = get_string('delete');

                    $editlink = $CFG->wwwroot .'/cms/pageupdate.php?id='. $pagedata->id .
                        '&amp;sesskey='. $USER->sesskey .'&amp;course='. $courseid;
                    $editicon = $CFG->wwwroot .'/pix/i/edit.gif';

                    $addlink = $CFG->wwwroot .'/cms/pageadd.php?id='. $pagedata->id .'&amp;'.
                          'sesskey='. $USER->sesskey .'&amp;parentid='.$pagedata->id.'&amp;course=' . $courseid .'';
                    $addicon = $CFG->wwwroot .'/cms/pix/add.gif';

                    $historylink = $CFG->wwwroot .'/cms/pagehistory.php?pageid='. $pagedata->id .'&amp;'.
                          'sesskey='. $USER->sesskey ;
                    $historyicon = $CFG->wwwroot .'/cms/pix/history.gif';

                    $deletelink = $CFG->wwwroot .'/cms/pagedelete.php?id='. $pagedata->id .'&amp;'.
                          'sesskey='. $USER->sesskey .'&amp;course=' . $courseid .'';
                    $deleteicon = $CFG->wwwroot .'/pix/t/delete.gif';

                    $toolbar = '<div class="cms-frontpage-toolbar"><a href="'. $editlink .'"><img src="'. $editicon .'"'
                             .  ' width="16" height="16" alt="'. $stredit .'"'
                             . ' title="'. $stredit .'" /></a>'."\n"
                              . ' <a href="'. $addlink .'"><img src="'. $addicon .'"'
                              . ' width="11" height="11" alt="'. $stradd .'"'
                              . ' title="'. $stradd .'" /></a>'."\n"
                              . ' <a href="'. $historylink .'"><img src="'. $historyicon .'"'
                              . ' width="16" height="16" alt="'. $strhistory .'"'
                              . ' title="'. $strhistory .'" /></a>'."\n";
                    if (isadmin() and intval($pagedata->isfp) != 1 ) {
                        $toolbar .= ' <a href="'. $deletelink .'"><img src="'. $deleteicon .'"'
                                  . ' width="11" height="11" alt="'. $strdelete .'"'
                                  . ' title="'. $strdelete .'" /></a>';
                    }
                    $toolbar .= '</div>' . "\n";
                    print $toolbar;

                }

                print cms_render($pagedata, $course, $sections);

                if ( !empty($pagedata->printdate) ) {
                    print '<p style="font-size: x-small;">'. get_string('lastmodified', 'cms', $modified) .'</p>';
                }
                print $toolbar;
            }

        } else {

            print "<p>". get_string('nocontent','cms') ."</p>";

            if ($editing) {

                $stradmin = get_string('admin');
                print "<p style=\"font-size: x-small;\"><a href=\"$CFG->wwwroot/cms";
                print "/index.php?course=$courseid&amp;sesskey=$USER->sesskey\">$stradmin</a></p>\n";

            }
        }

        ob_end_flush();

    ?>