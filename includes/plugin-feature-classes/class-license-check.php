<?php

// check if the license is valid
function check_if_license_is_valid() {
$licenseKey = get_option('tidy_wp_license_key');

if (!empty($licenseKey)) {
$urlToCheck = 'https://tidywp.com/?edd_action=check_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$response = wp_remote_get($urlToCheck);
$data = json_decode($response['body']);

if ($data->{'license'} == 'valid' && $data->{'expires'} > date("Y-m-d")) {
// license is valid

update_option('tidy_wp_license_key_last_checked' , strtotime('now'));

return 'true';
} else {
// license is invalid:(
deactivate_license_key();
return 'false';
}
} else {
    exit('no key found');
}
}


// activate_license_key
function activate_license_key($postLicenseKey) {
$licenseKey = $postLicenseKey; // input of the textfield here
$urlToCheck = 'https://tidywp.com/?edd_action=activate_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$response = wp_remote_get($urlToCheck);
$data = json_decode($response['body']);

if ($data->{'license'} == 'valid') {
    // succesfully activated
update_option('tidy_wp_license_key_last_checked' , strtotime('now'));
update_option('tidy_wp_license_key', $licenseKey, 'no');
update_option('tidy_wp_license_key_valid', 'true', 'no');

// enable all addons
update_option( 'tidy_wp_addons_snackbar', 'true', 'no' );
update_option( 'tidy_wp_addons_user_roles', 'true', 'no' );

return 'true';
} else {
    //something went wrong
 return 'false';
}
}





// deactivate_license_key
function deactivate_license_key() {
 
$licenseKey = get_option('tidy_wp_license_key');
$urlToCheck = 'https://tidywp.com/?edd_action=deactivate_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$response = wp_remote_get($urlToCheck);
$data = json_decode($response['body']);

if ($data->{'license'} == 'deactivated' || $data->{'license'} == 'failed') {
// succesfully deactivated
update_option( 'tidy_wp_license_key', '', 'no' );
update_option( 'tidy_wp_license_key_valid', 'false', 'no' );

// disable all addons
update_option( 'tidy_wp_addons_snackbar', 'false', 'no' );
update_option( 'tidy_wp_addons_user_roles', 'false', 'no' );

return 'true';
} else {
// something went wrong
return 'false';
}
}



// // only check if these conditions are met (Saves request to the license server checker)
// if (get_option('tidy_wp_license_key_valid') == 'true' && get_option('tidy_wp_license_key_valid') != '') {
// check_if_license_is_valid(); // return == true then the license is active!!
// }

// // cron to check license
// function tidy_wp_custom_license_cron_schedule( $schedules ) {
//     $schedules['every_24_hours'] = array(
//         'interval' => 86400, // Every 6 hours
//         'display'  => __( 'Every 24 hours' ),
//     );
//     return $schedules;
// }
// add_filter( 'cron_schedules', 'tidy_wp_custom_license_cron_schedule' );

// //Schedule an action if it's not already scheduled
// if ( ! wp_next_scheduled( 'tidy_wp_cron_hook_license' ) ) {
//     wp_schedule_event( time(), 'every_24_hours', 'tidy_wp_cron_hook_license' );
// }

// ///Hook into that action that'll fire every six hours
//  add_action( 'tidy_wp_cron_hook_license', 'tidy_wp_cron_function_license_check' );

// //create your function, that runs on cron
// function tidy_wp_cron_function_license_check() {
// if (get_option('tidy_wp_license_key_valid') == 'true' && get_option('tidy_wp_license_key_valid') != '') {
// check_if_license_is_valid(); // return == true then the license is active!!
// }
// }