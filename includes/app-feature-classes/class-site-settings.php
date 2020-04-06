<?php

function site_settings($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
        
        if ($data->get_param('siteTitle') != '') {
            update_option('blogname', $data->get_param('siteTitle'), 'yes' );
        } 
        if ($data->get_param('tagline') != '') {
            update_option('blogdescription', $data->get_param('tagline'), 'yes' );
        }
        
        // value 0 is disabled --- 1 is enabled
        if ($data->get_param('usersCanRegister') != '') {
            update_option('users_can_register', $data->get_param('usersCanRegister'), 'yes' );
        }
        
        // value 0 is search engine visibility on --- 1 is off
       if ($data->get_param('blogPublic') != '') {
            update_option('blog_public', $data->get_param('blogPublic'), 'yes' );
       }
	

        // send the redirect URL and which redirect type
        if ($data->get_param('redirectWebsiteURL') != '' && $data->get_param('redirectWebsiteType') != '') {
            update_option('tidywp_redirect_website_url', $data->get_param('redirectWebsiteURL'), 'no' );
            update_option('tidywp_redirect_type', $data->get_param('redirectWebsiteType'), 'no' );
        } 
        // set to true to empty the option
        if ($data->get_param('redirectWebsiteURLDisable') != '') {
            update_option('tidywp_redirect_website_url', '', 'no' );
            update_option('tidywp_redirect_type', '', 'no' );
        }


echo '{"SiteTitle":"' . get_option('blogname') . '", ';
echo '"Tagline":"' . get_option('blogdescription') . '", ';
echo '"UsersCanRegister":"' . get_option('users_can_register') . '", ';
echo '"RedirectWebsiteURL":"' . get_option('tidywp_redirect_website_url') . '", ';
echo '"RedirectWebsiteType":"' . get_option('tidywp_redirect_type') . '", ';
echo '"BlogPublic":"' . get_option('blog_public') . '"}';

   
}
}  
 
// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'site_settings', array(
    'methods' => 'GET',
    'callback' => 'site_settings',
  ) );
} );
