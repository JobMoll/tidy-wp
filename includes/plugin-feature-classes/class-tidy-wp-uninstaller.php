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
	public static function unistall() {
    // secret path
    delete_option('tidy_wp_secret_path');  
    
    // secret token
    delete_option('tidy_wp_secret_token');  
     
    // encryption keys
    delete_option('tidy_wp_encrypt_key');  
    delete_option('tidy_wp_encrypt_iv');  
    
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
    delete_option( 'tidy_wp_exclude_plugin_from_autoupdate');
    
    // autop update
    delete_option( 'tidy_wp_enable_plugin_autoupdate');
    delete_option( 'tidy_wp_enable_theme_autoupdate');
    delete_option( 'tidy_wp_enable_core_autoupdate');
    
    // custom wordpress login and wp-admin
    delete_option( 'tidy_wp_hide_login');
    delete_option( 'tidy_wp_smart_security');
    
    // license system
    deactivate_license_key();
    delete_option( 'tidy_wp_license_key');
    delete_option( 'tidy_wp_license_key_valid');
    delete_option( 'tidy_wp_license_key_last_checked');
    
    // tidywp login details
    removeWebsite(1);
    removeWebsite(2);
    delete_option( 'tidy_wp_website_username1');
    delete_option( 'tidy_wp_website_password1');
    delete_option( 'tidy_wp_website_userRole1');
    delete_option( 'tidy_wp_website_username2');
    delete_option( 'tidy_wp_website_password2');
    delete_option( 'tidy_wp_website_userRole2');
        
    // redirect url settings
    delete_option('tidy_wp_redirect_website_url');
    delete_option('tidy_wp_redirect_type');
    
    // anti brute force check
    delete_option('tidy_wp_brute_force_check');
    
    // addons
    delete_option('tidy_wp_addons_snackbar');
    delete_option('tidy_wp_addons_user_roles');
    
    // notifications
    delete_option('tidy_wp_woocommerce_sales_notification');
    delete_option('tidy_wp_woocommerce_new_order_notification');
    delete_option('tidy_wp_woocommerce_low_stock_notification');
    delete_option('tidy_wp_woocommerce_no_stock_notification');
    delete_option('tidy_wp_website_analytics_notification');
    delete_option('tidy_wp_user_register_notification');
    delete_option('tidy_wp_update_notification');
	}

}
