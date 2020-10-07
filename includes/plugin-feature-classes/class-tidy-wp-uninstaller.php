<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://sparknowmedia.com
 * @since      1.0.0
 *
 * @package    Tidy_WP
 * @subpackage Tidy_WP/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tidy_WP
 * @subpackage Tidy_WP/includes
 * @author     Job Moll <job@sparknowmedia.com>
 */
class Tidy_Wp_Uninstaller {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
    // secret path
    delete_option('tidy_wp_secret_path');  
    
    // secret token
    delete_option('tidy_wp_secret_token');  
    
    // maintaince mode
    delete_option('tidy_wp_maintaince_mode');  
    
    // custom website popup
    delete_option('tidy_wp_custom_website_snackbar_mode');
    delete_option('tidy_wp_custom_website_snackbar_text');
    delete_option('tidy_wp_custom_website_snackbar_action_text');
    delete_option('tidy_wp_custom_website_snackbar_position');
    delete_option('tidy_wp_custom_website_snackbar_theme');
    delete_option('tidy_wp_custom_website_snackbar_cookie_duration');
    delete_option('tidy_wp_custom_website_snackbar_show_duration_in_sec');
    
    // exclude plugins from auto update
    delete_option('tidy_wp_exclude_plugin_from_autoupdate');
    
    // autop update
    delete_option('tidy_wp_enable_plugin_autoupdate');
    delete_option('tidy_wp_enable_theme_autoupdate');
    delete_option('tidy_wp_enable_core_autoupdate');
    
    // custom wordpress login and wp-admin
    delete_option('tidy_wp_hide_login');
    delete_option('tidy_wp_smart_security');

    // addons
    delete_option('tidy_wp_addons_snackbar');
    
    // notifications
    delete_option('tidy_wp_woocommerce_sales_notification');
    delete_option('tidy_wp_woocommerce_new_order_notification');
    delete_option('tidy_wp_woocommerce_low_stock_notification');
    delete_option('tidy_wp_woocommerce_no_stock_notification');
    delete_option('tidy_wp_website_analytics_notification');
    delete_option('tidy_wp_user_register_notification');
    delete_option('tidy_wp_update_notification');
    delete_option('tidy_wp_new_form_submission_notification');
	
    // backend notice
    delete_option('tidy_wp_backend_notice');
    delete_option('tidy_wp_backend_notice_dismissible');
    delete_option('tidy_wp_backend_notice_type');
    delete_option('tidy_wp_backend_notice_content');
    delete_option('tidy_wp_backend_notice_header_text');
    
    // duplicate pages and posts
    delete_option('tidy_wp_duplicate_pages_and_posts');
	}

}
