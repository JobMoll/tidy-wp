<?php

function sendNotification($notificationTitle, $notificationMessage) {
$url = 'https://tidywp.com/wp-json/tidy-wp-admin/send_notification';
$data = array('tidy_wp_username1' => encrypt_and_decrypt(get_option('tidywp_website_username1'), 'd'), 'tidy_wp_password1' => encrypt_and_decrypt(get_option('tidywp_website_password1'), 'd'), 'notification_title' => $notificationTitle, 'notification_message' => $notificationMessage);

if (!empty(get_option('tidywp_website_username2')) && !empty(get_option('tidywp_website_password2'))) {
$data2 = array('tidy_wp_username2' => encrypt_and_decrypt(get_option('tidywp_website_username2'), 'd'), 'tidy_wp_password2' => encrypt_and_decrypt(get_option('tidywp_website_password2'), 'd'));

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
if (get_option('tidywp_user_register_notification') == 'true') {
  $user = get_user_by( 'id', $user_id);
  sendNotification('New user has been registered!', 'Jeej! A new user called ' . $user->user_login . ' has created an account on your website ' . get_bloginfo('name') . '!');
}
}


// woocommerce hooks: https://docs.woocommerce.com/wc-apidocs/hook-docs.html

// woocommerce no stock notification
add_action('woocommerce_no_stock','woocommerce_no_stock_tidy_wp_notification');
function woocommerce_no_stock_tidy_wp_notification($product){
// if (get_option('tidywp_woocommerce_no_stock_notification') == 'true') {
  sendNotification('Product is out of stock...', 'Just letting you know that the product ' . get_the_title($product->get_id()) . ' is out of stock on ' . get_bloginfo('name') . '...');
// }
}

// woocommerce low stock notification
add_action('woocommerce_low_stock','woocommerce_low_stock_tidy_wp_notification');
function woocommerce_low_stock_tidy_wp_notification($product){
// if (get_option('tidywp_woocommerce_low_stock_notification') == 'true') {
  sendNotification('Product is low on stock...', 'A swift announcement. The product ' . get_the_title($product->get_id()) . ' is almost sold out on' . get_bloginfo('name') . '.');
// }
}

// // woocommerce new order
add_action('woocommerce_new_order', 'woocommerce_new_order_tidy_wp_notification');
function woocommerce_new_order_tidy_wp_notification($order_id){
// if (get_option('tidywp_woocommerce_new_order_notification') == 'true') {
  sendNotification('You have a new order!', 'Yess! You have received a new order with the id ' . $order_id . ' on ' . get_bloginfo('name') . '!');
// }
}



// visitors and pageviews last 7 days
// Scheduled Action Hook
function tidy_wp_koko_analytics_pageview_notification( ) {
global $wpdb;
sendNotification('Visitor & pageviews last 7 days.', 'You have received ' . 
$wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now")))) . ' visitors & ' . 
$wpdb->get_var($wpdb->prepare("SELECT SUM(pageviews) FROM `{$wpdb->prefix}koko_analytics_site_stats` WHERE `date` >= %s AND `date` <= %s", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now")))) . ' pageviews from ' . date('d M', strtotime('-7 days')) . ' till ' . date('d M', strtotime('now')) . ' on ' . get_bloginfo('name') . '!');
}
add_action( 'tidy_wp_koko_analytics_pageview_notification', 'tidy_wp_koko_analytics_pageview_notification' );

// Custom Cron Recurrences
function tidy_wp_pageview_notification_cron_job_recurrence( $schedules ) {
	$schedules['weekly'] = array(
		'display' => __( 'Once Weekly', 'textdomain' ),
		'interval' => 604800,
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'tidy_wp_pageview_notification_cron_job_recurrence' );

// Schedule Cron Job Event
function tidy_wp_pageview_notification_cron_job() {
	if ( ! wp_next_scheduled( 'tidy_wp_koko_analytics_pageview_notification' ) ) {
		wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'tidy_wp_koko_analytics_pageview_notification' );
	}
}
add_action( 'wp', 'tidy_wp_pageview_notification_cron_job' );