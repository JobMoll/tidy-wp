<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */

// check_if_license_is_valid();

if (strpos($_SERVER["REQUEST_URI"], '/wp-admin/admin.php?page=tidy-wp-license') !== false) {
activate_license_key( sanitize_text_field($_POST['licenseKey']));
}


if (isset($_GET['deativateLicense'])) {
if ($_GET['deativateLicense'] == 'yes') {
   deactivate_license_key();
   header("refresh: 0; url = " . get_bloginfo('url') . "/wp-admin/admin.php?page=tidy-wp-license"); 
}
}
 
// content of the page 
function tidywp_license_page() {
    ?>
    <div class="wrap">
    <h1>Tidy WP License</h1>
<hr>
<p>If you already have a license you can activate it here below! <br> If you need a new license you can get it on the <a href="https://tidywp.com/pricing" target="_blank">Tidy WP website.</a></p>


<?php
if (get_option('tidywp_license_key_valid') == 'false') {
?>
<form id="licenseForm" action="" method="post">
  <h3 class="licenseKeyH3">License Key:</h3>
  <input class="licenseKeyInput" type="text" name="licenseKey" placeholder="Paste license key here" minlength="32" maxlength="32"><br>
  <!--<button class="tidy-wp-button"><input type="submit" value="Activate!"></button>-->
</form>
  <button type="submit" form="licenseForm" style="margin-top: 20px;" class="tidy-wp-button">Activate license</button> <a style="margin-left: 10px;" href="https://tidywp.com/pricing" target="_blank">Or get yourself a license.</a>
<?php
} if (get_option('tidywp_license_key_valid') == 'true') {
    echo '<h3 class="licenseKeyH3"> Current active license key:</h3>';
    echo '<p>' . get_option('tidywp_license_key') . '</p>';
?>
<a href="admin.php?page=tidy-wp-license&deativateLicense=yes">
 <button class="tidy-wp-button">Deactivate license key</button>
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
