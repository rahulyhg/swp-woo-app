<?php

class SWPsettingpopup
{   
    function swp_settings_popup_register_field(){
        
        register_setting(
            'swp_app_popup_option',
            'swp_app_popup_option'
            //array ( $this, 'swp_app_register_popup_settings' ) // Sanitize
        );
        
        register_setting(
            "swp_app_image_upload", 
            "background_picture",
            "handle_file_upload"
        );

        /* Header Options Section */
        add_settings_section( 
            'swp_app_popup_header',
            'Popup Setting',
           array( $this,'swp_app_popup_header_callback' ),
            'swp_app_popup_option'
        );
        
        add_settings_field(
            "background_picture",
            "Add Image",
            array( $this, "background_form_element_callback" ),
            "swp_app_popup_option", 
            "swp_app_popup_header"
        );
        
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
    
      /* Call Backs
    -----------------------------------------------------------------*/
       
    public function swp_app_register_popup_settings( $input )
    {
        
    }
    
    public function swp_app_popup_header_callback() { 
        echo '<p>Enter Popup Details :</p>'; 
    }

    public function swp_app_popup_link_callback() { 
        
    ?>
        <input type="text" id="swp_app_popup_link" name="swp_app_popup_link" value="<?php echo get_option('swp_app_popup_link'); ?>" />
    <?php
    }

    public function swp_app_popup_image_link_callback($args) { 

        $options = get_option('swp_app_popup_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_popup_option[' . $args[0] . ']" value="' . $options[ ''.$args[0].'' ] . '"></input>';

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

    function background_form_element_callback()
    {
        //echo form element for file upload
        ?>
            <input type="file" name="background_picture" id="background_picture" value="<?php echo get_option('background_picture'); ?>" />
            <?php echo get_option("background_picture"); ?>
        <?php
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