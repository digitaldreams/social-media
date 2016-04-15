<?php

namespace SocialMedia;

class Google extends SocialMedia {

    /**
     * Main class for Google Oauth
     *  
     *  useful method
     * refreshToken($refreshTOken) get new access token when accessToken is expire via $refreshToken.
     *  E.g you can get refreshToken by getRefreshToken(); make sure you save this token to session,file , database for long term use.
     * @var Google_Client
     */
    public $client;

    /**
     *
     * @var Google_Service_Oauth2
     */
    public $service;

    /**
     * long code given after auth prompt from google to get access token
     * @var string 
     */
    public $code;

    /**
     * When an access token is expire then new access token can be achieve with this refresh token
     * 
     * To get this call $this->getClient()->getRefreshToken() 
     * 
     * @var string 
     */
    public $refresh_token;

    /**
     * Errors container
     * @var array 
     */
    public $error = [];

    public function __construct(array $config = []) {
        $this->setConfig($config);
        $this->initClient();
        $this->media = static::GOOGLE;
    }

    /**
     * Get Google Client object
     * @return Google_Client
     */
    public function getClient() {
        return $this->client;
    }

    public function getLoginUrl() {
        return $this->getClient()->createAuthUrl();
    }

    /**
     * Create Google_client Class Object 
     */
    protected function initClient() {

        // $op=new \Google_Client();
        $this->client = new \Google_Client();
        $this->client->setClientId($this->config->app_id);
        $this->client->setClientSecret($this->config->app_secret);
        $this->client->setRedirectUri($this->config->redirect_url);

        $this->client->addScope($this->config->permissions);
        $this->service = new \Google_Service_Oauth2($this->client);
    }

    /**
     * Check whetehr user is authenticated
     */
    public function isAuthenticated() {
        $accessToken = $this->getAccessToken();

        if (!empty($accessToken)) {
            $this->client->setAccessToken($accessToken);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Authenticate a user  when code is return from Google
     * @return \GoogleAuth
     */
    public function response() {
        $code = $this->getCode();

        if (!empty($code)) {
            $this->client->authenticate($code);
            $accessToken = $this->client->getAccessToken();
            $this->setAccessToken($accessToken);
        } else {
            $this->setError('Code is not defined yet');
        }

        return $this;
    }

    /**
     * Get authenticated user
     *   Useful Method of Google_Serive_Oauth2 class
     *      getEmail() 
     *      getUserId()
     *      getEmail()
     *       get() // get all property 
     *
     * @return boolean if not authenticated user otherwise return Google_Service_Oauth2_Userinfoplus Object
     */
    public function fetchUserInfo() {
        if ($this->isAuthenticated() === FALSE) {
            $this->setError('No authenticated user found');
            return FALSE;
        }
        $guser = $this->service->userinfo;
        $this->user = get_object_vars($guser->get());
        return $this->user;
    }

    /**
     * Get Code from your storage.
     * @return type
     */
    public function getCode() {
        return isset($_GET['code']) ? $_GET['code'] : '';
    }

    /**
     * Set Code 
     *  This is one time code just use to get access token. So it is not recommened to store it in permanent storage
     * @param string $code 
     * @return \GoogleAuth
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * Check whether access token is expired
     * @return boolean 
     */
    public function isExpired() {
        return $this->getClient()->isAccessTokenExpired();
    }

    /**
     * Set error
     * @param string $message error message
     * @param string/integer $index Index of the error message. Optional parameter
     */
    public function setError($message, $index = '') {
        if (!empty($index)) {
            $this->error[$index] = $message;
        } else {
            $this->error[] = $message;
        }
    }

    /**
     * Check whether any error occured or not 
     * @return boolean
     */
    public function hasError() {
        return count($this->error) > 0 ? TRUE : FALSE;
    }

    /**
     * Return errors
     * @return array if errors occured then it will be fill with error otherwise it will be empty
     */
    public function getError() {
        return $this->error;
    }

    public function handler() {
        return $this->client;
    }

}
