<?php
if ( !defined ( 'ABSPATH' )){
    /***** Exit if directly access *****/
    exit;
}
/*************************************
* Main class.
* @package Rest_Api_Tutorial
**************************************/
if( !class_exists( 'swp_app_setting_endpoints' ) ){
    class swp_app_setting_endpoints {
        
        /***** Returns the instance. ****/
        public static function get_instance() {
            static $instance = null;
            if( is_null( $instance ) ) {
                $instance = new self();
            }
            return $instance;
        }

        /***** Constructor method. *****/
        private function __construct() {
            $this->includes();
        }
        
        /***** Includes *****/
        public function includes() {
            require_once('general-settings-endpoints/class-app-endpoints-general-settings.php');
            require_once('general-settings-endpoints/class-app-endpoints-about-us.php');
            require_once('general-settings-endpoints/class-app-endpoints-slider.php');
            require_once('general-settings-endpoints/class-app-endpoints-popup.php');
            require_once('general-settings-endpoints/class-app-endpoints-deals.php');
            require_once('general-settings-endpoints/class-app-endpoints-footer.php');
            
            /***** disable category on mobile *****/
            require_once('disable-category-on-mobile/class-app-endpoints-hide-cat-mobile.php');
            
            /****** send mail from contact page */
            require_once('contact-us-email/class-app-endpoints-send-email.php');
            
            /***** includes user endpoints *****/
            //require_once('user/class-app-endpoints-user.php');
        }
    }
    //$Rest_Api_Tutorial = new Rest_Api_Tutorial();
}