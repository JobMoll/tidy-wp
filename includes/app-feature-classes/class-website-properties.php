<?php 

// call to check
function tidy_wp_website_summary_specific(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if (sanitize_text_field($request['show']) == 'true') {
            
            $kokoAnalyticsActive = 'false';
            $woocommerceAdminActive = 'false';
            
            if (is_plugin_active('koko-analytics/koko-analytics.php')) {
                $kokoAnalyticsActive = 'true';
            }
            
            if (is_plugin_active('woocommerce-admin/woocommerce-admin.php')) {
                $woocommerceAdminActive = 'true';
            }
            
            $showWebsiteSummarySpecific = array(
            // plugins
            'KokoAnalyticsActive' => $kokoAnalyticsActive, 
            'WoocommerceAdminActive' => $woocommerceAdminActive,
            
            // addons
            'SnackbarAddon' => get_option('tidy_wp_addons_snackbar'), 
            'UserRolesAddon' => get_option('tidy_wp_addons_user_roles'), 
         );
           
           echo json_encode($showWebsiteSummarySpecific);
        }
        
}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'website-summary-specific', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_website_summary_specific',
    'permission_callback' => '__return_true',
 ));
});