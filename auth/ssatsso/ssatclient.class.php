<?php

/**
 * @author Matt Clarkson <mattc@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Specialist Schools and Academies Trust SSO plugin
 *
 * Authenticate users against SSAT webservices.
 *
 * 2009-04-27  File created.
 */

if (class_exists('SoapClient'))
{
	class SSATClient extends SoapClient {

	    private $appkey;


	    /**
	     * Constructor
	     *
	     * @param string $wsdl url to webservice
	     * @param string $appkey used to authenticate with webservice
	     * @param array $options for base class
	     * @return null
	     */
	    public function __construct($wsdl, $appkey, $options=array()) {
	        $this->appkey = $appkey;
	        $options['exceptions'] = true;
	        @parent::__construct($wsdl, $options);
	    }


	    /**
	     * Validate username and password returning user record
	     *
	     * @param string $username
	     * @param string $password
	     * @return object
	     */
	    public function validate_credentials($username, $password) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inUsername = $username;
	        $params->inPassword = $password;

	        $response = $this->wsValidateUserReturnDataset($params);

	        if ($user = $this->parse_message($response->wsValidateUserReturnDatasetResult->any, 'user')) {
	            if ($user->token) {
	                return $user;
	            }
	        }
	        return false;
	    }


	    /**
	     * Validate a user token returning a user record
	     *
	     * @param string $token
	     * @return object
	     */
	    public function validate_token($token) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inToken = $token;

	        $response = $this->wsGetUserDataset($params);
	        if (isset($response->wsGetUserDatasetResult) && isset($response->wsGetUserDatasetResult->any)) {
	            if ($user = $this->parse_message($response->wsGetUserDatasetResult->any, 'user')) {
	                return $user;
	            }
	        }
	        return false;
	    }


	    /**
	     * Return a user token from a user id
	     *
	     * @param int $user_id
	     * @return string
	     */
	    public function get_token($user_id) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inUserID = $user_id;

	        $response = $this->wsGetUserDatasetByID($params);

	        if (isset($response->wsGetUserDatasetByIDResult) && isset($response->wsGetUserDatasetByIDResult->any)) {
	            if ($user = $this->parse_message($response->wsGetUserDatasetByIDResult->any, 'user')) {
	                return $user->token;
	            }
	        }
	        return false;
	    }



	    /**
	     * Parse XML message returned from webservice call into a PHP data structure
	     *
	     * @param string $message XML document
	     * @param string $type type of message to parse
	     * @return object
	     */
	    private function parse_message($message, $type) {
	        if ($parsedmessage = new SimpleXMLElement($message)) {
	            switch($type) {
	                case 'user':
	                    $return = (object)array_change_key_case((array)$parsedmessage->NewDataSet->sst_sup_user);
	                    break;
	            }
	            return $return;
	        }
	        return false;
	    }


	}
}else{
	class SSATClient extends soap_client {

	    private $appkey;


	    /**
	     * Constructor
	     *
	     * @param string $wsdl url to webservice
	     * @param string $appkey used to authenticate with webservice
	     * @param array $options for base class
	     * @return null
	     */
	    public function __construct($wsdl, $appkey, $options=array()) {
	        $this->appkey = $appkey;
	        $options['exceptions'] = true;
	        @parent::__construct($wsdl, $options);
	    }


	    /**
	     * Validate username and password returning user record
	     *
	     * @param string $username
	     * @param string $password
	     * @return object
	     */
	    public function validate_credentials($username, $password) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inUsername = $username;
	        $params->inPassword = $password;

	        $response = $this->wsValidateUserReturnDataset($params);

	        if ($user = $this->parse_message($response->wsValidateUserReturnDatasetResult->any, 'user')) {
	            if ($user->token) {
	                return $user;
	            }
	        }
	        return false;
	    }


	    /**
	     * Validate a user token returning a user record
	     *
	     * @param string $token
	     * @return object
	     */
	    public function validate_token($token) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inToken = $token;

	        $response = $this->wsGetUserDataset($params);
	        if (isset($response->wsGetUserDatasetResult) && isset($response->wsGetUserDatasetResult->any)) {
	            if ($user = $this->parse_message($response->wsGetUserDatasetResult->any, 'user')) {
	                return $user;
	            }
	        }
	        return false;
	    }


	    /**
	     * Return a user token from a user id
	     *
	     * @param int $user_id
	     * @return string
	     */
	    public function get_token($user_id) {
	        $params = new stdClass;
	        $params->inKey = $this->appkey;
	        $params->inUserID = $user_id;

	        $response = $this->wsGetUserDatasetByID($params);

	        if (isset($response->wsGetUserDatasetByIDResult) && isset($response->wsGetUserDatasetByIDResult->any)) {
	            if ($user = $this->parse_message($response->wsGetUserDatasetByIDResult->any, 'user')) {
	                return $user->token;
	            }
	        }
	        return false;
	    }



	    /**
	     * Parse XML message returned from webservice call into a PHP data structure
	     *
	     * @param string $message XML document
	     * @param string $type type of message to parse
	     * @return object
	     */
	    private function parse_message($message, $type) {
	        if ($parsedmessage = new SimpleXMLElement($message)) {
	            switch($type) {
	                case 'user':
	                    $return = (object)array_change_key_case((array)$parsedmessage->NewDataSet->sst_sup_user);
	                    break;
	            }
	            return $return;
	        }
	        return false;
	    }


	}
}
?>
