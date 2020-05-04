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

include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
   global $wpdb;
                
// delete spam comments
  if ($data->get_param('comment-spam') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = 'spam'", $wpdb->comments));
  }
 // delete unapproved comments
   if ($data->get_param('comment-unapproved') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = '0'", $wpdb->comments));
   }
 // delete trash comments
   if ($data->get_param('comment-trash') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = 'trash'", $wpdb->comments));
   }
 
 // delete post revisions
   if ($data->get_param('post-revision') == 'true') {
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE post_type = 'revision'", $wpdb->posts));
   }
 // delete all posts in the trash
   if ($data->get_param('post-trash') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE post_status = 'trash'", $wpdb->posts));
   }
 // delete all posts in draft
//   if ($data->get_param('post-draft') == 'true') {
//  $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_status = 'draft'"));
//   }
 
 // delete transients
   if ($data->get_param('transients') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE `option_name` LIKE ('%\_transient\_%')", $wpdb->options));
   }
  // delete site transients
    if ($data->get_param('site-transients') == 'true') {
 $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE `option_name` LIKE ('_site_transient_%')", $wpdb->options));
 
 echo 'Finished'; 
    }
}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'cleanup_database', array(
    'methods' => 'GET',
    'callback' => 'cleanup_database',
  ) );
} );

//  https://tidywp.sparknowmedia.com/wp-json/tidywp/cleanup_database?comment-spam=true&comment-unapproved=true&comment-trash=true&post-revision=true&post-trash=true&post-draft=true&transients=true&site-transients=true&token=123





// show count database items
function show_count_database($data) {
include str_replace('app', 'plugin', plugin_dir_path(__FILE__)) . 'class-tidy-wp-auth.php';
if (isset($_SERVER['HTTP_TOKEN'])) {
$apiAuthOK = tidyWPAuth($_SERVER['HTTP_TOKEN']);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
 global $wpdb;
// count spam comments
 $spamComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_approved = 'spam'", $wpdb->comments));

 // count unapproved comments
 $unapprovedComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_approved = '0'", $wpdb->comments));
   
 // count trash comments
 $trashComment = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_approved = 'trash'", $wpdb->comments));
   
 
 // count post revisions
  $postRevisions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE post_type = 'revision'", $wpdb->posts));
   
 // count all posts in the trash
 $postTrash = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE post_status = 'trash'", $wpdb->posts));
   
 // count all posts in draft
// $postDraft = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'"));
  
 
 // count transients
 $transients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE `option_name` LIKE ('%\_transient\_%')", $wpdb->options));
   
  // count site transients
 $siteTransients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE `option_name` LIKE ('_site_transient_%')", $wpdb->options));
   
   $countsArr = array('SpamComment' => $spamComment, 'UnapprovedComment' => $unapprovedComment, 'TrashComment' => $trashComment, 'PostRevisions' => $postRevisions, 'PostTrash' => $postTrash, 'Transients' => $transients, 'SiteTransients' => $siteTransients, );

echo json_encode($countsArr);

}
} 

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidy_wp_secret_path'), 'show_count_database', array(
    'methods' => 'GET',
    'callback' => 'show_count_database',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/tidywp/show_count_database?token=123

