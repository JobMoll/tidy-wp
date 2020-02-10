<?php

function remove_website_from_server($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
 
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';

if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidywp_website_username1')) {
   removeWebsite(1);
}
if (encrypt_and_decrypt($data->get_param('username'), 'e') == get_option('tidywp_website_username2')) {
   removeWebsite(2);
}  
	
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

add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'remove_website_from_server', array(
    'methods' => 'GET',
    'callback' => 'remove_website_from_server',
  ) );
} );



function reset_website_secret_keys() {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
     
include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
	
resetTokenAndPath();
	
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

add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'reset_website_secret_keys', array(
    'methods' => 'GET',
    'callback' => 'reset_website_secret_keys',
  ) );
} );