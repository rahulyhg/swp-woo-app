<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Closed if access directly.
}
/**
 * Create Api involve with users
 */

        
    function swp_general_setting_product_titles(){
        //register product title
        register_rest_route(
            'swp/v1/general-settings',
            '/product-title/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_general_setting_product_title_callbacks',
            )
        );
    add_action( 'rest_api_init', 'swp_general_setting_product_titles' );    
     }
    
    function swp_general_setting_product_title_callbacks(){
        $a= 5;
        $b = 5;
        $c = $a+$b;
        return $c;
//        $SWPsettingform = new SWPsettingform();
//        $SWPsettingform->swp_product_title_for_buyer_callback();
//        return $SWPsettingform->swp_product_title_for_buyer_callback();
    }
    


