<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SocialMedia;

/**
 *
 * @author Tuhin
 */
interface SocialMediaInterface
{

    public function getLoginUrl();

    public function response();

    public function fetchUserInfo();

    public function handler();

    public function setTokenToHandler($token);

    public function isExpired();

    public function getNewAccessToken();
}