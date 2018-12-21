<?php

if( !defined( 'ABSPATH' ) ){
    /***** EXIT if access directly *****/
    exit;
}

/*************************************************************
* @param popup function
* add and register fields and section
* @return
*************************************************************/
if( !class_exists( 'SWPsettingpopup' ) ){
    class SWPsettingpopup{
        
        /***** register sections and fields *****/
        function swp_settings_popup_register_field(){

            register_setting(
                'swp_app_popup_option',
                'swp_app_popup_option',
                array( $this, 'swp_app_register_popup_settings' ) // Sanitize
            );

    //        register_setting(
    //            "swp_app_image_upload", 
    //            "background_picture",
    //            "handle_file_upload"
    //        );

            /* Header Options Section */
            add_settings_section( 
                'swp_app_popup_header',
                'Popup Setting',
               array( $this,'swp_app_popup_header_callback' ),
                'swp_app_popup_option'
            );

    //        add_settings_field(
    //            "background_picture",
    //            "Add Image",
    //            array( $this, "background_form_element_callback" ),
    //            "swp_app_popup_option", 
    //            "swp_app_popup_header"
    //        );
    //        
            add_settings_field(  
                'swp_app_popup_image_link',                      
                'Slide link',               
                array( $this, 'swp_app_popup_image_link_callback' ),   
                'swp_app_popup_option',                     
                'swp_app_popup_header',
                array(
                    'swp_app_popup_image_link' 
                ) 
            );
            add_settings_field(
                'swp_app_popup_link',
                'Popup Link',
                array( $this, 'swp_app_popup_link_callback' ),
                'swp_app_popup_option',
                'swp_app_popup_header',
                array(
                    'swp_app_popup_link'
                )
            );
            register_setting('swp_app_popup_link','swp_app_popup_link','swp_app_popup_link_callback');            
        }

        /***** Call Backs ******/

        public function swp_app_register_popup_settings( $input ){

            $new_input = array();

            if( isset( $input['swp_app_popup_image_link'] ) )
                    $new_input['swp_app_popup_image_link'] = sanitize_text_field( $input['swp_app_popup_image_link'] );

            if( isset( $input['swp_app_popup_link'] ) )
                    $new_input['swp_app_popup_link'] = sanitize_text_field( $input['swp_app_popup_link'] );

            if( isset( $input['background_picture'] ) )
                    $new_input['background_picture'] = sanitize_text_field( $input['background_picture'] );

            return $new_input;   
        }

        public function swp_app_popup_header_callback() { 
            echo '<p>Enter Popup Details :</p>'; 
        }

        public function swp_app_popup_link_callback() { 

        }

        public function swp_app_popup_image_link_callback($args) { 

            $options = get_option('swp_app_popup_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_popup_option[' . $args[0] . ']" value="' . $options[ ''.$args[0].'' ] . '"></input>';

        }

         public function swp_popup_setting_page()
         {
            settings_fields( 'swp_app_popup_option' );
            do_settings_sections( 'swp_app_popup_option' );
            settings_fields('swp_app_image_upload');
            //do_settings_sections('theme-options');
        }
    }
    if( is_admin() )
    $SWPsettingpopup = new SWPsettingpopup();
}