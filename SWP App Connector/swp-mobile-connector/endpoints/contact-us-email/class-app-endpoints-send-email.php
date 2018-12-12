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

if( !class_exists( 'swp_endpoint_send_email' ) ){
    class swp_endpoint_send_email{
        
        /***** create construct function *****/
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_send_email_details' ) );
        }
        
        /***** email details - endpoint *****/   
        function swp_endpoint_send_email_details(){
            
            /***** register email *****/
            register_rest_route(
                'swp/v1/contact',
                '/send-mail',
                array(
                    'methods' => 'POST',
                    'callback' => array( $this, 'swp_endpoint_send_email_details_callback' ),
                )
            );
         }
        
       /***** callback function for email details *****/    
       public function swp_endpoint_send_email_details_callback( $user_id, $notify = '' ) {
            
            global $wpdb;
            $user = get_userdata( $user_id );
            
            /***** The blogname option is escaped with esc_html on the way into the database in sanitize_option *****/
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            
            /***** email send to admin *****/
           
            $message = "testting";
                //$message .= sprintf( __( 'message: %s' ), $user->user_email ) . "\r\n";
            $to = 'nawales@sortedpixel.com';

            wp_mail( $to , sprintf( __( 'user contact from : [%s]' ), $blogname ), $message );
            
            return "Thank you for contact with us";
        }

    }
    
    $swp_endpoint_send_email = new swp_endpoint_send_email();
}