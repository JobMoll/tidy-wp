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

// get website details to add
$addWebsiteToAccount = '{"BaseDomainURL":"' . get_bloginfo( 'wpurl' ) . 
'","DomainURL":"' . $domainURL .
'","SecretToken":"' . get_option('tidywp_secret_token') . 
'","WebsiteName":"' . $websiteName .
'","AdminEmail":"' . $adminEmail .  
'"}';

return $addWebsiteToAccount;
}




if (strpos($_SERVER["REQUEST_URI"], '/wp-admin/admin.php?page=tidy-wp') !== false && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
    if ($_POST['tidywp_username1'] != '' && $_POST['tidywp_password1'] != '') {
    update_option( 'tidywp_website_username1', encrypt_and_decrypt( $_POST['tidywp_username1'], 'e' ), 'no' );
    update_option( 'tidywp_website_password1', encrypt_and_decrypt( $_POST['tidywp_password1'], 'e' ), 'no' );
    
    websiteToServer(get_option( 'tidywp_website_username1'), get_option( 'tidywp_website_password1'), '1');
    }
    
    if ($_POST['tidywp_username2'] != '' && $_POST['tidywp_password2'] != '') {
    update_option( 'tidywp_website_username2', encrypt_and_decrypt( $_POST['tidywp_username2'], 'e' ), 'no' );
    update_option( 'tidywp_website_password2', encrypt_and_decrypt( $_POST['tidywp_password2'], 'e' ), 'no' );
    
    websiteToServer(get_option( 'tidywp_website_username2'), get_option( 'tidywp_website_password2'), '2');
    }
}



function websiteToServer($username, $password, $accountNumber) {
// generate cookie for user  
$jsonRequest = url_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt( $username, 'd' ) . '&password=' . encrypt_and_decrypt( $password, 'd' ));

$addWebsiteToAccount = generateWebsiteDetails();

// get cookie and old description (old websites)
$credentialsStatus = json_decode($jsonRequest)->{'status'};

if ($credentialsStatus == 'ok') {
$authCookie = json_decode($jsonRequest)->{'cookie'};
$userDescription = json_decode($jsonRequest)->{'user'}->{'description'};

if ($userDescription == '') {
url_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=' . $addWebsiteToAccount . '');    
} else if (strpos($userDescription, $addWebsiteToAccount) === false)  {
url_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description='. $userDescription . ',' . $addWebsiteToAccount . '');  
} else {
// already added
}
} else {
    update_option( 'tidywp_website_username' . $accountNumber, '', 'no' );
    update_option( 'tidywp_website_password' . $accountNumber, '', 'no' );
    $_SESSION['wrongLoginMessage'] = 'We didn\'t find an account with these login credentials...';
    
}
}


if (isset($_GET['removeWebsite'])) {
if ($_GET['removeWebsite'] == '1' && get_option('tidywp_website_username1') != '') {
   removeWebsite(1);
}
if ($_GET['removeWebsite'] == '2' && get_option('tidywp_website_username2') != '') {
   removeWebsite(2);
}
}

function removeWebsite($accountNumber) {
removeWebsiteStringFromServer(get_option( 'tidywp_website_username' . $accountNumber), get_option( 'tidywp_website_password' . $accountNumber));
  
// last thing to do
update_option( 'tidywp_website_username' . $accountNumber, '', 'no' );
update_option( 'tidywp_website_password' . $accountNumber, '', 'no' );
header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp"); 
}

function removeWebsiteStringFromServer($username, $password) {
// generate cookie for user  
$jsonRequest = url_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt( $username, 'd' ) . '&password=' . encrypt_and_decrypt( $password, 'd' ));

$removeThisWebsiteString = generateWebsiteDetails();

// get cookie and old description (old websites)
$authCookie = json_decode($jsonRequest)->{'cookie'};
$userDescription = json_decode($jsonRequest)->{'user'}->{'description'};

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

url_get_contents('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=' . $removedUserDescription . '');  
}



// reset secrettoken and path
if (isset($_GET['reset'])) {
if ($_GET['reset'] == 'yes') {
   resetTokenAndPath();
}
}

function resetTokenAndPath() {
  if (get_option( 'tidywp_website_username1') != '' && get_option( 'tidywp_website_password1') != '') {
  removeWebsiteStringFromServer(get_option( 'tidywp_website_username1'), get_option( 'tidywp_website_password1'));
  }
  
  if (get_option( 'tidywp_website_username2') != '' && get_option( 'tidywp_website_password2') != '') {
    removeWebsiteStringFromServer(get_option( 'tidywp_website_username2'), get_option( 'tidywp_website_password2'));
  }
  
  update_option( 'tidywp_secret_path', generateRandomString(64), 'no' );
  update_option( 'tidywp_secret_token', generateRandomString(64), 'no' );
  
  if (get_option( 'tidywp_website_username1') != '' && get_option( 'tidywp_website_password1') != '') {
  websiteToServer(get_option( 'tidywp_website_username1'), get_option( 'tidywp_website_password1'), '1');
  }
  
  if (get_option( 'tidywp_website_username2') != '' && get_option( 'tidywp_website_password2') != '') {
    websiteToServer(get_option( 'tidywp_website_username2'), get_option( 'tidywp_website_password2'), '2');
  }

  header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp"); 
}



// content of the page 
function tidywp_main_page(  ) { 
    ?>
    <div class="wrap">
    <h1>Tidy WP</h1>
<hr>
<?php
if (get_option('tidywp_website_username1') == '') {
?>
<!-- first register form -->
<form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">Login to add this website:</h3>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="text" name="tidywp_username1" placeholder="Your username"><br>
  <input class="licenseKeyInput" type="password" name="tidywp_password1" placeholder="Your password"><br>
</form>
<?php
if (isset($_SESSION['wrongLoginMessage']))
{
    ?><p class='errorMessage'> <?php echo $_SESSION['wrongLoginMessage'];?> </p><?php
    unset($_SESSION['wrongLoginMessage']);
} ?>
  <button type="submit" form="licenseForm" style="margin-top: 20px;" class="tidy-wp-button">Add this website to the app</button> <a style="margin-left: 10px;" href="https://tidywp.com/wp-login.php?action=register" target="_blank">Or create a new account.</a>
<!-- end first register form -->
<?php
} else {
    echo '<h3 class="licenseKeyH3"> Remove this website from your account:</h3>';
    echo '<p>' . get_bloginfo( 'wpurl' ) . '</p>';
?>
<a href="admin.php?page=tidy-wp&removeWebsite=1">
 <button class="tidy-wp-button">Remove website from <?php echo encrypt_and_decrypt( get_option( 'tidywp_website_username1'),'d') ?>'s account</button>
 </a>
 <?php
}
 if (get_option('tidywp_website_username2') == '' && get_option('tidywp_website_username1') != '') { ?>
 <!-- second register form -->
 <form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">Add website to an extra Tidy WP account:</h3>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="text" name="tidywp_username2" placeholder="Your username"><br>
  <input class="licenseKeyInput" type="password" name="tidywp_password2" placeholder="Your password"><br>
</form>
<?php
if (isset($_SESSION['wrongLoginMessage']))
{
    ?><p class='errorMessage'> <?php echo $_SESSION['wrongLoginMessage'];?> </p><?php
    unset($_SESSION['wrongLoginMessage']);
} ?>
  <button type="submit" form="licenseForm" style="margin-top: 20px;" class="tidy-wp-button">Add this website to the app</button> <a style="margin-left: 10px;" href="https://tidywp.com/wp-login.php?action=register" target="_blank">Or create a new account.</a>
<!-- end second register form -->
<?php
} else if (get_option('tidywp_website_username2') != '') {
?>
<br>
<a href="admin.php?page=tidy-wp&removeWebsite=2">
 <button style="margin-top: 20px;"  class="tidy-wp-button">Remove website from <?php echo encrypt_and_decrypt( get_option( 'tidywp_website_username2'),'d') ?>'s account</button>
 </a>
<?php
}   if (get_option( 'tidywp_website_username1') != '' || get_option( 'tidywp_website_username2') != '') {
?>
 <p style="padding-top: 30px;">Did you have the code scanned by someone else who shouldn't have access anymore? <br> Or do you feel that someone else is running your website? <br> Reset the QR code with the button below and pair your app with the website again.</p>
<a href="admin.php?page=tidy-wp&reset=yes">
<button class="tidy-wp-button">Reset</button>
</a>
<?php
}
?>

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
