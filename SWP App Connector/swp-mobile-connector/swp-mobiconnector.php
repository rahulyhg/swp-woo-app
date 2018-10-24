<?php
/**
 * Plugin Name: SWP Mobile Connector
 * Plugin URI: https://sortedwp.com
 * Description: Intergrated to Wordpress Rest API
 * Version: 1.0.0
 * Author: SortedWP
 * Author URI: https://sortedwp.com
 * Requires at least: 2.0
 * Tested up to: 4.9.8
 * Compatibility with the REST API v2
 *
 * Text Domain:swp mobiconnector
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Main BAMobile
 * 
 * @class BAMobile
 */
if(! function_exists( 'SWPMobile' ) ){
    final class SWPMobile{

        public function __construct(){
            $this->swp_file_path_declare();
        }

        private function swp_construct(){
        }

        private function swp_file_path_declare(){
            //jwt Authetication
            require_once( 'includes/swpauthentication/jwt/class-mobionnector-jwt-core.php' );
            //add meta column in product order list
            require_once( 'includes/swp-mobile-orders-meta.php' );
            swp_flag_order_meta();
        }

    }
     new SWPMobile();
}
?>