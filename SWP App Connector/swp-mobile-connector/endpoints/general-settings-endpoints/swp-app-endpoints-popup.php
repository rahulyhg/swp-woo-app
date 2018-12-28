<?php
    
//popup Add image - endpoint    
    function swp_endpoint_popup_add_image(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/popup/add-image/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_popup_add_image_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_popup_add_image' );    

    function swp_endpoint_popup_add_image_callback(){
        $options = get_option('swp_app_popup_option');
        return $options['swp_app_popup_add_image'];
    }
    
//Popup image Link - endpoint    
    function swp_endpoint_popup_image_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/popup/image-link/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_popup_image_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_popup_image_link' );    

    function swp_endpoint_popup_image_link_callback(){
        $options = get_option('swp_app_popup_option');
        return $options['swp_app_popup_image_link'];
    }

