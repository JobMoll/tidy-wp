<?php

function tidy_wp_auth($secretToken) {
    if ($secretToken == get_option('tidy_wp_secret_token')){
       return true;
    } else {
        header("HTTP/1.1 401 Unauthorized");
        $errorMessage = array('status' => 'error', 'message' => 'This access key is invalid or revoked');
        echo json_encode($errorMessage);
       return false;
  }
}
