<?php
if( !defined( 'ABSPATH' ) ){
    /***** Exit if directly access *****/
    exit;   
}

/***************************************************
* @general setting function add privacy policy
* term of user, product title
* @return array
***************************************************/
if( !class_exists( 'swp_endpoint_general_setting' ) ){
    class swp_endpoint_general_setting{
        
        /****** create construct *******/
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_general_setting_register' ) );    
        }
        /****** register general setting endpoints ******/
        function swp_endpoint_general_setting_register(){
            
            //register product title
            register_rest_route(
                'swp/v1/general-settings',
                '/product-title/',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_general_setting_product_title_callback' ),
                )
            );
         }
        
        function swp_general_setting_product_title_callback(){
            $options = get_option('swp_app_general_options');
            return $options['swp_product_title_for_buyers'];
             
            
        }
    }
    $swp_endpoint_general_setting = new swp_endpoint_general_setting();
}