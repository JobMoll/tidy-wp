<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
    
    
// enable or disable maintaince mode

if (get_option('tidywp_maintaince_mode') == 'true') {
function maintenance_mode_switch() {
// Activate WordPress Maintenance Mode
function wp_maintenance_mode() {
    if (!current_user_can('edit_themes')) {
        wp_die("<h1>Under Maintenance</h1><br />Something ain't right, but we're working on it! Check back later.");
    }
}
add_action('get_header', 'wp_maintenance_mode');
}
maintenance_mode_switch();
}



// smart security

if (get_option('tidywp_smart_security') == 'true') {
// hide wordpress login errors
function no_wordpress_errors(){
  return 'Something is wrong!';
}
add_filter( 'login_errors', 'no_wordpress_errors' );

// thanks to https://coderwall.com/p/dc2bbg/limit-login-attemps
if ( ! class_exists( 'Limit_Login_Attempts' ) ) {
    class Limit_Login_Attempts {

        var $failed_login_limit = 3;                    //Number of authentification accepted
        var $lockout_duration   = 1800;                 //Stop authentification process for 30 minutes: 60*30 = 1800
        var $transient_name     = 'attempted_login';    //Transient used

        public function __construct() {
            add_filter( 'authenticate', array( $this, 'check_attempted_login' ), 30, 3 );
            add_action( 'wp_login_failed', array( $this, 'login_failed' ), 10, 1 );
        }

        /**
         * Lock login attempts of failed login limit is reached
         */
        public function check_attempted_login( $user, $username, $password ) {
            if ( get_transient( $this->transient_name ) ) {
                $datas = get_transient( $this->transient_name );

                if ( $datas['tried'] >= $this->failed_login_limit ) {
                    $until = get_option( '_transient_timeout_' . $this->transient_name );
                    $time = $this->when( $until );

                    //Display error message to the user when limit is reached
                    return new WP_Error( 'too_many_tried', sprintf( __( '<strong>ERROR</strong>: You have reached authentification limit, you will be able to try again in %1$s.' ) , $time ) );
                }
            }

            return $user;
        }


        /**
         * Add transient
         */
        public function login_failed( $username ) {
            if ( get_transient( $this->transient_name ) ) {
                $datas = get_transient( $this->transient_name );
                $datas['tried']++;

                if ( $datas['tried'] <= $this->failed_login_limit )
                    set_transient( $this->transient_name, $datas , $this->lockout_duration );
            } else {
                $datas = array(
                    'tried'     => 1
                );
                set_transient( $this->transient_name, $datas , $this->lockout_duration );
            }
        }


        /**
         * Return difference between 2 given dates
         * @param  int      $time   Date as Unix timestamp
         * @return string           Return string
         */
        private function when( $time ) {
            if ( ! $time )
                return;

            $right_now = time();

            $diff = abs( $right_now - $time );

            $second = 1;
            $minute = $second * 60;
            $hour = $minute * 60;
            $day = $hour * 24;

            if ( $diff < $minute )
                return floor( $diff / $second ) . ' secondes';

            if ( $diff < $minute * 2 )
                return "about 1 minute ago";

            if ( $diff < $hour )
                return floor( $diff / $minute ) . ' minutes';

            if ( $diff < $hour * 2 )
                return 'about 1 hour';

            return floor( $diff / $hour ) . ' hours';
        }
    }
}

//Enable it:
new Limit_Login_Attempts();



// RejectMaliciousRequests
global $user_ID; if($user_ID) {
    if(!current_user_can('administrator')) {
        if (strlen($_SERVER['REQUEST_URI']) > 255 ||
            stripos($_SERVER['REQUEST_URI'], "eval(") ||
            stripos($_SERVER['REQUEST_URI'], "CONCAT") ||
            stripos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
            stripos($_SERVER['REQUEST_URI'], "base64")) {
                @header("HTTP/1.1 414 Request-URI Too Long");
                @header("Status: 414 Request-URI Too Long");
                @header("Connection: Close");
                @exit;
        }
    }
}


// RemoveWPandScriptVersion
function remove_wordpress_version_number() {
return '';
}
add_filter('the_generator', 'remove_wordpress_version_number');
function remove_version_from_scripts( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_version_from_scripts');
add_filter( 'script_loader_src', 'remove_version_from_scripts');
remove_action('wp_head', 'wp_generator');


// HTTPResponseSecurity
function add_security_headers() {
    // Enforce the use of HTTPS
    header( "Strict-Transport-Security: max-age=31536000; includeSubDomains; preload" );

    // Prevent Clickjacking
    header( "X-Frame-Options: DENY" );

    // Block Access If XSS Attack Is Suspected
    header( "X-XSS-Protection: 1; mode=block" );

    // Prevent MIME-Type Sniffing
    header( "X-Content-Type-Options: nosniff" );

    // Referrer Policy
    header( "Referrer-Policy: strict-origin-when-cross-origin" );
}
add_action( 'send_headers', 'add_security_headers', 1 );


// force ssl
add_action('wp_head','nocdn_on_ssl_page');
function nocdn_on_ssl_page() {
    if($_SERVER['HTTPS'] == "on") {
        define('DONOTCDN', true);
    }
}
define('FORCE_SSL_LOGIN', true);
define( 'FORCE_SSL_ADMIN', true );

// DisablePHPErrorReporting
error_reporting(0);
@ini_set(‘display_errors’, 0);

// disable plugin and theme editor
define( 'DISALLOW_FILE_EDIT', true );
    
//Disable trackbacks and pingbacks
function filterPostComments($posts) {
    foreach ($posts as $key => $p) {
        if ($p->comment_count <= 0) { return $posts; }
        $comments = get_approved_comments((int)$p->ID);
        $comments = array_filter($comments, "stripTrackback");
        $posts[$key]->comment_count = sizeof($comments);
    }
    return $posts;
}
//Updates the count for comments and trackbacks
function filterTrackbacks($comms) {
global $comments, $trackbacks;
    $comments = array_filter($comms,"stripTrackback");
    return $comments;
}
//Strips out trackbacks/pingbacks
function stripTrackback($var) {
    if ($var->comment_type == 'trackback' || $var->comment_type == 'pingback') { return false; }
    return true;
}
  
add_filter('comments_array', 'filterTrackbacks', 0);
add_filter('the_posts', 'filterPostComments', 0);
    
// Prevent user enumeration
if (!is_admin()) {
    // default URL format
    if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
    add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request) {
    // permalink URL format
    if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
    else return $redirect;
}
    
//
// Disable use XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
    
// Disable X-Pingback to header
add_filter( 'wp_headers', 'disable_x_pingback' );
function disable_x_pingback( $headers ) {
    unset( $headers['X-Pingback'] );

return $headers;
}

// Disable Emojicons tinymce
function disable_wp_emojicons() {
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
    add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'disable_wp_emojicons' );

function disable_emojicons_tinymce( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

// Disable the message - JQMIGRATE: Migrate is installed, version 1.4.1
add_action('wp_default_scripts', function ($scripts) {
    if (!empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
    }
});
    
// Disable the WP REST API
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
add_filter('json_enabled', '__return_false');
    
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
}



// change login url
if (get_option('tidywp_hide_login') != 'false') {
function redirect_to_nonexistent_page(){
     $new_login=  get_option('tidywp_hide_login');
    if(strpos($_SERVER['REQUEST_URI'], $new_login) === false){
                wp_safe_redirect( home_url( 'NonExistentPage' ), 302 );
      exit();
    }
 }
add_action( 'login_head', 'redirect_to_nonexistent_page');

function redirect_to_actual_login(){
  $new_login =  get_option('tidywp_hide_login');
  if(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY) == $new_login&& ($_GET['redirect'] !== false)){
                 wp_safe_redirect(home_url("wp-login.php?$new_login&redirect=false"));
     exit();
 
  }
}
add_action( 'init', 'redirect_to_actual_login');
}



// autoupdate plugins or not and exclude some

if (get_option( 'tidywp_enable_plugin_autoupdate') == 'true') {
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
       
        return true; // return true to allow update to go ahead
    }

    return false;
}
add_filter( 'auto_update_plugin', 'filter_autoupdate_plugins' ,20  /* priority  */,2 /* argument count passed to filter function  */);
}



// autoupdate themes and core

if (get_option( 'tidywp_enable_theme_autoupdate') == 'true') {
add_filter( 'auto_update_theme', '__return_true' );
}

if (get_option( 'tidywp_enable_theme_autoupdate') == 'true') {
define( 'WP_AUTO_UPDATE_CORE', true );
}



// always auto update the TidyWP plugin

function include_plugins_from_auto_update( $update, $item ) {
    return (in_array( $item->plugin, array(
        'tidy-wp/tidywp.php',
    ) ) );
}
add_filter( 'auto_update_plugin', 'include_plugins_from_auto_update', 10, 2 );


// 301 redirect
if (get_option('tidywp_redirect_website_url') != '' && get_option('tidywp_redirect_type') == '301') {
function redirect_301(){
if ( ! is_admin() ) {
    wp_redirect( get_option('tidywp_redirect_website_url') . $_SERVER['REQUEST_URI'], 301, get_bloginfo('name') . ' - Tidy WP');
    exit;
}
 }
add_action( 'login_head', 'redirect_301');
}

// 302 redirect
if (get_option('tidywp_redirect_website_url') != '' && get_option('tidywp_redirect_type') == '302') {
function redirect_302(){
if ( ! is_admin() ) {
    wp_redirect( get_option('tidywp_redirect_website_url') . $_SERVER['REQUEST_URI'], 302, get_bloginfo('name') . ' - Tidy WP');
    exit;
}
 }
add_action( 'login_head', 'redirect_302');
}
