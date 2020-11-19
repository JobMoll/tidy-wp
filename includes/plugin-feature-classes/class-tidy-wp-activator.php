<?php

/**
 * Fired during plugin activation
 *
 * @link       https://sparknowmedia.com
 * @since      0.0.1
 *
 * @package    tidy-wp
 * @subpackage tidy-wp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    tidy-wp
 * @subpackage tidy-wp/includes
 * @author     Job Moll <job@sparknowmedia.com>
 */
class Tidy_Wp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
	public static function activate() {
    // secret path
    add_option('tidy_wp_secret_path', tidy_wp_generate_random_string(64), '', 'no');
    
    // secret token
    add_option('tidy_wp_secret_token', tidy_wp_generate_random_string(64), '', 'no');
    
    // maintaince mode
    add_option('tidy_wp_maintaince_mode', 'false', '', 'no');
    
    // custom website popup
    add_option('tidy_wp_custom_website_snackbar_mode', 'false', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_text', '', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_action_text', '', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_position', '0', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_theme', '0', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_cookie_duration', '0', '', 'no');
    add_option('tidy_wp_custom_website_snackbar_show_duration_in_sec', '5', '', 'no');
    
    // exclude plugins from auto update
    add_option('tidy_wp_exclude_plugin_from_autoupdate', '', '', 'no');
    
    // enable auto updates
    add_option('tidy_wp_enable_plugin_autoupdate', 'false' , '', 'no');
    add_option('tidy_wp_enable_theme_autoupdate', 'false' , '', 'no');
    add_option('tidy_wp_enable_core_autoupdate', 'false' , '', 'no');
    
    // custom wordpress login and wp-admin
    add_option('tidy_wp_hide_login', 'false', '', 'no');
    add_option('tidy_wp_smart_security', 'false', '', 'no');

    // addons
    add_option('tidy_wp_addons_snackbar', 'false', '', 'no');
    
    // notifications
    add_option('tidy_wp_woocommerce_sales_notification', 'true', '', 'no');
    add_option('tidy_wp_woocommerce_new_order_notification', 'true', '', 'no');
    add_option('tidy_wp_woocommerce_low_stock_notification', 'true', '', 'no');
    add_option('tidy_wp_woocommerce_no_stock_notification', 'true', '', 'no');
    add_option('tidy_wp_website_analytics_notification', 'true', '', 'no');
    add_option('tidy_wp_user_register_notification', 'false', '', 'no');
    add_option('tidy_wp_update_notification', 'true', '', 'no');
    add_option('tidy_wp_new_form_submission_notification', 'true', '', 'no');
    add_option('tidy_wp_on_wp_mail_failed_notification', 'true', '', 'no');
    add_option('tidy_wp_on_php_error_warning_notification', 'false', '', 'no');
    add_option('tidy_wp_on_php_error_fatal_notification', 'true', '', 'no');
    add_option('tidy_wp_ssl_certificate_validation_notification', 'true', '', 'no');  
		
			
    // backend notice
    add_option('tidy_wp_backend_notice', 'false', '', 'no');
    add_option('tidy_wp_backend_notice_dismissible', 'true', '', 'no');
    add_option('tidy_wp_backend_notice_type', 'notice-info', '', 'no'); // notice-success, notice-error, notice-warning, notice-info
    add_option('tidy_wp_backend_notice_content', '', '', 'no');
    add_option('tidy_wp_backend_notice_header_text', '', '', 'no');
    
    // duplicate pages and posts
    add_option('tidy_wp_duplicate_pages_and_posts', 'true', '', 'no');
	}

}
