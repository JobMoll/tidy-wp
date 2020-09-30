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

function tidy_wp_cleanup_database(WP_REST_Request $request) {

require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';
    
$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
    
  global $wpdb;
                
 // delete spam comments
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = 'spam'", $wpdb->comments));
 // delete unapproved comments
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = '0'", $wpdb->comments));
 // delete trash comments
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_approved = 'trash'", $wpdb->comments));
     
 
 // delete post revisions
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE post_type = 'revision'", $wpdb->posts));
 // delete auto draft
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE post_status = 'auto-draft'", $wpdb->posts));
 // delete all posts in the trash
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE post_status = 'trash'", $wpdb->posts));

 
 // delete all orphaned data
 // Orphaned post meta
  $wpdb->query($wpdb->prepare("DELETE pm FROM %1s pm LEFT JOIN %2s wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL", $wpdb->postmeta, $wpdb->posts));
 // Orphaned relationships
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM %2s)", $wpdb->term_relationships, $wpdb->posts));
 // Orphaned term meta
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE term_id NOT IN (SELECT term_id FROM %2s)", $wpdb->termmeta, $wpdb->terms));
 // Orphaned user meta
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE user_id NOT IN (SELECT ID FROM %2s)", $wpdb->usermeta, $wpdb->users));
 //  Orphaned comment meta
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_id NOT IN (SELECT comment_id FROM %2s)", $wpdb->commentmeta, $wpdb->comments));


 // delete transients
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE `option_name` LIKE ('%\_transient\_%')", $wpdb->options));
 // delete site transients
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE `option_name` LIKE ('_site_transient_%')", $wpdb->options));
 // delete pingbacks
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_type = 'pingback'", $wpdb->comments));
 // delete trackbacks
   $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE comment_type = 'trackback'", $wpdb->comments));
 // delete feed cache
  $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE `option_name` LIKE ('_transient%_feed_%')", $wpdb->options));
     
    
    // clean up weird characters: https://digwp.com/2011/07/clean-up-weird-characters-in-database/
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€œ', '“');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€', '”');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€™', '’');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€˜', '‘');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€”', '–');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€“', '—');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€¢', '-');", $wpdb->posts));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET post_content = REPLACE(post_content, 'â€¦', '…');", $wpdb->posts));

    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€œ', '“');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€', '”');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€™', '’');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€˜', '‘');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€”', '–');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€“', '—');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€¢', '-');", $wpdb->comments));
    $wpdb->query($wpdb->prepare("UPDATE %1s SET comment_content = REPLACE(comment_content, 'â€¦', '…');", $wpdb->comments));
   
   echo 'Finished';
}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'cleanup-database', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_cleanup_database',
    'permission_callback' => '__return_true',
 ));
});

//  https://tidywp.sparknowmedia.com/wp-json/tidywp/cleanup_database?comments=true&posts=true&orphaned=true&remaining=true&other-improvements=true




// show count database items
function tidy_wp_show_count_database(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
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
 $allCommentsCount = $spamComment + $unapprovedComment + $trashComment;
 
 // count post revisions
 $postRevisions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE post_type = 'revision'", $wpdb->posts));
 // count all posts in the trash
 $postTrash = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE post_status = 'trash'", $wpdb->posts));
 // count auto-draft posts
 $postAutoDraft = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE post_status = 'auto-draft'", $wpdb->posts));
 $allPostsCount = $postRevisions + $postTrash + $postAutoDraft;
 
 // Orphaned post meta
 $orphanedPostMeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) pm FROM %1s pm LEFT JOIN %2s wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL", $wpdb->postmeta, $wpdb->posts));
 // Orphaned relationships
 $orphanedRelationships = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM %2s)", $wpdb->term_relationships, $wpdb->posts));
 // Orphaned term meta
 $orphanedTermMeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE term_id NOT IN (SELECT term_id FROM %2s)", $wpdb->termmeta, $wpdb->terms));
 // Orphaned user meta
 $orphanedUserMeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE user_id NOT IN (SELECT ID FROM %2s)", $wpdb->usermeta, $wpdb->users));
 //  Orphaned comment meta
 $orphanedCommentMeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_id NOT IN (SELECT comment_id FROM %2s)", $wpdb->commentmeta, $wpdb->comments));
 $allOrphanedCount = $orphanedPostMeta + $orphanedRelationships + $orphanedTermMeta + $orphanedUserMeta + $orphanedCommentMeta;
 
 // count transients
 $remainingTransients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE `option_name` LIKE ('%\_transient\_%')", $wpdb->options));
 // count site transients
 $remainingSiteTransients = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE `option_name` LIKE ('_site_transient_%')", $wpdb->options));
 // count pingbacks
 $remainingPingback = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_type = 'pingback'", $wpdb->comments));
 // count trackbacks
 $remainingTrackback = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE comment_type = 'trackback'", $wpdb->comments));
 // count feed cache
 $remainingFeedCache = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE `option_name` LIKE ('_transient%_feed_%')", $wpdb->options));
 $allRemainingCount = $remainingTransients + $remainingSiteTransients + $remainingPingback + $remainingTrackback + $remainingFeedCache;

 $allTheCountsArray = array('CertainComments' => $allCommentsCount, 'CertainPP' => $allPostsCount, 'OrphanedData' => $allOrphanedCount, 'RemainingData' => $allRemainingCount);

 echo json_encode($allTheCountsArray);
}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'show-count-database', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_show_count_database',
    'permission_callback' => '__return_true',
 ));
});

// https://tidywp.sparknowmedia.com/wp-json/tidywp/show_count_database?token=123

