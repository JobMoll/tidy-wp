<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */

function tidy_wp_main_page() {
    ?>
    <div class="wrap">
    <h1>Tidy WP</h1>
<hr>
<?php if (is_ssl() == true) { ?>
<p style="margin-bottom: 15px;">Login with your Tidy WP account to add this website! <br> You can activate your license key <a href="<?php get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=tidy-wp-license">right here.</a></p>
<?php
if (strpos(get_bloginfo('wpurl'), 'localhost') !== false) {
   ?>
   <h3 class="licenseKeyH3">You can't add Tidy WP on a localhost...</h3>
   <?php
} else {
    
$addWebsiteToAccount = array(
"websiteName" => get_bloginfo('name'),
"domainURL" => get_bloginfo('wpurl'),
"secretAPIPath" => get_option('tidy_wp_secret_path'),
"secretAPIKey" => get_option('tidy_wp_secret_token'),
);

    
   ?>
   <h3 class="licenseKeyH3">Scan the QR code.</h3>
<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo http_build_query($addWebsiteToAccount); ?>" title="Scan this QR code in the Tidy WP app" style="background: #fff; padding: 25px;"/>
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
} else {
    ?>
   <p style="margin-bottom: 15px;">Your website doesn't have a valid SSL connection... <br> You need a valid SSL cartificate for a secure connection with the Tidy WP App.</p>
<?php
}
}
