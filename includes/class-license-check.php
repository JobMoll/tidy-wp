<?php

// check if the license is valid

function check_if_license_is_valid() {
// $licenseKey = get_option('tidywp_license_key');
$licenseKey = 'e1d8591b999bb8d46cdbb381c49e7b6d';

$urlToCheck = 'https://tidywp.com/?edd_action=check_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$json = file_get_contents($urlToCheck);
$data = json_decode($json);

if ($data->{'license'} == 'valid' && $data->{'expires'} > date("Y-m-d")) {
// license is valid
return 'true';
} else {
// license is invalid:(
deactivate_license_key();
    return 'false';
}

}


// activate_license_key

function activate_license_key($postLicenseKey) {
$licenseKey = $postLicenseKey; // input of the textfield here
$urlToCheck = 'https://tidywp.com/?edd_action=activate_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$json = file_get_contents($urlToCheck);
$data = json_decode($json);

if ($data->{'license'} == 'valid') {
    // succesfully activated
update_option( 'tidywp_license_key', $licenseKey, 'no' );
update_option( 'tidywp_license_key_valid', 'true', 'no' );
update_option( 'tidywp_license_key_expired', $data->{'expires'}, 'no' );

return 'true';
} else {
    //something went wrong
 return 'false';
}
}

// activate_license_key();





// deactivate_license_key

function deactivate_license_key() {
$licenseKey = get_option('tidywp_license_key');
$urlToCheck = 'https://tidywp.com/?edd_action=deactivate_license&item_id=147&license=' . $licenseKey . '&url=' . get_bloginfo('wpurl');
    
$json = file_get_contents($urlToCheck);
$data = json_decode($json);

if ($data->{'license'} == 'deactivated') {
// succesfully deativated
update_option( 'tidywp_license_key', '', 'no' );
update_option( 'tidywp_license_key_valid', 'false', 'no' );
update_option( 'tidywp_license_key_expire_date', '', 'no' );

return 'true';
} else {
// something went wrong
return 'false';
}
}

// deactivate_license_key();






// only check if these conditions are met (Safes request to the license server checker)

if (get_option('tidywp_license_key_valid') == 'true' && get_option('tidywp_license_key_valid') != '' && get_option('tidywp_license_key_expire_date') >= date("Y-m-d") && get_option('tidywp_license_key_expire_date') != '') {
check_if_license_is_valid(); // return == true then the license is active!!
}