<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function duplicate_pages_and_posts($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
 
    add_option('tidy_wp_backend_notice_content', '', '', 'no');
    
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidy_wp_duplicate_pages_and_posts', 'true', 'no' );
        } 
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidy_wp_duplicate_pages_and_posts', 'false', 'no' );
        }
    
        
        if ($data->get_param('show') == 'true') {
            $showDuplicatePagesAndPostsData = array(
            'DuplicatePagesAndPostsEnabled' => get_option('tidy_wp_duplicate_pages_and_posts'), 

           );
           
           echo json_encode($showDuplicatePagesAndPostsData);
        }
    
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'duplicate_pages_and_posts', array(
    'methods' => 'GET',
    'callback' => 'duplicate_pages_and_posts',
  ) );
} );