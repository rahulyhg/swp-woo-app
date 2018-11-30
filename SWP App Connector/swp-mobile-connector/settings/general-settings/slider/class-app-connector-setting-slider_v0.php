<?php
class SWPsettingslider
{   
    function swp_settings_slider_register_field(){
           register_setting(
            'swp_app_slider_option',
            'swp_app_slider_option'
        );
        
        /* Header Options Section */
        add_settings_section( 
            'swp_app_slider_header',
            'Slider',
           array( $this,'swp_app_slider_header_callback' ),
            'swp_app_slider_option'
        );

        add_settings_field(  
            'swp_app_slider_add_slide',                      
            'Add Slide',               
            array( $this, 'swp_app_slider_add_slide_callback' ),   
            'swp_app_slider_option',                     
            'swp_app_slider_header',
            array(
                'swp_app_slider_add_slide_title' 
            ) 
        );
        add_settings_field(  
            'swp_app_slider_slide_link',                      
            'Slide link',               
            array( $this, 'swp_app_slider_slide_link_callback' ),   
            'swp_app_slider_option',                     
            'swp_app_slider_header',
            array(
                'swp_app_slider_slide_link' 
            ) 
        );
        add_settings_field(  
            'swp_app_slider_width',                      
            'Slider Width (px)',               
            array( $this, 'swp_app_slider_width_callback' ),   
            'swp_app_slider_option',                     
            'swp_app_slider_header',
            array(
                'swp_app_slider_width' 
            ) 
        );
        add_settings_field(  
            'swp_app_slider_height',                      
            'Slider Height (px)',               
            array( $this, 'swp_app_slider_height_callback' ),   
            'swp_app_slider_option',                     
            'swp_app_slider_header',
            array(
                'swp_app_slider_height' 
            ) 
        );
        
        register_setting(
            "header_section",
            "background_picture",
            "handle_file_upload"
        );

        add_settings_field(
            "background_picture",
            "Picture File Upload",
            "background_form_element",
            "theme-options",
            "header_section"
        );
        
    }
    
      /* Call Backs
    -----------------------------------------------------------------*/
       
    
    public function swp_app_slider_header_callback() { 
        echo '<p>Enter Slider Details :</p>'; 
    }
    
        
    public function swp_app_slider_add_slide_callback($args) {
        
       $options = get_option('swp_app_slider_option'); 

        echo '<input type="file" id="' . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }
    
    public function swp_app_slider_slide_link_callback($args) { 

        $options = get_option('swp_app_slider_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }

    public function swp_app_slider_width_callback($args) { 

        $options = get_option('swp_app_slider_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }

    public function swp_app_slider_height_callback($args) { 

        $options = get_option('swp_app_slider_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

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

    function background_form_element()
    {
        //echo form element for file upload
        ?>
            <input type="file" name="background_picture" id="background_picture" value="<?php echo get_option('background_picture'); ?>" />
            <?php echo get_option("background_picture"); ?>
        <?php
    }
    
    public function swp_slider_setting_page(){
        settings_fields( 'swp_app_slider_option' );
        do_settings_sections( 'swp_app_slider_option' );
        settings_fields('header_section');
        do_settings_sections("theme-options");
    }
}
    
if( is_admin() )
    $SWPsettingslider = new SWPsettingslider();