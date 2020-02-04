<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
                                          
// true and false statement handler
function backup($data) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) &&          (in_array($_SERVER['LOGGEDIN_USERNAME'], $GLOBALS['$usernameArray']))) {
        
        if ($data->get_param('last-backup') == 'true') {
     
            echo '{"LastBackup":"' . get_option('tidywp_last_backup_date') . '","BackWPupInstalled":"' . in_array('backwpup/backwpup.php', apply_filters('active_plugins', get_option('active_plugins'))) . '"}';
            
        } 
        
        if ($data->get_param('new-backup') == 'true') {

$defaultquantityarray = array (
  1 => 
  array (
  //  'jobid' => (int) get_option('tidywp_BackWPup_key'),
    'jobid' => 1,
    'backuptype' => 'archive',
    'type' => 
    array (
      0 => 'DBCHECK',
      1 => 'DBDUMP',
      2 => 'FILE',
      3 => 'WPEXP',
      4 => 'WPPLUGIN',
    ),
    'destinations' => 
    array (
      0 => 'FOLDER',
    ),
    'name' => 'Tidy WP',
    'mailaddresslog' => get_bloginfo( 'admin_email' ),
    'mailaddresssenderlog' => 'BackWPup Tidy WP <' . get_bloginfo( 'admin_email' ) . '>',
    'mailerroronly' => true,
    'archiveformat' => '.zip',
    'archiveencryption' => false,
    'archivename' => '%d-%m-%Y_%H-%i-%s_%hash%',
    'activetype' => 'link',
    'cronselect' => 'basic',
    'cron' => '0 3 * * *',
    'dbdumpfilecompression' => '',
    'dbdumpfile' => 'tidywp',
    'dbdumpexclude' => 
    array (
    ),
  ),
);  


function callBackupURl() {
    $url = get_bloginfo('wpurl') . '/wp-cron.php?_nonce='. get_option('backwpup_cfg_jobrunauthkey') . '&backwpup_run=runext&jobid=1';// . get_option('tidywp_BackWPup_key');
    file_get_contents($url);
    
            if (get_bloginfo('language') == 'en-US') {
              $dateFormat = 'm-d-Y'; 
            } else {
              $dateFormat = 'd-m-Y'; 
            }
    update_option('tidywp_last_backup_date', date("H:i $dateFormat", strtotime("now")), 'no' );
}
// only update_option on new plugin installations

// it inserts it into the database but the user has to click 'save' first in the admin panel weird
if (count(get_option('backwpup_jobs')) < 10){
  update_option('backwpup_jobs', $defaultquantityarray); 
  callBackupURl();
} else {
    callBackupURl();
    
}
  

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
