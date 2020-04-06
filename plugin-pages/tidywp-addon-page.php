<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */

// content of the page 
function tidywp_addon_page() {
    ?>
    <div class="wrap">
<h1>Tidy WP Addons</h1>
<hr>
<p style="margin-bottom: 15px;">You can enable / disable Tidy WP addons right here! </br> To use the pro addons you need to submit a valid license key <a href="<?php get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=tidy-wp-license">right here.</a></p>
<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">Snackbar </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Gives you the option to change the theme, gives you more 'how often' options & allows you to exclude pages within the app.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable snackbar</button>
 </a>
</div> 

<div class="addonBlock">
    <h2 class="titlenospace" style="display: inline;">User roles </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>When adding a new account you can choose a user role for the user. This allows you to limit some features for certain users.</p>
<a href="admin.php?page=tidy-wp-license&enableUserRoles=yes">
 <button class="tidy-wp-button">Enable user roles</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Heatmap </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Will create a heatmap of your site with the following data: where the your users click, interact and focusses.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable heatmap</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Redirections </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to make 301 (Moved permanently), 302 (Temporarily moved), 404 (Not found) redirects. Good for your SEO.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable redirections</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">PHP code snippets </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to insert custom code into your website header & footer from within the app. Handy for adding code snippets on the go.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable insert header & footer</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Back-end notice </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to write a notice that can be seen by all the people that have access to the wp back-end. You can even customize how it looks.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable back-end notice</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Uptime monitor </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>We will check your website every 60 seconds to see if it is still live. If it is down we will send you a notification on your phone!</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable uptime monitor</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Performance check </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>Allows you to run a performance test for your website. We will show you things you can improve and thing that are already optimal.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable performance check</button>
 </a>
</div> 

<div class="addonBlock addonComingSoon">
    <h2 class="titlenospace" style="display: inline;">Client reports </h2><a style="text-decoration: none;" href="https://tidywp.com/pricing/" target="_blank"><h2 class="proBadge">- Tidy WP Pro</h2></a>
    <p>We'll send you a report on how the website has been presented in the last week, month or 3 months. You can customize the looks of the report.</p>
<a href="admin.php?page=tidy-wp-license&enableWebsiteSnackbar=yes">
 <button class="tidy-wp-button">Enable client reports</button>
 </a>
</div> 

    </div>
    <?php
}
