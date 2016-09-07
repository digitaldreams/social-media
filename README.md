# Social Media
Social Media login and registration are now common in almost all website. But its very hard to find a solution which support Facebook, Google, Linkedin and Twitter in Object Oriented PHP. This package can be used in any PHP framework. See Demo here: **http://ieltsmaster.net/social/login.php**

###Installation

  "require-dev": {
  
     "digitaldream/social-media": "1.0.*"
        
}
##Uses

###Facebook

<?php

  $facebook = new \SocialMedia\Facebook([
  
            'app_id' => 'xxxxxxxxxxxxxxxxxxxxx',
            
            'app_secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxx',
            
            'redirect_url' => 'http://example.com/facebook-response.php',
            
            'permissions' => ['public_profile', 'email'],
            
        ]);
        
        echo $facebook->getLoginUrl();
?>

//facebook-response.php page
<?php 

   try {
   
         $facebook = new \SocialMedia\Facebook([
         
              'app_id' => 'xxxxxxxxxxxxxxxxxxx',
              
              'app_secret' => 'xxxxxxxxxxxxxxxxxxx',
              
              'redirect_url' => 'http://example.com/facebook-response.php',
              
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

LinkedIn And Google are same configuration as facebok has and these have same common functionality as Facebook Class has. So just change the Facebook Class with LinkedIn and Google and pass those credential then thats work fine. **For More Example see on Demo page**
