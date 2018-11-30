<?php
/**
* Main class.
*
* @package Rest_Api_Tutorial
*/
class swp_app_setting_endpoints {
    /**
     * Returns the instance.
     */
    public static function get_instance() {
        static $instance = null;
        if( is_null( $instance ) ) {
            $instance = new self();
        }
        return $instance;
    }
    
    /**
     * Constructor method.
     */
    private function __construct() {
        $this->includes();
    }
    // Includes
    public function includes() {
        require_once('general-settings-endpoints/swp-app-endpoints-general-setting.php');
        require_once('general-settings-endpoints/swp-app-endpoints-about-us.php');
        require_once('general-settings-endpoints/swp-app-endpoints-slider.php');
        require_once('general-settings-endpoints/swp-app-endpoints-popup.php');
        require_once('general-settings-endpoints/swp-app-endpoints-footer.php');
    }
}
//$Rest_Api_Tutorial = new Rest_Api_Tutorial();