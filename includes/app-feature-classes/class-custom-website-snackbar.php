<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function custom_website_snackbar($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidywp_custom_website_snackbar_mode', 'true', 'no' );
            echo 'true';
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidywp_custom_website_snackbar_mode', 'false', 'no' );
            echo 'false';
        }
        
        if ($data->get_param('text') !== null) {
            update_option( 'tidywp_custom_website_snackbar_text', sanitize_text_field($data->get_param('text')), 'no' );
        }
        
        if ($data->get_param('action_text') !== null) {
            update_option( 'tidywp_custom_website_snackbar_action_text', sanitize_text_field($data->get_param('action_text')), 'no' );
        }
        
        if ($data->get_param('position') !== null) {
            update_option( 'tidywp_custom_website_snackbar_position', sanitize_text_field($data->get_param('position')), 'no' );
        }
        
        if ($data->get_param('theme') !== null) {
            update_option( 'tidywp_custom_website_snackbar_theme', sanitize_text_field($data->get_param('theme')), 'no' );
        }
        
        if ($data->get_param('cookie_duration') !== null) {
            update_option( 'tidywp_custom_website_snackbar_cookie_duration', sanitize_text_field($data->get_param('cookie_duration')), 'no' );
        }
        
        if ($data->get_param('show_duration_in_sec') !== null) {
            update_option( 'tidywp_custom_website_snackbar_show_duration_in_sec', sanitize_text_field($data->get_param('show_duration_in_sec')), 'no' );
        }
        
        if ($data->get_param('show') == 'true') {
            $showSnackbarData = array(
            'SnackbarEnabled' => get_option('tidywp_custom_website_snackbar_mode'), 
            'SnackbarText' => get_option('tidywp_custom_website_snackbar_text'), 
            'SnackbarActionText' => get_option('tidywp_custom_website_snackbar_action_text'),
            'SnackbarPosition' => get_option('tidywp_custom_website_snackbar_position'),
            'SnackbarTheme' => get_option('tidywp_custom_website_snackbar_theme'),
            'SnackbarCookieDuration' => get_option('tidywp_custom_website_snackbar_cookie_duration'),
            'SnackbarDurationInSec' => get_option('tidywp_custom_website_snackbar_show_duration_in_sec'),
           );
           
           echo json_encode($showSnackbarData);
        }
    
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'custom_website_snackbar', array(
    'methods' => 'GET',
    'callback' => 'custom_website_snackbar',
  ) );
} );