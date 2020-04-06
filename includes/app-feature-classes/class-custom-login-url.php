<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function hide_wp_login_admin($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
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
     }
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'hide_wp_login_admin', array(
    'methods' => 'GET',
    'callback' => 'hide_wp_login_admin',
  ) );
} );







