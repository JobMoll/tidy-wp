<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function tidy_wp_maintaince_mode(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if (sanitize_text_field($request['enabled']) == 'true') {
            update_option('tidy_wp_maintaince_mode', 'true', 'no');
            echo 'true';
        } 
        
        if (sanitize_text_field($request['enabled']) == 'false') {
            update_option('tidy_wp_maintaince_mode', 'false', 'no');
            echo 'false';
        }
        
        if (sanitize_text_field($request['show']) == 'true') {
            echo '{"ModeEnabled":"' . get_option('tidy_wp_maintaince_mode') . '"}';
        }
        
}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'maintaince-mode', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_maintaince_mode',
    'permission_callback' => '__return_true',
 ));
});






