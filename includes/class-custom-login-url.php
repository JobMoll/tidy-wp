<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function hide_wp_login_admin($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
        if ($data->get_param('secret-auth') == 'true') {
            update_option( 'tidywp_hide_login', $data->get_param('new-auth'), 'no' );
            echo get_option('tidywp_hide_login');
        } 
        
   if ($data->get_param('secret-auth') == 'false') {
            update_option( 'tidywp_hide_login', 'false', 'no' );
            echo 'false';
        }
       
   if ($data->get_param('show') == 'true') {
                    if (get_option('tidywp_hide_login') == 'false') {
                         $EnabledOrDisabled = 'false';
                    } else {
                         $EnabledOrDisabled = 'true';
                    }
            echo '{"NewLoginLink":"/wp-login.php?' . get_option('tidywp_hide_login') . '", "Enabled":"' . $EnabledOrDisabled . '"}';
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

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'hide_wp_login_admin', array(
    'methods' => 'GET',
    'callback' => 'hide_wp_login_admin',
  ) );
} );







