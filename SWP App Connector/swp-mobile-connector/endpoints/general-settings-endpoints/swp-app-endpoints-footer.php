<?php
    
//footer title details - endpoint    
    function swp_endpoint_footer_title(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/title/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_title_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_title' );    

    function swp_endpoint_footer_title_callback(){
        $options = get_option('swp_app_footer_option');
        return $options['swp_app_footer_title'];
    }
    
    
//footer address details - endpoint    
    function swp_endpoint_footer_address(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/address/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_address_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_address' );    

    function swp_endpoint_footer_address_callback(){
        $options = get_option('swp_app_footer_option');
        return $options['swp_app_footer_address'];
    }

    
//footer contact details - endpoint    
    function swp_endpoint_footer_contact(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/contact/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_contact_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_contact' );    

    function swp_endpoint_footer_contact_callback(){
        $options = get_option('swp_app_footer_option');
        return $options['swp_app_footer_contact'];
    }
    
//footer Email details - endpoint    
    function swp_endpoint_footer_email(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/email/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_email_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_email' );    

    function swp_endpoint_footer_email_callback(){
        $options = get_option('swp_app_footer_option');
        return $options['swp_app_footer_email'];
    }

// Footer Social link

//footer social facebook link - endpoint    
    function swp_endpoint_footer_social_facebook_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/facebook/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_social_facebook_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_social_facebook_link' );    

    function swp_endpoint_footer_social_facebook_link_callback(){
        $options = get_option('swp_app_footer_social_link');
        return $options['swp_app_footer_social_facebook'];
    }

//footer social instagram link - endpoint    
    function swp_endpoint_footer_social_instagram_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/instagram/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_social_instagram_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_social_instagram_link' );    

    function swp_endpoint_footer_social_instagram_link_callback(){
        $options = get_option('swp_app_footer_social_link');
        return $options['swp_app_footer_social_instagram'];
    }

//footer social Twitter link - endpoint    
    function swp_endpoint_footer_social_twitter_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/twitter/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_social_twitter_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_social_twitter_link' );    

    function swp_endpoint_footer_social_twitter_link_callback(){
        $options = get_option('swp_app_footer_social_link');
        return $options['swp_app_footer_social_twitter'];
    }

//footer social Google+ link - endpoint    
    function swp_endpoint_footer_social_google_plus_link(){
        //register slider title
        register_rest_route(
            'swp/v1/general-settings',
            '/footer/google-plus/',
            array(
                'methods' => 'GET',
                'callback' => 'swp_endpoint_footer_social_google_plus_link_callback',
            )
        );
     }
    add_action( 'rest_api_init', 'swp_endpoint_footer_social_google_plus_link' );    

    function swp_endpoint_footer_social_google_plus_link_callback(){
        $options = get_option('swp_app_footer_social_link');
        return $options['swp_app_footer_social_google_plus'];
    }

