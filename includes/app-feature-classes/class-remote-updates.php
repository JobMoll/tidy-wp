<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function enable_plugin_autoupdate($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
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
        
    
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'enable_plugin_autoupdate', array(
    'methods' => 'GET',
    'callback' => 'enable_plugin_autoupdate',
  ) );
} );


// 1. get list of all plugin names
// 2. get_plugin_file
// 3. add to the array and the array in the function
// 4. true or false for auto update all plugins

// 1. get list of all plugin names
function get_installed_plugins_info() {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
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
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
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
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
	
	if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
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

$plugin_updates_empty = false;
$update_plugins = get_site_transient( 'update_plugins' );
if ( !empty( $update_plugins->response ) ) {
echo '"UpdatablePlugins":"' . $counts['plugins'] = count($update_plugins->response) . '", ';
} else {
	echo '"UpdatablePlugins":"0", ';
	$plugin_updates_empty = true;
	
}
// get theme updates
$theme_updates_empty = false;
$update_themes = get_site_transient( 'update_themes' );
if ( ! empty( $update_themes->response ) ) {
echo '"UpdatableThemes":"' . $counts['themes'] = count($update_themes->response) . '", ';
} else {
	echo '"UpdatableThemes":"0", ';
	$theme_updates_empty = true;
}
	
// get core updates
$core_updates_empty = false;
require_once ABSPATH . '/wp-admin/includes/update.php';
if ( function_exists( 'get_core_updates' ) ) {
   $update_wordpress = get_core_updates( array( 'dismissed' => false ) );
   if ( ! empty( $update_wordpress ) && ! in_array($update_wordpress[0]->response, array( 'development', 'latest') ) ) {
echo '"UpdatableCore":"' .  $counts['wordpress'] = 1 . '", ';
        } else {
	   echo '"UpdatableCore":"' .  $counts['wordpress'] = 0 . '", ';
	   $core_updates_empty = true;
   }
    }
 
// get translations updates
$translations_updates_empty = false;
if ( wp_get_translation_updates() ) {
	   echo '"UpdatableTranslations":"' .  $counts['translations'] = 1 . '", ';
    } else {
	  echo '"UpdatableTranslations":"' .  $counts['translations'] = 0 . '", ';
	  $translations_updates_empty = true;
	}
	
// calculate total updates
$totalUpdates = '0';
if ($plugin_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['plugins'];
}
if ($theme_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['themes'];
}
if ($core_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['wordpress'];
}
if ($translations_updates_empty == false) {
$totalUpdates = $totalUpdates + $counts['translations'];
}
echo '"UpdatableTotal":"' . $totalUpdates . '", ';

echo '"AutoUpdateEnabled":"' . get_option( 'tidywp_enable_plugin_autoupdate') . '"}';
   
} 
}  
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_installed_plugins_info_summary', array(
    'methods' => 'GET',
    'callback' => 'get_installed_plugins_info_summary',
  ) );
} );