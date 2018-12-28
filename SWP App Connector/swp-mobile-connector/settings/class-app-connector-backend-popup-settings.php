<?php
class SWPpopupsetting
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_init', array( $this, 'swp_popup_settings_page_init' ) );
    }

         
    /**
     * Register and add settings
     */
    public function swp_popup_settings_page_init()
    {        
        register_setting(
            'swp-popup-settings-option-group', // Option group
            'swp_app_popup_settings_options', // Option name
            array( $this, 'swp_app_register_popup_settings' ) // Sanitize
        );

        add_settings_section(
            'swp_app_popup_settings_section', // ID
            'Popup Settings', // Title
            array( $this, 'print_popup_settings_section_info' ), // Callback
            'swp-app-popup-settings' // Page
        );  

        add_settings_field(
            'swp_popup_settings_image', 
            'Slider Images', 
            array( $this, 'swp_popup_settings_image_callback' ), 
            'swp-app-popup-settings', 
            'swp_app_popup_settings_section'
        );      
        add_settings_field(
            'swp_popup_link', 
            'Popup Link', 
            array( $this, 'swp_popup_link_callback' ), 
            'swp-app-popup-settings', 
            'swp_app_popup_settings_section'
        );
            
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function swp_app_register_popup_settings( $input )
    {
        $new_input = array();
        
        if( isset( $input['swp_popup_settings_image'] ) )
                $new_input['swp_popup_settings_image'] = sanitize_file_name( $input['swp_popup_settings_image'] );
       
        if( isset( $input['swp_popup_link'] ) )
                $new_input['swp_popup_link'] =  sanitize_text_field( $input['swp_popup_link'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_popup_settings_section_info()
    {
        print 'Enter your mobile Popup Settings:';
    }

    //About Us
    public function swp_popup_settings_image_callback()
    {
        
        printf(
            '<input type="file" id="swp_popup_settings_image" name="swp_app_popup_settings_options[swp_popup_settings_image]" width="swp_app_popup_settings_options[swp_popup_link]" height="swp_app_popup_settings_options[swp_slider_images_height]" value="%s" />',
        isset( $this->options['swp_popup_settings_image'] ) ? esc_attr( $this->options['swp_popup_settings_image']) : '');
        
    }
    
    public function swp_popup_link_callback()
    {
        
       printf(
           '<input type="text" id="swp_popup_link" name="swp_app_popup_settings_options[swp_popup_link]" value="%s" />',
       isset( $this->options['swp_popup_link'] ) ? esc_attr( $this->options['swp_popup_link']) : '');
                
    }
    
}

if( is_admin() )
    $SWPpopupsetting = new SWPpopupsetting();