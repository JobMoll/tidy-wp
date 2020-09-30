<?php

//https://docs.easydigitaldownloads.com/article/377-checking-if-license-keys-are-valid-in-wordpress-plugins-and-themes
    
function tidy_wp_plugins_check_license() {
        $store_url = 'http://yoursite.com';
        $item_name = 'Your Item Name';
        $license = '834bbb2d27c02eb1ac11f4ce6ffa20bb';
        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode( $item_name ),
            'url' => home_url()
        );
        $response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
          if ( is_wp_error( $response ) ) {
            return false;
          }

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        if( $license_data->license == 'valid' ) {
            echo 'valid';
            exit;
            // this license is still valid
        } else {
            echo 'invalid';
            exit;
            // this license is no longer valid
        }
    }
