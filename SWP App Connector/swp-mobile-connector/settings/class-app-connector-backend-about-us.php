<?php
class SWPaboutus
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $optionss;

    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_init', array( $this, 'swp_about_us_page_init' ) );
       // include( 'class-app-connector-backend-settings.php' );
    }

         
    /**
     * Register and add settings
     */
    public function swp_about_us_page_init()
    {        
        register_setting(
            'swp-about-us-option-group', // Option group
            'swp_app_about_us_options', // Option name
            array( $this, 'swp_app_register_about_us_settings' ) // Sanitize
        );

        add_settings_section(
            'swp_app_about_us_section', // ID
            'About Us', // Title
            array( $this, 'print_about_us_section_info' ), // Callback
            'swp-app-about-us-setting' // Page
        );  

        add_settings_field(
            'swp_about_us_title', 
            'About Us (Title)', 
            array( $this, 'swp_about_us_title_callback' ), 
            'swp-app-about-us-setting', 
            'swp_app_about_us_section'
        );      
        add_settings_field(
            'swp_about_us_description', 
            'About Us (Description)', 
            array( $this, 'swp_about_us_description_callback' ), 
            'swp-app-about-us-setting', 
            'swp_app_about_us_section'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function swp_app_register_about_us_settings( $input )
    {
        $new_input = array();
        
        if( isset( $input['swp_about_us_title'] ) )
                $new_input['swp_about_us_title'] = wp_nonce_field( sanitize_text_field( $input['swp_about_us_title']) );
       
        if( isset( $input['swp_about_us_description'] ) )
                $new_input['swp_about_us_description'] = sanitize_text_field( $input['swp_about_us_description'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_about_us_section_info()
    {
        print 'Enter your mobile About Us:';
    }

    //About Us
    public function swp_about_us_title_callback()
    {
        printf(
            '<input type="text" id="swp_about_us_title" name="swp_app_about_us_options[swp_about_us_title]" value"%s" />',
        isset( $this->optionss['swp_about_us_title'] ) ? esc_attr( $this->optionss['swp_about_us_title']) : '');
        
        
    }
    
    public function swp_about_us_description_callback()
    {
        
//        printf(
//            '<input type="text" id="swp_about_us_description" name="swp_app_about_us_options[swp_about_us_description]" value="%s" />',
//        isset( $this->options['swp_about_us_description'] ) ? esc_attr( $this->options['swp_about_us_description']) : '');
       
        $settings = array(
        'tinymce' => false,
        'textarea_rows' => 10,
        'tabindex' => 1,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 6
        );
        //$content = $_POST['swp_buyer_product_description'];
        wp_editor('', 'swp_about_us_description', $settings);
        
    }
    
    public function  swp_setting_fields(){
        $this->optionss = get_option('swp_app_about_us_options');
        settings_fields( 'swp-about-us-option-group' );
        do_settings_sections( 'swp-app-about-us-setting' );
        submit_button('save');
    }
}

if( is_admin() )
    $SWPaboutus = new SWPaboutus();