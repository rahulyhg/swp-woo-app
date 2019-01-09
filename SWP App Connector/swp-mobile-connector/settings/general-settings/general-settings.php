<?php
if( !defined( 'ABSPATH' ) ){
    /***** EXIT if direct access *****/
    exit;
}

if( !class_exists( 'SWPappgeneralsettings' ) ){
    class SWPappgeneralsettings
    {
        
        /***** Holds the values to be used in the fields callbacks  *****/
        private $options;

        /***** Start up  *****/
        public function __construct(){
            $this->swp_general_init_hooks();
        }

        /***** Hook into actions and filters. *****/
        public function swp_general_init_hooks(){
            add_action( 'admin_menu',array($this,'swpapp_create_menu'));
            require_once('general-setting/class-app-connector-setting-form.php');
            require_once('about-us/class-app-connector-setting-about-us.php');
            require_once('slider/class-app-connector-setting-slider.php');
            require_once('popup/class-app-connector-setting-popup.php');
            require_once('footer/class-app-connector-setting-footer.php');
            require_once( 'deals/class-app-connector-setting-deals.php' );
        }

        /***** Add options page *****/
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
                __('Product Review','appconnector'),
                __('Product Review','appconnector'),
                'manage_options',
                'swpapp-slider',
                array($this,'swpapp_redirect_to_product_review')
            );

            add_action( 'admin_init', array( $this, 'swp_page_init' ) );

        }

        /***** Options page callback *****/
        public function swpapp_redirect_to_app_connector()
        {
        }

        /****** Register and add settings *****/
        public function swp_page_init()
        {            
            $SWPsettingform = new SWPsettingform();
                $SWPsettingform->swp_general_settings_register_section_and_add_field();
            //register aboutus sections and field
            $SWPsettingaboutus = new SWPsettingaboutus();
                $SWPsettingaboutus->swp_general_settings_about_us_register_field();
            //register sections and fields for slider
            $SWPsettingslider = new SWPsettingslider();
                $SWPsettingslider->swp_settings_slider_register_field();
            //register section and fields for Popup
            $SWPsettingpopup = new SWPsettingpopup();
                $SWPsettingpopup->swp_settings_popup_register_field();
            //register section and fields for Popup
            $SWPsettingdeals = new SWPsettingdeals();
                $SWPsettingdeals->swp_settings_deals_register_field();
            //register section and fields for footer
            $SWPsettingfooter = new SWPsettingfooter();
                $SWPsettingfooter->swp_settings_footer_register_field();
            //testing
//            $swp_testing = new swp_testing();
//                $swp_testing->swp_general_settings_testing_register_field();
        }


        /*** callback funtion from form setting file***/ 
        public function swp_app_general_setting_page() {
            $SWPsettingform = new SWPsettingform();
            $SWPsettingform->swp_setting_form();

           }

        public function swpapp_redirect_to_product_review(){
        }


    }

    if( is_admin() )
        $SWPappgeneralsettings = new SWPappgeneralsettings();
}