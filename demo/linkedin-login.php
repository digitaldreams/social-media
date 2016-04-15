<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Linkedin Login</title>
    </head>
    <body>
        <?php
        require_once './vendor/autoload.php';
        try {
            $linkedin = new SocialMedia\LinkedIn([
                'app_id' => '757psni3ncamxi',
                'app_secret' => 'pBFlfmQtl8htDJzM',
                'redirect_url' => 'http://ieltsmaster.net/social/linkedin-response.php',
                'permissions' => [   
                     SocialMedia\LinkedIn::SCOPE_EMAIL_ADDRESS,
                     SocialMedia\LinkedIn::SCOPE_BASIC_PROFILE],
            ]);
            echo $linkedin->getLoginUrl();
        } catch (Exception $ex) {
            echo $ex->getMessage() . ' on' . $ex->getLine() . ' in' . $ex->getFile();
        }
        ?>
    </body>
</html>
