<?php
class SWPappsettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        $this->init_hooks();
    }

    /**
	 * Hook into actions and filters.
	 */
    public function init_hooks(){
        add_action( 'admin_menu',array($this,'swpapp_create_menu'));
        //add_action( 'admin_enqueue_scripts', array( $this, 'swpapp_admin_style' ));
        //add_action( 'wp_ajax_mobiconnector_show_url', array($this,'bamobile_mobiconnector_process_show_url_function' ));
        require_once('class-app-connector-backend-about-us.php');
        require_once( 'class-app-connector-backend-slider-settings.php' );
        require_once( 'class-app-connector-backend-popup-settings.php' );
        require_once( 'class-app-connector-backend-footer.php' );
    }
    /**
     * Add options page
     */
    public function swpapp_create_menu(){
		add_menu_page(
			__('App Connector','appconnector'),
			__('App Connector','appconnector'),
			'manage_options',
			'appconnector-settings',
			array($this,'swpapp_redirect_to_app_connector'),
            '',
            50
        );
        add_submenu_page(
            'appconnector-settings',
            __('General Setting','appconnector'),
            __('General Setting','appconnector'),
            'manage_options',
            'swp-app-settings',
            array($this,'swpapp_redirect_to_general_setting')
        );
        add_submenu_page(
            'appconnector-settings',
            __('Push Notification','appconnector'),
            __('Push Notification','appconnector'),
            'manage_options',
            'swpapp-notification',
            array($this,'swpapp_redirect_to_notification')
        );
        add_submenu_page(
            'appconnector-settings',
            __('One Signal API','appconnector'),
            __('One Signal API','appconnector'),
            'manage_options',
            'swpapp-popup',
            array($this,'swpapp_redirect_to_one_signal')
        );
        add_submenu_page(
            'appconnector-settings',
            __('Product Review','appconnector'),
            __('Product Review','appconnector'),
            'manage_options',
            'swpapp-slider',
            array($this,'swpapp_redirect_to_product_review')
        );
        add_action( 'admin_init', array( $this, 'swp_page_init' ) );
       
    }
    


    /**
     * Options page callback
     */
    public function swpapp_redirect_to_app_connector()
    {
    }

    /**
     * Register and add settings
     */
    public function swp_page_init()
    {        
        register_setting(
            'swp-option-group', // Option group
            'swp_app_options', // Option name
            array( $this, 'swp_app_register_settings' ) // Sanitize
        );

        add_settings_section(
            'swp_app_general_setting_section', // ID
            'General Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'swp-app-general-setting' // Page
        );  

        add_settings_field(
            'swp_product_title_for_buyer', // ID
            'Product Title', // Title 
            array( $this, 'swp_product_title_for_buyer_callback' ), // Callback
            'swp-app-general-setting', // Page
            'swp_app_general_setting_section' // Section           
        );      

        add_settings_field(
            'swp_buyer_product_description', 
            'Description of Buyer Product', 
            array( $this, 'swp_buyer_product_description_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        ); 
       add_settings_field(
            'swp_terms_of_use_title', 
            'Terms Of Use (Title)', 
            array( $this, 'swp_terms_of_use_title_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        );
        add_settings_field(
            'swp_terms_of_use_description', 
            'Terms Of Use (Description)', 
            array( $this, 'swp_terms_of_use_description_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        );
        add_settings_field(
            'swp_privacy_policy_title', 
            'Privacy Policy (Title)', 
            array( $this, 'swp_privacy_policy_title_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        );
        add_settings_field(
            'swp_privacy_policy_description', 
            'Privacy Policy (Description)', 
            array( $this, 'swp_privacy_policy_description_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        ); 
        add_settings_field(
            'swp_privacy_policy_description', 
            'Privacy Policy (Description)', 
            array( $this, 'swp_privacy_policy_description_callback' ), 
            'swp-app-general-setting', 
            'swp_app_general_setting_section'
        ); 
        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function swp_app_register_settings( $input )
    {
        $new_input = array();
        if( isset( $input['swp_product_title_for_buyer'] ) )
            $new_input['swp_product_title_for_buyer'] = sanitize_text_field( $input['swp_product_title_for_buyer'] );

    //    if( isset( $input['swp_buyer_product_description'] ) )
      //      $new_input['swp_buyer_product_description'] =  $swp_buyer_product_description ;
     
        if( isset( $input['swp_terms_of_use_title'] ) )
                $new_input['swp_terms_of_use_title'] = sanitize_text_field( $input['swp_terms_of_use_title'] );
        
        if( isset( $input['swp_terms_of_use_description'] ) )
                $new_input['swp_terms_of_use_description'] = sanitize_text_field( $input['swp_terms_of_use_description'] );

        if( isset( $input['swp_privacy_policy_title'] ) )
                $new_input['swp_privacy_policy_title'] = sanitize_text_field( $input['swp_privacy_policy_title'] );

        if( isset( $input['swp_privacy_policy_description'] ) )
                $new_input['swp_privacy_policy_description'] = sanitize_text_field( $input['swp_privacy_policy_description'] );
        //About us
        if( isset( $input['swp_about_us_title'] ) )
                $new_input['swp_about_us_title'] = sanitize_text_field( $input['swp_about_us_title'] );
       
        if( isset( $input['swp_about_us_description'] ) )
                $new_input['swp_about_us_description'] = sanitize_text_field( $input['swp_about_us_description'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your mobile app settings:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function swp_product_title_for_buyer_callback()
    {
        printf(
            '<input type="text" id="swp_product_title_for_buyer" name="swp_app_options[swp_product_title_for_buyer]" value="%s" />',
            isset( $this->options['swp_product_title_for_buyer'] ) ? esc_attr( $this->options['swp_product_title_for_buyer']) : ''
        );
    }
    

    /** 
     * Get the settings option array and print one of its values
     */
    public function swp_buyer_product_description_callback()
    {
        
        $settings = array(
        'tinymce' => false,
        'textarea_rows' => 10,
        'tabindex' => 1,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 6
        );
        //$content = $_POST['swp_buyer_product_description'];
        wp_editor('', 'swp_buyer_product_description', $settings);
                  
    }
    /** 
     * Get the settings option array and print one of terms of use title
     */
    public function swp_terms_of_use_title_callback()
    {
        
        printf(
            '<input type="text" id="swp_terms_of_use_title" rows="20" cols="10" name="swp_app_options[swp_terms_of_use_title]" value="%s" />',
        isset( $this->options['swp_terms_of_use_title'] ) ? esc_attr( $this->options['swp_terms_of_use_title']) : '');
        
    }
   
    public function swp_terms_of_use_description_callback()
    {
        
//        printf(
//            '<input type="text" id="swp_terms_of_use_description" rows="20" cols="10" name="swp_app_options[swp_terms_of_use_description]" value="%s" />',
//        isset( $this->options['swp_terms_of_use_description'] ) ? esc_attr( $this->options['swp_terms_of_use_description']) : '');
     $settings = array(
        'tinymce' => false,
        'textarea_rows' => 10,
        'tabindex' => 1,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 6
        );
        //$content = $_POST['swp_buyer_product_description'];
        wp_editor('', 'swp_buyer_product_description', $settings);
   
    }
    public function swp_privacy_policy_title_callback()
    {
        
        printf(
            '<input type="text" id="swp_privacy_policy_title" rows="20" cols="10" name="swp_app_options[swp_privacy_policy_title]" value="%s" />',
        isset( $this->options['swp_privacy_policy_title'] ) ? esc_attr( $this->options['swp_privacy_policy_title']) : '');
        
    }
    
    public function swp_privacy_policy_description_callback()
    {
        
//        printf(
//            '<input type="text" id="swp_privacy_policy_description" rows="20" cols="10" name="swp_app_options[swp_privacy_policy_description]" value="%s" />',
//        isset( $this->options['swp_privacy_policy_description'] ) ? esc_attr( $this->options['swp_privacy_policy_description']) : '');
        $settings = array(
        'tinymce' => false,
        'textarea_rows' => 10,
        'tabindex' => 1,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 6
        );
        //$content = $_POST['swp_buyer_product_description'];
        wp_editor('', 'swp_privacy_policy_description', $settings);
        
    }
    
    public function swpapp_redirect_to_general_setting(){
      // Set class property
        ?>
         <?php
                if( isset( $_GET[ 'tab' ] ) ) {
                    $active_tab = $_GET[ 'tab' ];
                } // end if
                ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=swp-app-settings&tab=swp_app_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_settings_tab' ? 'nav-tab-active' : ''; ?>">General Setting</a>
            <a href="?page=swp-app-settings&tab=swp_app_about_us_tab" class="nav-tab <?php echo $active_tab == 'swp_app_about_us_tab' ? 'nav-tab-active' : ''; ?>">About Us</a>
            <a href="?page=swp-app-settings&tab=swp_app_slider_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_slider_settings_tab' ? 'nav-tab-active' : ''; ?>">Slider</a>
            <a href="?page=swp-app-settings&tab=swp_app_popup_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_popup_settings_tab' ? 'nav-tab-active' : ''; ?>">Popup</a>
            <a href="?page=swp-app-settings&tab=swp_app_footer_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_footer_settings_tab' ? 'nav-tab-active' : ''; ?>">Footer</a>
        </h2>
        <?php settings_errors() ?>

        <?php if(isset($_POST['swp_buyer_product_description'])){
            update_option('swp_buyer_product_description',$_POST['swp_buyer_product_description']); 
        }
        $this->options = get_option( 'swp_app_options' );
        
        //$SWPaboutus->optionss = get_option('swp_app_about_us_options');
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php $active_tab = $_GET[ 'tab' ]; ?>
                <?php if ( $active_tab == 'swp_app_settings_tab'  ){ ?>
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'swp-option-group' );
                    do_settings_sections( 'swp-app-general-setting' );
                    submit_button();
                } elseif( $active_tab == 'swp_app_about_us_tab' ){
                   //$this->optionss = get_option( 'swp_app_about_us_options' );
                    // This prints out all hidden setting fields
                    $SWPaboutus = new SWPaboutus();
                    $SWPaboutus->swp_setting_fields();

                }elseif( $active_tab == 'swp_app_slider_settings_tab' ){
                    // This prints out all hidden setting fields
                    settings_fields( 'swp-slider-settings-option-group' );
                    do_settings_sections( 'swp-app-slider-settings' );
                    submit_button();

                }elseif( $active_tab == 'swp_app_popup_settings_tab' ){
                    // This prints out all hidden setting fields
                    settings_fields( 'swp-popup-settings-option-group' );
                    do_settings_sections( 'swp-app-popup-settings' );
                    submit_button();

                }elseif( $active_tab == 'swp_app_footer_settings_tab' ){
                    // This prints out all hidden setting fields
                    settings_fields( 'swp-footer-option-group' );
                    do_settings_sections( 'swp-app-footer-settings' );
                    do_settings_sections( 'swp-app-footer-social-link-settings' );
                    submit_button();

                }else{
                    settings_fields( 'swp-option-group' );
                    do_settings_sections( 'swp-app-general-setting' );
                    submit_button();
                }
                ?>
              </form>
        </div>
        <?php
    }
//    public function swpapp_redirect_to_notification(){}
//    public function swpapp_redirect_to_one_signal(){}
//    public function swpapp_redirect_to_product_review(){}
}

if( is_admin() )
    $SWPappsettings = new SWPappsettings();