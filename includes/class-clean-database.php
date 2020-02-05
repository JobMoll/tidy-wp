<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
// https://www.techfry.com/php-tutorial/how-to-execute-mysql-query-in-php


// delete data

// delete post and page revisions, transient, trashed and spam comments, cache of feed, 

// krijg de nummers van hoeveel objecten er bijvoorbeeld in de spam zitten: https://designmodo.com/wpdb-object-wordpress/


// cleanup database items
function cleanup_database($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
     if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($_SERVER['LOGGEDIN_USERNAME'], 'e' ), $GLOBALS['$usernameArray']))) {
                global $wpdb;
                
// delete spam comments
  if ($data->get_param('comment-spam') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'"));
  }
 // delete unapproved comments
   if ($data->get_param('comment-unapproved') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->comments WHERE comment_approved = '0'"));
   }
 // delete trash comments
   if ($data->get_param('comment-trash') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'"));
   }
 
 // delete post revisions
   if ($data->get_param('post-revision') == 'true') {
  $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_type = 'revision'"));
   }
 // delete all posts in the trash
   if ($data->get_param('post-trash') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_status = 'trash'"));
   }
 // delete all posts in draft
//   if ($data->get_param('post-draft') == 'true') {
//  $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_status = 'draft'"));
//   }
 
 // delete transients
   if ($data->get_param('transients') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` LIKE ('%\_transient\_%')"));
   }
  // delete site transients
    if ($data->get_param('site-transients') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` LIKE ('_site_transient_%')"));
 
 echo 'Finished'; 
    }
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
  register_rest_route( get_option('tidywp_secret_path'), 'cleanup_database', array(
    'methods' => 'GET',
    'callback' => 'cleanup_database',
  ) );
} );

//  https://tidywp.sparknowmedia.com/wp-json/tidywp/cleanup_database?comment-spam=true&comment-unapproved=true&comment-trash=true&post-revision=true&post-trash=true&post-draft=true&transients=true&site-transients=true&token=123





// show count database items
function show_count_database($data) {
   if (intval(get_option('tidywp_brute_force_check')) <= 3) {
    if (isset($_SERVER['HTTP_TOKEN'])) {
     if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken']) && (in_array(encrypt_and_decrypt($_SERVER['LOGGEDIN_USERNAME'], 'e' ), $GLOBALS['$usernameArray']))) {
        global $wpdb;
// count spam comments
 $spamComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'"));

 // count unapproved comments
 $unapprovedComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'"));
   
 // count trash comments
 $trashComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'"));
   
 
 // count post revisions
  $postRevisions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'"));
   
 // count all posts in the trash
 $postTrash = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'trash'"));
   
 // count all posts in draft
// $postDraft = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'"));
  
 
 // count transients
 $transients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->options WHERE `option_name` LIKE ('%\_transient\_%')"));
   
  // count site transients
 $siteTransients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->options WHERE `option_name` LIKE ('_site_transient_%')"));
   
   $countsArr = array('SpamComment' => $spamComment, 'UnapprovedComment' => $unapprovedComment, 'TrashComment' => $trashComment, 'PostRevisions' => $postRevisions, 'PostTrash' => $postTrash, 'PostDraft' => $postDraft, 'Transients' => $transients, 'SiteTransients' => $siteTransients, );

echo json_encode($countsArr);

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
  register_rest_route( get_option('tidywp_secret_path'), 'show_count_database', array(
    'methods' => 'GET',
    'callback' => 'show_count_database',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/tidywp/show_count_database?token=123

