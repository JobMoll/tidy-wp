<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */

// voeg addon option name toe aan plugin activate en deactivate
// tidy-wp/includes/plugin-feature-classes/class-license-check.php op lijn 67 voeg toe zodat ie wordt gedeactiveerd als de licensie is verlopen

if (isset($_GET['which_addon']) && isset($_GET['enable'])) {
    
if (get_option('tidy_wp_license_key_valid') == 'true' && get_option('tidy_wp_license_key_valid') != '' && strtotime(get_option('tidy_wp_license_key_last_checked')) <= strtotime('-12 hours')) {
  if (check_if_license_is_valid() == 'true') {
  addon_activate_or_deactivate(sanitize_text_field($_GET['which_addon']), sanitize_text_field($_GET['enable']));
  }
} else {
  addon_activate_or_deactivate(sanitize_text_field($_GET['which_addon']), sanitize_text_field($_GET['enable']));
}
}

function addon_activate_or_deactivate($addonName, $enable) {
    
if (get_option('tidy_wp_license_key_valid') == 'true' && get_option('tidy_wp_license_key_valid') != '') {
   update_option('tidy_wp_addons_' . $addonName, $enable);
   header("Refresh:0; url=admin.php?page=tidy-wp-addon");
} else {
   header("Refresh:0; url=admin.php?page=tidy-wp-addon");
}
}


// content of the page 
function tidy_wp_addon_page() {
    
if (get_option('tidy_wp_license_key_valid') == 'false') {
    ?>
    <style>
    .notAddonPro {
        cursor: not-allowed;
        pointer-events: none;
    }    
    </style>
    <?php
}
    ?>
    
    <div class="wrap">
<h1>Tidy WP Addons
<?php
if (get_option('tidy_wp_license_key_valid') == 'false') {
    echo '(You don\'t have Tidy WP Pro)';
}
    ?>
</h1>

<hr>
<p style="margin-bottom: 15px;">You can enable / disable Tidy WP addons right here! </br> To use the pro addons you need to submit a valid license key <a href="<?php get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=tidy-wp-license">right here.</a></p>

<div class="pluginsAvailable">
<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Snackbar </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Gives you the option to change the theme, gives you more 'how often' options & allows you to exclude pages within the app.</p>

<?php
if (get_option('tidy_wp_addons_snackbar') == 'true') {
?>
<a href="admin.php?page=tidy-wp-addon&which_addon=snackbar&enable=false" class="notAddonPro">  
 <button class="tidy-wp-button">Disable snackbar</button>
 </a> 
<?php
} else {
?>
<a href="admin.php?page=tidy-wp-addon&which_addon=snackbar&enable=true" class="notAddonPro">
 <button class="tidy-wp-button">Enable snackbar</button>
 </a> 
<?php
}
?>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">User roles </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>When adding a new account you can choose a user role for the user. This allows you to limit some features for certain users.</p>
    
<?php
if (get_option('tidy_wp_addons_user_roles') == 'true') {
?>
<a href="admin.php?page=tidy-wp-addon&which_addon=user_roles&enable=false" class="notAddonPro">  
 <button class="tidy-wp-button">Disable user roles</button>
 </a> 
<?php
} else {
?>
<a href="admin.php?page=tidy-wp-addon&which_addon=user_roles&enable=true" class="notAddonPro">
 <button class="tidy-wp-button">Enable user roles</button>
 </a> 
<?php
}
?>
</div> 
</div>


<div class="pluginsComingSoon">
    <h1 style="margin-top: 50px;">Addons that might be coming soon!</h1>
<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Heatmap </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Will create a heatmap of your site with the following data: where the your users click, interact and focusses.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable heatmap</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Redirections </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to make 301 (Moved permanently), 302 (Temporarily moved), 404 (Not found) redirects. Good for your SEO.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable redirections</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">PHP code snippets </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to insert custom code into your website header & footer from within the app. Handy for adding code snippets on the go.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable insert header & footer</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Back-end notice </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to write a notice that can be seen by all the people that have access to the wp back-end. You can even customize how it looks.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable back-end notice</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Uptime monitor </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>We will check your website every 60 seconds to see if it is still live. If it is down we will send you a notification on your phone!</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable uptime monitor</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Performance check </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to run a performance test for your website. We will show you things you can improve and thing that are already optimal.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable performance check</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Client reports </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>We'll send you a report on how the website has been presented in the last week, month or 3 months. You can customize the looks of the report.</p>
    <!--(traffic, welke functies aan en uit, hoeveel database cleanup, woocommerce sales etc)-->
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable client reports</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Private pages </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Gives you the option to make pages private with a password or to allow only logged in users of a certain role to access them.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable private pages</button>
 </a>
</div> 


</div>
    </div>
    <?php
}
