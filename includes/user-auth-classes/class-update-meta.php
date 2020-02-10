<?php
function update_meta_data($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
// create a new nonce
wp_create_nonce('update-meta-data');

// validate auth credentials
function wp_authenticate( $username, $password ) {
    $username = sanitize_user( $username );
    $password = trim( $password );

    $user = apply_filters( 'authenticate', null, $username, $password );
 
    if ( $user == null ) {
        $user = new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Invalid username, email address or incorrect password.' ) );
    }
 
    $ignore_codes = array( 'empty_username', 'empty_password' );
 
    if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
        do_action( 'wp_login_failed', $username );
    }
 
    return $user;
}
// run auth credentials check
wp_authenticate($data->get_param('username'), $data->get_param('password'));



// end of code    
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
  register_rest_route( get_option('tidywp_secret_path'), 'update_meta_data', array(
    'methods' => 'GET',
    'callback' => 'update_meta_data',
  ) );
} );