<?php 

// call to check
function website_summary_specific($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if ($data->get_param('show') == 'true') {
            
            $kokoAnalyticsActive = 'false';
            $woocommerceAdminActive = 'false';
            
            if ( is_plugin_active( 'koko-analytics/koko-analytics.php' ) ) {
                $kokoAnalyticsActive = 'true';
            }
            
            if ( is_plugin_active( 'woocommerce-admin/woocommerce-admin.php' ) ) {
                $woocommerceAdminActive = 'true';
            }
            
            $showWebsiteSummarySpecific = array(
            'LicenseKeyValid' => get_option('tidywp_license_key_valid'), 
            
            // plugins
            'KokoAnalyticsActive' => $kokoAnalyticsActive, 
            'WoocommerceAdminActive' => $woocommerceAdminActive,
            
            // addons
            'SnackbarAddon' => get_option('tidywp_addons_snackbar'), 
            'UserRolesAddon' => get_option('tidywp_addons_user_roles'), 
           );
           
           echo json_encode($showWebsiteSummarySpecific);
        }
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'website_summary_specific', array(
    'methods' => 'GET',
    'callback' => 'website_summary_specific',
  ) );
} );