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
     
    // encryption keys
    delete_option('tidywp_encrypt_key');  
    delete_option('tidywp_encrypt_iv');  
    
    // maintaince mode
    delete_option('tidywp_maintaince_mode');  
    
    // custom website popup
    delete_option('tidywp_custom_website_snackbar_mode');
    delete_option('tidywp_custom_website_snackbar_text');
    delete_option('tidywp_custom_website_snackbar_action_text');
    delete_option('tidywp_custom_website_snackbar_position');
    delete_option('tidywp_custom_website_snackbar_theme');
    delete_option('tidywp_custom_website_snackbar_cookie_duration');
    delete_option('tidywp_custom_website_snackbar_show_duration_in_sec');
    
    // exclude plugins from auto update
    delete_option( 'tidywp_exclude_plugin_from_autoupdate');
    
    // autop update
    delete_option( 'tidywp_enable_plugin_autoupdate');
    delete_option( 'tidywp_enable_theme_autoupdate');
    delete_option( 'tidywp_enable_core_autoupdate');
    
    // custom wordpress login and wp-admin
    delete_option( 'tidywp_hide_login');
    delete_option( 'tidywp_smart_security');
    
    // license system
    deactivate_license_key();
    delete_option( 'tidywp_license_key');
    delete_option( 'tidywp_license_key_valid');
    delete_option( 'tidywp_license_key_last_checked');
    
    // tidywp login details
    removeWebsite(1);
    removeWebsite(2);
    delete_option( 'tidywp_website_username1');
    delete_option( 'tidywp_website_password1');
    delete_option( 'tidywp_website_userRole1');
    delete_option( 'tidywp_website_username2');
    delete_option( 'tidywp_website_password2');
    delete_option( 'tidywp_website_userRole2');
        
    // redirect url settings
    delete_option('tidywp_redirect_website_url');
    delete_option('tidywp_redirect_type');
    
    // anti brute force check
    delete_option('tidywp_brute_force_check');
    
    // addons
    delete_option('tidywp_addons_snackbar');
    delete_option('tidywp_addons_user_roles');
	}

}
