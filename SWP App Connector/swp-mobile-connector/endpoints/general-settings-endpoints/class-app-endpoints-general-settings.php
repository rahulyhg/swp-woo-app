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
if(!class_exists( 'swp_endpoint_general_settings' ) ){
    class swp_endpoint_general_settings{
        
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_settings' ) ); 
        }
   
        /***** about us title - endpoint *****/    
        public function swp_endpoint_settings(){
            
            /***** register about us title *****/
            register_rest_route(
                'swp/v1',
                '/general-settings',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_endpoint_general_settings_callback' ),
                )
            );
            
         }
         
        public function swp_endpoint_general_settings_callback(){
            $options = get_option('swp_app_general_options'); 
            return array (
               'Google_Play_Store' => $options['swp_general_settings_google_play_store_review'],
               'Itunes_Play_Store' => $options['swp_general_settings_itune_play_store_review'],
               'Privacy_Policy_Title' => $options['swp_privacy_policy_title'],
               'Privacy_Policy_Description' => $options['swp_privacy_policy_description'],
            );
        }
        
    }
    $swp_endpoint_general_settings = new swp_endpoint_general_settings();
}
        