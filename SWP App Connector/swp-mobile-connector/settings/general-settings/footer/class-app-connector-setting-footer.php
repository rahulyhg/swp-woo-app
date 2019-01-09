<?php

if( !defined( 'ABSPATH' ) ){
    /***** Exit if access directly *****/
}

/**************************************************
* @param footer function
* add and register fields and section
* @return
***************************************************/
if( !class_exists( 'SWPsettingfooter' ) ){
    class SWPsettingfooter
    {   
        function swp_settings_footer_register_field(){
            register_setting(
                'swp_app_footer_option',
                'swp_app_footer_option',
                array( $this, 'swp_app_register_footer_settings' )
            );

            /***** register Social links *****/
            register_setting(
                'swp_app_footer_social_link',
                'swp_app_footer_social_link'
            );

            /****** Header Options Section ******/
            add_settings_section( 
                'swp_app_footer_header',
                'Footer Options',
               array( $this,'swp_app_footer_header_callback' ),
                'swp_app_footer_option'
            );
            /****** Header Options Section ******/
            add_settings_section( 
                'swp_app_footer_social_header',
                'Footer Social Links',
               array( $this,'swp_app_footer_social_header_callback' ),
                'swp_app_footer_social_link'
            );

            add_settings_field(  
                'swp_app_footer_title',                      
                'Footer Title',               
                array( $this, 'swp_app_footer_title_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_title' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_address',                      
                'Footer Address',               
                array( $this, 'swp_app_footer_address_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_address' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_email',                      
                'Email',               
                array( $this, 'swp_app_footer_email_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_email' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_contact',                      
                'Contact',               
                array( $this, 'swp_app_footer_contact_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_contact' 
                ) 
            );

            /***** add social links fields *****/
            add_settings_field(  
                'swp_app_footer_social_facebook',                      
                'Facebook',               
                array( $this, 'swp_app_footer_social_facebook_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_social_facebook' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_social_instagram',                      
                'Instagram',               
                array( $this, 'swp_app_footer_social_instagram_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_social_instagram' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_social_twitter',                      
                'Twitter',               
                array( $this, 'swp_app_footer_social_twitter_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_social_twitter' 
                ) 
            );
            add_settings_field(  
                'swp_app_footer_social_google_plus',                      
                'Google+',               
                array( $this, 'swp_app_footer_social_google_plus_callback' ),   
                'swp_app_footer_option',                     
                'swp_app_footer_header',
                array(
                    'swp_app_footer_social_google_plus' 
                ) 
            );



        }

        /***** Call Backs ******/
        //sanitize and then register fields  
        public function swp_app_register_footer_settings( $input )
        {
            $new_input = array();

            if( isset( $input['swp_app_footer_title'] ) )
                    $new_input['swp_app_footer_title'] = sanitize_text_field( $input['swp_app_footer_title'] );

            if( isset( $input['swp_app_footer_address'] ) )
                    $new_input['swp_app_footer_address'] = sanitize_text_field( $input['swp_app_footer_address'] );

            if( isset( $input['swp_app_footer_email'] ) )
                    $new_input['swp_app_footer_email'] = sanitize_text_field( $input['swp_app_footer_email'] );

            if( isset( $input['swp_app_footer_contact'] ) )
                    $new_input['swp_app_footer_contact'] = sanitize_text_field( $input['swp_app_footer_contact'] );

            if( isset( $input['swp_app_footer_social_facebook'] ) )
                    $new_input['swp_app_footer_social_facebook'] = sanitize_text_field( $input['swp_app_footer_social_facebook'] );

            if( isset( $input['swp_app_footer_social_instagram'] ) )
                    $new_input['swp_app_footer_social_instagram'] = sanitize_text_field( $input['swp_app_footer_social_instagram'] );

            if( isset( $input['swp_app_footer_social_twitter'] ) )
                    $new_input['swp_app_footer_social_twitter'] = sanitize_text_field( $input['swp_app_footer_social_twitter'] );

            if( isset( $input['swp_app_footer_social_google_plus'] ) )
                    $new_input['swp_app_footer_social_google_plus'] = sanitize_text_field( $input['swp_app_footer_social_google_plus'] );

            return $new_input;
        }

        public function swp_app_footer_header_callback() { 
            echo '<p>Enter Your Footer Details :</p>'; 
        }

        public function swp_app_footer_title_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_address_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_email_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_contact_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        /****** Call Backs of social links *****/

        public function swp_app_footer_social_header_callback() { 
            echo '<p>Enter Your Footer Social Links :</p>'; 
        }

        public function swp_app_footer_social_facebook_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_social_instagram_callback($args) { 

            $options = get_option('swp_app_footer_social_link'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_social_twitter_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_footer_social_google_plus_callback($args) { 

            $options = get_option('swp_app_footer_option'); 

            echo '<input type="text" class="tab-content" id="' . $args[0] . '" name="swp_app_footer_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_footer_setting_page(){
            settings_fields( 'swp_app_footer_option' );
            do_settings_sections( 'swp_app_footer_option' );
           
        }

    }
    if( is_admin() )
    $SWPsettingfooter = new SWPsettingfooter();
}