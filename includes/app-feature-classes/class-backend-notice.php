<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function backend_notice($data) {
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
            update_option( 'tidy_wp_backend_notice', 'true', 'no' );
        } 
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidy_wp_backend_notice', 'false', 'no' );
        }
        
        if ($data->get_param('dismissible') == 'true') {
            update_option( 'tidy_wp_backend_notice_dismissible', 'true', 'no' );
        }
        if ($data->get_param('dismissible') == 'false') {
            update_option( 'tidy_wp_backend_notice_dismissible', 'false', 'no' );
        }
       
        // notice-success, notice-error, notice-warning, notice-info
        if ($data->get_param('type') !== null) {
            update_option( 'tidy_wp_backend_notice_type', sanitize_text_field($data->get_param('type')), 'no' );
        }
        
        if ($data->get_param('content') !== null) {
            update_option( 'tidy_wp_backend_notice_content', sanitize_text_field($data->get_param('content')), 'no' );
        }
        
        if ($data->get_param('show') == 'true') {
            $showBackendNoticeData = array(
            'BackendNoticeEnabled' => get_option('tidy_wp_backend_notice'), 
            'BackendNoticeDismissible' => get_option('tidy_wp_backend_notice_dismissible'), 
            'BackendNoticeType' => get_option('tidy_wp_backend_notice_type'),
            'BackendNoticeContent' => get_option('tidy_wp_backend_notice_content'),
           );
           
           echo json_encode($showBackendNoticeData);
        }
    
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'backend_notice', array(
    'methods' => 'GET',
    'callback' => 'backend_notice',
  ) );
} );