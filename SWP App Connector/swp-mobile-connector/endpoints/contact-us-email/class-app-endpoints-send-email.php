<?php

if( !defined( 'ABSPATH' ) ){
    /***** EXIT if direct accessed file ******/ 
    exit;
}

/************************************************
* @send mail to admin
* add email, message, send headers
* return string
************************************************/

if( !class_exists( 'SWPappsendmail' ) ){
    class SWPappsendmail{
        
        /***** create construct function *****/
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_endpoint_send_email_details' ) );
        }
        
        /***** email details - endpoint *****/   
        function swp_endpoint_send_email_details(){
            
            /***** register email *****/
            register_rest_route( 'swp/v1/contact', '/send-mail',
                array(
                    array(
                        'methods'         => 'POST',
                        'callback'        => array( $this, 'swp_endpoint_send_email_details_callback' ),
                        'args' => array(
                            'email' => array(
                                'required' => true							
                            ),
                            'name' => array(
                                'required' => true							
                            ),
                            'subject' => array(
                                'required' => true							
                            ),
                            'message' => array(
                                'required' => true							
                            )

                        ),
                    )
                ) 
            );
         }
        
       /***** callback function for email details *****/    
       public function swp_endpoint_send_email_details_callback( $request ) {
              
            $parameters = $request->get_params();
            $options  = get_option('swp_app_general_options');	
            $tomail  = $options['swp_general_settings_contact_us'];	
            $email   = sanitize_email($parameters['email']);	
            $sendmail = strip_tags($email);		
            $name    = sanitize_text_field($parameters['name']);
            $subject = sanitize_text_field($parameters['subject']);
            $message = esc_textarea($parameters['message']);
            $sendmes = '<html><body>';
            $sendmes .= "<strong>Name</strong>: " . strip_tags($name) ."<br>";
            $sendmes .= "<strong>Email</strong>: " . strip_tags($email)."<br>";
            $sendmes .= "<strong>Message</strong>: ";
            $sendmes .= "<p>".nl2br($message)."</p>";
            $sendmes .= '</body></html>';
            $headers = "From:$name <$sendmail>" . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .=	'X-Mailer: PHP/' . phpversion();

            if(wp_mail($tomail, $subject.' - '.$email, $sendmes, $headers)){
                return array(
                    'result' => __('success','wooconnector'),
                    'message' => __('Your message has been sent!','wooconnector')
                );
            }
            else{
                return array(
                    'result' => __('fail','wooconnector'),
                    'message' => __('Something went wrong, go back and try again!','wooconnector')
                );
            }
        }	
    }
    
    $SWPappsendmail = new SWPappsendmail();
}