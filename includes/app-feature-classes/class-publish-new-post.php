<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
          

       
// get categorie names and id's

// $categorieNames = []; 
// $categorieIDS = []; 
// $category_list_items = get_terms('category', 'orderby=count&hide_empty=0');

// foreach($category_list_items as $category_list_item){
//     if(! empty($category_list_item->name)){
//      $category_list_item->name;
//      $category_list_item->term_id;
//         array_push($categorieNames, $category_list_item->name . $category_list_item->term_id);
//     }
// }

// foreach($category_list_items as $category_list_item){
//     if(! empty($category_list_item->term_id)){
//      $category_list_item->term_id;
//         array_push($categorieIDS, $category_list_item->term_id);
//     }
// }

// echo json_encode($categorieIDS);
// echo json_encode($categorieNames);
       
function tidy_wp_publish_new_post(WP_REST_Request $request) {
require_once TIDY_WP_PLUGIN_DIR . 'includes/plugin-feature-classes/class-tidy-wp-auth.php';

$secretAPIKey = sanitize_text_field($request['secretAPIKey']);
   
if (isset($secretAPIKey)) {
$apiAuthOK = tidy_wp_auth($secretAPIKey);
} else { 
$apiAuthOK = false;
echo 'Sorry... you are not allowed to view this data.';
}
if ($apiAuthOK == true) {
       
       // get category name and id in json format
       $categories = get_terms('category', 'orderby=count&hide_empty=0');
       echo json_encode($categories)."\n";
       
       
        
// https://developer.wordpress.org/reference/functions/wp_insert_post/
$post_array = array(
    'ID' => 0,
    'post_date'     => sanitize_text_field($request['post_date']), // format: '2010-02-23 18:57:33' (if post date ia in the future change post status to future)
    'post_title'    => sanitize_text_field($request['post_title']), // a string
    'post_content'  => sanitize_text_field($request['post_content']) , // a string
    'post_status'   => sanitize_text_field($request['post_status']), // draft, publish, private, future
    'post_author'   => 1,
    'post_category' => array(sanitize_text_field($request['post_categories'])) // 7,89 int gescheiden met een komma
);
 
// Insert the post into the database.
wp_insert_post($post_array);


}
} 

// add to rest api
add_action('rest_api_init', function () {
  register_rest_route(get_option('tidy_wp_secret_path'), 'publish-new-post', array(
    'methods' => 'POST',
    'callback' => 'tidy_wp_publish_new_post',
    'permission_callback' => '__return_true',
 ));
});

// https://tidywp.sparknowmedia.com/wp-json/MkmU2WcL8vhD2U7N/publish_new_post