<?php
/*
 Saves evidence in both RAFL and Moodle tables.

 Note: designed to be called by HTTP/AJAX request from Flash App so all output should go to logs rather than screen

 todo work out escaping quirks between rafl and moodle initiated saves

*/

      require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
      require_once($CFG->dirroot . '/mod/rafl/locallib.php');

      $share_id        = optional_param('share_id', '', PARAM_INT);
      $success_item_id = optional_param('success_item_id', '', PARAM_INT);
      $unit_item_id    = optional_param('unit_item_id', '', PARAM_INT);
      $webcelltext     = $_POST['webcelltext'];  // using this because optional_param messes HTML up 

      // check we have all our parameters
      if (empty($share_id)) {
          error_log("No share_id provided");
          return; 
      }

      if (empty($success_item_id)) {
          error_log("No success_item_id provided");
          return; 
      }

      if (empty($unit_item_id)) {
          error_log("No unit_item_id provided");
          return; 
      }
       
      if (empty($webcelltext)) {
          error_log("No webcelltext provided");
          return; 
      }

      // call the store_evidence routine
      $rafl = new localLibRafl();

      error_log($webcelltext);

      $rating = $rafl->store_evidence($share_id, $success_item_id, $unit_item_id, $webcelltext); 

      if (empty($rating)) {
          error_log('Could not store RAFL evidence');
          return;
      }

      if(!$rafl->update_moodle_item($share_id, $success_item_id, $webcelltext)) {
          error_log('Could not store evidence in Moodle tables');
          return;
      }

      // Success 
      error_log("SUCCESS: updated share $share_id, webcell $success_item_id with '$webcelltext'");

      // return rating to AJAX call
      echo $rating;
?>
