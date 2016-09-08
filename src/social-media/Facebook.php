<?php

namespace SocialMedia;

/**
 * Description of Facebook
 *
 * @author Tuhin
 */
class Facebook extends SocialMedia implements SocialMediaInterface
{
    public $fb;
    public $permissions = ['public_profile', 'email'];

    //put your code here

    public function __construct(array $config)
    {
        $this->media = static::FACEBOOK;
        $this->setConfig($config);
        $this->fb    = new \Facebook\Facebook([
            'app_id' => $this->config->app_id,
            'app_secret' => $this->config->app_secret,
            'default_graph_version' => 'v2.5',
        ]);
    }

    public function getLoginUrl()
    {
        $helper         = $this->fb->getRedirectLoginHelper();
        return $this->loginUrl = $helper->getLoginUrl($this->config->redirect_url,
            $this->config->permissions);
    }

    public function response()
    {

        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            throw new \Exception('Graph returned an error: '.$e->getMessage(),
            $e->getCode(), $e);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            throw new \Exception('Facebook SDK returned an error: '.$e->getMessage(),
            $e->getCode(), $e);
        }

        if (isset($accessToken)) {
            // Logged in!
            // OAuth 2.0 client handler
            $oAuth2Client = $this->fb->getOAuth2Client();

// Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            $this->setAccessToken($accessToken);

            $this->saveRefreshToken($longLivedAccessToken);

            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
        }
        return $this;
    }

    public function fetchUserInfo()
    {
        $this->fb->setDefaultAccessToken($this->getAccessToken());

        try {
            $response   = $this->fb->get('/me?fields=id,first_name,last_name,email,link,gender,locale,timezone,updated_time');
            $userNode   = $response->getGraphUser();
            $this->user = $response->getDecodedBody();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            throw new \Exception('Graph returned an error: '.$e->getMessage(),
            $e->getCode(), $e);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {

            throw new \Exception('Facebook SDK returned an error: '.$e->getMessage(),
            $e->getCode(), $e);
        }
        return $this;
    }

    public function setTokenToHandler($token)
    {
        $this->fb->setDefaultAccessToken($token);
        return $this;
    }

    public function handler()
    {
        return $this->fb;
    }

    public function isExpired()
    {
        try {
            
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getNewAccessToken()
    {
        try {

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
            $retUser['first_name']    = isset($user['first_name']) ? $user['first_name']
                    : NULL;
            $retUser['last_name']     = isset($user['last_name']) ? $user['last_name']
                    : NULL;
            $retUser['full_name']     = $retUser['first_name'].' '.$retUser['last_name'];
            $retUser['email_address'] = isset($user['email']) ? $user['email'] : NULL;
            $retUser['gender']        = isset($user['gender']) ? $user['gender']
                    : NULL;
            $retUser['link']          = isset($user['link']) ? $user['link'] : NULL;
            $retUser['picture']       = NULL;
            $retUser['locale']        = isset($user['locale']) ? $user['locale']
                    : NULL;
        }
        return $retUser;
    }
}