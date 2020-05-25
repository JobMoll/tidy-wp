<?php

function sendNotification($notificationTitle, $notificationMessage) {
$url = 'https://tidywp.com/wp-json/tidy-wp-admin/send_notification';
$data = array('tidy_wp_username1' => encrypt_and_decrypt(get_option('tidy_wp_website_username1'), 'd'), 'tidy_wp_password1' => encrypt_and_decrypt(get_option('tidy_wp_website_password1'), 'd'), 'notification_title' => $notificationTitle, 'notification_message' => $notificationMessage);

if (!empty(get_option('tidy_wp_website_username2')) && !empty(get_option('tidy_wp_website_password2'))) {
$data2 = array('tidy_wp_username2' => encrypt_and_decrypt(get_option('tidy_wp_website_username2'), 'd'), 'tidy_wp_password2' => encrypt_and_decrypt(get_option('tidy_wp_website_password2'), 'd'));

$data = $data + $data2;
}

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }
}



// new user registered notification
add_action('user_register','user_register_tidy_wp_notification');
function user_register_tidy_wp_notification($user_id){
if (get_option('tidy_wp_user_register_notification') == 'true') {
  $user = get_user_by( 'id', $user_id);
  sendNotification('New user has been registered!', 'Jeej! A new user called ' . $user->user_login . ' has created an account on your website ' . get_bloginfo('name') . '!');
}
}


// woocommerce hooks: https://docs.woocommerce.com/wc-apidocs/hook-docs.html

// woocommerce no stock notification
add_action('woocommerce_no_stock','woocommerce_no_stock_tidy_wp_notification');
function woocommerce_no_stock_tidy_wp_notification($product){
if (get_option('tidy_wp_woocommerce_no_stock_notification') == 'true') {
  sendNotification('Product is out of stock...', 'Just letting you know that the product ' . get_the_title($product->get_id()) . ' is out of stock on ' . get_bloginfo('name') . '...');
}
}

// woocommerce low stock notification
add_action('woocommerce_low_stock','woocommerce_low_stock_tidy_wp_notification');
function woocommerce_low_stock_tidy_wp_notification($product){
if (get_option('tidy_wp_woocommerce_low_stock_notification') == 'true') {
  sendNotification('Product is low on stock...', 'A swift announcement. The product ' . get_the_title($product->get_id()) . ' is almost sold out on' . get_bloginfo('name') . '.');
}
}

// woocommerce new order
add_action('woocommerce_new_order', 'woocommerce_new_order_tidy_wp_notification');
function woocommerce_new_order_tidy_wp_notification($order_id){
if (get_option('tidy_wp_woocommerce_new_order_notification') == 'true') {
  sendNotification('You have a new order!', 'Yess! You have received a new order with the id ' . $order_id . ' on ' . get_bloginfo('name') . '!');
}
}



// visitors and pageviews last 7 days
// Scheduled Action Hook
function tidy_wp_website_analytics_notification( ) {
global $wpdb;
sendNotification('Visitor & pageviews last 7 days.', 'You have received ' . 
$wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now")))) . ' visitors & ' . 
$wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now")))) . ' pageviews from ' . date('d M', strtotime('-7 days')) . ' till ' . date('d M', strtotime('now')) . ' on ' . get_bloginfo('name') . '!');
}
add_action( 'tidy_wp_website_analytics_notification', 'tidy_wp_website_analytics_notification' );


function tidy_wp_woocommerce_sales_notification( ) {
global $wpdb;
if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}
sendNotification('Sales from the last 7 days.', 'You have received ' . 
$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now") . ' 00:00:00'))) . ' order(s) with a total value of ' . 
$cashSign . round($wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", date('Y-m-d', strtotime('-7 days') . ' 00:00:00'), date("Y-m-d", strtotime("now")))), 2) . ' from ' . date('d M', strtotime('-7 days')) . ' till ' . date('d M', strtotime('now')) . ' on ' . get_bloginfo('name') . '!');
}
add_action( 'tidy_wp_woocommerce_sales_notification', 'tidy_wp_woocommerce_sales_notification' );


function tidy_wp_update_notification( ) {
if ( ! function_exists( 'get_plugins' ) ) {
require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$update_plugins = get_site_transient('update_plugins');


if (!empty( $update_plugins->response )) {
$counts['plugins'] = count($update_plugins->response);
} else {
$counts['plugins'] = '0';
}

$update_themes = get_site_transient('update_themes');
if (!empty( $update_themes->response)) {
$counts['themes'] = count($update_themes->response);
} else {
$counts['themes'] = '0';
}

require_once ABSPATH . '/wp-admin/includes/update.php';
if (function_exists( 'get_core_updates' )) {
   $update_wordpress = get_core_updates( array( 'dismissed' => false ) );
   if (!empty( $update_wordpress ) && ! in_array($update_wordpress[0]->response, array( 'development', 'latest'))) {
$counts['wordpress'] = '1';
        } else {
$counts['wordpress'] = '0';

   }
    }

sendNotification('Plugin & Theme updates summary.', 'You have ' . 
$counts['plugins'] . ' plugin update(s), ' . 
$counts['themes'] . ' theme update(s) & ' . 
$counts['wordpress'] . ' core update(s) on ' . get_bloginfo('name') . '!');
}
add_action( 'tidy_wp_update_notification', 'tidy_wp_update_notification' );



// Schedule Cron Job Event
function tidy_wp_weekly_woocommerce_sales_notification_cron_job() {
    if (!wp_next_scheduled( 'tidy_wp_woocommerce_sales_notification')) {
		wp_schedule_event(strtotime('next sunday 14 hours'), 'weekly', 'tidy_wp_woocommerce_sales_notification');
	}
}
if (get_option('tidy_wp_woocommerce_sales_notification') == 'true' && in_array('woocommerce-admin/woocommerce-admin.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      add_action('wp', 'tidy_wp_weekly_woocommerce_sales_notification_cron_job');

}

function tidy_wp_weekly_website_notification_cron_job() {
	if (!wp_next_scheduled( 'tidy_wp_website_analytics_notification')) {
		wp_schedule_event(strtotime('next sunday 15 hours'), 'weekly', 'tidy_wp_website_analytics_notification');
	}
}
if (get_option('tidy_wp_website_analytics_notification') == 'true') {
add_action('wp', 'tidy_wp_weekly_website_notification_cron_job');
}

function tidy_wp_weekly_update_notification_cron_job() {
	if (!wp_next_scheduled( 'tidy_wp_update_notification')) {
		wp_schedule_event(strtotime('next sunday 16 hours'), 'weekly', 'tidy_wp_update_notification');
	}
}
if (get_option('tidy_wp_update_notification') == 'true') {
add_action('wp', 'tidy_wp_weekly_update_notification_cron_job');
}



// Custom Cron Recurrences
function tidy_wp_weekly_cron_job_recurrence( $schedules ) {
	$schedules['weekly'] = array(
		'display' => __( 'Once Weekly', 'textdomain' ),
		'interval' => 604800,
	);
	return $schedules;
}
add_filter('cron_schedules', 'tidy_wp_weekly_cron_job_recurrence');








function notification_summary($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {

        if ($data->get_param('enabled') == 'true') {
            update_option(sanitize_text_field($data->get_param('option_name')), 'true', 'no');
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option(sanitize_text_field($data->get_param('option_name')), 'false', 'no');
        }
        
        if ($data->get_param('enabled') == 'true' && ($data->get_param('cron_job_name') == 'tidy_wp_weekly_update_notification_cron_job' || $data->get_param('cron_job_name') == 'tidy_wp_weekly_website_notification_cron_job'  || $data->get_param('cron_job_name') == 'tidy_wp_weekly_woocommerce_sales_notification_cron_job')) {
            add_action('wp', sanitize_text_field($data->get_param('cron_job_name')));
            update_option(sanitize_text_field($data->get_param('option_name')), 'true', 'no');
        }
        
        if ($data->get_param('enabled') == 'false' && ($data->get_param('cron_job_name') == 'tidy_wp_weekly_update_notification_cron_job' || $data->get_param('cron_job_name') == 'tidy_wp_weekly_website_notification_cron_job'  || $data->get_param('cron_job_name') == 'tidy_wp_weekly_woocommerce_sales_notification_cron_job')) {
            wp_clear_scheduled_hook(sanitize_text_field($data->get_param('option_name')));
            update_option(sanitize_text_field($data->get_param('option_name')), 'false', 'no');
        }
        
        
        if ($data->get_param('show') == 'true') {
            $showNotificationSummary = array(
            'WoocommeceSalesNotification' => get_option('tidy_wp_woocommerce_sales_notification'),  
            'WoocommeceNewOrderNotification' => get_option('tidy_wp_woocommerce_new_order_notification'), 
            'WoocommeceLowStockNotification' => get_option('tidy_wp_woocommerce_low_stock_notification'), 
            'WoocommeceNoStockNotification' => get_option('tidy_wp_woocommerce_no_stock_notification'),
            'KokoWebsiteAnalyticsNotification' => get_option('tidy_wp_website_analytics_notification'), 
            'WordpressUserRegisterNotification' => get_option('tidy_wp_user_register_notification'),
            'WordpressUpdatesNotification' => get_option('tidy_wp_update_notification')
                );
            
            echo json_encode($showNotificationSummary);
        }
        
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'notification_summary', array(
    'methods' => 'GET',
    'callback' => 'notification_summary',
  ) );
} );