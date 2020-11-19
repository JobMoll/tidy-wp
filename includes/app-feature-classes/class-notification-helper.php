<?php

function tidy_wp_send_notification($notificationTitle, $notificationMessage) {
$url = 'https://tidywp.com/wp-json/tidy-wp-admin/send-notification';
    $data = array(
        'notification_title' => $notificationTitle,
        'notification_message' => $notificationMessage,
        'domain' => get_bloginfo('wpurl'),
        'secret_api_key' => get_option('tidy_wp_secret_token'),
        'notification_send_date' => current_time(get_option('time_format') . ' - ' . get_option('date_format')),
    );

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
  $user = get_user_by('id', $user_id);
  tidy_wp_send_notification('New user has been registered!', 'Jeej! A new user called ' . $user->user_login . ' has created an account on your website ' . get_bloginfo('name') . '!');
}
}

add_action('wpforms_process_complete', 'new_form_submission_tidy_wp_notification', 10, 4);
add_action('ninja_forms_after_submission', 'new_form_submission_tidy_wp_notification');
add_action('gform_after_submission', 'new_form_submission_tidy_wp_notification', 10, 2);
add_action('wpcf7_mail_sent', 'new_form_submission_tidy_wp_notification', 10, 1); 
function new_form_submission_tidy_wp_notification() {
if (get_option('tidy_wp_new_form_submission_notification') == 'true') {
  tidy_wp_send_notification('New form submission!', 'You have a new form submission on your website ' . get_bloginfo('name') . '!');
}
}


// woocommerce hooks: https://docs.woocommerce.com/wc-apidocs/hook-docs.html

// only push these notifications if woocommerce is installed

if (in_array('woocommerce-admin/woocommerce-admin.php', apply_filters('active_plugins', get_option('active_plugins')))) { 

// woocommerce no stock notification
add_action('woocommerce_no_stock','woocommerce_no_stock_tidy_wp_notification');
function woocommerce_no_stock_tidy_wp_notification($product){
if (get_option('tidy_wp_woocommerce_no_stock_notification') == 'true') {
  tidy_wp_send_notification('Product is out of stock...', 'Just letting you know that the product ' . get_the_title($product->get_id()) . ' is out of stock on ' . get_bloginfo('name') . '...');
}
}

// woocommerce low stock notification
add_action('woocommerce_low_stock','woocommerce_low_stock_tidy_wp_notification');
function woocommerce_low_stock_tidy_wp_notification($product){
if (get_option('tidy_wp_woocommerce_low_stock_notification') == 'true') {
  tidy_wp_send_notification('Product is low on stock...', 'A swift announcement. The product ' . get_the_title($product->get_id()) . ' is almost sold out on' . get_bloginfo('name') . '.');
}
}

// woocommerce new order
add_action('woocommerce_new_order', 'woocommerce_new_order_tidy_wp_notification');
function woocommerce_new_order_tidy_wp_notification($order_id){
if (get_option('tidy_wp_woocommerce_new_order_notification') == 'true') {
  tidy_wp_send_notification('You have a new order!', 'Yess! You have received a new order with the id ' . $order_id . ' on ' . get_bloginfo('name') . '!');
}
}

function tidy_wp_woocommerce_sales_notification() {
global $wpdb;
if (get_option('woocommerce_currency') == 'EUR') {
    $cashSign = '€';
} else if (get_option('woocommerce_currency') == 'USD') {
    $cashSign = '$';
} else {
    $cashSign = '';
}
	
$ordersTotal = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", date('Y-m-d', strtotime('-7 days')), date("Y-m-d", strtotime("now") . ' 00:00:00')));
$ordersTotalValue = round($wpdb->get_var($wpdb->prepare("SELECT SUM(total_sales) FROM `{$wpdb->prefix}wc_order_stats` WHERE status = 'wc-completed' AND `date_created` >= %s AND `date_created` <= %s AND `tax_total` > '0'", date('Y-m-d', strtotime('-7 days') . ' 00:00:00'), date("Y-m-d", strtotime("now")))), 2);	
	
if (intval($ordersTotal) >= 1) {	
tidy_wp_send_notification('Sales from the last 7 days.', 'You have received ' . $ordersTotal
 . ' order(s) with a total value of ' . 
$cashSign . $ordersTotalValue . ' from ' . date('d M', strtotime('-7 days')) . ' till ' . date('d M', strtotime('now')) . ' on ' . get_bloginfo('name') . '!');
}
}
add_action('tidy_wp_woocommerce_sales_notification', 'tidy_wp_woocommerce_sales_notification');

// Schedule Cron Job Event
function tidy_wp_weekly_woocommerce_sales_notification_cron_job() {
    if (!wp_next_scheduled('tidy_wp_woocommerce_sales_notification')) {
		wp_schedule_event(strtotime('next sunday 14 hours'), 'weekly', 'tidy_wp_woocommerce_sales_notification');
	}
}
if (get_option('tidy_wp_woocommerce_sales_notification') == 'true' && in_array('woocommerce-admin/woocommerce-admin.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      add_action('wp', 'tidy_wp_weekly_woocommerce_sales_notification_cron_job');

}
}


function tidy_wp_update_notification() {
if (! function_exists('get_plugins')) {
require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$update_plugins = get_site_transient('update_plugins');


if (!empty($update_plugins->response)) {
$counts['plugins'] = count($update_plugins->response);
} else {
$counts['plugins'] = '0';
}

$update_themes = get_site_transient('update_themes');
if (!empty($update_themes->response)) {
$counts['themes'] = count($update_themes->response);
} else {
$counts['themes'] = '0';
}

require_once ABSPATH . '/wp-admin/includes/update.php';
if (function_exists('get_core_updates')) {
   $update_wordpress = get_core_updates(array('dismissed' => false));
   if (!empty($update_wordpress) && ! in_array($update_wordpress[0]->response, array('development', 'latest'))) {
$counts['wordpress'] = '1';
        } else {
$counts['wordpress'] = '0';

   }
    }

if (intval($counts['plugins']) >= 1 || intval($counts['themes']) >= 1 || intval($counts['wordpress']) >= 1) {		
tidy_wp_send_notification('Plugin & Theme updates summary.', 'You have ' . 
$counts['plugins'] . ' plugin update(s), ' . 
$counts['themes'] . ' theme update(s) & ' . 
$counts['wordpress'] . ' core update(s) on ' . get_bloginfo('name') . '!');
}
}
add_action('tidy_wp_update_notification', 'tidy_wp_update_notification');

// Schedule Cron Job Event
function tidy_wp_weekly_update_notification_cron_job() {
	if (!wp_next_scheduled('tidy_wp_update_notification')) {
		wp_schedule_event(strtotime('next sunday 16 hours'), 'weekly', 'tidy_wp_update_notification');
	}
}
if (get_option('tidy_wp_update_notification') == 'true') {
add_action('wp', 'tidy_wp_weekly_update_notification_cron_job');
}


function tidy_wp_ssl_certificate_validation_notification() {
// thanks to https://stackoverflow.com/questions/3464113/is-it-possible-to-read-ssl-information-in-php-from-any-website
class SSL {

    public $domain, $validFrom, $validTo, $issuer, $validity, $validitytot, $crtValRemaining;

    private static function instantiate($url, $info) {
        $obj = new static;
        $obj->domain = $url;
        $obj->validFrom = $info['validFrom'];
        $obj->validTo = $info['validTo'];
        $obj->issuer = $info['issuer'];
        $obj->validity = $info['validity'];
        $obj->validitytot = $info['validitytot'];
        $obj->crtValRemaining = $info['crtValRemaining'];

        return $obj;
    }

    public static function getSSLinfo($url) {
        $ssl_info = [];
        $certinfo = static::getCertificateDetails($url);
        $validFrom_time_t_m = static::dateFormatMonth($certinfo['validFrom_time_t']);
        $validTo_time_t_m = static::dateFormatMonth($certinfo['validTo_time_t']);

        $validFrom_time_t = static::dateFormat($certinfo['validFrom_time_t']);
        $validTo_time_t = static::dateFormat($certinfo['validTo_time_t']);
        $current_t = static::dateFormat(time());

        $ssl_info['validFrom'] = $validFrom_time_t_m;
        $ssl_info['validTo'] = $validTo_time_t_m;
        $ssl_info['issuer'] = $certinfo['issuer']['O'];

        $ssl_info['validity'] = static::diffDate($current_t, $validTo_time_t)." days";
        $ssl_info['validitytot'] = (static::diffDate($validFrom_time_t, $validTo_time_t)-1).' days';

        $ssl_info['crtValRemaining'] =$certinfo['validTo_time_t'];

        return static::instantiate($url, $ssl_info); // return an object
    }

    private static function getCertificateDetails($url) {
        $urlStr = strtolower(trim($url)); 

        $parsed = parse_url($urlStr);// add http://
        if (empty($parsed['scheme'])) {
            $urlStr = 'http://' . ltrim($urlStr, '/');
        }
        $orignal_parse = parse_url($urlStr, PHP_URL_HOST);
        $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
        $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
        $cert = stream_context_get_params($read);
        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
        return $certinfo;
    }

    private static function dateFormat($stamp) {
        return  strftime("%Y-%m-%d", $stamp);
    }

    private static function dateFormatMonth($stamp) {
        return  strftime("%Y-%b-%d", $stamp);
    }

    private static function diffDate($from, $to) {
        $date1=date_create($from);
        $date2=date_create($to);
        $diff=date_diff($date1,$date2);
        return ltrim($diff->format("%R%a"), "+");
    }	
}
$validForAnotherXDays = str_replace(" days", "", $certInfo->validitytot);
if (intval($validForAnotherXDays) <= 30) {	
tidy_wp_send_notification('SSL Certificate about to expire', 'Your SSL certificate on your site ' . get_bloginfo('name') . ' is about to expire. It is only valid for another ' . $validForAnotherXDays . ' days (till ' . $certInfo->validTo . ').');
}
}
add_action('tidy_wp_ssl_certificate_validation_notification', 'tidy_wp_ssl_certificate_validation_notification');

// Schedule Cron Job Event
function tidy_wp_weekly_ssl_certificate_validation_notification_cron_job() {
	if (!wp_next_scheduled('tidy_wp_ssl_certificate_validation_notification')) {
		wp_schedule_event(strtotime('next sunday 16 hours'), 'weekly', 'tidy_wp_ssl_certificate_validation_notification');
	}
}
if (get_option('tidy_wp_ssl_certificate_validation_notification') == 'true') {
add_action('wp', 'tidy_wp_weekly_ssl_certificate_validation_notification_cron_job');
}


// Custom Cron Recurrences
function tidy_wp_weekly_cron_job_recurrence($schedules) {
	$schedules['weekly'] = array(
		'display' => __('Once Weekly', 'textdomain'),
		'interval' => 604800,
	);
	return $schedules;
}
add_filter('cron_schedules', 'tidy_wp_weekly_cron_job_recurrence');


// notification on wp mail error
function tidy_wp_notification_on_wp_mail_failed($wp_error) { 
if (get_option('tidy_wp_on_wp_mail_failed_notification') == 'true') {
    tidy_wp_send_notification('WP Mail error!', 'Something went wrong on ' . get_bloginfo('name') . ' when the website tried to send an email! More info: ' . $response->get_error_message());
}
} 
add_action('wp_mail_failed', 'tidy_wp_notification_on_wp_mail_failed', 10, 1); 




// notification on php shutdown
function tidy_wp_notification_on_php_error() {
$warningNotificationActive = get_option('tidy_wp_on_php_error_warning_notification');
$fatalNotificationActive = get_option('tidy_wp_on_php_error_fatal_notification');

if ($warningNotificationActive == 'true' || $fatalNotificationActive == 'true') {
$last_error_data = error_get_last();
    		    
    if (is_null($last_error_data)) {
			return;
		}

    // excluded 8, 2048, 4096, 8192, 16384, 32767

    $errorDataTypesWorthChecking = array();
    if ($warningNotificationActive == 'true') {
    // fatal errors    
    $errorDataTypesWorthChecking = array(1, 16, 64, 256, 1024);
    }
    
    if (empty($errorDataTypesWorthChecking)) {
    // warning errors
    $errorDataTypesWorthChecking = array(2, 4, 32, 128, 512); 
    } else {
    // combine warning errors and fatal errors  
    $errorDataTypesWorthChecking = array_merge($errorDataTypesWorthChecking, array(2, 4, 32, 128, 512));
    }
 
    $errorDataType = $last_error_data['type'];
    if (in_array($errorDataType, $errorDataTypesWorthChecking)) {
            
        $hash = md5($last_error_data['message']);
        $transient = get_transient('tidy_wp_' . $hash);
        
        if (!empty($transient)) {
             return;
			} else {
            // set transient so it will only send once every 20 minutes
            set_transient('tidy_wp_' . $hash, true, 1200);
            
            //  send notification   
            tidy_wp_send_notification('Error detected!', 'We have detected an error on your site ' . get_bloginfo('name') . '! The error is as follows: ' . $last_error_data['message'] . ' -- ' . $last_error_data['file'] . ' - ' . $last_error_data['line']);
            }  
    }
  }   
}  

register_shutdown_function('tidy_wp_notification_on_php_error');
    



function tidy_wp_notification_summary(WP_REST_Request $request) {
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

        $enabled = sanitize_text_field($request['enabled']);
        $option_name = sanitize_text_field($request['option_name']);
        $cron_job_name = sanitize_text_field($request['cron_job_name']);   
	
        if ($enabled == 'true' && empty($cron_job_name)) {
            update_option($option_name, 'true', 'no');
        } 
        
        if ($enabled == 'false' && empty($cron_job_name)) {
            update_option($option_name, 'false', 'no');
        }
        
        if ($enabled == 'true' && !empty($cron_job_name)) {
            add_action('wp', $cron_job_name);
            update_option($option_name, 'true', 'no');
        }
        
        if ($enabled == 'false' && !empty($cron_job_name)) {
            wp_clear_scheduled_hook($option_name);
            update_option($option_name, 'false', 'no');
        }
        
        
            $showNotificationSummary = array(
            'WoocommeceSalesNotification' => get_option('tidy_wp_woocommerce_sales_notification') === 'true'? true : false,  
            'WordpressUpdatesNotification' => get_option('tidy_wp_update_notification') === 'true'? true : false,
			'SSLCertificationNotification' => get_option('tidy_wp_ssl_certificate_validation_notification') === 'true'? true : false,	
            'WoocommeceNewOrderNotification' => get_option('tidy_wp_woocommerce_new_order_notification') === 'true'? true : false, 
            'WoocommeceLowStockNotification' => get_option('tidy_wp_woocommerce_low_stock_notification') === 'true'? true : false, 
            'WoocommeceNoStockNotification' => get_option('tidy_wp_woocommerce_no_stock_notification') === 'true'? true : false,
            'WordpressUserRegisterNotification' => get_option('tidy_wp_user_register_notification') === 'true'? true : false,
			'NewFormSubmissionNotification' => get_option('tidy_wp_new_form_submission_notification') === 'true'? true : false,	
			'WPMailFailedNotification' => get_option('tidy_wp_on_wp_mail_failed_notification') === 'true'? true : false,
			'OnPHPWarningNotification' => get_option('tidy_wp_on_php_error_warning_notification') === 'true'? true : false,
			'OnPHPFatalNotification' => get_option('tidy_wp_on_php_error_fatal_notification') === 'true'? true : false,
             );	
	         echo json_encode($showNotificationSummary);
        
}
} 

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'notification-summary', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_notification_summary',
    'permission_callback' => '__return_true',
 ));
});
