<?php // $Id$
    //copy of login/confirm.php with a couple of local modifications.
    require_once("../../config.php");

    $data = optional_param('data', '', PARAM_CLEAN);  // Formatted as:  secret/username

    $p = optional_param('p', '', PARAM_ALPHANUM);     // Old parameter:  secret
    $s = optional_param('s', '', PARAM_CLEAN);        // Old parameter:  username

    if (empty($CFG->registerauth)) {
        error("Sorry, you may not use this page.");
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!$authplugin->can_confirm()) {
        error("Sorry, you may not use this page.");
    }

    if (!empty($data) || (!empty($p) && !empty($s))) {

        if (!empty($data)) {
            $dataelements = explode('/',$data);
            $usersecret = $dataelements[0];
            $username   = $dataelements[1];
        } else {
            $usersecret = $p;
            $username   = $s;
        }
        require_once('confirm_form.php');
        $mform_signup = new login_confirm_form();
        $user = get_complete_user_data('username', $username);
        if ($user->secret == stripslashes($usersecret) && $user->confirmed==0) {
           if ($mform_signup->is_cancelled()) {              
               redirect($CFG->httpswwwroot.'/login/index.php');

           } else if ($userdata = $mform_signup->get_data()) {
                //update this user record.
                $userdata->password = $userdata->password1;
                update_record('user', $userdata);
                $confirmed = $authplugin->user_confirm($username, $usersecret);
                
                // The user has confirmed successfully, let's log them in

                if (!$USER = get_complete_user_data('username', $username)) {
                    error("Something serious is wrong with the database");
                }

                set_moodle_cookie($USER->username);

                if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
                    $goto = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                    redirect($goto);
                }

                print_header(get_string("confirmed"), get_string("confirmed"), array(), "");
                print_box_start('generalbox centerpara boxwidthnormal boxaligncenter');
                echo "<h3>".get_string("thanks").", ". fullname($USER) . "</h3>\n";
                echo "<p>".get_string("confirmed")."</p>\n";
                print_single_button("$CFG->wwwroot/course/", null, get_string('courses'));
                print_box_end();
                print_footer();
                exit;
            } else { //no data submitted - display the form.
                print_header(get_string("confirmed"), get_string("confirmed"), array(), "");
                $mform_signup->set_data(array('firstname'=>$user->firstname, 'lastname'=>$user->lastname, 'idnumber'=>$user->idnumber,'data'=>$data));
                $mform_signup->display();
                print_footer();
                exit;
            }
            
        } else { 
            print_error("errorwhenconfirming");
        }
        
    } else {
        print_error("errorwhenconfirming");
    }

    redirect("$CFG->wwwroot/");

?>