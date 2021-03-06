<?php
class SWPappgeneralsettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct(){
        $this->swp_general_init_hooks();
    }
    

    /**
	 * Hook into actions and filters.
	 */
    public function swp_general_init_hooks(){
        add_action( 'admin_menu',array($this,'swpapp_create_menu'));
        //add_action( 'admin_enqueue_scripts', array( $this, 'swpapp_admin_style' ));
        //add_action( 'wp_ajax_mobiconnector_show_url', array($this,'bamobile_mobiconnector_process_show_url_function' ));
        //require_once('class-app-connector-backend-about-us.php');
        require_once('general-setting/class-app-connector-setting-form.php');
        require_once('about-us/class-app-connector-setting-about-us.php');
        require_once('slider/class-app-connector-setting-slider.php');
        require_once('footer/class-app-connector-setting-footer.php');
        require_once( 'endpoints/swp-app-endpoints-general-setting.php' );
        //require_once( 'class-app-connector-backend-popup-settings.php' );
        //require_once( 'class-app-connector-backend-footer.php' );
             
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
           __('General Settings','appconnector'),
            __('General Settings','appconnector'),
            'manage_options',
            'swp-app-general-setting-options',
            array($this, 'swp_app_general_setting_page')
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
        $SWPsettingform = new SWPsettingform();
            $SWPsettingform->swp_general_settings_register_section_and_add_field();
        $SWPsettingaboutus = new SWPsettingaboutus();
            $SWPsettingaboutus->swp_general_settings_about_us_register_field();
    }

            
    public function swpapp_redirect_to_notification(){
        
    }

    /*** callback funtion from form setting file***/ 
    public function swp_app_general_setting_page() {
        $SWPsettingform = new SWPsettingform();
        $SWPsettingform->swp_setting_form();
           
       }


    public function swpapp_redirect_to_one_signal(){
    }
    
    public function swpapp_redirect_to_product_review(){
    }

   
}
 
if( is_admin() )
    $SWPappgeneralsettings = new SWPappgeneralsettings();