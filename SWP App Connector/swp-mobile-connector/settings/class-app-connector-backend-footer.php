<?php
class SWPfooter
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_init', array( $this, 'swp_footer_page_init' ) );
    }

         
    /**
     * Register and add settings
     */
    public function swp_footer_page_init()
    {        
        register_setting(
            'swp-footer-option-group', // Option group
            'swp_app_footer_settings_options', // Option name
            array( $this, 'swp_app_register_footer_settings' ) // Sanitize
        );

        add_settings_section(
            'swp_app_footer_settings_section', // ID
            'Footer Settings', // Title
            array( $this, 'print_footer_settings_section_info' ), // Callback
            'swp-app-footer-settings' // Page
        );
        add_settings_section(
            'swp_app_footer_settings_social_link_section', // ID
            'Footer Social Link Settings', // Title
            array( $this, 'print_footer_settings_social_link_section_info' ), // Callback
            'swp-app-footer-social-link-settings' // Page
        );  

        add_settings_field(
            'swp_footer_settings_details_title', 
            'Title', 
            array( $this, 'swp_footer_settings_details_title_callback' ), 
            'swp-app-footer-settings', 
            'swp_app_footer_settings_section'
        );      
        add_settings_field(
            'swp_footer_settings_address', 
            'Address', 
            array( $this, 'swp_footer_settings_address_callback' ), 
            'swp-app-footer-settings', 
            'swp_app_footer_settings_section'
        );
        add_settings_field(
            'swp_footer_settings_contact', 
            'Contact', 
            array( $this, 'swp_footer_settings_contact_callback' ), 
            'swp-app-footer-settings', 
            'swp_app_footer_settings_section'
        );
        add_settings_field(
            'swp_footer_settings_email', 
            'Email Id', 
            array( $this, 'swp_footer_settings_email_callback' ), 
            'swp-app-footer-settings', 
            'swp_app_footer_settings_section'
        );
        add_settings_field(
            'swp_footer_settings_bottom_section', 
            'Copyright', 
            array( $this, 'swp_footer_settings_bottom_section_callback' ), 
            'swp-app-footer-settings', 
            'swp_app_footer_settings_section'
        );
        //social Links
        add_settings_field(
            'swp_footer_settings_social_link_facebook', 
            'Facebook', 
            array( $this, 'swp_footer_settings_social_link_facebook_callback' ), 
            'swp-app-footer-social-link-settings', 
            'swp_app_footer_settings_social_link_section'
        );
        add_settings_field(
            'swp_footer_settings_social_link_twitter', 
            'Twitter', 
            array( $this, 'swp_footer_settings_social_link_twitter_callback' ), 
            'swp-app-footer-social-link-settings', 
            'swp_app_footer_settings_social_link_section'
        );
        add_settings_field(
            'swp_footer_settings_social_link_instagram', 
            'Instagram', 
            array( $this, 'swp_footer_settings_social_link_instagram_callback' ), 
            'swp-app-footer-social-link-settings', 
            'swp_app_footer_settings_social_link_section'
        );
        add_settings_field(
            'swp_footer_settings_social_link_linkedin', 
            'LinkedIn', 
            array( $this, 'swp_footer_settings_social_link_linkedin_callback' ), 
            'swp-app-footer-social-link-settings', 
            'swp_app_footer_settings_social_link_section'
        ); 
        add_settings_field(
            'swp_footer_settings_social_link_youtube', 
            'Youtube', 
            array( $this, 'swp_footer_settings_social_link_youtube_callback' ), 
            'swp-app-footer-social-link-settings', 
            'swp_app_footer_settings_social_link_section'
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
        
        if( isset( $input['swp_footer_settings_details_title'] ) )
                $new_input['swp_footer_settings_details_title'] = sanitize_text_field( $input['swp_footer_settings_details_title'] );
       
        if( isset( $input['swp_footer_settings_address'] ) )
                $new_input['swp_footer_settings_address'] =  sanitize_text_field( $input['swp_footer_settings_address'] );

        if( isset( $input['swp_footer_settings_contact'] ) )
                $new_input['swp_footer_settings_contact'] = sanitize_text_field( $input['swp_footer_settings_contact'] );

        if( isset( $input['swp_footer_settings_email'] ) )
                $new_input['swp_footer_settings_email'] = sanitize_text_field( $input['swp_footer_settings_email'] );

        if( isset( $input['swp_footer_settings_bottom_section'] ) )
                $new_input['swp_footer_settings_bottom_section'] = sanitize_text_field( $input['swp_footer_settings_bottom_section'] );
        //social Links
        if( isset( $input['swp_footer_settings_social_link_facebook'] ) )
                $new_input['swp_footer_settings_social_link_facebook'] = sanitize_text_field( $input['swp_footer_settings_social_link_facebook'] );

        if( isset( $input['swp_footer_settings_social_link_twitter'] ) )
                $new_input['swp_footer_settings_social_link_twitter'] = sanitize_text_field( $input['swp_footer_settings_social_link_twitter'] );

        if( isset( $input['swp_footer_settings_social_link_instagram'] ) )
                $new_input['swp_footer_settings_social_link_instagram'] = sanitize_text_field( $input['swp_footer_settings_social_link_instagram'] );
       
        if( isset( $input['swp_footer_settings_social_link_linkedin'] ) )
                $new_input['swp_footer_settings_social_link_linkedin'] = sanitize_text_field( $input['swp_footer_settings_social_link_linkedin'] );
       
        if( isset( $input['swp_footer_settings_social_link_youtube'] ) )
                $new_input['swp_footer_settings_social_link_youtube'] = sanitize_text_field( $input['swp_footer_settings_social_link_youtube'] );
       
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_footer_settings_section_info()
    {
        print 'Enter your mobile footer settings:';
    }
    /** 
     * Print the Section text
     */
    public function print_footer_settings_social_link_section_info()
    {
        print 'Enter your mobile footer social link settings:';
    }
    
    //Footer callback function
    public function swp_footer_settings_details_title_callback()
    {
        
        printf(
            '<input type="text" id="swp_footer_settings_details_title" name="swp_app_footer_settings_options[swp_footer_settings_details_title]" value="%s" />',
        isset( $this->options['swp_footer_settings_details_title'] ) ? esc_attr( $this->options['swp_footer_settings_details_title']) : '');
        
    }
    
    public function swp_footer_settings_address_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_address" name="swp_app_footer_settings_options[swp_footer_settings_address]" value="%s" />',
       isset( $this->options['swp_footer_settings_address'] ) ? esc_attr( $this->options['swp_footer_settings_address']) : '');
                
    }
    
    public function swp_footer_settings_contact_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_contact" name="swp_footer_settings_options[swp_footer_settings_address]" value="%s" />',
       isset( $this->options['swp_footer_settings_contact'] ) ? esc_attr( $this->options['swp_footer_settings_contact']) : '');
                
    }

    public function swp_footer_settings_email_callback()
    {
        
       printf(
           '<input type="email" id="swp_footer_settings_email" name="swp_footer_settings_options[swp_footer_settings_email]" value="%s" />',
       isset( $this->options['swp_footer_settings_email'] ) ? esc_attr( $this->options['swp_footer_settings_email']) : '');
                
    }

    public function swp_footer_settings_bottom_section_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_bottom_section" name="swp_footer_settings_options[swp_footer_settings_bottom_section]" value="%s" />',
       isset( $this->options['swp_footer_settings_bottom_section'] ) ? esc_attr( $this->options['swp_footer_settings_bottom_section']) : '');
                
    }

    //social Links
    public function swp_footer_settings_social_link_facebook_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_social_link_facebook" name="swp_footer_settings_options[swp_footer_settings_social_link_facebook]" value="%s" />',
       isset( $this->options['swp_footer_settings_social_link_facebook'] ) ? esc_attr( $this->options['swp_footer_settings_social_link_facebook']) : '');
                
    }
    
    public function swp_footer_settings_social_link_twitter_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_social_link_twitter" name="swp_footer_settings_options[swp_footer_settings_social_link_twitter]" value="%s" />',
       isset( $this->options['swp_footer_settings_social_link_twitter'] ) ? esc_attr( $this->options['swp_footer_settings_social_link_twitter']) : '');
                
    }

    public function swp_footer_settings_social_link_instagram_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_social_link_instagram" name="swp_footer_settings_options[swp_footer_settings_social_link_instagram]" value="%s" />',
       isset( $this->options['swp_footer_settings_social_link_instagram'] ) ? esc_attr( $this->options['swp_footer_settings_social_link_instagram']) : '');
                
    }

    public function swp_footer_settings_social_link_linkedin_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_social_link_linkedin" name="swp_footer_settings_options[swp_footer_settings_social_link_linkedin]" value="%s" />',
       isset( $this->options['swp_footer_settings_social_link_linkedin'] ) ? esc_attr( $this->options['swp_footer_settings_social_link_linkedin']) : '');
                
    }

    public function swp_footer_settings_social_link_youtube_callback()
    {
        
       printf(
           '<input type="text" id="swp_footer_settings_social_link_youtube" name="swp_footer_settings_options[swp_footer_settings_social_link_youtube]" value="%s" />',
       isset( $this->options['swp_footer_settings_social_link_youtube'] ) ? esc_attr( $this->options['swp_footer_settings_social_link_youtube']) : '');
                
    }
}

if( is_admin() )
    $SWPfooter = new SWPfooter();