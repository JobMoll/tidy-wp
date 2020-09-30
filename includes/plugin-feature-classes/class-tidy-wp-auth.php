<?php

function tidy_wp_auth($secretToken) {
    if ($secretToken == get_option('tidy_wp_secret_token')){
        return true;
    } else {
        echo 'Sorry... you are not allowed to view this data. Secret token is invalid';
        return false;
  }
}
