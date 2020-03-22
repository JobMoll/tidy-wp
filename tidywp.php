<?php

/**
Plugin Name: Tidy WP
Plugin URI: https://tidywp.com/
Description: A clean & easy way to manage multiple Wordpress websites! This plugin is needed to get the Tidy WP app working!
Version: 0.0.4
Requires at least: 5.1
Requires PHP:      7.0
Author:            Job Moll
Author URI:        https://sparknowmedia.com
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Text Domain:       tidy-wp

@package tidy-wp
@license GPL-3.0+
@author Job Moll

Tidy WP - Wordpress management made easy!
Copyright (C) 2019-2020, Job Moll, job@sparknowmedia.com
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
**/

if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_tidy_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tidy-wp-activator.php';
	Tidy_Wp_Activator::activate();
}

function uninstall_tidy_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tidy-wp-deactivator.php';
	Tidy_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tidy_wp' );
register_uninstall_hook( __FILE__, 'uninstall_tidy_wp' );




// include the code snippets
include 'includes/include-code-snippets.php';
include 'includes/class-license-check.php';
require_once 'tidywp-recommended-plugins-helper.php';


$baseURL = get_bloginfo('wpurl') . '/wp-json/' . get_option('tidywp_secret_path');
$actualURL = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] 
     . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

if ($baseURL . '/exclude_new_plugin_from_autoupdate' == $actualURL || $baseURL . '/get_installed_plugins_info_summary' == $actualURL || $baseURL . '/enable_plugin_autoupdate' == $actualURL || $baseURL . '/get_installed_plugins_info' == $actualURL) {
include 'includes/class-remote-updates.php';
}


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

if ($baseURL . '/visitors_pageviews' == $actualURL || $baseURL . '/populair_pages' == $actualURL || $baseURL . '/top_referrers' == $actualURL) {
include 'includes/class-koko-analytics.php';
}

if ($baseURL . '/website_summary' == $actualURL) {
include 'includes/class-website-summary.php';
}

if ($baseURL . '/publish_new_post' == $actualURL) {
include 'includes/class-publish-new-post.php';
}

if ($baseURL . '/remove_website_from_server' == $actualURL || $baseURL . '/reset_website_secret_keys' == $actualURL) {
include 'includes/class-website-communication-with-app.php';
}

if ($baseURL . '/site_settings' == $actualURL) {
include 'includes/class-site-settings.php';
}



// secretToken key and path
$secretToken = get_option('tidywp_secret_token');
$usernameArray = array(get_option('tidywp_website_username1'), get_option('tidywp_website_username2'));

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



// add plugin page to sidebar menu
if (strpos($_SERVER["REQUEST_URI"], 'wp-admin') !== false) {
add_action( 'admin_menu', 'tidywp_add_admin_menu' );
function tidywp_add_admin_menu(  ) { 
	add_menu_page( 
	'Tidy WP', 
	'Tidy WP', 
	'manage_options', 
	'tidy-wp', 
	'tidywp_main_page', 
	// add the tidy wp logo
	plugin_dir_url( __FILE__ ) . '/assets/TidyWP-Icon.png' );
	add_submenu_page(
    'tidy-wp',       // parent slug
    'License',    // page title
    'License',             // menu title
    'manage_options',           // capability
    'tidy-wp-license', // slug
    'tidywp_license_page' // callback
); 
}
if (strpos($_SERVER["REQUEST_URI"], 'wp-admin/admin.php?page=tidy-wp') !== false) {
    
function load_custom_wp_admin_style() {
wp_enqueue_style( 'custom_wp_admin_css', plugins_url('css/plugin-page-style.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
}
include 'tidywp-main-page.php';
include 'tidywp-license-page.php';
}

function pair_with_app_link( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/admin.php?page=tidy-wp' ) ) . '">' . __( 'Connect with the app', 'textdomain' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pair_with_app_link' );



require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/JobMoll/tidy-wp',
    __FILE__,
    'tidy-wp'
);
//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


// encryption to be used
function encrypt_and_decrypt( $string, $action = 'e' ) {
    // you may change these values to your own
    $secret_key = get_option('tidywp_encrypt_key');
    $secret_iv = get_option('tidywp_encrypt_iv');
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}

function url_get_contents($Url) {
    if (!function_exists('curl_init')){
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
