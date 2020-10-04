<?php

/**
Plugin Name: Tidy WP
Plugin URI: https://tidywp.com/
Description: A clean & easy way to manage multiple Wordpress websites! This plugin is needed to get the Tidy WP app working!

When changing the version here also change it here below in the define variable
Version: 0.0.8
Requires at least: 4.4.0
Tested up to: 5.5.1
Requires PHP: 7.0
Author: Mollup
Author URI: https://mollup.nl/
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Text Domain: tidy-wp

@package tidy-wp
@license GPL-3.0+
@author Job Moll

Tidy WP - Wordpress management made easy!
Copyright (C) 2019-2020, Job Moll, job@mollup.com
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

if (! defined('WPINC')) {
	die;
}

define('TIDY_WP_CURRENT_PLUGIN_VERSION', '0.0.8');
define('TIDY_WP_PLUGIN_DIR', plugin_dir_path( __FILE__));
    
function activate_tidy_wp() {
	require_once plugin_dir_path(__FILE__) . 'includes/plugin-feature-classes/class-tidy-wp-activator.php';
	Tidy_Wp_Activator::activate();
}

function deactivator_tidy_wp() {
	require_once plugin_dir_path(__FILE__) . 'includes/plugin-feature-classes/class-tidy-wp-deactivator.php';
	Tidy_Wp_Deactivator::deactivate();
}

function uninstall_tidy_wp() {
	require_once plugin_dir_path(__FILE__) . 'includes/plugin-feature-classes/class-tidy-wp-uninstaller.php';
    Tidy_Wp_Uninstaller::uninstall();
}

register_activation_hook(__FILE__, 'activate_tidy_wp');
register_deactivation_hook(__FILE__, 'deactivator_tidy_wp');
register_uninstall_hook(__FILE__, 'uninstall_tidy_wp');


// include the code snippets
include 'includes/include-code-snippets.php';
include 'includes/app-feature-classes/class-notification-helper.php';
require_once  __DIR__ . '/tidy-wp-recommended-plugins-helper.php';

// include all the endpoints
include 'includes/app-feature-classes/class-remote-updates.php';
include 'includes/app-feature-classes/class-clean-database.php';
include 'includes/app-feature-classes/class-maintaince-mode.php';
include 'includes/app-feature-classes/class-custom-website-snackbar.php';
include 'includes/app-feature-classes/class-custom-login-url.php';
include 'includes/app-feature-classes/class-smart-security.php';
include 'includes/app-feature-classes/class-woocommerce.php';
include 'includes/app-feature-classes/class-koko-analytics.php';
include 'includes/app-feature-classes/class-website-summary.php';
include 'includes/app-feature-classes/class-publish-new-post.php';
include 'includes/app-feature-classes/class-site-settings.php';
include 'includes/app-feature-classes/class-website-properties.php';
include 'includes/app-feature-classes/class-backend-notice.php';
include 'includes/app-feature-classes/class-duplicate-pages-and-posts.php';


// secretToken key and path
$secretToken = get_option('tidy_wp_secret_token');

function tidy_wp_generate_random_string($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


// add plugin page to sidebar menu
if (strpos($_SERVER["REQUEST_URI"], 'wp-admin') !== false) {
add_action('admin_menu', 'tidy_wp_add_admin_menu');
function tidy_wp_add_admin_menu() { 
	add_menu_page(
	'Tidy WP', 
	'Tidy WP', 
	'manage_options', 
	'tidy-wp', 
	'tidy_wp_main_page', 
	// add the tidy wp logo
	plugin_dir_url(__FILE__) . '/backend-assets/images/TidyWP-Icon.png' 
	);
	add_submenu_page(
    'tidy-wp',       // parent slug
    'Addons',    // page title
    'Addons',             // menu title
    'manage_options',           // capability
    'tidy-wp-addon', // slug
    'tidy_wp_addon_page' // callback
);
}
if (strpos($_SERVER["REQUEST_URI"], 'wp-admin/admin.php?page=tidy-wp') !== false) {
    
function tidy_wp_load_custom_wp_admin_style() {
wp_enqueue_style('custom_wp_admin_css', plugins_url('/backend-assets/css/plugin-page-style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'tidy_wp_load_custom_wp_admin_style');
}
include 'plugin-pages/tidy-wp-main-page.php';
include 'plugin-pages/tidy-wp-addon-page.php';
}

function tidy_wp_pair_with_app_link($links) {
	$links = array_merge(array(
		'<a href="' . esc_url(admin_url('/admin.php?page=tidy-wp')) . '">' . __('Connect with the app', 'textdomain') . '</a>'
	), $links);
	return $links;
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'tidy_wp_pair_with_app_link');