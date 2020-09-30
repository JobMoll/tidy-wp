<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function tidy_wp_enable_plugin_autoupdate(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        $plugins_enabled = sanitize_text_field($request['pluginsEnabled']);
        if ($plugins_enabled == 'true') {
            update_option('tidy_wp_enable_plugin_autoupdate', 'true', 'no');
            echo 'true';
        } 
        if ($plugins_enabled == 'false') {
            update_option('tidy_wp_enable_plugin_autoupdate', 'false', 'no');
            echo 'false';
        }
      
        $theme_enabled = sanitize_text_field($request['themeEnabled']);
        if ($theme_enabled == 'true') {
            update_option('tidy_wp_enable_theme_autoupdate', 'true', 'no');
            echo 'true';
        } 
        if ($theme_enabled == 'false') {
            update_option('tidy_wp_enable_theme_autoupdate', 'false', 'no');
            echo 'false';
        }
        
        $core_enabled = sanitize_text_field($request['coreEnabled']);
        if ($core_enabled == 'true') {
            update_option('tidy_wp_enable_core_autoupdate', 'true', 'no');
            echo 'true';
        } 
        if ($core_enabled == 'false') {
            update_option('tidy_wp_enable_core_autoupdate', 'false', 'no');
            echo 'false';
        }
        
        
        
        if (sanitize_text_field($request['show']) == 'true') {
            echo '{"AutoUpdatePluginsEnabled":"' . get_option('tidy_wp_enable_plugin_autoupdate') . 
            '","AutoUpdateThemesEnabled":"' . get_option('tidy_wp_enable_theme_autoupdate') .
            '","AutoUpdateCoreEnabled":"' . get_option('tidy_wp_enable_core_autoupdate') . '"}';
        }
        
    
}
} 

add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'enable-plugin-autoupdate', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_enable_plugin_autoupdate',
    'permission_callback' => '__return_true',
 )); 
});


// 1. get list of all plugin names
// 2. get_plugin_file
// 3. add to the array and the array in the function
// 4. true or false for auto update all plugins

// 1. get list of all plugin names
function tidy_wp_get_installed_plugins_info() {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
    // Get all plugins
    include_once('wp-admin/includes/plugin.php');
    $all_plugins = get_plugins();

    // Get active plugins
    $active_plugins = get_option('active_plugins');


    // Assemble array of name, version, and whether plugin is active (boolean)
    foreach ($all_plugins as $key => $value) {
        
        $is_active = (in_array($key, $active_plugins)) ? true : false;
        
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
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'get-installed-plugins-info', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_get_installed_plugins_info',
    'permission_callback' => '__return_true',
 ));
});





// 2. add plugin directory to array
function tidy_wp_exclude_new_plugin_from_autoupdate(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
    if (!empty($data->get_param('add-directory'))) {
 // this line of code checks if the option is an array or not
                $exclude_plugins_array = [];
               
                   // In case of multidimentional array you can push array to an array
                $exclude_plugins_array  =  is_array(get_option('tidy_wp_exclude_plugin_from_autoupdate')) ? get_option('tidy_wp_exclude_plugin_from_autoupdate') : [];
               // check for unique value
    if(!in_array($data->get_param('add-directory'), $exclude_plugins_array, true)){
              array_push($exclude_plugins_array, $data->get_param('add-directory'));
    }
                update_option('tidy_wp_exclude_plugin_from_autoupdate', $exclude_plugins_array);

echo print_r($exclude_plugins_array);

} else if (!empty($data->get_param('remove-directory'))) {
        $exclude_plugins_array = [];
        $exclude_plugins_array  =  is_array(get_option('tidy_wp_exclude_plugin_from_autoupdate')) ? get_option('tidy_wp_exclude_plugin_from_autoupdate') : [];
        
 if (($key = array_search($data->get_param('remove-directory'), $exclude_plugins_array)) !== false) {
    unset($exclude_plugins_array[$key]);
    update_option('tidy_wp_exclude_plugin_from_autoupdate', $exclude_plugins_array);
    echo print_r($exclude_plugins_array);
}


        
}
        if (sanitize_text_field($request['show']) == 'true') {
            
                echo print_r(get_option('tidy_wp_exclude_plugin_from_autoupdate'));
        }

     
}
}

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'exclude-new-plugin-from-autoupdate', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_exclude_new_plugin_from_autoupdate',
    'permission_callback' => '__return_true',
 ));
});






function tidy_wp_get_installed_plugins_info_summary(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
	
	if (! function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $all_plugins = get_plugins();

    // Get active plugins
    $active_plugins = get_option('active_plugins');


  echo '{"TotalInstalledPlugins":"' . count($all_plugins) . '", ';
  echo '"ActivePlugins":"' . count($active_plugins) . '", ';
  if (empty(get_option('tidy_wp_exclude_plugin_from_autoupdate'))) {
          echo '"ExcludedPluginsCount":"0", ';
  } else {
          echo '"ExcludedPluginsCount":"' . count(get_option('tidy_wp_exclude_plugin_from_autoupdate')) . '", ';
  }
  
// are there updates available?
// get plugin updates

$plugin_updates_empty = false;
$update_plugins = get_site_transient('update_plugins');
if (!empty($update_plugins->response)) {
echo '"UpdatablePlugins":"' . $counts['plugins'] = count($update_plugins->response) . '", ';
} else {
	echo '"UpdatablePlugins":"0", ';
	$plugin_updates_empty = true;
}

// get theme updates
$theme_updates_empty = false;
$update_themes = get_site_transient('update_themes');
if (!empty($update_themes->response)) {
echo '"UpdatableThemes":"' . $counts['themes'] = count($update_themes->response) . '", ';
} else {
	echo '"UpdatableThemes":"0", ';
	$theme_updates_empty = true;
}
	
// get core updates
$core_updates_empty = false;
require_once ABSPATH . '/wp-admin/includes/update.php';
if (function_exists('get_core_updates')) {
   $update_wordpress = get_core_updates(array('dismissed' => false));
   if (! empty($update_wordpress) && ! in_array($update_wordpress[0]->response, array('development', 'latest'))) {
echo '"UpdatableCore":"' .  $counts['wordpress'] = 1 . '", ';
        } else {
	   echo '"UpdatableCore":"' .  $counts['wordpress'] = 0 . '", ';
	   $core_updates_empty = true;
   }
    }
 
// get translations updates
$translations_updates_empty = false;
if (wp_get_translation_updates()) {
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

echo '"AutoUpdateEnabled":"' . get_option('tidy_wp_enable_plugin_autoupdate') . '"}';
   
} 
}  
 
// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'get-installed-plugins-info-summary', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_get_installed_plugins_info_summary',
    'permission_callback' => '__return_true',
 ));
});