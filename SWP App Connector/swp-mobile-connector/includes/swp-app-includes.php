<?php

if ( ! defined( 'ABSPATH' ) ) {
	/***** Exit if accessed directly. *****/
    exit; 
}

if( !class_exists( 'SWPappinludes' ) ){
    class SWPappinludes{
        public function __construct(){
            $this->swp_include_init_hooks();
        }

        /***** Hook into actions and filters. *****/
        public function swp_include_init_hooks(){
            require_once( 'shipping/class-app-shipping.php' );
            $Swpappshipping = new Swpappshipping();
//            require_once( 'shipping/wooconnector-core.php' );
            require_once( 'mobile-order-flag/class-app-mobile-orders-meta.php' );
            $swp_flag_order_meta = new swp_flag_order_meta();
            require_once( 'disable-category-on-mobile/class-app-disable-category-on-mobile.php' );
            //$swp_product_cat_on_mobile = new swp_product_cat_on_mobile();
            require_once( 'user/class-app-user.php' );
            
            require_once( 'shipping/class-app-specific-users-order.php' );
            require_once( 'shipping/class-app-coupon.php' );
            
            
            /***** notices *****/
            require_once( 'functions-notices/swp-functions-notices.php' );    
        }
    }

    //if( is_admin() )
    //    $SWPappinludes = new SWPappinludes();
}