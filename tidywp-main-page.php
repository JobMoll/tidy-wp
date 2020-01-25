<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    

function generateWebsiteDetails() {
$domainURL = get_bloginfo( 'wpurl' ) . '/wp-json/' . get_option('tidywp_secret_path');
$websiteName = get_bloginfo( 'name' );
$adminEmail = get_bloginfo( 'admin_email' ); 

require_once(ABSPATH.'wp-includes/pluggable.php');
if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $loggedinUsername = $current_user->user_login;
}

// get website details to add
$addWebsiteToAccount = '{"BaseDomainURL":"' . get_bloginfo( 'wpurl' ) . 
'","DomainURL":"' . $domainURL .
'","SecretToken":"' . get_option('tidywp_secret_token') . 
'","WebsiteName":"' . $websiteName .
'","AdminEmail":"' . $adminEmail .  
'","LoggedInUsername":"' . $loggedinUsername . 
'"}';

return $addWebsiteToAccount;
}




if (strpos($_SERVER["REQUEST_URI"], '/wp-admin') !== false && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
    update_option( 'tidywp_website_username', encrypt_and_decrypt( $_POST['username'], 'e' ), 'no' );
    update_option( 'tidywp_website_password', encrypt_and_decrypt( $_POST['password'], 'e' ), 'no' );
    websiteToServer();
}

function websiteToServer() {
// generate cookie for user  
$jsonRequest = file_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt( get_option( 'tidywp_website_username'), 'd' ) . '&password=' . encrypt_and_decrypt( get_option( 'tidywp_website_password'), 'd' ));

$addWebsiteToAccount = generateWebsiteDetails();

// get cookie and old description (old websites)
$credentialsStatus = json_decode($jsonRequest)->{'status'};

if ($credentialsStatus == 'ok') {
$authCookie = json_decode($jsonRequest)->{'cookie'};
$userDescription = json_decode($jsonRequest)->{'user'}->{'description'};
$userDescription = substr($userDescription, 1, -1);

if ($userDescription == '' || $userDescription == '[]') {
file_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=[' . $addWebsiteToAccount . ']');    
} else if (strpos($userDescription, $addWebsiteToAccount) === false)  {
file_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=['. $userDescription . ',' . $addWebsiteToAccount . ']');  
} else {
// already added
}
} else {
    update_option( 'tidywp_website_username', '', 'no' );
    update_option( 'tidywp_website_password', '', 'no' );
    echo 'fgjkhsdklfvhgsdlkjfvhsdkfvgsdklfvhgsfkjhvgsdfv wrong login';
}
}



if (isset($_GET['removeWebsite'])) {
if ($_GET['removeWebsite'] == 'yes') {
   removeWebsite();
}
}

function removeWebsite() {
removeWebsiteStringFromServer();
// last thing to do
update_option( 'tidywp_website_username', '', 'no' );
update_option( 'tidywp_website_password', '', 'no' );
header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp"); 
}

function removeWebsiteStringFromServer() {
// generate cookie for user  
$jsonRequest = file_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt( get_option( 'tidywp_website_username'), 'd' ) . '&password=' . encrypt_and_decrypt( get_option( 'tidywp_website_password'), 'd' ));

$removeThisWebsiteString = generateWebsiteDetails();

// get cookie and old description (old websites)
$authCookie = json_decode($jsonRequest)->{'cookie'};
$userDescription = json_decode($jsonRequest)->{'user'}->{'description'};
$userDescription = substr($userDescription, 1, -1);  

$removedUserDescription = str_replace($removeThisWebsiteString, '', $userDescription);

$removedUserDescription = str_replace(',,', ',', $removedUserDescription);

// first
if (substr($removedUserDescription, 0, 1) == ',') {
    $removedUserDescription = substr($removedUserDescription, 1);  
}
// last
if (substr($removedUserDescription, -1) == ',') {
    $removedUserDescription = substr($removedUserDescription, 0, -1);  
}

file_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=[' . $removedUserDescription . ']');  
}




// reset secrettoken and path
if (isset($_GET['reset'])) {
if ($_GET['reset'] == 'yes') {
   resetTokenAndPath();
}
}

function resetTokenAndPath() {
   removeWebsiteStringFromServer();
  update_option( 'tidywp_secret_path', generateRandomString(16), 'no' );
  update_option( 'tidywp_secret_token', generateRandomString(16), 'no' );
  websiteToServer();
  header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp"); 
}



// content of the page 
function tidywp_main_page(  ) { 
    ?>
    <div class="wrap">
    <h1>Tidy WP</h1>
<hr>
<?php
if (get_option('tidywp_website_username') == '') {
?>
<form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">Login to add this website:</h3>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="text" name="username" placeholder="Your username"><br>
  <input class="licenseKeyInput" type="password" name="password" placeholder="Your password"><br>
</form>
  <button type="submit" form="licenseForm" style="margin-top: 20px;" class="tidy-wp-button">Add this website to the app</button> <a style="margin-left: 10px;" href="https://tidywp.com/wp-login.php?action=register" target="_blank">Or create a new account.</a>
<?php
} if (get_option('tidywp_website_username') != '') {
    echo '<h3 class="licenseKeyH3"> Remove this website from your account:</h3>';
    echo '<p>' . get_bloginfo( 'wpurl' ) . '</p>';
?>
<a href="admin.php?page=tidy-wp&removeWebsite=yes">
 <button class="tidy-wp-button">Remove website</button>
 </a>
<?php
}
?>
<p style="padding-top: 30px;">Did you have the code scanned by someone else who shouldn't have access anymore? <br> Or do you feel that someone else is running your website? <br> Reset the QR code with the button below and pair your app with the website again.</p>
<a href="admin.php?page=tidy-wp&reset=yes">
<button class="tidy-wp-button">Reset</button>
</a>

<div class="tipsandmore">
    <div class="tipsblock">
        <div class="infocontainer">

            <h2 class="titlenospace">Did you know that?</h2>

            <div class="content-slider">
                <div class="slider">
                    <div class="mask">
                        <ul>
                            <li class="anim1">
                                <p>Tidy WP can update your plugins automatically for you.</a></p>
                            </li>
                            <li class="anim2">
                                <p>Tidy WP has a feature to make your website safer and more secure.</p>
                            </li>
                            <li class="anim3">
                                <p>Tidy WP can make a backup of your site in seconds.</p>
                            </li>
                            <li class="anim4">
                                <p>Tidy WP is the easiest way to manage multiple Wordpress websites</p>
                            </li>
                            <li class="anim5">
                                <p>Tidy WP doens't store any off your information on our own servers.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="infoblock">
            <div class="infocontainer">
                <h2>Feedback, bugs & ideas</h2>
                <p>Send me your feedback, the bugs you found or your new ideas so I can improve the plugin & app!</p>
                <a href="https://tidywp.com/feature-requests/" target="_blank">
                    <button class="tidy-wp-button">Feature request</button>
                </a>
            </div>

            <div style="padding-top: 15px;" class="infocontainer">
                <h2>Rating & Spread the word</h2>
                <p>If you find this plugin useful, could you give it 5 stars? That keeps me motivated to release new updates.</p>
                <a href="https://tidywp.com" target="_blank">
                    <button class="tidy-wp-button" onclick="">Rate this plugin</button>
                </a>

            </div>
        </div>
    </div>
    </div>
    <?php
}
