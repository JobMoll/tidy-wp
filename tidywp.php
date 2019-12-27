<?php

/**
 * @link              https://tidywp.com
 * @since             1.0.0
 * @package           Tidy_WP
 * 
* @wordpress-plugin
* Plugin Name: Tidy WP
* Plugin URI: https://tidywp.com/
* Description: A clean & easy way to manage multiple Wordpress websites! This plugin is needed to get the Tidy WP app working!
* Version: 1.0.0
 * Author:            Job Moll
 * Author URI:        https://sparknowmedia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tidy-wp
**/


if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_tidy_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tidy-wp-activator.php';
	Tidy_Wp_Activator::activate();
}

function deactivate_tidy_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tidy-wp-deactivator.php';
	Tidy_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tidy_wp' );
register_deactivation_hook( __FILE__, 'deactivate_tidy_wp' );




// include the code snippets
include 'includes/include-code-snippets.php';
include 'tgm-plugin-activation-helper.php';


$baseURL = get_bloginfo('wpurl') . '/wp-json/' . get_option('tidywp_secret_path');
$actualURL = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] 
     . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

if ($baseURL . '/exclude_new_plugin_from_autoupdate' == $actualURL || $baseURL . '/get_installed_plugins_info_summary' == $actualURL || $baseURL . '/enable_plugin_autoupdate' == $actualURL || $baseURL . '/get_installed_plugins_info' == $actualURL || $baseURL . '/update_wp_core' == $actualURL) {
include 'includes/class-remote-updates.php';
}


// include 'create-backup/functions.php';

if ($baseURL . '/show_count_database' == $actualURL || $baseURL . '/cleanup_database' == $actualURL) {
include 'includes/class-clean-database.php';
}

if ($baseURL . '/maintaince_mode' == $actualURL) {
include 'includes/class-maintaince-mode.php';
}

if ($baseURL . '/hide_wp_login_admin' == $actualURL) {
include 'includes/class-custom-login-url.php';
}

if ($baseURL . '/smart_security' == $actualURL) {
include 'includes/class-smart-security.php';
}

if ($baseURL . '/woocommerce_data' == $actualURL) {
include 'includes/class-woocommerce.php';
}

if ($baseURL . '/visitors_pageviews' == $actualURL || $baseURL . '/populair_pages' == $actualURL) {
include 'includes/class-koko-analytics.php';
}

if ($baseURL . '/website_summary' == $actualURL) {
include 'includes/class-website-summary.php';
}

// secretToken key
$secretToken = get_option('tidywp_secret_token');



// autoupdate plugins or not and exclude some
function filter_autoupdate_plugins($update, $plugin)
{
    $pluginsNotToUpdate = [];
               
    $pluginsNotToUpdate  =  is_array(get_option('tidywp_exclude_plugin_from_autoupdate')) ? get_option('tidywp_exclude_plugin_from_autoupdate') : [];

    if (is_object($plugin))
    {
        $pluginName = $plugin->plugin;
    }
    else // compatible with earlier versions of wordpress
    {
        $pluginName = $plugin;
    }

    // Allow all plugins except the ones listed above to be updated
    if (!in_array(trim($pluginName),$pluginsNotToUpdate))
    {
       
        return get_option( 'tidywp_enable_plugin_autoupdate'); // return true to allow update to go ahead
    }

    return false;
}
add_filter( 'auto_update_plugin', 'filter_autoupdate_plugins' ,20  /* priority  */,2 /* argument count passed to filter function  */);



// add plugin page to sidebar menu
if (strpos($_SERVER["REQUEST_URI"], 'wp-admin') !== false) {
add_action( 'admin_menu', 'tidywp_add_admin_menu' );
function tidywp_add_admin_menu(  ) { 
	add_menu_page( 
	'Tidy WP', 
	'Tidy WP', 
	'manage_options', 
	'tidy-wp', 
	'tidywp_options_page', 
	// add the tidy wp logo
	plugin_dir_url( __FILE__ ) . '/assets/TidyWP-Icon.png' );
}
if (strpos($_SERVER["REQUEST_URI"], 'tidy-wp') !== false) {
include 'plugin-page-index.php';
}
}

function pair_with_app_link( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/admin.php?page=tidy-wp' ) ) . '">' . __( 'Pair with the app', 'textdomain' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pair_with_app_link' );