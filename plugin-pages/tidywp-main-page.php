<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    

function generateWebsiteDetails($accountNumber) {
$domainURL = get_bloginfo( 'wpurl' ) . '/wp-json/' . get_option('tidy_wp_secret_path');
$websiteName = get_bloginfo('name');
$adminEmail = get_bloginfo( 'admin_email' );

require_once(ABSPATH . 'wp-includes/pluggable.php');

// get website details to add
$addWebsiteToAccount = '{"BaseDomainURL":"' . get_bloginfo( 'wpurl' ) .
'","DomainURL":"' . esc_url_raw($domainURL) .
'","SecretToken":"' . get_option('tidy_wp_secret_token') .
'","UserRole":"' . get_option('tidy_wp_website_userRole' . $accountNumber) .
'","WebsiteName":"' . sanitize_user($websiteName) .
'","AdminEmail":"' . sanitize_email($adminEmail) .
'"}';

return $addWebsiteToAccount;
}

if (strpos($_SERVER["REQUEST_URI"], '/wp-admin/admin.php?page=tidy-wp') !== false && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
    if (isset($_POST['tidy_wp_username1']) && isset($_POST['tidy_wp_password1']) && isset($_POST['tidy_wp_userRole1'])) {
    update_option( 'tidy_wp_website_username1', encrypt_and_decrypt( sanitize_text_field($_POST['tidy_wp_username1']), 'e' ), 'no' );
    update_option( 'tidy_wp_website_password1', encrypt_and_decrypt( sanitize_text_field($_POST['tidy_wp_password1']), 'e' ), 'no' );
    update_option( 'tidy_wp_website_userRole1', sanitize_text_field($_POST['tidy_wp_userRole1']), 'no' );
        
    websiteToServer(get_option( 'tidy_wp_website_username1'), get_option( 'tidy_wp_website_password1'), '1');
    }
    
    if (isset($_POST['tidy_wp_username2']) &&
        isset($_POST['tidy_wp_userRole2']) &&
        isset($_POST['tidy_wp_password2']) && sanitize_text_field($_POST['tidy_wp_username2']) != encrypt_and_decrypt( get_option( 'tidy_wp_website_username1'),'d')) {
    update_option( 'tidy_wp_website_username2', encrypt_and_decrypt( sanitize_text_field($_POST['tidy_wp_username2']), 'e' ), 'no' );
    update_option( 'tidy_wp_website_password2', encrypt_and_decrypt( sanitize_text_field($_POST['tidy_wp_password2']), 'e' ), 'no' );
    update_option( 'tidy_wp_website_userRole2', sanitize_text_field($_POST['tidy_wp_userRole2']), 'no' );
    
    websiteToServer(get_option( 'tidy_wp_website_username2'), get_option( 'tidy_wp_website_password2'), '2');
    } else if (isset($_POST['tidy_wp_username2']) && get_option( 'tidy_wp_website_username1') != '' && sanitize_text_field($_POST['tidy_wp_username1']) == '') {
        $_SESSION['wrongLoginMessage'] = 'This account is already added...';
    }
}



function websiteToServer($username, $password, $accountNumber) {
// generate cookie for user
$jsonRequest = wp_remote_get('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt($username, 'd') . '&password=' . encrypt_and_decrypt($password, 'd'));

$addWebsiteToAccount = generateWebsiteDetails($accountNumber);

// get cookie and old description (old websites)
$credentialsStatus = json_decode($jsonRequest['body'])->{'status'};

if ($credentialsStatus == 'ok') {
$authCookie = json_decode($jsonRequest['body'])->{'cookie'};
$userDescription = json_decode($jsonRequest['body'])->{'user'}->{'description'};

if ($userDescription == '') {
wp_remote_get('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=' . $addWebsiteToAccount . '');
} else if (strpos($userDescription, $addWebsiteToAccount) === false)  {
wp_remote_get('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description='. $userDescription . ',' . $addWebsiteToAccount . '');
} else {
    $_SESSION['wrongLoginMessage'] = 'This domain is already added to the this Tidy WP Account...';
}
} else {
    update_option( 'tidy_wp_website_username' . $accountNumber, '', 'no' );
    update_option( 'tidy_wp_website_password' . $accountNumber, '', 'no' );
    $_SESSION['wrongLoginMessage'] = 'We didn\'t find an account with these login credentials...';
}
}


if (isset($_GET['removeWebsite'])) {
if ($_GET['removeWebsite'] == '1' && get_option('tidy_wp_website_username1') != '') {
   removeWebsite(1);
}
if ($_GET['removeWebsite'] == '2' && get_option('tidy_wp_website_username2') != '') {
   removeWebsite(2);
}
}

function removeWebsite($accountNumber) {
removeWebsiteStringFromServer(get_option( 'tidy_wp_website_username' . $accountNumber), get_option( 'tidy_wp_website_password' . $accountNumber), $accountNumber);
  
// last thing to do
update_option( 'tidy_wp_website_username' . $accountNumber, '', 'no' );
update_option( 'tidy_wp_website_password' . $accountNumber, '', 'no' );
update_option( 'tidy_wp_website_userRole' . $accountNumber, '', 'no' );
header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp");
}

function removeWebsiteStringFromServer($username, $password, $accountNumber) {
// generate cookie for user
$jsonRequest = wp_remote_get('https://tidywp.com/56hd835Hd8q12ksf/user/generate_auth_cookie/?username=' . encrypt_and_decrypt( $username, 'd' ) . '&password=' . encrypt_and_decrypt( $password, 'd' ));

$removeThisWebsiteString = generateWebsiteDetails($accountNumber);

// get cookie and old description (old websites)
$authCookie = json_decode($jsonRequest['body'])->{'cookie'};
$userDescription = json_decode($jsonRequest['body'])->{'user'}->{'description'};

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

wp_remote_get('https://tidywp.com/56hd835Hd8q12ksf/user/update_user_meta_vars/?cookie=' . $authCookie . '&description=' . $removedUserDescription . '');
}



// reset secrettoken and path
if (isset($_GET['reset'])) {
if ($_GET['reset'] == 'yes') {
   resetTokenAndPath();
}
}

function resetTokenAndPath() {
  if (get_option( 'tidy_wp_website_username1') != '' && get_option( 'tidy_wp_website_password1') != '') {
  removeWebsiteStringFromServer(get_option( 'tidy_wp_website_username1'), get_option( 'tidy_wp_website_password1'), 1);
  }
  
  if (get_option( 'tidy_wp_website_username2') != '' && get_option( 'tidy_wp_website_password2') != '') {
    removeWebsiteStringFromServer(get_option( 'tidy_wp_website_username2'), get_option( 'tidy_wp_website_password2'), 2);
  }
  
  update_option( 'tidy_wp_secret_path', generateRandomString(64), 'no' );
  update_option( 'tidy_wp_secret_token', generateRandomString(64), 'no' );
  
  if (get_option( 'tidy_wp_website_username1') != '' && get_option( 'tidy_wp_website_password1') != '') {
  websiteToServer(get_option( 'tidy_wp_website_username1'), get_option( 'tidy_wp_website_password1'), '1');
  }
  
  if (get_option( 'tidy_wp_website_username2') != '' && get_option( 'tidy_wp_website_password2') != '') {
    websiteToServer(get_option( 'tidy_wp_website_username2'), get_option( 'tidy_wp_website_password2'), '2');
  }

  header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp");
}



// content of the page
function tidy_wp_main_page(  ) {
    ?>
    <div class="wrap">
    <h1>Tidy WP</h1>
<hr>
<?php if (is_ssl() == true) { ?>
<p style="margin-bottom: 15px;">Login with your Tidy WP account to add this website! </br> You can activate your license key <a href="<?php get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=tidy-wp-license">right here.</a></p>
<?php
if (strpos(get_bloginfo( 'wpurl' ), 'localhost') !== false) {
   ?>
   <h3 class="licenseKeyH3">You can't add Tidy WP on a localhost...</h3>
   <?php
} else {
if (get_option('tidy_wp_website_username1') == '') {
?>
<!-- first register form -->
<form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">Login to add this website:</h3>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="text" name="tidy_wp_username1" placeholder="Your username" required><br>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="password" name="tidy_wp_password1" placeholder="Your password" required><br>
<select class="userRoleSelectBox" name="tidy_wp_userRole1" required>
  <option value="full-access">Full Access</option>
  <option value="read-only"<?php if (get_option('tidy_wp_addon_user_roles_license_key_valid') == 'false' || get_option('tidy_wp_addon_user_roles_license_key_valid') == '') { echo ' label="Read Only (Need Tidy WP Pro)" disabled'; } ?>>Read Only</option>
</select><br>
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
 <button class="tidy-wp-button">Remove website from <?php echo encrypt_and_decrypt( get_option( 'tidy_wp_website_username1'),'d') ?>'s account</button>
 </a>
 <?php
}
 if (get_option('tidy_wp_website_username2') == '' && get_option('tidy_wp_website_username1') != '') { ?>
 <!-- second register form -->
 <form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">Add website to an extra Tidy WP account:</h3>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="text" name="tidy_wp_username2" placeholder="Your username" required><br>
  <input class="licenseKeyInput" style="margin-bottom: 10px;" type="password" name="tidy_wp_password2" placeholder="Your password" required><br>
<select class="userRoleSelectBox" name="tidy_wp_userRole2" required>
  <option value="full-access">Full Access</option>
  <option value="read-only"<?php if (get_option('tidy_wp_addon_user_roles_license_key_valid') == 'false' || get_option('tidy_wp_addon_user_roles_license_key_valid') == '') { echo ' label="Read Only (Need Tidy WP Pro)" disabled'; } ?>>Read Only</option>
</select><br>
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
} else if (get_option('tidy_wp_website_username2') != '') {
?>
<br>
<a href="admin.php?page=tidy-wp&removeWebsite=2">
 <button style="margin-top: 20px;"  class="tidy-wp-button">Remove website from <?php echo encrypt_and_decrypt( get_option( 'tidy_wp_website_username2'),'d') ?>'s account</button>
 </a>
<?php
}   if (get_option( 'tidy_wp_website_username1') != '' || get_option( 'tidy_wp_website_username2') != '') {
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
                                <p>Tidy WP can update your plugins automatically for you.</p>
                            </li>
                            <li class="anim2">
                                <p>Tidy WP has a feature to make your website safer and more secure.</p>
                            </li>
                            <li class="anim3">
                                <p>Tidy WP shows your handy statistics about your website.</p>
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
} else {
    ?>
   <p style="margin-bottom: 15px;">Your website doesn't have a valid SSL connection... </br> You need a valid SSL cartificate for a secure connection with the Tidy WP App.</p>
<?php
}
} 
