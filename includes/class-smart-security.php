<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function smart_security($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
     if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($_SERVER['LOGGEDIN_USERNAME'], 'e' ), $GLOBALS['$usernameArray']))) {
        
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidywp_smart_security', 'true', 'no' );
            echo 'true';
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidywp_smart_security', 'false', 'no' );
            echo 'false';
        }
        
                if ($data->get_param('show') == 'true') {
           
            echo '{"ModeEnabled":"' . get_option('tidywp_smart_security') . '"}';
        }
        
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
  register_rest_route( get_option('tidywp_secret_path'), 'smart_security', array(
    'methods' => 'GET',
    'callback' => 'smart_security',
  ) );
} );
