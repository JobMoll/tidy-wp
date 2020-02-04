<?php

function remove_website_from_server($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) &&          (in_array($_SERVER['LOGGEDIN_USERNAME'], $GLOBALS['$usernameArray']))) {
 
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';

if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidywp_website_username1')) {
   removeWebsite(1);
}
if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidywp_website_username2')) {
   removeWebsite(2);
}  
	
    }
    } else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'remove_website_from_server', array(
    'methods' => 'GET',
    'callback' => 'remove_website_from_server',
  ) );
} );



function reset_website_secret_keys() {
    if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) &&          (in_array($_SERVER['LOGGEDIN_USERNAME'], $GLOBALS['$usernameArray']))) {
     
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
	
resetTokenAndPath();
	
    }
    } else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'reset_website_secret_keys', array(
    'methods' => 'GET',
    'callback' => 'reset_website_secret_keys',
  ) );
} );