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
header("HTTP/1.1 401 Unauthorized");
$errorMessage = array('status' => 'error', 'message' => 'This access key is invalid or revoked');
echo json_encode($errorMessage);
exit;
}
if ($apiAuthOK == true) {
    $closestDateWoo = date("Y-m-d", strtotime("last day of this month")) . ' 23:59:59';
    $furthestDateWoo = date("Y-m-d", strtotime("first day of this month")) . ' 00:00:00';
    
    global $wpdb;

    $totalSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", $furthestDateWoo, $closestDateWoo));

	$totalOrders = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", $furthestDateWoo, $closestDateWoo));

if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}

$update_plugins = get_site_transient('update_plugins');
$update_themes = get_site_transient('update_themes');

require_once ABSPATH . '/wp-admin/includes/update.php';
if (function_exists('get_core_updates')) {
   $update_wordpress = get_core_updates(array('dismissed' => false));
   if (! empty($update_wordpress) && ! in_array($update_wordpress[0]->response, array('development', 'latest'))) {
       $counts['wordpress'] = 1 . '", ';
     } else {
	   $counts['wordpress'] = 0 . '", ';
   }
 }
 
if (wp_get_translation_updates()) {
	 $counts['translations'] = 1 . '", ';
    } else {
	 $counts['translations'] = 0 . '", ';
}
	
$totalUpdates = '0';
if ($plugin_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['plugins'] = count($update_plugins->response);
}
if ($theme_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['themes'] = count($update_themes->response);
}
if ($core_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['wordpress'];
}
if ($translations_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['translations'];
}

$get_issues = get_transient( 'health-check-site-status-result' );
$issue_counts = array();
 
 if ( false !== $get_issues ) {
        $issue_counts = json_decode( $get_issues, true );
    }
 
    if ( ! is_array( $issue_counts ) || ! $issue_counts ) {
        $issue_counts = array(
            'good'        => 0,
            'recommended' => 0,
            'critical'    => 0,
        );
    }
$healthCheckIssues = $issue_counts['critical'] + $issue_counts['recommended'];
	
    $dataArr = array(
        'CurrencySymbol' => $cashSign ?: '',
        'TotalSales' => strval(round($totalSales, 2)) ?: '0',
        'TotalOrders' => strval(round($totalOrders)) ?: '0',
        'TotalUpdates' => strval($totalUpdates) ?: '0',
        'HealthCheckIssues' => strval($healthCheckIssues) ?? '0',
		'CurrentTidyWPVersion' => str_replace(".", "", strval(TIDY_WP_CURRENT_PLUGIN_VERSION)) ?? '0.0.1'
  );
    
echo json_encode($dataArr);
}
} 

add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidy_wp_secret_path'), 'website-summary', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_website_summary',
    'permission_callback' => '__return_true',
   ));
});
