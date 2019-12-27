<?php

/**
 * Fired during plugin activation
 *
 * @link       https://sparknowmedia.com
 * @since      1.0.0
 *
 * @package    Tidy_WP
 * @subpackage Tidy_WP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tidy_WP
 * @subpackage Tidy_WP/includes
 * @author     Job Moll <job@sparknowmedia.com>
 */
class Tidy_Wp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
add_option( 'tidywp_secret_path', generateRandomString(16), '', 'no' );
    
    // secret token
    add_option( 'tidywp_secret_token', generateRandomString(16), '', 'no' );
    
    // maintaince mode
    add_option( 'tidywp_maintaince_mode', 'false', '', 'no' );
    
    // exclude plugins from auto update
    add_option( 'tidywp_exclude_plugin_from_autoupdate', '', '', 'no' );
    add_option( 'tidywp_enable_plugin_autoupdate', 'false' , '', 'no' );
    
    // custom wordpress login and wp-admin
    add_option( 'tidywp_hide_login', 'false', '', 'no' );
    add_option( 'tidywp_smart_security', 'false', '', 'no' );
	}

}
