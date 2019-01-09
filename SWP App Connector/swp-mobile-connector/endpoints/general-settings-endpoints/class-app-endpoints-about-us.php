<?php
if( !defined( 'ABSPATH' ) ){
    /***** Exit if directly access file *****/
    exit;
}

/******************************************************
* @aboutus endpoints
* create aboutus title, description function
* @return array
*******************************************************/
if(!class_exists( 'swp_endpoint_about_us' ) ){
    class swp_endpoint_about_us{
        
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_about' ) ); 
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_about_us_title' ) ); 
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_about_us_description' ) );    
        }
   
        /***** about us title - endpoint *****/    
        public function swp_endpoint_about(){
            
            /***** register about us title *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/about-us',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_about_us_callback' ),
                )
            );
         }
         
        public function swp_endpoint_about_us_callback(){
            $options = get_option('swp_app_about_us_option');
            return array (
               'About_Title' => $options['swp_app_about_us_title'],
                'About_Description' => $options['swp_app_about_us_description']
                );
        }

        /***** about us title - endpoint *****/    
        public function swp_endpoint_about_us_title(){
            
            /***** register about us title *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/about-us/title/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_about_us_title_callback' ),
                )
            );
         }
        
        public function swp_endpoint_about_us_title_callback(){
            $options = get_option('swp_app_about_us_option');
            return $options['swp_app_about_us_title'];
        }

        /***** about us description - endpoint *****/    
        public function swp_endpoint_about_us_description(){
            
            /***** register about us description *****/
            register_rest_route(
                'swp/v1/general-settings',
                '/about-us/description/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_about_us_description_callback' ),
                )
            );
         }
        
        public function swp_endpoint_about_us_description_callback(){
            $options = get_option('swp_app_about_us_option');
            return $options['swp_app_about_us_description'];
        }
    }
    $swp_endpoint_about_us = new swp_endpoint_about_us();
}
        