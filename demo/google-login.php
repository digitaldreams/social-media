<?php
session_start();
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Google Login</title>
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
            echo $google->getLoginUrl();
        } catch (Exception $ex) {
            echo $ex->getMessage().' on'.$ex->getLine().' in'.$ex->getFile();
        }
        ?>
    </body>
</html>
