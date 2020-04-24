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
    add_option( 'tidywp_secret_path', generateRandomString(64), '', 'no' );
    
    // secret token
    add_option( 'tidywp_secret_token', generateRandomString(64), '', 'no' );

    // encryption keys
    add_option( 'tidywp_encrypt_key', generateRandomString(64), '', 'no' );
    add_option( 'tidywp_encrypt_iv', generateRandomString(64), '', 'no' );
    
    // maintaince mode
    add_option( 'tidywp_maintaince_mode', 'false', '', 'no' );
    
    // custom website popup
    add_option( 'tidywp_custom_website_snackbar_mode', 'false', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_text', '', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_action_text', '', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_position', '0', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_theme', '0', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_cookie_duration', '0', '', 'no' );
    add_option( 'tidywp_custom_website_snackbar_show_duration_in_sec', '5', '', 'no' );
    
    // exclude plugins from auto update
    add_option( 'tidywp_exclude_plugin_from_autoupdate', '', '', 'no' );
    
    // enable auto updates
    add_option( 'tidywp_enable_plugin_autoupdate', 'false' , '', 'no' );
    add_option( 'tidywp_enable_theme_autoupdate', 'false' , '', 'no' );
    add_option( 'tidywp_enable_core_autoupdate', 'false' , '', 'no' );
    
    // custom wordpress login and wp-admin
    add_option( 'tidywp_hide_login', 'false', '', 'no' );
    add_option( 'tidywp_smart_security', 'false', '', 'no' );
    
    // license system
    add_option( 'tidywp_license_key', '', '', 'no' );
    add_option( 'tidywp_license_key_valid', 'false', '', 'no' );
    add_option( 'tidywp_license_key_last_checked', strtotime('now'), '', 'no' );
    
    // tidywp login details
    add_option( 'tidywp_website_username1', '', '', 'no' );
    add_option( 'tidywp_website_password1', '', '', 'no' );
    add_option( 'tidywp_website_userRole1', '', '', 'no' );
    add_option( 'tidywp_website_username2', '', '', 'no' );
    add_option( 'tidywp_website_password2', '', '', 'no' );
    add_option( 'tidywp_website_userRole2', '', '', 'no' );
        
    // redirect url settings
    add_option('tidywp_redirect_website_url', '', 'no' );
    add_option('tidywp_redirect_type', '', 'no' );
    
    // anti brute force check
    add_option('tidywp_brute_force_check', '0', 'no' );
	}

}
