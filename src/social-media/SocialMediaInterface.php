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

    public abstract function getLoginUrl();

    public abstract function response();

    public abstract function fetchUserInfo();

    public abstract function handler();
}