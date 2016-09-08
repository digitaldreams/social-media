<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SocialMedia;

/**
 * Description of Model
 *
 * @author Tuhin
 */
class Model
{
    protected $media;

    public function __construct($media)
    {
        $this->media = $media;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function hasAccessToken()
    {
        if (isset($_SESSION[$this->media.'_access_token']) && !empty($_SESSION[$this->media.'_access_token'])) {
            return $_SESSION[$this->media.'_access_token'];
        }
        return FALSE;
    }

    public function getAccessToken()
    {
        $accessToken = $this->hasAccessToken($this->media);
        return !empty($accessToken) ? $accessToken : FALSE;
    }

    public function saveAccessToken($token)
    {
        return $_SESSION[$this->media.'_access_token'] = $token;
    }

    public function saveRefreshToken($token)
    {
        return $_SESSION[$this->media.'_refresh_token'] = $token;
    }

    public function getRefreshToken($token)
    {
        return isset($_SESSION[$this->media.'_refresh_token']) ? $_SESSION[$this->media.'_refresh_token']
                : FALSE;
    }

    /**
     *
     * @param \SocialMedia $socialMedia
     */
    public function smSuccess($socialMedia)
    {
        
    }

    /**
     *
     * @param \SocialMedia $socialMedia
     */
    public function smFail($socialMedia)
    {

    }

    public function forgetStorage()
    {
        if (isset($_SESSION[$this->media.'_access_token'])) {
            unset($_SESSION[$this->media.'_access_token']);
            return true;
        }
        return false;
    }
}