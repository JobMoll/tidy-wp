<?php
 
// Enable this setting to automatically login to your WP Admin area!
 
function get_sign_on_key($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
    $data->get_param('username');
  
  // generate a random key that only can be used once! Then it changed.

     }
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_sign_on_key', array(
    'methods' => 'GET',
    'callback' => 'get_sign_on_key',
  ) );
} );





function sign_on_page($data) {
        
    if ($data->get_param('sign-on-key') == get_option('tidywp_sign_on_key')) {

    // reset the sign-on-key
    
    function programmatic_login( $username ) {
        if ( is_user_logged_in() ) {
            wp_logout();
        }

    add_filter( 'authenticate', 'allow_programmatic_login', 10, 3 );    // hook in earlier than other callbacks to short-circuit them
    $user = wp_signon( array( 'user_login' => $username ) );
    remove_filter( 'authenticate', 'allow_programmatic_login', 10, 3 );

    if ( is_a( $user, 'WP_User' ) ) {
        wp_set_current_user( $user->ID, $user->user_login );

        if ( is_user_logged_in() ) {
            
            // go the the domain url + /wp-admin
            
            return true;
        }
    }

    return false;
 }

 function allow_programmatic_login( $user, $username, $password ) {
    return get_user_by( 'login', $username );
}

programmatic_login($data->get_param('username'));

} else {
echo 'Sorry... you are not allowed to view this data.';    
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'sign_on_page', array(
    'methods' => 'GET',
    'callback' => 'sign_on_page',
  ) );
} );

