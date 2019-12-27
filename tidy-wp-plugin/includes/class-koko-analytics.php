<?php
/**
* @package tidy-wp
* @license GPL-3.0+
* @author Job Moll
*/

function visitors_pageviews($data) {
if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
    
    if (!empty($data->get_param('inicialDateSelected')) && !empty($data->get_param('finalDateSelected'))) {
    $closestDate = $data->get_param('finalDateSelected');
    $furthestDate = $data->get_param('inicialDateSelected');
    
    //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($furthestDate);
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2));
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($closestDate) - strtotime($furthestDate);
    $timestamp = strtotime($closestDate)-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp));

    } else {
    $closestDate  = date("Y-m-d", strtotime("last day of this month"));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
    
      $previousClosestDate = date("Y-m-d", strtotime("last day of last month"));
      $previousFurthestDate = date("Y-m-d", strtotime("first day of last month"));
    }
    
    global $wpdb;
    
    $totalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$furthestDate' AND `date` <= '$closestDate'"));
    
    $totalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$furthestDate' AND `date` <= '$closestDate'"));
    
    $previousTotalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$previousFurthestDate' AND `date` <= '$previousClosestDate'"));
    
    $previousTotalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= '$previousFurthestDate' AND `date` <= '$previousClosestDate'"));
    
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

$percentageTotalVisitors = getPercentages($totalVisitors, $previousTotalVisitors, $percentageTotalVisitors);

$percentageTotalPageviews = getPercentages($totalPageviews, $previousTotalPageviews, $percentageTotalPageviews);
    
    
    $dataArr = array(
        'TotalVisitors' => strval(round($totalVisitors, 2)) ?: '0',
        'PreviousTotalVisitors' => strval(round($previousTotalVisitors, 2)) ?: '0',
        'TotalPageviews' => strval(round($totalPageviews, 2)) ?: '0',
        'PreviousTotalPageviews' => strval(round($previousTotalPageviews, 2)) ?: '0',
        'PercentageTotalVisitors' => $percentageTotalVisitors ?: '0',
        'PercentageTotalPageviews' => $percentageTotalPageviews ?: '0',
         'SelectionClosest' => strval($closestDate) ?: '0',
          'SelectionFurthest' => strval($furthestDate) ?: '0'
    );
    

echo '{"Stats": ' . json_encode($dataArr) . '}';

    
      } 
}
      else {
     echo 'Sorry... you are not allowed to view this data.';
    }
}

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidywp_secret_path'), 'visitors_pageviews', array(
        'methods' => 'GET',
        'callback' => 'visitors_pageviews'
    ));
});

// https://tidywp.sparknowmedia.com/wp-json/NNO6ZKvzjdX8eUuJ/visitors_pageviews?finalDateSelected= &inicialDateSelected=


function populair_pages($data) {
if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
    
    if (!empty($data->get_param('inicialDateSelected')) && !empty($data->get_param('finalDateSelected'))) {
    $closestDate = $data->get_param('finalDateSelected');
    $furthestDate = $data->get_param('inicialDateSelected');
    
    //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($furthestDate);
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2));
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($closestDate) - strtotime($furthestDate);
    $timestamp = strtotime($closestDate)-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp));

    } else {
    $closestDate  = date("Y-m-d", strtotime("last day of this month"));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
    
      $previousClosestDate = date("Y-m-d", strtotime("last day of last month"));
      $previousFurthestDate = date("Y-m-d", strtotime("first day of last month"));
    }
    
    global $wpdb;
    
    $topTenPosts = $wpdb->get_results($wpdb->prepare("SELECT  `id`, SUM(pageviews) AS pageviews, SUM(visitors) AS visitors FROM `{$wpdb->prefix}koko_analytics_post_stats` WHERE `date` >= '$furthestDate' AND `date` <= '$closestDate' GROUP BY `id` ORDER BY `pageviews` DESC  LIMIT 10"));
    
    
    $topTenPostsDone = array();
    
    foreach ($topTenPosts as $item) {
        $data = array(
            'Id' => $item->id,
            'Pageviews' => $item->pageviews,
            'Visitors' => $item->visitors,
            'PageName' => get_the_title($item->id),
            'PageSlug' => get_post_permalink($item->id)
            
        );
        
        array_push($topTenPostsDone, $data);
    }
    

echo json_encode($topTenPostsDone);
 //  echo preg_replace('[]', '', json_encode($topTenPostsDone));
 
      } 
}
      else {
     echo 'Sorry... you are not allowed to view this data.';
    }
}

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidywp_secret_path'), 'populair_pages', array(
        'methods' => 'GET',
        'callback' => 'populair_pages'
    ));
});

// https://tidywp.sparknowmedia.com/wp-json/NNO6ZKvzjdX8eUuJ/populair_pages?finalDateSelected= &inicialDateSelected=