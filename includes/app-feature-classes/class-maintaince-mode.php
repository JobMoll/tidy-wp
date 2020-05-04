<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function maintaince_mode($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidy_wp_maintaince_mode', 'true', 'no' );
            echo 'true';
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidy_wp_maintaince_mode', 'false', 'no' );
            echo 'false';
        }
        
                if ($data->get_param('show') == 'true') {
           
            echo '{"ModeEnabled":"' . get_option('tidy_wp_maintaince_mode') . '"}';
        }
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'maintaince_mode', array(
    'methods' => 'GET',
    'callback' => 'maintaince_mode',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/tidywp/maintaince_mode?enabled=false&token=123
// https://tidywp.sparknowmedia.com/wp-json/tidywp/maintaince_mode?show=true&token=123






