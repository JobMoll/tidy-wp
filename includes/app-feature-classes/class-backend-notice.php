<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function tidy_wp_backend_notice(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
 
    add_option('tidy_wp_backend_notice_content', '', '', 'no');
    
        $enabled = sanitize_text_field($request['enabled']);
        if ($enabled == 'true') {
            update_option('tidy_wp_backend_notice', 'true', 'no');
        } 
        if ($enabled == 'false') {
            update_option('tidy_wp_backend_notice', 'false', 'no');
        }
        
        $dismissible = sanitize_text_field($request['dismissible']);
        if ($dismissible == 'true') {
            update_option('tidy_wp_backend_notice_dismissible', 'true', 'no');
        }
        if ($dismissible == 'false') {
            update_option('tidy_wp_backend_notice_dismissible', 'false', 'no');
        }
       
        // notice-success, notice-error, notice-warning, notice-info
        $type = sanitize_text_field($request['type']);
        if ($type !== null) {
            update_option('tidy_wp_backend_notice_type', $type, 'no');
        }
        
        $header = sanitize_text_field($request['header']);
        if ($header !== null) {
            update_option('tidy_wp_backend_notice_header_text', $header, 'no');
        }
        
        $content = sanitize_text_field($request['content']);
        if ($content !== null) {
            update_option('tidy_wp_backend_notice_content', $content, 'no');
        }
        
        
        
        if (sanitize_text_field($request['show']) == 'true') {
            $showBackendNoticeData = array(
            'BackendNoticeEnabled' => get_option('tidy_wp_backend_notice'), 
            'BackendNoticeDismissible' => get_option('tidy_wp_backend_notice_dismissible'), 
            'BackendNoticeType' => get_option('tidy_wp_backend_notice_type'),
            'BackendNoticeContent' => get_option('tidy_wp_backend_notice_content'),
            'BackendNoticeHeaderText' => get_option('tidy_wp_backend_notice_header_text'),
         );
           
           echo json_encode($showBackendNoticeData);
        }
    
        
}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'backend-notice', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_backend_notice',
    'permission_callback' => '__return_true',
 ));
});
