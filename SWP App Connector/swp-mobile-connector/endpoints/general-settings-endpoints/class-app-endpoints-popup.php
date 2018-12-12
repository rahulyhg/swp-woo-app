<?php
if( !defined( 'ABSPATH' ) ){
    /***** EXIT if direct accessed file ******/ 
    exit;
}
/************************************************
*@popup endpoints for popup
* add image, link ,url for popup
*return array
************************************************/

if( !class_exists( 'swp_endpoint_popup' ) ){
    class swp_endpoint_popup{
        
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_popup_add_image' ) );
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_popup_image_link' ) );    
        }
        
        /***** popup Add image - endpoint *****/   
        function swp_endpoint_popup_add_image(){
            
            /***** register slider title *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/popup/add-image/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_popup_add_image_callback' ),
                )
            );
         }
        
        function swp_endpoint_popup_add_image_callback(){
            $options = get_option('swp_app_popup_option');
            return $options['swp_app_popup_add_image'];
        }

        /***** Popup image Link - endpoint *****/    
        function swp_endpoint_popup_image_link(){
            
            /***** register slider image link *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/popup/image-link/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_popup_image_link_callback' ),
                )
            );
         }
        
        function swp_endpoint_popup_image_link_callback(){
            $options = get_option('swp_app_popup_option');
            return $options['swp_app_popup_image_link'];
        }
    }
    $swp_endpoint_popup = new swp_endpoint_popup();
}