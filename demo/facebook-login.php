<?php
session_start();
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Facebook Login</title>
    </head>
    <body>
        <?php
        require_once './vendor/autoload.php';
        $facebook = new SocialMedia\Facebook([
            'app_id' => '1018653624814139',
            'app_secret' => '1a0cb28c8a5169191d38fc8e9ca3f9f6',
            'redirect_url' => 'http://ieltsmaster.net/social/facebook-response.php',
            'permissions' => ['public_profile', 'email'],
        ]);
        echo $facebook->getLoginUrl();
        ?>
    </body>
</html>
