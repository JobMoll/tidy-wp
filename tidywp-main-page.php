<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    

// create QRCode with data
if (strpos($_SERVER["REQUEST_URI"], '/wp-admin') !== false) {

$domainURL = get_bloginfo( 'wpurl' ) . '/wp-json/' . get_option('tidywp_secret_path');
$websiteName = get_bloginfo( 'name' );
$adminEmail = get_bloginfo( 'admin_email' ); 

require_once(ABSPATH.'wp-includes/pluggable.php');
if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $loggedinUsername = $current_user->user_login;
}

if (current_user_can( 'manage_options' )) {
$stringForQRCodeImage = 'https://chart.googleapis.com/chart?cht=qr&chs=270x270&chld=L|1&chl=' . 
'{"BaseDomainURL":"' . get_bloginfo( 'wpurl' ) .
'","DomainURL":"' . $domainURL .
'","SecretToken":"' . $secretToken . 
'","WebsiteName":"' . $websiteName .
'","AdminEmail":"' . $adminEmail .  
'","LoggedInUsername":"' . $loggedinUsername . 
'"}]&choe=UTF-8"';
} else {
    $stringForQRCodeImage = '';
}
// in the app get the value from the qr code and save the token together by the website profile and add to the user account.
}



// reset secrettoken and path
if (isset($_GET['reset'])) {
if ($_GET['reset'] == 'yes') {
    resetTokenAndPath();
}
}
function resetTokenAndPath() {
   update_option( 'tidywp_secret_path', generateRandomString(16), 'no' );
   update_option( 'tidywp_secret_token', generateRandomString(16), 'no' );
   header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp"); 
}




// content of the page 
function tidywp_main_page(  ) { 
    ?>
    <div class="wrap">
    <h1>Tidy WP</h1>
<hr>
<p>Scan this QR code within the <a href="https://tidywp.com" target="_blank">Tidy WP app</a> to pair your website automatically!</p>
<img class="QRCode" src='<? echo $GLOBALS['stringForQRCodeImage'] ?>' />

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
