<?php

function remove_website_from_server($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
 
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';

if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidy_wp_website_username1')) {
   removeWebsite(1);
}
if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidy_wp_website_username2')) {
   removeWebsite(2);
}  
	
}
} 

add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'remove_website_from_server', array(
    'methods' => 'GET',
    'callback' => 'remove_website_from_server',
  ) );
} );



function reset_website_secret_keys() {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
     
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
	
resetTokenAndPath();
	
}
}   

add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'reset_website_secret_keys', array(
    'methods' => 'GET',
    'callback' => 'reset_website_secret_keys',
  ) );
} );