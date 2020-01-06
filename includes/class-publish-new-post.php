<?php
    /**
    * @package tidy-wp
    * @license GPL-3.0+
    * @author Job Moll
    */
    
function publish_new_post($data) {
    // if (isset($_SERVER['HTTP_TOKEN'])) {
    // if (($_SERVER['HTTP_TOKEN'] == $GLOBALS['secretToken'])) {
       
       // get category name and id in json format
       $categories = get_terms( 'category', 'orderby=count&hide_empty=0' );
       echo json_encode($categories)."\n";
       
       
       
       
// https://developer.wordpress.org/reference/functions/wp_insert_post/
$post_array = array(
    'ID' => 0,
    'post_date'     => $data->get_param('post_date'), // format: '2010-02-23 18:57:33' (if post date ia in the future change post status to future)
    'post_title'    => $data->get_param('post_title'), // a string
    'post_content'  => $data->get_param('post_content') , // a string
    'post_status'   => $data->get_param('post_status'), // draft, publish, private, future
    'post_author'   => 1,
    'post_category' => array( $data->get_param('post_title') ) // 7,89 int gescheiden met een komma
);
 
// Insert the post into the database.
wp_insert_post( $post_array );


//     }
//     } else {
//     echo 'Sorry... you are not allowed to view this data.';
// }
}

// add to rest api
add_action( 'rest_api_init', function () {
  register_rest_route( get_option('tidywp_secret_path'), 'publish_new_post', array(
    'methods' => 'GET',
    'callback' => 'publish_new_post',
  ) );
} );

// https://tidywp.sparknowmedia.com/wp-json/MwsojtrJvbdVhWIk/publish_new_post