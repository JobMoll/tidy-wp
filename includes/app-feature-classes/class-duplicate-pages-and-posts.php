<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function tidy_wp_duplicate_pages_and_posts(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
header("HTTP/1.1 401 Unauthorized");
$errorMessage = array('status' => 'error', 'message' => 'This access key is invalid or revoked');
echo json_encode($errorMessage);
exit; 
}
if ($apiAuthOK == true) {
        
        if (sanitize_text_field($request['enabled']) == 'true') {
           update_option('tidy_wp_duplicate_pages_and_posts', 'true', 'no');
        } 
        if (sanitize_text_field($request['enabled']) == 'false') {
           update_option('tidy_wp_duplicate_pages_and_posts', 'false', 'no');
        }
     
        if (sanitize_text_field($request['show']) == 'true') {
           $showDuplicatePagesAndPostsData = array(
           'DuplicatePagesAndPostsEnabled' => get_option('tidy_wp_duplicate_pages_and_posts'), 
           );
           echo json_encode($showDuplicatePagesAndPostsData);
        }
    
}
} 

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'duplicate-pages-and-posts', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_duplicate_pages_and_posts',
    'permission_callback' => '__return_true',
 ));
});