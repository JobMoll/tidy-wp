<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// true and false statement handler
function backup($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
    if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
        
        if ($data->get_param('last-backup') == 'true') {
        
            echo '{"LastBackup":"' . get_option('tidywp_last_backup_date') . '","BackWPupInstalled":"' . in_array('backwpup/backwpup.php', apply_filters('active_plugins', get_option('active_plugins'))) . '"}';
            
        } 
        
        if ($data->get_param('new-backup') == 'true') {

            if (get_bloginfo('language') == 'en-US') {
              $dateFormat = 'm-d-Y'; 
            } else {
              $dateFormat = 'd-m-Y'; 
            }
            
    update_option('tidywp_last_backup_date', date("H:i $dateFormat", strtotime("now")), 'no' );
    
    $url = get_bloginfo('wpurl') . '/wp-cron.php?_nonce='. get_option('backwpup_cfg_jobrunauthkey') . '&backwpup_run=runext&jobid=' . '1';
 
    file_get_contents($url);
        }
        
        
    }
    } else {
    echo 'Sorry... you are not allowed to view this data.';
}
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'backup', array(
    'methods' => 'GET',
    'callback' => 'backup',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/backup?last-backup=true
// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/backup?new-backup=true
