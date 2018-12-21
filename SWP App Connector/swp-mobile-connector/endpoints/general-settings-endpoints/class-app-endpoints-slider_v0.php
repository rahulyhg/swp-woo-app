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

if( !class_exists('swp_endpoint_slider') ){
    class swp_endpoint_slider{
        
        /***** create construct function *****/
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_slider_add_slide' ) ); 
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_slider_slide_link' ) ); 
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_slider_width' ) ); 
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_slider_height' ) );    
        }
        
        /***** slider Add slider - endpoint *****/    
        function swp_endpoint_slider_add_slide(){
            //register slider title
            register_rest_route(
                'swp/v1/general-settings',
                '/slider/add-slide/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_slider_add_slide_callback' ),
                )
            );
         }
        
        function swp_endpoint_slider_add_slide_callback(){
            $options = get_option('swp_app_slider_option');
            return $options['swp_app_slider_add_slide'];
        }

        /***** Slider slide Link - endpoint *****/    
        function swp_endpoint_slider_slide_link(){
            //register slider title
            register_rest_route(
                'swp/v1/general-settings',
                '/slider/slide-link/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_slider_slide_link_callback' ),
                )
            );
         }
        
        function swp_endpoint_slider_slide_link_callback(){
            $options = get_option('swp_app_slider_option');
            return $options['swp_app_slider_slide_link'];
        }

        /***** Slider slide Link - endpoint *****/    
        function swp_endpoint_slider_width(){
            //register slider title
            register_rest_route(
                'swp/v1/general-settings',
                '/slider/width/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_slider_width_callback' ),
                )
            );
         }
        
        function swp_endpoint_slider_width_callback(){
            $options = get_option('swp_app_slider_option');
            return $options['swp_app_slider_width'];
        }

        /***** Slider slide Height - endpoint *****/    
        function swp_endpoint_slider_height(){
            //register slider title
            register_rest_route(
                'swp/v1/general-settings',
                '/slider/height/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_slider_height_callback' ),
                )
            );
         }
        
        function swp_endpoint_slider_height_callback(){
            $options = get_option('swp_app_slider_option');
            return $options['swp_app_slider_heights'];
        }
    }
    $swp_endpoint_slider = new swp_endpoint_slider();
}