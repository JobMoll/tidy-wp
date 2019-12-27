<?php
function woocommerce_data($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
        
    if (!empty($data->get_param('inicialDateSelected')) && !empty($data->get_param('finalDateSelected'))) {
    $closestDate = $data->get_param('finalDateSelected') . ' 23:59:59';
    $furthestDate = $data->get_param('inicialDateSelected') . ' 00:00:00';
    
    
        //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($data->get_param('inicialDateSelected'));
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2)) . ' 23:59:59';
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($data->get_param('finalDateSelected')) - strtotime($data->get_param('inicialDateSelected'));
    $timestamp = strtotime($data->get_param('finalDateSelected'))-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp)) . ' 00:00:00';
    

    } else {
    $closestDate  = date("Y-m-d", strtotime("last day of this month")) . ' 23:59:59';;
    $furthestDate = date("Y-m-d", strtotime("first day of this month")) . ' 00:00:00';
    
    $previousClosestDate = date("Y-m-d", strtotime("last day of last month")) . ' 23:59:59';
    $previousFurthestDate = date("Y-m-d", strtotime("first day of last month")) . ' 00:00:00';
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






$stringsArr = array( 'SelectionClosest' => strval(substr($closestDate, 0, 10))  ?: '0', 
'SelectionFurthest' => strval(substr($furthestDate, 0, 10))  ?: '0');



echo '{ "Numbers": ' . json_encode($dataArr) . ', ';
echo '"Percentages": ' . json_encode($percentageArr) . ', ';
echo '"Strings": ' . json_encode($stringsArr) . '}';

    }
    } else {
    echo 'Sorry... you are not allowed to view this data.';
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