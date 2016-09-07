<?php

namespace SocialMedia;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SocialMedia
 *
 * @author Tuhin
 */
class SocialMedia
{
    const FACEBOOK = 'facebook';
    const GOOGLE   = 'google';
    const LINKEDIN = 'linkedin';
    const TWITTER  = 'twitter';

    public $user;
    public $loginUrl;
    public $media;

    /**
     *
     * @var stdClass
     * {
     * app_id
     * app_secret
     * redirect_url
     * permissions
     * } 
     */
    public $config;
    protected $access_token;

    public function setConfig(array $config)
    {
        try {
            $this->config = new \stdClass();
            if (!isset($config['app_id']) || empty($config['app_id'])) {
                throw new \Exception('app_id is required');
            }
            if (!isset($config['app_secret']) || empty($config['app_secret'])) {
                throw new \Exception('app_secret is required');
            }
            if (!isset($config['redirect_url']) || empty($config['redirect_url'])) {
                throw new \Exception('redirect_url is required');
            }

            if (!isset($config['permissions']) || empty($config['permissions'])) {
                throw new \Exception('permissions is required');
            }

            $this->config->app_id       = $config['app_id'];
            $this->config->app_secret   = $config['app_secret'];
            $this->config->redirect_url = $config['redirect_url'];
            $this->config->permissions  = $config['permissions'];
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
        $this->saveAccessToken($token);
    }

    public function hasAccessToken()
    {
        if (isset($_SESSION[$this->media.'_access_token']) && !empty($_SESSION[$this->media.'_access_token'])) {
            return $_SESSION[$this->media.'_access_token'];
        }
        return FALSE;
    }

    /**
     * Save Access Token for further use. Save this to a permanent storage so that when web page reload or change then it can be retrivable 
     * @param string $accessToken Access Token 
     */
    public function saveAccessToken($token)
    {
        $_SESSION[$this->media.'_access_token'] = $token;
    }

    public static function init($config, $media)
    {
        switch ($media) {
            case SocialMedia::FACEBOOK:
                return new Facebook($config);
                break;
            case SocialMedia::GOOGLE:
                return new Google($config);
                break;
            case SocialMedia::LINKEDIN:
                return new LinkedIn($config);
                break;
            default :
                throw new \Exception('Unsupported Social Media.');
                break;
        }
    }

}