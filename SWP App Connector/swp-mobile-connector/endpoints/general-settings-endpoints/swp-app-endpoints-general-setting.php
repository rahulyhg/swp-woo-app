<?php
    
        function swp_general_setting_product_title(){
            //register product title
            register_rest_route(
                'swp/v1/general-settings',
                '/product-title/',
                array(
                    'methods' => 'GET',
                    'callback' => 'swp_general_setting_product_title_callback',
                )
            );
         }
        add_action( 'rest_api_init', 'swp_general_setting_product_title' );    

        function swp_general_setting_product_title_callback(){
            $options = get_option('swp_app_general_options');
            return $options['swp_product_title_for_buyers'];
             
            
        }


        