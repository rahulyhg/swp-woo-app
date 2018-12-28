<?php
if( !defined( 'ABSPATH' )){
    /****** EXITE if file directly access ******/
    exit;
} 
/******************************************************
* @param create slider endpoints
* return array
*
*******************************************************/

if( !class_exists('swp_app_endpoint_deals') ){
    class swp_app_endpoint_deals{
        
        private $endpoint_url = 'swp/v1/product';

        /***** create construct function *****/
        public function __construct(){
             add_action( 'rest_api_init', array( $this, 'swp_app_register_deals_api'));
         }
        
        /***** slider Add slider - endpoint *****/    
        
        public function swp_app_register_deals_api() {
            register_rest_route( $this->endpoint_url, '/deals', array(
                        'methods'         => 'GET',
                        'callback'        => array( $this, 'swp_app_get_deals' ),
                       // 'permission_callback' => array( $this, 'get_items_permissions_check' ),	
                        'args'            => array(
                            'post_per_page' => array(
                                'default' => 10,
                                'sanitize_callback' => 'absint',
                            ),
                            'post_num_page' => array(
                                'default' => 1,
                                'sanitize_callback' => 'absint',
                            ),
                        ),					
                ) 
            );
        }
        
        public function swp_app_get_deals($request){	
            $parameters = $request->get_params();
            $post_per_page = $parameters['post_per_page'];
            $post_num_page = $parameters['post_num_page'];
            $args = array(
                'posts_per_page'   => $post_per_page,
                'page'             => $post_num_page,
                'offset'           => 0,
                'category'         => '',
                'category_name'    => '',
                'orderby'          => 'menu_order',
                'order'            => 'ASC',			
                'post_type'        => 'app_deals',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'	       => '',
                'author_name'	   => '',
                'post_status'      => 'publish',
                'suppress_filters' => true 
            );
            $sliders = get_posts($args);
            $wp_upload_dir = wp_upload_dir();	
            $listimages = array();
            $home = get_home_url();	
            foreach($sliders as $slider){
                $imageid = get_post_thumbnail_id($slider->ID);		
                $oldurl = get_post_meta($slider->ID, 'app_deals_link', true);	
                $oldurl = apply_filters('post_title',trim($oldurl,'/'));
                if(!empty( $imageid ) ) {		
                    $post_id = url_to_postid($oldurl);	
                    $newurl =  str_replace($oldurl, $post_id );							
                        $listimages[] = array(
                            'slider_images' => $wp_upload_dir['baseurl']."/". get_post_meta($imageid, 'swp_app_deals_large', true),
                            'url' => $oldurl
                        );						
                    }
            }			
            return $listimages;
        }
        
    }
    $swp_app_endpoint_deals = new swp_app_endpoint_deals();
}