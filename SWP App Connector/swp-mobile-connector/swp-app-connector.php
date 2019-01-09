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
            
            add_action( 'admin_enqueue_scripts', array( $this , 'swp_define_css_files' ));
            
        }

        /***** Define App Connector Constants. ******/
		private function define_constants(){
			define( 'SWP_PLUGIN_FILE', __FILE__ );		
			define( 'SWP_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'SWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'SWP_VERSION', $this->version );
            
            define( 'WOOCONNECTOR_PLUGIN_FILE', __FILE__ );		
			define( 'WOOCONNECTOR_ABSPATH', dirname( __FILE__ ) . '/' );
			define( 'WOOCONNECTOR_VERSION', $this->version );
	        define( 'WOOCONNECTOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			
		}

        private function swp_file_path_declare(){
            
            /***** add meta column in product order list *****/
            require_once( 'includes/swp-app-includes.php' );
            $SWPappinludes = new SWPappinludes();
            
            /***** backend view *****/
//            require_once( 'settings/class-mobiconnector-settings-api.php' );
            require_once( 'settings/class-app-connector-backend-settings.php' );
//            require_once( 'settings/form-redirect-to-general-setting.php' );
//          require_once( 'settings/popup/class-app-connector-popup.php' );
           // require_once( 'settings/app-connector-tab-settings.php' );
            
            /***** Include the endpoints class. *****/
            require( plugin_dir_path( __FILE__ ) . 'endpoints/swp-app-connector-endpoints.php');
            
            /***** include hooks *****/
            require_once('hooks/wooconnector-core.php');
            require_once('hooks/pushnotification.php');
        }
        
        /***** *****/
        public function swp_define_css_files(){
            wp_register_style( 'swps-admin-style', plugins_url('assets/css/wooconnector-admin-style.css', SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
            wp_enqueue_style( 'swps-admin-style' );			
            wp_register_script( 'wooconnector_postpushnotice_script', plugins_url('assets/js/postpushnotice.js',SWP_PLUGIN_FILE), array( 'jquery' ), SWP_VERSION );		
            $remove = array(
                'base_url' => ABSPATH							
            );	
            wp_localize_script( 'wooconnector_postpushnotice_script', 'wooconnector_postpushnotice_params',  $remove  );
            wp_enqueue_script( 'wooconnector_postpushnotice_script' );
            wp_enqueue_media();
			
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