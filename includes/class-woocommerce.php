<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function woocommerce_data($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
    if (get_bloginfo('language') == 'en-US') {
       $dateFormat = 'm-d-Y'; 
    } else {
       $dateFormat = 'd-m-Y'; 
    }
    $currentTime = date("H:i:s");
    $currentDate = date("Y-m-d");
    
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && in_array('woocommerce-admin/woocommerce-admin.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

    if (!empty($data->get_param('inicialDateSelected')) && !empty($data->get_param('finalDateSelected'))) {
    $closestDate = $data->get_param('finalDateSelected') . ' ' . $currentTime; 
    $furthestDate = $data->get_param('inicialDateSelected') . ' 00:00:00';
    
    $closestDateShowInApp  = date($dateFormat, strtotime($closestDate));
    $furthestDateShowInApp = date($dateFormat, strtotime($furthestDate));
    
    // is closest to now
    $previousClosestDate = date("Y-m-d", strtotime($data->get_param('finalDateSelected') . "-1 month")) . ' ' . $currentTime;
    // is furthest away in the past
    $previousFurthestDate = date("Y-m-d", strtotime($data->get_param('inicialDateSelected') . "-1 month")) . ' 00:00:00';
    
    // //previous closest date - 1 day of from furthestdate
    // $timestamp2 = strtotime($data->get_param('inicialDateSelected'));
    // $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2)) . ' ' . $currentTime;
        
    // // furthest date - difference and - 1 extra day
    // $diff = strtotime($data->get_param('finalDateSelected')) - strtotime($data->get_param('inicialDateSelected'));
    // $timestamp = strtotime($data->get_param('finalDateSelected'))-($diff + 86400);
    // $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp)) . ' 00:00:00';
    

    } else {
    $closestDate =  date("Y-m-d H:i:s", strtotime($currentDate . $currentTime));
    $furthestDate = date("Y-m-d", strtotime("first day of this month")) . ' 00:00:00';
    
    // is closest to now
    $previousClosestDate = date("Y-m-d", strtotime($currentDate . "-1 month")) . ' ' . $currentTime;
    // is furthest away in the past
    $previousFurthestDate = date("Y-m-d", strtotime("first day of last month")) . ' 00:00:00';
    
    $closestDateShowInApp  = date($dateFormat, strtotime("now"));
    $furthestDateShowInApp = date($dateFormat, strtotime("first day of this month"));
    
    $previousClosestDateShowInApp  = date($dateFormat, strtotime("now"));
    $previousFurthestDateShowInApp = date($dateFormat, strtotime("first day of last month"));
    }
       
       if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}


   global $wpdb;
   $totalNetSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(net_total) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDate' AND `date_created` <= '$closestDate' AND `tax_total` > '0'"));
   $totalSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDate' AND `date_created` <= '$closestDate' AND `tax_total` > '0'"));
   $numItemsSold = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_items_sold) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDate' AND `date_created` <= '$closestDate' AND `tax_total` > '0'"));
   $orders = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$furthestDate' AND `date_created` <= '$closestDate' AND `tax_total` > '0'"));
   $averageOrderValueNet = $totalNetSales / $orders;
   $averageOrderValueNet = str_replace('NAN','0',$averageOrderValueNet);
      
   $previousTotalNetSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(net_total) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$previousFurthestDate' AND `date_created` <= '$previousClosestDate' AND `tax_total` > '0'"));
   $previousTotalSales = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$previousFurthestDate' AND `date_created` <= '$previousClosestDate' AND `tax_total` > '0'"));
   $previousNumItemsSold = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_items_sold) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$previousFurthestDate' AND `date_created` <= '$previousClosestDate' AND `tax_total` > '0'"));
   $previousOrders = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= '$previousFurthestDate' AND `date_created` <= '$previousClosestDate' AND `tax_total` > '0'"));
   $previousAverageOrderValueNet = $previousTotalNetSales / $previousOrders;
   $previousAverageOrderValueNet = str_replace('NAN','0',$previousAverageOrderValueNet);
   
$dataArr = array(
    'TotalNetSales' => $cashSign . strval(round($totalNetSales, 2))  ?: '0', 
'TotalSales' => $cashSign . strval(round($totalSales, 2))  ?: '0', 
'Orders' => $orders  ?: '0', 
'AverageOrderValueNet' => $cashSign . strval(round($averageOrderValueNet, 2))  ?: '0', 
'NumItemsSold' => $numItemsSold  ?: '0', 
'PreviousTotalNetSales' => $cashSign . strval(round($previousTotalNetSales, 2))  ?: '0', 
'PreviousTotalSales' => $cashSign . strval(round($previousTotalSales, 2))  ?: '0', 
'PreviousOrders' => $previousOrders  ?: '0', 
'PreviousAverageOrderValueNet' => $cashSign . strval(round($previousAverageOrderValueNet, 2)) ?: '0' , 
'PreviousNumItemsSold' => $previousNumItemsSold ?: '0');

function getPercentages($current, $previous, $varName) {
if ((empty($previous)) || (empty($current))) {
    $varName = '0';
} else {
  $varName = ((($current / $previous) * 100) - 100); 
  if (substr( $varName, 0, 1 ) !== "-") {
   $varName = '+ ' . strval(round($varName, 0));
  } else {
   $varName = substr($varName, 1);
   $varName = '- ' . strval(round($varName, 0)); 
  }
}
return $varName;
}

$totalNetSalesPercentage = getPercentages($totalNetSales, $previousTotalNetSales, $totalNetSalesPercentage);

$totalSalesPercentage = getPercentages($totalSales, $previousTotalSales, $totalSalesPercentage);

$ordersPercentage = getPercentages($orders, $previousOrders, $ordersPercentage);

$averageOrderValueNetPercentage = getPercentages($averageOrderValueNet, $previousAverageOrderValueNet, $averageOrderValueNetPercentage);

$numItemsSoldPercentage = getPercentages($numItemsSold, $previousNumItemsSold, $numItemsSoldPercentage);

$percentageArr = array('TotalNetSalesPercentage' => $totalNetSalesPercentage ?: '0', 'TotalSalesPercentage' => $totalSalesPercentage ?: '0', 'OrdersPercentage' => $ordersPercentage ?: '0', 'AverageOrderValueNetPercentage' => $averageOrderValueNetPercentage ?: '0', 'NumItemsSoldPercentage' => $numItemsSoldPercentage ?: '0');

$stringsArr = array( 'SelectionClosest' => strval(substr($closestDateShowInApp, 0, 10))  ?: '0', 
'SelectionFurthest' => strval(substr($furthestDateShowInApp, 0, 10))  ?: '0');

echo '{ "Numbers": ' . json_encode($dataArr) . ', ';
echo '"Percentages": ' . json_encode($percentageArr) . ', ';
echo '"Strings": ' . json_encode($stringsArr) . '}';




            } else {
              
    $closestDateShowInApp  = date($dateFormat, strtotime("now"));
    $furthestDateShowInApp = date($dateFormat, strtotime("first day of this month"));
    
                $dataArr = array(
    'TotalNetSales' => '0', 
'TotalSales' => '0', 
'Orders' => '0', 
'AverageOrderValueNet' => '0', 
'NumItemsSold' => '0', 
'PreviousTotalNetSales' => '0', 
'PreviousTotalSales' => '0', 
'PreviousOrders' => '0', 
'PreviousAverageOrderValueNet' => '0' , 
'PreviousNumItemsSold' => '0');

                $percentageArr = array('TotalNetSalesPercentage' => '0', 'TotalSalesPercentage' => '0', 'OrdersPercentage' => '0', 'AverageOrderValueNetPercentage' => '0', 'NumItemsSoldPercentage' => '0');
                $stringsArr = array( 'SelectionClosest' => strval(substr($closestDateShowInApp, 0, 10))  ?: '0', 'SelectionFurthest' => strval(substr($furthestDateShowInApp, 0, 10))  ?: '0');

    echo '{ "Numbers": ' . json_encode($dataArr) . ', ';
echo '"Percentages": ' . json_encode($percentageArr) . ', ';
echo '"Strings": ' . json_encode($stringsArr) . '}';
            }

    } else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
}  

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'woocommerce_data', array(
    'methods' => 'GET',
    'callback' => 'woocommerce_data',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/2isqa8BxI2F3zaR/woocommerce_data?finalDateSelected= &inicialDateSelected=
