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
class Tidy_Wp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
    // cron jobs
    wp_clear_scheduled_hook('tidy_wp_website_analytics_notification');
    update_option('tidy_wp_website_analytics_notification', 'false');
    
    wp_clear_scheduled_hook('tidy_wp_woocommerce_sales_notification');
    update_option('tidy_wp_woocommerce_sales_notification', 'false');
    
    wp_clear_scheduled_hook('tidy_wp_update_notification');
    update_option('tidy_wp_update_notification', 'false');
	
    wp_clear_scheduled_hook('tidy_wp_ssl_certificate_validation_notification');
    update_option('tidy_wp_ssl_certificate_validation_notification', 'false');
	}

}
