<?php
class SWPslidersetting
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_init', array( $this, 'swp_slider_settings_page_init' ) );
    }

         
    /**
     * Register and add settings
     */
    public function swp_slider_settings_page_init()
    {        
        register_setting(
            'swp-slider-settings-option-group', // Option group
            'swp_app_slider_settings_options', // Option name
            array( $this, 'swp_app_register_slider_settings' ) // Sanitize
        );

        add_settings_section(
            'swp_app_slider_settings_section', // ID
            'Slider Settings', // Title
            array( $this, 'print_slider_settings_section_info' ), // Callback
            'swp-app-slider-settings' // Page
        );  

        add_settings_field(
            'swp_slider_settings_image', 
            'Slider Images', 
            array( $this, 'swp_slider_settings_image_callback' ), 
            'swp-app-slider-settings', 
            'swp_app_slider_settings_section'
        );      
        add_settings_field(
            'swp_slider_images_width', 
            'Image Width', 
            array( $this, 'swp_slider_images_width_callback' ), 
            'swp-app-slider-settings', 
            'swp_app_slider_settings_section'
        );
        add_settings_field(
            'swp_slider_images_height', 
            'Image Height', 
            array( $this, 'swp_slider_images_height_callback' ), 
            'swp-app-slider-settings', 
            'swp_app_slider_settings_section'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function swp_app_register_slider_settings( $input )
    {
        $new_input = array();
        
        if( isset( $input['swp_slider_settings_image'] ) )
                $new_input['swp_slider_settings_image'] = sanitize_file_name( $input['swp_slider_settings_image'] );
       
        if( isset( $input['swp_slider_images_width'] ) )
                $new_input['swp_slider_images_width'] =  sanitize_text_field( $input['swp_slider_images_width'] );

        if( isset( $input['swp_slider_images_height'] ) )
                $new_input['swp_slider_images_height'] = sanitize_text_field( $input['swp_slider_images_height'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_slider_settings_section_info()
    {
        print 'Enter your mobile slider settings:';
    }

    //About Us
    public function swp_slider_settings_image_callback()
    {
        
        printf(
            '<input type="file" id="swp_slider_settings_image" name="swp_app_slider_settings_options[swp_slider_settings_image]" width="swp_app_slider_settings_options[swp_slider_images_width]" height="swp_app_slider_settings_options[swp_slider_images_height]" value="%s" />',
        isset( $this->options['swp_slider_settings_image'] ) ? esc_attr( $this->options['swp_slider_settings_image']) : '');
        
    }
    
    public function swp_slider_images_width_callback()
    {
        
       printf(
           '<input type="text" id="swp_slider_images_width" name="swp_app_slider_settings_options[swp_slider_images_width]" value="%s" />',
       isset( $this->options['swp_slider_images_width'] ) ? esc_attr( $this->options['swp_slider_images_width']) : '');
                
    }
    
    public function swp_slider_images_height_callback()
    {
        
       printf(
           '<input type="text" id="swp_slider_images_height" name="swp_app_slider_settings_options[swp_slider_images_width]" value="%s" />',
       isset( $this->options['swp_slider_images_height'] ) ? esc_attr( $this->options['swp_slider_images_height']) : '');
                
    }
}

if( is_admin() )
    $SWPslidersetting = new SWPslidersetting();