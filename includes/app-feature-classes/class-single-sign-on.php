<?php
 
// Enable this setting to automatically login to your WP Admin area!
 
function tidy_wp_get_sign_on_key(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
    sanitize_user($request['username']);
  
  // generate a random key that only can be used once! Then it changes.

     }
}

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'get-sign-on-key', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_get_sign_on_key',
    'permission_callback' => '__return_true',
 ));
});





function tidy_wp_sign_on_page(WP_REST_Request $request) {
        
    if (sanitize_text_field($request['sign_on_key']) == get_option('tidy_wp_sign_on_key')) {

    // reset the sign-on-key
    
    function programmatic_login($username) {
        if (is_user_logged_in()) {
            wp_logout();
        }

    add_filter('authenticate', 'allow_programmatic_login', 10, 3);    // hook in earlier than other callbacks to short-circuit them
    $user = wp_signon(array('user_login' => $username));
    remove_filter('authenticate', 'allow_programmatic_login', 10, 3);

    if (is_a($user, 'WP_User')) {
        wp_set_current_user($user->ID, $user->user_login);

        if (is_user_logged_in()) {
            
            // go the the domain url + /wp-admin
            
            return true;
        }
    }

    return false;
 }

 function allow_programmatic_login($user, $username, $password) {
    return get_user_by('login', $username);
}

programmatic_login(sanitize_text_field($request['username']));

} else {
echo 'Sorry... you are not allowed to view this data.';    
}
}

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'sign-on-page', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_sign_on_page',
    'permission_callback' => '__return_true',
 ));
});

