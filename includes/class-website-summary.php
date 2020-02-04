<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function website_summary($data) {
if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
    
    $closestDate  = date("Y-m-d", strtotime("last day of this month"));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
      
      $closestDateWoo = date("Y-m-d", strtotime("last day of this month")) . ' 23:59:59';
      $furthestDateWoo = date("Y-m-d", strtotime("first day of this month")) . ' 00:00:00';
    
    global $wpdb;
    
    $totalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$furthestDate' AND `date` <= '$closestDate'"));
        
    $totalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$furthestDate' AND `date` <= '$closestDate'"));
    
    $totalSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDateWoo' AND `date_created` <= '$closestDateWoo' AND `tax_total` > '0'"));
    
    $totalNetSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(net_total) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDate' AND `date_created` <= '$closestDate' AND `tax_total` > '0'"));

           if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}
        
    $dataArr = array(
        'TotalSales' => $cashSign . strval(round($totalSales, 2)) ?: '0',
        'TotalNetSales' => $cashSign . strval(round($totalNetSales, 2)) ?: '0',
        'TotalPageviews' => strval(round($totalPageviews, 2)) ?: '0',
        'TotalVisitors' => strval(round($totalVisitors, 2)) ?: '0',
    );
    

echo json_encode($dataArr);

    
      } 
}
      else {
     echo 'Sorry... you are not allowed to view this data.';
    }
}

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidywp_secret_path'), 'website_summary', array(
        'methods' => 'GET',
        'callback' => 'website_summary'
    ));
});

// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/website_summary
