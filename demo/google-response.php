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
        <title>Google Response</title>
    </head>
    <body>
        <?php
        require_once './vendor/autoload.php';
        try {
          $google = new SocialMedia\Google([
            'app_id' => '43002302133-ibuvttcndsbs428s8dvhlqrah15mt148.apps.googleusercontent.com',
            'app_secret' => 'AKv1DYlClTSBfVLQ-pBB0TKf',
            'redirect_url' => 'http://ieltsmaster.net/social/google-response.php',
            'permissions' => ["email", "profile"],
        ]);
            $google->response();
            $google->fetchUserInfo();
            print_r($google->getUser());
           // print_r($facebook);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        ?>
    </body>
</html>
