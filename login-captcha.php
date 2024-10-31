<?php
/*
 *Plugin Name: Login Captcha
 *Author: Sachin Londhe
 *Description: Captcha on Login Page for security purpose.
 *Version: 1.0
 *Author: Sachin Londhe
 *Author URI: #
 *Text Domain:  login-captcha
 *License: GPLv3
 *License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


add_action('init', 'Captcha_Session_Start', 1);

function Captcha_Session_Start()
{
	//Session Start
    if (!session_id()) {
        session_start();
    }
}

class Login_Captcha
{
    public function __construct()
    {
        add_action('login_form', array(
            $this,
            'Captcha_login_field'
        ));
        add_filter('authenticate', array(
            $this,
            'Captcha_Code_authenticate'
        ), 10, 3);
    }
    public function Captcha_Session_End()
    {
		//Session Destroy
        session_destroy();
    }
    
    
    public function Captcha_login_field()
    {
        
        $arr = array(
            'add',
            'sub',
            'mul',
            'div'
        );
        shuffle($arr);
        foreach ($arr as $value) {
            
            $digit1 = mt_rand(10, 100);
            $digit2 = mt_rand(1, 20);
            
            switch ($value) {
                case "add":
                    $math               = "$digit1 &plus; $digit2";
                    $_SESSION['answer'] = $digit1 + $digit2;
                    break;
                case "sub":
                    $math               = "$digit1 &minus; $digit2";
                    $_SESSION['answer'] = $digit1 - $digit2;
                    break;
                case "mul":
                    $math               = "$digit1 &times; $digit2";
                    $_SESSION['answer'] = $digit1 * $digit2;
                    break;
                case "div":
                    $math               = "$digit1 &divide; $digit2";
                    $_SESSION['answer'] = round($digit1 / $digit2);
                    break;
            }
        }
        
        //Output your HTML
?>
   <p>
        <strong>Captcha Code :  <?php
        echo $math;
?> </strong>
        <input type="text" size="20" value="" class="input" id="captcha_field" name="captcha_field"></label>
    </p>
<?php
    }
    
    
    public function Captcha_Code_authenticate($user, $username, $password)
    {
        Captcha_Session_Start();
        
        if (isset($_POST) && !empty($_POST)) {
            //Get POSTED value
            $captcha = $_POST['captcha_field'];
            $_SESSION['answer'];
            //Get user object
            $user = get_user_by('login', $username);
            
            if (!$user || empty($captcha) || $captcha != $_SESSION['answer']) {
                //User not found, or no value entered or doesn't match stored value - don't proceed.
                remove_action('authenticate', 'wp_authenticate_username_password', 20);
                remove_action('authenticate', 'wp_authenticate_email_password', 20);
                
                //Create an error to return to user
                return new WP_Error('denied', __("<strong>ERROR</strong>: Enter the Valid Captcha."));
            }
            
            //Make sure you return null 
            return null;
        }
    }
    
}
new Login_Captcha();