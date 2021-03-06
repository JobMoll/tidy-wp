<?php

function tidy_wp_site_settings(WP_REST_Request $request) {
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
        
//         $site_title = sanitize_text_field($request['siteTitle']);
//         if ($site_title != '') {
//             update_option('blogname', $site_title, 'yes');
//         } 
    
//         $tagline = sanitize_text_field($request['tagline']);
//         if ($tagline != '') {
//             update_option('blogdescription', $tagline, 'yes');
//         }
        
        // value 0 is disabled --- 1 is enabled
        $user_can_register = sanitize_text_field($request['usersCanRegister']);
        if ($user_can_register != '') {
            update_option('users_can_register', $user_can_register, 'yes');
        }
        
        // value 0 is search engine visibility on --- 1 is off
        $blog_public = sanitize_text_field($request['blogPublic']);
        if ($blog_public != '') {
            update_option('blog_public', $blog_public, 'yes');
       }

	$arrayData = array(
	"SiteTitle" => get_option('blogname'),
	"Tagline" => get_option('blogdescription'),
	"UsersCanRegister" => get_option('users_can_register'),
	"BlogPublic" => get_option('blog_public'),
	);
	
	echo json_encode($arrayData);
}
}  
 
// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'site-settings', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_site_settings',
    'permission_callback' => '__return_true',
 ));
});
