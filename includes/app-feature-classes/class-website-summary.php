<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */

function tidy_wp_website_summary(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
    $closestDate  = date("Y-m-d", strtotime("last day of this month"));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
      
      $closestDateWoo = date("Y-m-d", strtotime("last day of this month")) . ' 23:59:59';
      $furthestDateWoo = date("Y-m-d", strtotime("first day of this month")) . ' 00:00:00';
    
    global $wpdb;
    
    $totalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $furthestDate, $closestDate));
        
    $totalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $furthestDate, $closestDate));
    
    $totalSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", $furthestDateWoo, $closestDateWoo));
    
    $totalNetSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(net_total) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", $furthestDate, $closestDate));


if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}
       
    
    $dataArr = array(
        'CurrencySymbol' => $cashSign ?: '',
        'TotalSales' => strval(round($totalSales, 2)) ?: '0',
        'TotalNetSales' => strval(round($totalNetSales, 2)) ?: '0',
        'TotalPageviews' => strval(round($totalPageviews, 2)) ?: '0',
        'TotalVisitors' => strval(round($totalVisitors, 2)) ?: '0',
        'LatestPluginVersion' => TIDY_WP_CURRENT_PLUGIN_VERSION ?? '0.0.1',
  );
    
echo json_encode($dataArr);
}
} 

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidy_wp_secret_path'), 'website-summary', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_website_summary',
    'permission_callback' => '__return_true',
   ));
});
