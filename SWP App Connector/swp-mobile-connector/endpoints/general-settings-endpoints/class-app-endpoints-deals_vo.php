<?php
if( !defined( 'ABSPATH' ) ){
    /***** exit if file directly access *****/
    exit;
}

/****************************************************
* @param deals class use for creating two section below the slider
* with the help deals, easily link to image
* return string in array 
* @return array 
****************************************************/

if( !class_exists('swp_endpoint_deals') ){
    
    class swp_endpoint_deals{
        
       public function __construct(){
           add_action( 'rest_api_init', array( $this, 'swp_endpoint_deals_image_with_link' ) ); 
        }
        /***** slider Add slider - endpoint *****/   
        function swp_endpoint_deals_image_with_link(){
            
            /***** register slider title *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/deal',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this,'swp_endpoint_deals_image_with_link_callback' ),
                )
            );
         }

        function swp_endpoint_deals_image_with_link_callback(){
            $options = get_option('swp_app_deals_options');
            $test = get_option('header_section');
            return array(
                'Deals On Mobile' => $options['swp_app_deals_check'],
                'First Image url' => $options['swp_app_deals_add_first_image'],
                'First Image Link' => $options['swp_app_deals_first_image_link'],
                'Second Image url' => $options['swp_app_deals_add_second_image'],
                'Second Image Link' => $options['swp_app_deals_second_image_link']
            );
        }

    }
    
    $swp_endpoint_deals = new swp_endpoint_deals();
}
        