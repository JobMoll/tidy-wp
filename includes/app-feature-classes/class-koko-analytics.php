<?php
/**
* @package tidy-wp
* @license GPL-3.0+
* @author Job Moll
*/

function tidy_wp_visitors_pageviews(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';
        
$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
    if (get_bloginfo('language') == 'en-US') {
       $dateFormat = 'm-d-Y'; 
    } else {
       $dateFormat = 'd-m-Y'; 
    }
    
    $currentDate = date("Y-m-d");
    
        if (in_array('koko-analytics/koko-analytics.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    
    $closestDate = sanitize_text_field($request['finalDateSelected']);
    $furthestDate = sanitize_text_field($request['inicialDateSelected']);
            
    if (!empty($closestDate) && !empty($furthestDate)) {
   
    
    $closestDateShowInApp  = date($dateFormat, strtotime($closestDate));
    $furthestDateShowInApp = date($dateFormat, strtotime($furthestDate));
    
    //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($furthestDate);
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2));
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($closestDate) - strtotime($furthestDate);
    $timestamp = strtotime($closestDate)-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp));

    } else {
    $closestDate =  date("Y-m-d", strtotime($currentDate));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
    
    $closestDateShowInApp  = date($dateFormat, strtotime($currentDate));
    $furthestDateShowInApp = date($dateFormat, strtotime("first day of this month"));
    
    
    $previousClosestDate = date("Y-m-d", strtotime($currentDate . "-1 month"));
    $previousFurthestDate = date("Y-m-d", strtotime("first day of last month"));
    }

    
    global $wpdb;
    
    $totalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $furthestDate, $closestDate));
    
    $totalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $furthestDate, $closestDate));
    
    $previousTotalVisitors = $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $previousFurthestDate, $previousClosestDate));
    
    $previousTotalPageviews = $wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", $previousFurthestDate, $previousClosestDate));
    
function getPercentages($current, $previous, $varName) {
if ((empty($previous)) || (empty($current))) {
    $varName = '0';
} else {
  $varName = ((($current / $previous) * 100) - 100); 
  if (substr($varName, 0, 1) !== "-") {
   $varName = '+ ' . strval(round($varName, 0));
  } else {
   $varName = substr($varName, 1);
   $varName = '- ' . strval(round($varName, 0)); 
  }
}
return $varName;
}
$percentageTotalVisitors = '0';
$percentageTotalVisitors = getPercentages($totalVisitors, $previousTotalVisitors, $percentageTotalVisitors);

$percentageTotalPageviews = '0';
$percentageTotalPageviews = getPercentages($totalPageviews, $previousTotalPageviews, $percentageTotalPageviews);
    
    
    $dataArr = array(
        'TotalVisitors' => strval(round($totalVisitors, 2)) ?: '0',
        'PreviousTotalVisitors' => strval(round($previousTotalVisitors, 2)) ?: '0',
        'TotalPageviews' => strval(round($totalPageviews, 2)) ?: '0',
        'PreviousTotalPageviews' => strval(round($previousTotalPageviews, 2)) ?: '0',
        'PercentageTotalVisitors' => $percentageTotalVisitors ?: '0',
        'PercentageTotalPageviews' => $percentageTotalPageviews ?: '0',
        'SelectionClosest' => strval($closestDateShowInApp) ?: '0',
        'SelectionFurthest' => strval($furthestDateShowInApp) ?: '0'
  );
    

echo '{"Stats": ' . json_encode($dataArr) . '}';

    
    
        } else {
            
    $closestDateShowInApp  = date($dateFormat, strtotime($currentDate));
    $furthestDateShowInApp = date($dateFormat, strtotime("first day of this month"));
    
                $dataArr = array(
        'TotalVisitors' => '0',
        'PreviousTotalVisitors' => '0',
        'TotalPageviews' => '0',
        'PreviousTotalPageviews' => '0',
        'PercentageTotalVisitors' => '0',
        'PercentageTotalPageviews' => '0',
        'SelectionClosest' => strval($closestDateShowInApp) ?: '0',
        'SelectionFurthest' => strval($furthestDateShowInApp) ?: '0'
  );
    

echo '{"Stats": ' . json_encode($dataArr) . '}';
        }
    
    
    
    
}
} 

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidy_wp_secret_path'), 'visitors-pageviews', array(
        'methods' => 'POST',
        'callback' => 'tidy_wp_visitors_pageviews',
    'permission_callback' => '__return_true',
   ));
});










function tidy_wp_populair_pages(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
  if (in_array('koko-analytics/koko-analytics.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    
    $currentDate = date("Y-m-d");  
    
    $closestDate = sanitize_text_field($request['finalDateSelected']);
    $furthestDate = sanitize_text_field($request['inicialDateSelected']);
      
    if (!empty($closestDate) && !empty($furthestDate)) {
    
    //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($furthestDate);
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2));
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($closestDate) - strtotime($furthestDate);
    $timestamp = strtotime($closestDate)-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp));

    } else {
    $closestDate =  date("Y-m-d", strtotime($currentDate));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
    
    $previousClosestDate = date("Y-m-d", strtotime($currentDate . "-1 month"));
    $previousFurthestDate = date("Y-m-d", strtotime("first day of last month"));
    }
    
    global $wpdb;
    
    $topTenPosts = $wpdb->get_results($wpdb->prepare("SELECT  `id`, SUM(pageviews) AS pageviews, SUM(visitors) AS visitors FROM `{$wpdb->prefix}koko_analytics_post_stats` WHERE `date` >= %s AND `date` <= %s GROUP BY `id` ORDER BY `pageviews` DESC  LIMIT 15", $furthestDate, $closestDate));
    
    
    $top15PostsDone = array();
    
    foreach ($topTenPosts as $item) {
        
        $postName;
        if (get_the_title($item->id) == '' || empty(get_the_title($item->id))) {
             $postName = 'Page deleted...';
        } else {
             $postName = get_the_title($item->id);
        }
        
        $data = array(
            'Id' => $item->id,
            'Pageviews' => $item->pageviews,
            'Visitors' => $item->visitors,
            'PageName' => $postName,
            'PageSlug' => get_post_permalink($item->id)
      );
        
        array_push($top15PostsDone, $data);
    }
    

echo json_encode($top15PostsDone);


            } else {
            $top15PostsDone = array();
             echo json_encode($top15PostsDone);   
            }

}
} 

// add to rest api
add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidy_wp_secret_path'), 'populair-pages', array(
        'methods' => 'POST',
        'callback' => 'tidy_wp_populair_pages',
    'permission_callback' => '__return_true',
   ));
});








function tidy_wp_top_referrers(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
    if (in_array('koko-analytics/koko-analytics.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    
    $currentDate = date("Y-m-d");  
    
    $closestDate = sanitize_text_field($request['finalDateSelected']);
    $furthestDate = sanitize_text_field($request['inicialDateSelected']);
      
    if (!empty($closestDate) && !empty($furthestDate)) {
    
    //previous closest date - 1 day of from furthestdate
    $timestamp2 = strtotime($furthestDate);
    $previousClosestDate  = date("Y-m-d", strtotime('-'. '86400' . 'seconds',  $timestamp2));
        
    // furthest date - difference and - 1 extra day
    $diff = strtotime($closestDate) - strtotime($furthestDate);
    $timestamp = strtotime($closestDate)-($diff + 86400);
    $previousFurthestDate  = date("Y-m-d", strtotime('-'. $diff . 'seconds',  $timestamp));

    } else {
    $closestDate =  date("Y-m-d", strtotime($currentDate));
    $furthestDate = date("Y-m-d", strtotime("first day of this month"));
    
    $previousClosestDate = date("Y-m-d", strtotime($currentDate . "-1 month"));
    $previousFurthestDate = date("Y-m-d", strtotime("first day of last month"));
    }
    
    
    global $wpdb;
    
    $top15Referrers = $wpdb->get_results($wpdb->prepare("SELECT  `id`, SUM(pageviews) AS pageviews, SUM(visitors) AS visitors FROM `{$wpdb->prefix}koko_analytics_referrer_stats` WHERE `date` >= %s AND `date` <= %s GROUP BY `id` ORDER BY `pageviews` DESC  LIMIT 15", $furthestDate, $closestDate));
    
    
    $top15ReferrersDone = array();
	$cleanReferrerName = ["[{\"url\":\"", "\"}]", "https://", "http://", "www."];
    $cleanReferrerURL = ["[{\"url\":\"", "\"}]"];
    
    foreach ($top15Referrers as $item) {
        
        $data = array(
            'Id' => $item->id ?: '0',
            'Pageviews' => $item->pageviews ?: '0',
            'Visitors' => $item->visitors ?: '0',
            'ReferrerName' => str_replace($cleanReferrerName,"",stripslashes((json_encode($wpdb->get_results($wpdb->prepare("SELECT  `url` FROM `{$wpdb->prefix}koko_analytics_referrer_urls` WHERE id=%s", $item->id)))))) ?: '0',
            'ReferrerURL' => str_replace($cleanReferrerURL,"",stripslashes((json_encode($wpdb->get_results($wpdb->prepare("SELECT  `url` FROM `{$wpdb->prefix}koko_analytics_referrer_urls` WHERE id=%s", $item->id)))))) ?: '0',
      );
        
        array_push($top15ReferrersDone, $data);
    }
    

echo stripslashes(json_encode($top15ReferrersDone));
    } else {
        $top15ReferrersDone = array();
        echo json_encode($top15ReferrersDone);
    }
      
}
}  


add_action('rest_api_init', function()
{
    register_rest_route(get_option('tidy_wp_secret_path'), 'top-referrers', array(
        'methods' => 'POST',
        'callback' => 'tidy_wp_top_referrers',
    'permission_callback' => '__return_true',
   ));
});
