<?php

/*****************************************************
 * Plugin Name: SWP App Connector
 * Plugin URI: https://sortedwp.com
 * Description: Intergrated to Wordpress Rest API
 * Version: 1.0.0
 * Author: SortedWP
 * Author URI: https://sortedwp.com
 * Requires at least: 2.0
 * Tested up to: 4.9.8
 * Compatibility with the REST API v2
 *
 * Text Domain:swp app connector
 * Domain Path: /languages/
 ******************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/********************************************************
 * Main Main App Connector
 * @class of app connector
 *******************************************************/
if(! function_exists( 'SWPApp' ) ){
    final class SWPApp{
        
        /***** Mobile Connector version. *****/
	   public $version = '1.0.0';

        public function __construct(){
            $this->swp_file_path_declare();
            // Define constants.
           define( 'SWP_APP_CONNECTOR_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
            $this->swp_app_general_setting_endpoints();
            $this->define_constants();
        }

        /***** Define App Connector Constants. ******/
		private function define_constants(){
			define( 'SWP_PLUGIN_FILE', __FILE__ );		
			define( 'SWP_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'SWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'SWP_VERSION', $this->version );
            
            define( 'WOOCONNECTOR_PLUGIN_FILE', __FILE__ );		
			define( 'WOOCONNECTOR_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WOOCONNECTOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'WOOCONNECTOR_VERSION', $this->version );
	
		}

        private function swp_file_path_declare(){
            /***** jwt Authetication *****/
            require_once( 'includes/swpauthentication/jwt/class-mobionnector-jwt-core.php' );
            
            /***** add meta column in product order list *****/
            require_once( 'includes/swp-app-includes.php' );
            $SWPappinludes = new SWPappinludes();
            
            /***** backend view *****/
            require_once( 'settings/class-mobiconnector-settings-api.php' );
            require_once( 'settings/class-app-connector-backend-settings.php' );
            require_once( 'settings/form-redirect-to-general-setting.php' );
//            require_once( 'settings/popup/class-app-connector-popup.php' );
           // require_once( 'settings/app-connector-tab-settings.php' );
            
            /***** Include the endpoints class. *****/
            require( plugin_dir_path( __FILE__ ) . 'endpoints/swp-app-connector-endpoints.php');
            }
        
        /***** *****/
        public function swp_define_css_files(){

        }
        
        /***** Main instance of plugin. *****/
        function swp_app_general_setting_endpoints() {
            return swp_app_setting_endpoints::get_instance();
            
            /***** Global for backwards compatibility. *****/
            $GLOBALS['swp_app_general_setting_endpoints'] = swp_app_general_setting_endpoints();

        }
        
     }
    new SWPApp();
      
}
////call main function
//function swp(){
//    return SWPMobile::instance();
//}
////Run Main Class
//swp();

if ( ! class_exists( 'WooCommerce' ) ) {
	if(file_exists(WP_PLUGIN_DIR.'/woocommerce/woocommerce.php')){
		require_once(WP_PLUGIN_DIR.'/woocommerce/woocommerce.php');
	}
}
       /***** Extensions *****/
        $current = get_option('mobiconnector_extensions_active');
        if(!empty($current)){
            if(is_string($current)){
                $current = unserialize($current);
            }
            foreach($current as $file){
                if(file_exists('includes/settings/'.$file) && !is_plugin_active($file)){
                    require_once('includes/settings'.$file);
                }
            }
        }

      
?>