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

    /**
     *
     * @var 
     */
    public $user;

    /**
     *
     * @var URL
     */
    public $loginUrl;

    /**
     *
     * @var string
     */
    public $media;

    /**
     *
     * @var Model
     */
    public $model;

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

            if (isset($config['model']) && !empty($config['model']) && class_exists($config['model'])) {
                $this->model = new $config['model'];
            } else {
                $this->model = new Model($this->media);
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
        return $this->model->getAccessToken();
    }

    public function setAccessToken($token)
    {
        $this->model->saveAccessToken($token);
        $this->access_token = $token;
    }

    public function hasAccessToken()
    {
        return $this->model->hasAccessToken();
    }

    /**
     * Save Access Token for further use. Save this to a permanent storage so that when web page reload or change then it can be retrivable 
     * @param string $accessToken Access Token 
     */
    public function saveAccessToken($token)
    {
        return $this->model->saveAccessToken($token);
    }

    public function success()
    {
        if (method_exists($this->model, 'smSuccess')) {
            return $this->model->smSuccess($this);
        }
        return false;
    }

    public function fail()
    {
        if (method_exists($this->model, 'smFail')) {
            return $this->model->smFail($this);
        }
        return false;
    }

    public static function make($config, $media)
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

    public function call()
    {
        try {
            if ($token = $this->hasAccessToken()) {

                return $this->setTokenToHandler($token)->fetchUserInfo();
            } elseif (isset($_REQUEST['code']) && !empty($_REQUEST['code'])) {

                return $this->response()->fetchUserInfo();
            }
            return false;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}