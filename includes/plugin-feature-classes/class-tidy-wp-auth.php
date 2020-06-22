<?php

function tidyWPAuth($http_token) {
   if (intval(get_option('tidy_wp_brute_force_check')) <= 20) {
	$arrayHeaderHTTP = explode(',', $http_token);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
         
         if (strtotime(get_option('tidy_wp_license_key_last_checked')) <= strtotime('-12 hours') && get_option('tidy_wp_license_key_valid') == 'true' && get_option('tidy_wp_license_key_valid') != '') {
             include 'class-license-check.php';
             check_if_license_is_valid();
         }
         
         return true;

} else {
echo 'Sorry... you are not allowed to view this data.';

$oldBruteForceCheck = intval(get_option('tidy_wp_brute_force_check'));
update_option('tidy_wp_brute_force_check', strval($oldBruteForceCheck + 1), 'no' );
}
} else {
echo 'Sorry... you are not allowed to view this data.';

include ABSPATH . 'wp-content/plugins/tidy-wp/tidywp-main-page.php';
resetTokenAndPath();

update_option('tidy_wp_brute_force_check', '0', 'no' );
}
}