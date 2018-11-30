<?php
    
//slider Add slider - endpoint    
    function swp_endpoint_slider_add_slide(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/slider/add-slide/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_slider_add_slide_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_slider_add_slide' );    

    function swp_endpoint_slider_add_slide_callback(){
        $options = get_option('swp_app_slider_option');
        return $options['swp_app_slider_add_slide'];
    }
    
//Slider slide Link - endpoint    
    function swp_endpoint_slider_slide_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/slider/slide-link/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_slider_slide_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_slider_slide_link' );    

    function swp_endpoint_slider_slide_link_callback(){
        $options = get_option('swp_app_slider_option');
        return $options['swp_app_slider_slide_link'];
    }

//Slider slide Link - endpoint    
    function swp_endpoint_slider_width(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/slider/width/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_slider_width_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_slider_width' );    

    function swp_endpoint_slider_width_callback(){
        $options = get_option('swp_app_slider_option');
        return $options['swp_app_slider_width'];
    }

//Slider slide Height - endpoint    
    function swp_endpoint_slider_height(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/slider/height/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_slider_height_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_slider_height' );    

    function swp_endpoint_slider_height_callback(){
        $options = get_option('swp_app_slider_option');
        return $options['swp_app_slider_heights'];
    }


        