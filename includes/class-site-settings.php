<?php

function site_settings($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
	$arrayHeaderHTTP = explode(',', $_SERVER['HTTP_TOKEN']);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
        
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
  register_rest_route( get_option('tidywp_secret_path'), 'site_settings', array(
    'methods' => 'GET',
    'callback' => 'site_settings',
  ) );
} );
