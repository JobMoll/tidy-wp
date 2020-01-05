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
    // secret path
    delete_option('tidywp_secret_path');  
    
    // secret token
    delete_option('tidywp_secret_token');  
      
    // maintaince mode
    delete_option('tidywp_maintaince_mode');  
    
    // exclude plugins from auto update
    delete_option( 'tidywp_exclude_plugin_from_autoupdate');
    
    // autop update
    delete_option( 'tidywp_enable_plugin_autoupdate');
    delete_option( 'tidywp_enable_theme_autoupdate');
    delete_option( 'tidywp_enable_core_autoupdate');
    
    // custom wordpress login and wp-admin
    delete_option( 'tidywp_hide_login');
    delete_option( 'tidywp_smart_security');
    
    // backup last date
    delete_option( 'tidywp_last_backup_date');
	}

}
