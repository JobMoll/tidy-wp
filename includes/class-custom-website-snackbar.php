<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function custom_website_snackbar($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidywp_custom_website_snackbar_mode', 'true', 'no' );
            echo 'true';
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidywp_custom_website_snackbar_mode', 'false', 'no' );
            echo 'false';
        }
        
        if (isset($data->get_param('text'))) {
            update_option( 'tidywp_custom_website_snackbar_text', sanitize_text_field($data->get_param('text')), 'no' );
        }
        
        if (isset($data->get_param('action_text'))) {
            update_option( 'tidywp_custom_website_snackbar_action_text', sanitize_text_field($data->get_param('action_text')), 'no' );
        }
        
        if (isset($data->get_param('position'))) {
            update_option( 'tidywp_custom_website_snackbar_position', sanitize_text_field($data->get_param('position')), 'no' );
        }
        
        if (isset($data->get_param('theme'))) {
            update_option( 'tidywp_custom_website_snackbar_theme', sanitize_text_field($data->get_param('theme')), 'no' );
        }
        
        if (isset($data->get_param('cookie_duration'))) {
            update_option( 'tidywp_custom_website_snackbar_cookie_duration', sanitize_text_field($data->get_param('cookie_duration')), 'no' );
        }
        
        if (isset($data->get_param('show_duration_in_sec'))) {
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
    
        
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'custom_website_snackbar', array(
    'methods' => 'GET',
    'callback' => 'custom_website_snackbar',
  ) );
} );