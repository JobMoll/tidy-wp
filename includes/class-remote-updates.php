<?php

// true and false statement handler
function enable_plugin_autoupdate($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
        
        if ($data->get_param('enabled') == 'true') {
            update_option( 'tidywp_enable_plugin_autoupdate', 'true', 'no' );
            echo 'true';
        } 
        
        if ($data->get_param('enabled') == 'false') {
            update_option( 'tidywp_enable_plugin_autoupdate', 'false', 'no' );
            echo 'false';
        }
        
                if ($data->get_param('show') == 'true') {
            echo '{"AutoUpdateEnabled":"' . get_option( 'tidywp_enable_plugin_autoupdate') . '"}';
        }
        
    }
    } else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'enable_plugin_autoupdate', array(
    'methods' => 'GET',
    'callback' => 'enable_plugin_autoupdate',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/NNO6ZKvzjdX8eUuJ/enable_plugin_autoupdate?show=true


// 1. get list of all plugin names
// 2. get_plugin_file
// 3. add to the array and the array in the function
// 4. true or false for auto update all plugins


function get_installed_plugins_info_summary($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
    // Get all plugins
    include_once( 'wp-admin/includes/plugin.php' );
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
echo '"AutoUpdateEnabled":"' . get_option( 'tidywp_enable_plugin_autoupdate') . '"}';

   
}
} else {
    echo 'Sorry... you are not allowed to view this data.';
}
}
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_installed_plugins_info_summary', array(
    'methods' => 'GET',
    'callback' => 'get_installed_plugins_info_summary',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/tidywp/get_installed_plugins_info_summary?token=123




// 1. get list of all plugin names
function get_installed_plugins_info($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
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
} else {
    echo 'Sorry... you are not allowed to view this data.';
}
}
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'get_installed_plugins_info', array(
    'methods' => 'GET',
    'callback' => 'get_installed_plugins_info',
  ) );
} );
// https://tidywp.sparknowmedia.com/wp-json/tidywp/get_installed_plugins_info





// 2. add plugin directory to array
function exclude_new_plugin_from_autoupdate($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
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
    }  else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'exclude_new_plugin_from_autoupdate', array(
    'methods' => 'GET',
    'callback' => 'exclude_new_plugin_from_autoupdate',
  ) );
} );
// https://tidywp.sparknowmedia.com/wp-json/tidywp/exclude_new_plugin_from_autoupdate?add-directory=
// https://tidywp.sparknowmedia.com/wp-json/tidywp/exclude_new_plugin_from_autoupdate?remove-directory=
// https://tidywp.sparknowmedia.com/wp-json/tidywp/exclude_new_plugin_from_autoupdate?show=true










 
 
 
 







// function
function update_wp_core() {
global $wp_version;
echo $wp_version;
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'update_core', array(
    'methods' => 'GET',
    'callback' => 'update_wp_core',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/tidywp/update_core