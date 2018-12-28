<?php

if( !defined( 'ABSPATH' ) ){
    /****** EXIT if access directly ******/
    exit;
}

/******************************************************
* @param settings function
* callback values
* @return
******************************************************/
if( !class_exists( 'SWPappsettings' ) ){
    class SWPappsettings
    {
        /**** Holds the values to be used in the fields callbacks *****/
        private $options;

        /******* Start up *****/
        public function __construct(){
            $this->init_hooks();
        }

        /***** Hook into actions and filters. ******/
        public function init_hooks(){
            //require_once('class-app-connector-backend-about-us.php');
            require_once('general-settings/general-settings.php');
            require_once( 'popup/class-app-connector-popup.php' );
            require_once( 'deals/class-app-connector-deals.php' );
            require_once( 'slider/class-core.php' );
            require_once( 'slider/class-app-connector-slider.php' );
            
            //require_once( 'class-app-connector-backend-footer.php' );
        }
    }
    if( is_admin() )
    $SWPappsettings = new SWPappsettings();
}