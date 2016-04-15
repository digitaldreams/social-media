<?php
session_start();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Facebook Response</title>
    </head>
    <body>
        <?php
        require_once './vendor/autoload.php';
        try {
            $facebook = new SocialMedia\Facebook([
                'app_id' => '1018653624814139',
                'app_secret' => '1a0cb28c8a5169191d38fc8e9ca3f9f6',
                'redirect_url' => 'http://ieltsmaster.net/social/facebook-response.php',
                'permissions' => ['public_profile', 'email'],
            ]);
            $facebook->response();
            $facebook->fetchUserInfo();
            print_r($facebook->getUser());
           // print_r($facebook);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        ?>
    </body>
</html>
