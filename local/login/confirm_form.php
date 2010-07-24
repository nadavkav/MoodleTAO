<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class login_confirm_form extends moodleform {
    function definition() {
        global $USER, $CFG;
        $invite = false;
        $sitecontext = get_context_instance(CONTEXT_SYSTEM); 
        if (isloggedin() && has_capability('moodle/local:invitenewuser',$sitecontext)) {
            $invite = true;
        }
        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('createuserandpass'), '');
        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12"');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'server');

        $mform->addElement('passwordunmask', 'password1', get_string('password'), 'maxlength="32" size="12"');
        $mform->setType('password1', PARAM_RAW);
        $mform->addRule('password1', get_string('missingpassword'), 'required', null, 'server');
            
        $mform->addElement('passwordunmask', 'password2', get_string('password').' ('.get_String('again').')', 'maxlength="32" size="12"');
        $mform->setType('password2', PARAM_RAW);
        $mform->addRule('password2', get_string('missingpassword'), 'required', null, 'server');
                       
        $mform->addElement('header', '', get_string('supplyinfo'),'');
        

        $nameordercheck = new object();
        $nameordercheck->firstname = 'a';
        $nameordercheck->lastname  = 'b';
        if (fullname($nameordercheck) == 'b a' ) {  // See MDL-4325
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        } else {
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        }

        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('missingfirstname'), 'required', null, 'server');

        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('missinglastname'), 'required', null, 'server');
       
        $mform->addElement('text', 'city', get_string('city'), 'maxlength="20" size="20"');
        $mform->setType('city', PARAM_TEXT);
        $mform->addRule('city', get_string('missingcity'), 'required', null, 'server');

        $country = get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        $mform->addRule('country', get_string('missingcountry'), 'required', null, 'server');

        if( !empty($CFG->country) ){
            $mform->setDefault('country', $CFG->country);
        }else{
            $mform->setDefault('country', '');
        }

        $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="20" size="20"');
        $mform->setType('idnumber', PARAM_NOTAGS);
        $mform->addRule('idnumber', get_string('missingidnumber','local'), 'required', null, 'server');
        
        profile_signup_fields($mform);

        if (!empty($CFG->sitepolicy)) {
            $mform->addElement('header', '', get_string('policyagreement'), '');
            $mform->addElement('static', 'policylink', '', '<a href="'.$CFG->sitepolicy.'" onclick="this.target=\'_blank\'">'.get_String('policyagreementclick').'</a>');
            $mform->addElement('checkbox', 'policyagreed', get_string('policyaccept'));
            $mform->addRule('policyagreed', get_string('policyagree'), 'required', null, 'server');
        }
        $mform->addElement('hidden', 'data');
        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform =& $this->_form;

        $mform->applyFilter('username', 'moodle_strtolower');
        $mform->applyFilter('username', 'trim');
    }

    function validation($data, $files) {
        global $CFG;
        $invite = false;
        $sitecontext = get_context_instance(CONTEXT_SYSTEM); 
        if (isloggedin() && has_capability('moodle/local:invitenewuser',$sitecontext)) {
            $invite = true;
        }
        $errors = parent::validation($data, $files);

        $authplugin = get_auth_plugin($CFG->registerauth);

        if ($data['password1'] <> $data['password2']) {
            $errors['password1'] = get_string('passwordsdiffer');
            $errors['password2'] = get_string('passwordsdiffer');
            return $errors;
        }
        
        if (record_exists('user', 'username', $data['username'], 'mnethostid', $CFG->mnet_localhost_id)) {
            $errors['username'] = get_string('usernameexists');
        } else {
            if (empty($CFG->extendedusernamechars)) {
                $string = eregi_replace("[^(-\.[:alnum:])]", '', $data['username']);
                if (strcmp($data['username'], $string)) {
                    $errors['username'] = get_string('alphanumerical');
                }
            }
        }

        //check if user exists in external db
        //TODO: maybe we should check all enabled plugins instead
        if ($authplugin->user_exists($data['username'])) {
            $errors['username'] = get_string('usernameexists');
        }


        $errmsg = '';
        if (!check_password_policy($data['password1'], $errmsg)) {
            $errors['password1'] = $errmsg;
        }

        if (function_exists('local_user_signup_validation') ) {
            if ($localvalidation = local_user_signup_validation()) {
                $errors = array_merge($errors, $localvalidation);
            }
        }

        return $errors;
    }
}

?>