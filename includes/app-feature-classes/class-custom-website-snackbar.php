<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function tidy_wp_custom_website_snackbar(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        $enabled = sanitize_text_field($request['enabled']);
        if ($enabled == 'true') {
            update_option('tidy_wp_custom_website_snackbar_mode', 'true', 'no');
            echo 'true';
        } 
        if ($enabled == 'false') {
            update_option('tidy_wp_custom_website_snackbar_mode', 'false', 'no');
            echo 'false';
        }
    
        $text = sanitize_text_field($request['text']);
        if ($text !== null) {
            update_option('tidy_wp_custom_website_snackbar_text', $text, 'no');
        }
    
        $action_text = sanitize_text_field($request['action_text']);
        if ($action_text !== null) {
            update_option('tidy_wp_custom_website_snackbar_action_text', $action_text, 'no');
        }
    
        $position = sanitize_text_field($request['position']);
        if ($position !== null) {
            update_option('tidy_wp_custom_website_snackbar_position', $position, 'no');
        }
    
        $theme = sanitize_text_field($request['theme']);
        if ($theme !== null) {
            update_option('tidy_wp_custom_website_snackbar_theme', $theme, 'no');
        }
        
        $cookie_duration = sanitize_text_field($request['cookie_duration']);
        if ($cookie_duration !== null) {
            update_option('tidy_wp_custom_website_snackbar_cookie_duration', $cookie_duration, 'no');
        }
        
        $show_duration_in_sec = sanitize_text_field($request['show_duration_in_sec']);
        if ($show_duration_in_sec !== null) {
            update_option('tidy_wp_custom_website_snackbar_show_duration_in_sec', $show_duration_in_sec, 'no');
        }
        
        if (sanitize_text_field($request['show']) == 'true') {
            $showSnackbarData = array(
            'SnackbarEnabled' => get_option('tidy_wp_custom_website_snackbar_mode'), 
            'SnackbarText' => get_option('tidy_wp_custom_website_snackbar_text'), 
            'SnackbarActionText' => get_option('tidy_wp_custom_website_snackbar_action_text'),
            'SnackbarPosition' => get_option('tidy_wp_custom_website_snackbar_position'),
            'SnackbarTheme' => get_option('tidy_wp_custom_website_snackbar_theme'),
            'SnackbarCookieDuration' => get_option('tidy_wp_custom_website_snackbar_cookie_duration'),
            'SnackbarDurationInSec' => get_option('tidy_wp_custom_website_snackbar_show_duration_in_sec'),
         );
           
           echo json_encode($showSnackbarData);
        }
    
        
}
} 

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'custom-website-snackbar', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_custom_website_snackbar',
    'permission_callback' => '__return_true',
 ));
});
