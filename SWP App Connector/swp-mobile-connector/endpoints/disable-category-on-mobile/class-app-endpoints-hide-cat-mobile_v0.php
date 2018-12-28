<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Closed if access directly.
}
/**
 * Create Api involve with users
 */
class swp_endpoints_hide_on_mobile{
    
    function __construct(){
        //require_once('/home/ecommerceway/public_html/test/wp-content/plugins/swp-mobile-connector/endpoints/swp-user-access.php');
        add_action( 'rest_api_init', array( $this , 'swp_hide_product_category' ) );    
        add_action( 'rest_api_init', array( $this , 'swp_product_category_thumbnail' ) );    
    }
    
    function swp_hide_product_category(){
        
         $swp_mobile_cat_schema = array(
                'description' => 'Mobile order flag',
                'type' => 'string',
                'context' => array('view','edit'),
                'default' => 'no'
            );
                //register product title
               register_rest_field( 'product_cat', 'swp_cat_hide_on_mobile', array(
                'get_callback'    => array ( $this , 'swp_hide_product_category_callback' ),
                //'update_callback' => array ( $this, 'swp_hide_product_category_update_callback'),
                'schema' => $swp_mobile_cat_schema,
               ));
             }

    // Get post views
    function swp_hide_product_category_callback($post, $field_name, $response) {
       // $category = get_queried_object_id();
        
        return get_term_meta($post['id'], $field_name);
    }
    
    //update category
//    function swp_hide_product_category_update_callback($term){
//        return update_term_meta ;    
//    }
    
    // mobile view product thumbnail
    function swp_product_category_thumbnail(){
                //register product title
               register_rest_field( 'product_cat', 'category-image-id', array(
                'get_callback'    => array ( $this , 'swp_product_category_thumbnail_callback' ),
                //'update_callback' => array ( $this, 'swp_hide_product_category_update_callback'),
                'schema' => null,
               ));
        
                register_rest_field( 'product_cat', 'category-image-url', array(
                'get_callback'    => array ( $this , 'swp_product_category_thumbnail_url_callback' ),
                //'update_callback' => array ( $this, 'swp_hide_product_category_update_callback'),
                'schema' => null,
               ));
             }

    // Get post views
    function swp_product_category_thumbnail_callback($post, $field_name, $response) {
       
        return get_term_meta($post['id'], $field_name);
    }
    
    function swp_product_category_thumbnail_url_callback($post, $field_name, $response) {
       
        return get_term_meta($post['id'], $field_name);
    }
    
}
$swp_endpoints_hide_on_mobile = new swp_endpoints_hide_on_mobile();
