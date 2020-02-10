<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function enable_plugin_autoupdate($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
        if ($data->get_param('pluginsEnabled') == 'true') {
            update_option( 'tidywp_enable_plugin_autoupdate', 'true', 'no' );
            echo 'true';
        } 
        if ($data->get_param('pluginsEnabled') == 'false') {
            update_option( 'tidywp_enable_plugin_autoupdate', 'false', 'no' );
            echo 'false';
        }
      
        
        if ($data->get_param('themeEnabled') == 'true') {
            update_option( 'tidywp_enable_theme_autoupdate', 'true', 'no' );
            echo 'true';
        } 
        if ($data->get_param('themeEnabled') == 'false') {
            update_option( 'tidywp_enable_theme_autoupdate', 'false', 'no' );
            echo 'false';
        }
        
        
        if ($data->get_param('coreEnabled') == 'true') {
            update_option( 'tidywp_enable_core_autoupdate', 'true', 'no' );
            echo 'true';
        } 
        if ($data->get_param('coreEnabled') == 'false') {
            update_option( 'tidywp_enable_core_autoupdate', 'false', 'no' );
            echo 'false';
        }
        
        
        
        if ($data->get_param('show') == 'true') {
            echo '{"AutoUpdatePluginsEnabled":"' . get_option( 'tidywp_enable_plugin_autoupdate') . 
            '","AutoUpdateThemesEnabled":"' . get_option( 'tidywp_enable_theme_autoupdate') .
            '","AutoUpdateCoreEnabled":"' . get_option( 'tidywp_enable_core_autoupdate') . '"}';
        }
        
    
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'enable_plugin_autoupdate', array(
    'methods' => 'GET',
    'callback' => 'enable_plugin_autoupdate',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/enable_plugin_autoupdate?enabled=show




// 1. get list of all plugin names
// 2. get_plugin_file
// 3. add to the array and the array in the function
// 4. true or false for auto update all plugins

// 1. get list of all plugin names
function get_installed_plugins_info() {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
    // Get all plugins
    include_once( 'wp-admin/includes/plugin.php' );
    $all_plugins = get_plugins();

    // Get active plugins
    $active_plugins = get_option('active_plugins');


    // Assemble array of name, version, and whether plugin is active (boolean)
    foreach ( $all_plugins as $key => $value ) {
        
        $is_active = ( in_array( $key, $active_plugins ) ) ? true : false;
        
        $plugins[ $key ] = array(
            'directory' => $key,
            'name'    => $value['Name'],
            'version' => $value['Version'],
            'active'  => $is_active,
        );
       
    }
    return $plugins;
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
} 
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_installed_plugins_info', array(
    'methods' => 'GET',
    'callback' => 'get_installed_plugins_info',
  ) );
} );
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/get_installed_plugins_info





// 2. add plugin directory to array
function exclude_new_plugin_from_autoupdate($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
    if (!empty($data->get_param('add-directory'))) {
 // this line of code checks if the option is an array or not
                $exclude_plugins_array = [];
               
                   // In case of multidimentional array you can push array to an array
                $exclude_plugins_array  =  is_array(get_option('tidywp_exclude_plugin_from_autoupdate')) ? get_option('tidywp_exclude_plugin_from_autoupdate') : [];
               // check for unique value
    if(!in_array($data->get_param('add-directory'), $exclude_plugins_array, true)){
              array_push($exclude_plugins_array, $data->get_param('add-directory'));
    }
                update_option('tidywp_exclude_plugin_from_autoupdate', $exclude_plugins_array);

echo print_r($exclude_plugins_array);

} else if (!empty($data->get_param('remove-directory'))) {
        $exclude_plugins_array = [];
        $exclude_plugins_array  =  is_array(get_option('tidywp_exclude_plugin_from_autoupdate')) ? get_option('tidywp_exclude_plugin_from_autoupdate') : [];
        
 if (($key = array_search($data->get_param('remove-directory'), $exclude_plugins_array)) !== false) {
    unset($exclude_plugins_array[$key]);
    update_option('tidywp_exclude_plugin_from_autoupdate', $exclude_plugins_array);
    echo print_r($exclude_plugins_array);
}


        
}
        if ($data->get_param('show') == 'true') {
            
                echo print_r(get_option('tidywp_exclude_plugin_from_autoupdate'));
        }

     
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'exclude_new_plugin_from_autoupdate', array(
    'methods' => 'GET',
    'callback' => 'exclude_new_plugin_from_autoupdate',
  ) );
} );
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/exclude_new_plugin_from_autoupdate?add-directory=
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/exclude_new_plugin_from_autoupdate?remove-directory=
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/exclude_new_plugin_from_autoupdate?show=true




 


function get_installed_plugins_info_summary($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
	
    $all_plugins = get_plugins();

    // Get active plugins
    $active_plugins = get_option('active_plugins');


  echo '{"TotalInstalledPlugins":"' . count($all_plugins) . '", ';
  echo '"ActivePlugins":"' . count($active_plugins) . '", ';
  if (empty(get_option('tidywp_exclude_plugin_from_autoupdate'))) {
          echo '"ExcludedPluginsCount":"0", ';
  } else {
          echo '"ExcludedPluginsCount":"' . count(get_option('tidywp_exclude_plugin_from_autoupdate')) . '", ';
  }
  
// are there updates available?
// get plugin updates
$update_plugins = get_site_transient( 'update_plugins' );
if ( ! empty( $update_plugins->response ) ) {
echo '"UpdatablePlugins":"' . $counts['plugins'] = count( $update_plugins->response ) . '", ';
} else {
	echo '"UpdatablePlugins":"0", ';
}
// get theme updates
$update_themes = get_site_transient( 'update_themes' );
if ( ! empty( $update_themes->response ) ) {
echo '"UpdatableThemes":"' . $counts['themes'] = count( $update_themes->response ) . '", ';
} else {
	echo '"UpdatableThemes":"0", ';
}
	
// get core updates
require_once ABSPATH . '/wp-admin/includes/update.php';
if ( function_exists( 'get_core_updates' ) ) {
   $update_wordpress = get_core_updates( array( 'dismissed' => false ) );
   if ( ! empty( $update_wordpress ) && ! in_array( $update_wordpress[0]->response, array( 'development', 'latest' ) ) ) {
echo '"UpdatableCore":"' .  $counts['wordpress'] = 1 . '", ';
        } else {
	   echo '"UpdatableCore":"' .  $counts['wordpress'] = 0 . '", ';
   }
    }
 
// get translations updates
if ( wp_get_translation_updates() ) {
	   echo '"UpdatableTranslations":"' .  $counts['translations'] = 1 . '", ';
    } else {
	  echo '"UpdatableTranslations":"' .  $counts['translations'] = 0 . '", ';
	}
	
// calculate total updates
echo '"UpdatableTotal":"' . ($counts['plugins'] + $counts['themes'] + $counts['wordpress'] + $counts['translations']). '", ';
	
echo '"AutoUpdateEnabled":"' . get_option( 'tidywp_enable_plugin_autoupdate') . '"}';
   
     } else { 
echo 'Sorry... you are not allowed to view this data.';
}
} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidywp_brute_force_check'));
update_option('tidywp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidywp_brute_force_check', '0', 'no' );
}
}  
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_installed_plugins_info_summary', array(
    'methods' => 'GET',
    'callback' => 'get_installed_plugins_info_summary',
  ) );
} );
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/get_installed_plugins_info_summary