<?php   // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once('../../config.php');
    $viewtype = required_param('viewtype', PARAM_ALPHA);

    require_login();

    if (isguestuser()) {
        error("Guests are not allowed to rate entries.");
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    } else {
        $returnurl = $CFG->wwwroot.'/local/mahara/taoview'.$viewtype.'.php';
    }

    if (!$scale = get_record("scale", "name", 'TAO: Stars')) {
        error("TAO: Stars scale not found!");
    }

    if ($data = data_submitted()) {    // form submitted

    /// Calculate scale values
        $scale_values = make_grades_menu(-$scale->id);

        foreach ((array)$data as $entryid => $rating) {
            if (!is_numeric($entryid)) {
                continue;
            }

        /// Check rate is valid for that glossary scale values
            if (!array_key_exists($rating, $scale_values) && $rating != -999) {
                print_error('invalidrate', 'local', '', $rating);
            }

            if ($oldrating = get_record("taoview_ratings", "userid", $USER->id, "artefactid", $entryid)) {
                //Check if we must delete the rate
                if ($rating == -999) {
                    delete_records('taoview_ratings','userid',$oldrating->userid, 'artefactid',$oldrating->entryid);
                    glossary_update_grades($glossary, $entry->userid);

                } else if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    if (! update_record("taoview_ratings", $oldrating)) {
                        error("Could not update an old rating ($entry = $rating)");
                    }
                }

            } else if ($rating >= 0) {
                $newrating = new object();
                $newrating->userid  = $USER->id;
                $newrating->time    = time();
                $newrating->artefactid = $entryid;
                $newrating->rating  = $rating;

                if (! insert_record("taoview_ratings", $newrating)) {
                    error("Could not insert a new rating ($entry->id = $rating)");
                }
            }
        }

        redirect($returnurl, get_string("ratingssaved", "local"));

    } else {
        error("This page was not accessed correctly");
    }

?>