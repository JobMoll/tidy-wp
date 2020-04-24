<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function smart_security($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
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
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'smart_security', array(
    'methods' => 'GET',
    'callback' => 'smart_security',
  ) );
} );
