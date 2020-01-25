<?php
function register_new_user($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
        
// create a new nonce
wp_create_nonce( 'create-register-nonce');

function create_new_user() {
    wp_create_user( $data->get_param('username'),  $data->get_param('password'), $data->get_param('email'));
}

// verify the nonce
$nonce = $_REQUEST['_wpnonce'];
if ( ! wp_verify_nonce( $nonce, 'create-register-nonce' ) ) {
    die( __( 'Security check', 'textdomain' ) ); 
} else {
    // if nonce is correct do stuff here
    create_new_user();
}


// end of code    
}
} else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'register_new_user', array(
    'methods' => 'GET',
    'callback' => 'register_new_user',
  ) );
} );