<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Accept, process and reply to ajax calls to rate forums

/// TODO: Centralise duplicate code in rate.php and rate_ajax.php

    require_once('../../config.php');
    require_once($CFG->dirroot . '/local/tao.php');
/// In developer debug mode, when there is a debug=1 in the URL send as plain text
/// for easier debugging.
    if (debugging('', DEBUG_DEVELOPER) && optional_param('debug', false, PARAM_BOOL)) {
        header('Content-type: text/plain; charset=UTF-8');
        $debugmode = true;
    } else {
        header('Content-type: application/json');
        $debugmode = false;
    }

/// Here we maintain response contents
    $response = array('status'=> 'Error', 'message'=>'kk');

/// Check access.
    if (!isloggedin()) {
        print_error('mustbeloggedin');
    }
    if (isguestuser()) {
        print_error('noguestrate', 'forum');
    }
    if (!confirm_sesskey()) {
        print_error('invalidsesskey');
    }

    if (!$scale = get_record("scale", "name", 'TAO: Stars')) {
        error("TAO: Stars scale not found!");
    }

/// Check required params
    $artefactid = required_param('artefactid', PARAM_INT); // The postid to rate
    $rate   = required_param('rate', PARAM_INT); // The rate to apply

/// Calculate scale values
    $scale_values = make_grades_menu(-$scale->id);

/// Check rate is valid for for that forum scale values
    if (!array_key_exists($rate, $scale_values) && $rate != FORUM_UNSET_POST_RATING) {
        print_error('invalidrate', 'local');
    }

/// Everything ready, process rate

    if ($oldrating = get_record('taoview_ratings', 'userid', $USER->id, 'artefactid', $artefactid)) {
        if ($rate != $oldrating->rating) {
            $oldrating->rating = $rate;
            $oldrating->time   = time();
            if (!update_record('taoview_ratings', $oldrating)) {
                error("Could not update an old rating ($artefact->id = $rate)");
            }
        }

/// Inserting rate
    } else {
        $newrating = new object();
        $newrating->userid = $USER->id;
        $newrating->time   = time();
        $newrating->artefactid   = $artefactid;
        $newrating->rating = $rate;

        if (!insert_record('taoview_ratings', $newrating)) {
            print_error('cannotinsertrate', 'error', '', (object)array('id'=>$artefactid, 'rating'=>$rate));
        }
    }
    $rateinfo = tao_print_ratings($artefactid, $scale_values, true);
/// Calculate response
    $response['status']  = 'Ok';
    $response['message'] = $rateinfo;
    echo json_encode($response);

?>