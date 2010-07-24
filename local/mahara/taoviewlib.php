<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage local
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 *
 */

//function to filter artefacts based on the filters set.
//this is a bit messy and could be tidied a bit
function taoview_filter_artefacts($artefacts, $viewtype, $tagfilter, $userfilter) {
    global $CFG;
    if (!empty($tagfilter) || !empty($userfilter)) {
        $printuser = '';
        $printtags = '';
        if (!empty($userfilter)) {
            $user = get_record('user', 'username', $userfilter);
            $printuser = get_string('user').': '.fullname($user);
        }
        if (!empty($tagfilter)) {
            $printtags = get_string('tags', 'tag').': '.$tagfilter;
        }
        if (!empty($printtags) && !(empty($printuser))) {
            $printtags .= ', ';
        }
        $printtags .= $printuser;
        $printtags .= ' <a href="'.$CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php">'.get_string('removefilters', 'local').'</a>';
        echo ' <div class="filteredby">'.get_string('filteredby', 'local').': '.$printtags.'</div>';
        foreach ($artefacts as $aid => $artefact) {
            if (!empty($tagfilter)) {
                //check to see if this artefact uses the tag mentioned in the tagfilter
                if (empty($artefact['tags']) || !is_array($artefact['tags'])) {
                    unset($artefacts[$aid]);
                    continue;
                }
                $showartefact = false; //assume this doesn't contain the right tags
                foreach($artefact['tags'] as $tag) {
                   if($tag==$tagfilter) {
                       $showartefact = true;
                   }
                }
                if (!$showartefact) { //if this artefact doesn't contain the tagfilter, hide it!
                    unset($artefacts[$aid]);
                    continue;
                }
            }
            //now check if should be filtered by user
            if ((!empty($userfilter) && $userfilter <> $artefact['uploader'])) {
                unset($artefacts[$aid]);
            }
        }
    }
    return $artefacts;
}

//function to print artefacts to screen.
function taoview_print_artefacts($artefacts, $viewtype, $tagfilter, $userfilter, $sort, $page, $perpage) {
    global $CFG,$USER;
    //get scale
    $scale = get_record("scale", "name", 'TAO: Stars');
    $artefacts = taoview_get_paginated_results($artefacts, $page, $perpage);

    foreach ($artefacts as $artefact) {
        if ($perpage <= 0) { //not a great way to paginate.
            break;
        }
            echo '<div class="taoview">';
            if (!empty($artefact['thumbnail'])) {
                echo '<div class="taoview-thumb"><img src="'.$artefact['thumbnail'].'"></div>';
            }
            echo '<div class="taoview-download"><a href="'.$artefact['download'].'" target="_blank">'.$artefact['name'].'</a></div>';
            if (!empty($artefact['uploader'])) {
                $user = get_record('user', 'username', $artefact['uploader']);
                if (!empty($user)) {
                    echo '<div class="taoview-user">'.get_string('submittedby','local').': <a href="'.$CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php?tag='.$tagfilter.'&filteruser='.$artefact['uploader'].'&sort='.$sort.'">'.fullname($user).'</a></div>';
                }
            }
            if (!empty($artefact['ctime'])) {
                echo '<div class="taoview-date">'.$artefact['ctime'].'</div>';
            }
            if (!empty($artefact['description'])) {
                echo '<div class="taoview-desc">'.$artefact['description'].'</div>';
            }
            if (!empty($artefact['tags']) && is_array($artefact['tags'])) {
                echo '<div class="taoview-tags">'.get_string('tags').': ';
                foreach($artefact['tags'] as $tag) {
                    echo '<a href="'.$CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php?tag='.$tag.'&sort='.$sort.'">'.$tag.'</a>, ';
                }
                echo '</div>';
            }
            //now do ratings stuff
            echo '<div class="ratings">';
            $possiblevalues = make_grades_menu(-$scale->id);
            echo '<span class="taoviewratingtext">';
            tao_print_ratings($artefact['id'], $possiblevalues);
            echo '</span>';
            if (!empty($user) && $user->id <> $USER->id && !isguest()) {
                tao_print_rating_menu($artefact['id'],$USER->id,$possiblevalues);
            }
            echo '</div>';
            //end of ratings stuff
            if (!empty($artefact['page'])) {
                echo '<div class="taoview-page"><a href="'.$artefact['page'].'">'.get_string('moreinfo', 'local').'</a></div>';
            }
            echo '</div>';
            $perpage--;
    }
    if (!empty($artefacts)  && !isguest()) {
        echo "<div class=\"boxaligncenter\"><input id=\"taoviewratingsubmit\" type=\"submit\" value=\"".get_string("sendinratings", "local")."\" />";
        if (ajaxenabled()) { /// AJAX enabled, standard submission form
             $rate_ajax_config_settings = array("pixpath"=>$CFG->pixpath, "wwwroot"=>$CFG->wwwroot, "sesskey"=>sesskey());
            echo "<script type=\"text/javascript\">//<![CDATA[\n".
                 "var rate_ajax_config = " . json_encode($rate_ajax_config_settings) . ";\n".
                 "init_rate_ajax();\n".
                 "//]]></script>\n";
        }
        //print_scale_menu_helpbutton(SITEID, $scale); //no help file written yet.
        echo "</div>";
    }
}
function taoview_print_tag_cloud($artefacts, $viewtype) {
    global $CFG;
    //first generate a list of tags and the frequency of use
    $tags = array();
    $maxcount = 0;
    foreach ($artefacts as $artefact) {
        if (!empty($artefact['tags']) && is_array($artefact['tags'])) {
            foreach($artefact['tags'] as $tag) {
                if(isset($tags[$tag])) {
                    $tags[$tag]++;
                } else {
                    $tags[$tag] = 1;
                }
                if ($tags[$tag] > $maxcount) {
                    $maxcount = $tags[$tag];
                }
            }
        }
    }
    if (!empty($tags)) {
        ksort($tags); //sort by highest number of tags.
        $etags = array();

        foreach ($tags as $tag => $tagcount) {
            $tagob = new stdclass();
            $tagob->count = $tagcount;
            $tagob->name = $tag;
            $size = (int) (( $tagcount / $maxcount) * 20);
            $tagob->class = "default s$size";
            $etags[] = $tagob;
        }

        $output = '';
        $output .= '<div class="taoviewtagcloud">';
        $output .= '<div class="taoviewtagcloud-name">'.get_string('tags','tag').'</div>';
        $output .= "\n<ul class='tag_cloud inline-list'>\n";
        foreach ($etags as $tag) {

            $link = $CFG->wwwroot .'/local/mahara/taoview'.$viewtype.'.php?tag='. rawurlencode($tag->name);
            $output .= '<li><a href="'. $link .'" class="'. $tag->class .'" '.
                'title="'. get_string('numberofentries', 'blog', $tag->count) .'">'.
                $tag->name .'</a></li> ';
        }
        $output .= "\n</ul>\n</div>\n";
        echo $output;

    }
}
function taoview_sort_artefacts($artefacts, $viewtype, $tagfilter='', $userfilter='', $sort='') {
    global $CFG;
    $returnartefacts = array();
    $ratings = array();

    echo ' <div class="sortby">'.get_string('sortby', 'local').': ';
    if (empty($sort) || $sort=='date') {
        echo get_string('date'). ' | <a href="'.$CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php?tag='.$tagfilter.'&filteruser='.$userfilter.'&sort=rating">'.get_string('rating', 'local').'</a>';
        return $artefacts;
    } elseif ($sort=='rating') {
        echo '<a href="'.$CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php?tag='.$tagfilter.'&filteruser='.$userfilter.'">'.get_string('date'). '</a> | '.get_string('rating', 'local');
        $scale = get_record("scale", "name", 'TAO: Stars');
        $scalevars = explode(',', $scale->scale);
        $possiblevalues = array(); //use values instead of the scale so we can order them!
        $i = 1;
        foreach($scalevars as $sc) {
            $possiblevalues[$i] = $i;
            $i++;
        }
        echo '<div class="ratings">';
        foreach($artefacts as $aid => $artefact) {
            $ratings[$aid] = tao_get_ratings_mean($artefact['id'], $possiblevalues);
        }
        arsort($ratings);
        foreach($ratings as $id => $rat) {
            $returnartefacts[] = $artefacts[$id];
        }
    }
    echo '</div>';

    return $returnartefacts;
}
function taoview_call_mnet($viewtype) {
    /// Setup MNET environment
    global $MNET,$CFG;
    if (empty($MNET)) {
        $MNET = new mnet_environment();
        $MNET->init();
    }

/// Setup the server
    $host = get_record('mnet_host','name', 'localmahara'); //we retrieve the server(host) from the 'mnet_host' table
    if (empty($host)) {
        error('Mahara not configured');
    }

    $a = new stdclass();
    $a->link = $CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$host->id.'&wantsurl=local/taoview.php?view='.$viewtype;
    echo '<div class="taoviwdesc">';
    print_string('toaddartefacts','local', $a);
    echo '</div>';

    $mnet_peer = new mnet_peer();                          //we create a new mnet_peer (server/host)
    $mnet_peer->set_wwwroot($host->wwwroot);               //we set this mnet_peer with the host http address

    $client = new mnet_xmlrpc_client();        //create a new client
    $client->set_method('local/mahara/rpclib.php/get_artefacts_by_viewtype'); //tell it which method we're going to call
    $client->add_param($viewtype);
    $client->send($mnet_peer);                 //Call the server
    if (!empty($client->response['faultString'])) {
        error("Mahara error:".$artefacts['faultString']);
    }
    return $client->response;
}
function taoview_print_artefacts_form($artefacts, $viewtype, $tagfilter, $userfilter, $sort, $page, $perpage) {
        if (!empty($artefacts) && is_array($artefacts)) {
        print_paging_bar(count($artefacts), $page, $perpage,
            "taoview$viewtype.php?tag=$tagfilter&amp;filteruser=$userfilter&amp;sort=$sort&amp;");

        echo "<form method=\"post\" action=\"rate.php\">";
        echo "<input type='hidden' name='viewtype' value='$viewtype'/>";

        taoview_print_artefacts($artefacts, $viewtype, $tagfilter, $userfilter, $sort, $page, $perpage);
        echo "</form>";
        print_paging_bar(count($artefacts), $page, $perpage,
            "taoview$viewtype.php?tag=$tagfilter&amp;filteruser=$userfilter&amp;sort=$sort&amp;");
        //now print tags cloud:
        taoview_print_tag_cloud($artefacts, $viewtype);

    } else {
        notify(get_string('noartefactsfound', 'local'));
    }
}

function taoview_print_view($viewtype, $tagfilter, $userfilter, $sort, $page, $perpage) {

    $artefacts = taoview_call_mnet($viewtype);             //get Artefacts.
    $artefacts = taoview_filter_artefacts($artefacts, $viewtype, $tagfilter, $userfilter); //filter the artefacts
    $artefacts = taoview_sort_artefacts($artefacts, $viewtype, $tagfilter, $userfilter, $sort); //sort the artefacts

    taoview_print_artefacts_form($artefacts, $viewtype, $tagfilter, $userfilter, $sort, $page, $perpage);
}
function taoview_get_paginated_results($artefacts, $page, $perpage) {
    $artefactnum = 0;
    $retart = array();
    $skipartefacts = $page * $perpage;
    if (empty($artefacts)) {
        return array();
    }
    foreach($artefacts as $artefact) {
        if ($skipartefacts > 0) { //check if need to skip some artefacts.
            $skipartefacts--;
        } elseif ($artefactnum >= $perpage) { //check if have already obtained enough artefacts.
            break;
        } else {
            $retart[] = $artefact;
            $artefactnum++;
        }
    }
    return $retart;
}
?>