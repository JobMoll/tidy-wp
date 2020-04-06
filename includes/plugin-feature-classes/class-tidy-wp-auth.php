<?php

function tidyWPAuth($http_token) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
	$arrayHeaderHTTP = explode(',', $http_token);
     if (($arrayHeaderHTTP[0] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($arrayHeaderHTTP[1], 'e' ), $GLOBALS['usernameArray']))) {
         
         return true;

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