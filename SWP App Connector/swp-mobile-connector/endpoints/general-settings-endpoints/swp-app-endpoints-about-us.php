<?php
    
//about us title - endpoint    
    function swp_about_us_title(){
        //register about us title
        register_rest_route(
            'swp/v1/general-settings',
            '/about-us/title/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_about_us_title_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_about_us_title' );    

    function swp_about_us_title_callback(){
        $options = get_option('swp_app_about_us_option');
        return $options['swp_app_about_us_title'];
    }
    
//about us title - endpoint    
    function swp_about_us_description(){
        //register about us title
        register_rest_route(
            'swp/v1/general-settings',
            '/about-us/description/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_about_us_description_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_about_us_description' );    

    function swp_about_us_description_callback(){
        $options = get_option('swp_app_about_us_option');
        return $options['swp_app_about_us_description'];
    }


        