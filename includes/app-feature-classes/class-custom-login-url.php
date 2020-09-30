<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function tidy_wp_hide_wp_login_admin(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if (sanitize_text_field($request['secret_auth']) == 'true') {
            update_option('tidy_wp_hide_login', sanitize_text_field($request['new_auth']), 'no');
            echo get_option('tidy_wp_hide_login');
        } 
        
   if (sanitize_text_field($request['secret_auth']) == 'false') {
            update_option('tidy_wp_hide_login', 'false', 'no');
            echo 'false';
        }
       
   if (sanitize_text_field($request['show']) == 'true') {
                    if (get_option('tidy_wp_hide_login') == 'false') {
                         $EnabledOrDisabled = 'false';
                    } else {
                         $EnabledOrDisabled = 'true';
                    }
            echo '{"NewLoginLink":"/wp-login.php?' . get_option('tidy_wp_hide_login') . '", "Enabled":"' . $EnabledOrDisabled . '"}';
        }
     }
}

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'hide-wp-login-admin', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_hide_wp_login_admin',
    'permission_callback' => '__return_true',
 ));
});







