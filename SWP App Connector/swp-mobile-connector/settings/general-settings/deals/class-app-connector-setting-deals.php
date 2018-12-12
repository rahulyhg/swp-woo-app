<?php
if ( !defined( 'ABSPATH' ) ){
    /***** exit if directly access *****/
    exit;
}

if( !class_exists( 'SWPsettingdeals' ) ){
    class SWPsettingdeals{   
       
        function swp_settings_deals_register_field(){
               register_setting(
                'swp_app_deals_option',
                'swp_app_deals_option',
                array( $this, 'swp_app_register_deal_settings' )
            );

            /* Header Options Section */
            add_settings_section( 
                'swp_app_deal_header',
                'Deals Setting',
               array( $this,'swp_app_deal_header_callback' ),
                'swp_app_deals_option'
            );

            add_settings_field(  
                'swp_app_deals_add_first_image',                      
                'First Image',               
                array( $this, 'swp_app_deals_add_first_image_callback' ),   
                'swp_app_deals_option',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_add_first_image' 
                ) 
            );
            add_settings_field(  
                'swp_app_deals_first_image_link',                      
                'First Image link',               
                array( $this, 'swp_app_deals_first_image_link_callback' ),   
                'swp_app_deals_option',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_first_image_link' 
                ) 
            );
            add_settings_field(  
                'swp_app_deals_add_second_image',                      
                'Second Image',               
                array( $this, 'swp_app_deals_add_second_image_callback' ),   
                'swp_app_deals_option',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_add_second_image' 
                ) 
            );
            add_settings_field(  
                'swp_app_deals_second_image_link',                      
                'Second Image Link',               
                array( $this, 'swp_app_deals_second_image_link_callback' ),   
                'swp_app_deals_option',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_second_image_link' 
                ) 
            );
            
           
            add_settings_section("header_section", "Header Options", array($this, "display_header_options_content"), "theme-options");
            add_settings_field("header_logo", "Logo Url", array($this,"display_logo_form_element"), "theme-options", "header_section");
            register_setting("header_section", "header_logo");
            
            add_settings_field("background_picture", "Picture File Upload", array($this,"background_form_element"), "theme-options", "header_section");
            register_setting("header_section", "background_picture", array($this,"handle_file_upload"));
                    
        }

        /***** Call Backs *****/
        /***** sanitize and then register fields *****/  
        public function swp_app_register_deal_settings( $input )
        {
            $new_input = array();

            if( isset( $input['swp_app_deals_add_first_image'] ) )
                    $new_input['swp_app_deals_add_first_image'] = sanitize_text_field( $input['swp_app_deals_add_first_image'] );

            if( isset( $input['swp_app_deals_first_image_link'] ) )
                    $new_input['swp_app_deals_first_image_link'] = sanitize_text_field( $input['swp_app_deals_first_image_link'] );

            if( isset( $input['swp_app_deals_add_second_image'] ) )
                    $new_input['swp_app_deals_add_second_image'] = sanitize_text_field( $input['swp_app_deals_add_second_image'] );

            if( isset( $input['swp_app_deals_second_image_link'] ) )
                    $new_input['swp_app_deals_second_image_link'] = sanitize_text_field( $input['swp_app_deals_second_image_link'] );

            return $new_input;
        }

        public function swp_app_deal_header_callback() { 
            echo '<p>Deals Detail :</p>'; 
        }


        public function swp_app_deals_add_first_image_callback($args) {

           $options = get_option('swp_app_deals_option'); 

            echo '<input type="file" id="' . $args[0] . '" name="swp_app_deals_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_first_image_link_callback($args) { 

            $options = get_option('swp_app_deals_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_deals_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_add_second_image_callback($args) { 

            $options = get_option('swp_app_deals_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_deals_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_second_image_link_callback($args) { 

            $options = get_option('swp_app_deals_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_deals_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
        }
                
        public function swp_deals_setting_page(){
            settings_fields( 'swp_app_deals_option' );
            do_settings_sections( 'swp_app_deals_option' );
            
            settings_fields("header_section");
            do_settings_sections("theme-options");
        }
        
               
        
        function handle_file_upload($options)
    {
        //check if user had uploaded a file and clicked save changes button
        if(!empty($_FILES["background_picture"]["tmp_name"]))
        {
            $urls = wp_handle_upload($_FILES["background_picture"], array('test_form' => FALSE));
            $temp = $urls["url"];
            return $temp;   
        }

        //no upload. old file url is the new value.
        return get_option("background_picture");
    }


    function display_header_options_content(){echo "The header of the theme";}
    function background_form_element()
    {
        //echo form element for file upload
        ?>
            <input type="file" name="background_picture" id="background_picture" value="<?php echo get_option('background_picture'); ?>" />
            <?php echo get_option("background_picture"); ?>
        <?php
    }
    function display_logo_form_element()
    {
        ?>
            <input type="text" name="header_logo" id="header_logo" value="<?php echo get_option('header_logo'); ?>" />
        <?php
    }
    
                
    }
   
    if( is_admin() )
        $SWPsettingdeals = new SWPsettingdeals();
}