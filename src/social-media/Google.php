<?php

namespace SocialMedia;

class Google extends SocialMedia implements SocialMediaInterface
{
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

    public function __construct(array $config = [])
    {
        $this->media = static::GOOGLE;
        $this->setConfig($config);
        $this->initClient();
    }

    /**
     * Get Google Client object
     * @return Google_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getLoginUrl()
    {
        return $this->getClient()->createAuthUrl();
    }

    /**
     * Create Google_client Class Object 
     */
    protected function initClient()
    {

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
    public function isAuthenticated()
    {
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
    public function response()
    {
        $code = $this->getCode();

        if (!empty($code)) {
            $this->client->authenticate($code);
            $accessToken = $this->client->getAccessToken();
            $this->setAccessToken($accessToken);
            $this->saveRefreshToken($this->client->getRefreshToken());
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
    public function fetchUserInfo()
    {
        if ($this->isAuthenticated() === FALSE) {
            $this->setError('No authenticated user found');
            return FALSE;
        }
        $guser      = $this->service->userinfo;
        $this->user = get_object_vars($guser->get());
        return $this;
    }

    /**
     * Get Code from your storage.
     * @return type
     */
    public function getCode()
    {
        return isset($_GET['code']) ? $_GET['code'] : '';
    }

    /**
     * Set Code 
     *  This is one time code just use to get access token. So it is not recommened to store it in permanent storage
     * @param string $code 
     * @return \GoogleAuth
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Check whether access token is expired
     * @return boolean 
     */
    public function isExpired()
    {
        return $this->getClient()->isAccessTokenExpired();
    }

    /**
     * Set error
     * @param string $message error message
     * @param string/integer $index Index of the error message. Optional parameter
     */
    public function setError($message, $index = '')
    {
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
    public function hasError()
    {
        return count($this->error) > 0 ? TRUE : FALSE;
    }

    /**
     * Return errors
     * @return array if errors occured then it will be fill with error otherwise it will be empty
     */
    public function getError()
    {
        return $this->error;
    }

    public function handler()
    {
        return $this->client;
    }

    public function setTokenToHandler($token)
    {
        $this->client->setAccessToken($token);
        return $this;
    }

    public function getNewAccessToken()
    {
        try {
            $refreshToken = $this->getRefreshToken();
            if (!empty($refreshToken)) {
                return $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            }
            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getUser()
    {
        $retUser = [];
        $user    = $this->getRawUser();
        if (!empty($user)) {
            $retUser['id']            = isset($user['id']) ? $user['id'] : NULL;
            $retUser['first_name']    = isset($user['givenName']) ? $user['givenName']
                    : NULL;
            $retUser['last_name']     = isset($user['familyName']) ? $user['familyName']
                    : NULL;
            $retUser['full_name']     = isset($user['name']) ? $user['name'] : NULL;
            $retUser['email_address'] = isset($user['email']) ? $user['email'] : NULL;
            $retUser['gender']        = isset($user['gender']) ? $user['gender']
                    : NULL;
            $retUser['link']          = isset($user['link']) ? $user['link'] : NULL;
            $retUser['picture']       = isset($user['picture']) ? $user['picture']
                    : NULL;
            $retUser['locale']        = isset($user['locale']) ? $user['locale']
                    : NULL;
        }
    }
}