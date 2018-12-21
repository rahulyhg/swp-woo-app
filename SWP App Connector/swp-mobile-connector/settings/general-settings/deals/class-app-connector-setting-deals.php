<?php
if ( !defined( 'ABSPATH' ) ){
    /***** exit if directly access *****/
    exit;
}

/**********************************************************
* @param deals function
* add and register fields and sections
* @return
***********************************************************/
if( !class_exists( 'SWPsettingdeals' ) ){
    class SWPsettingdeals{   
       
        function swp_settings_deals_register_field(){
               register_setting(
                'swp_app_deals_options',
                'swp_app_deals_options',
                array( $this, 'swp_app_register_deal_settings' )
            );
            
            /****** Header Options Section ******/
                      
            add_settings_section( 
                'swp_app_deal_header',
                'Deals Setting',
               array( $this,'swp_app_deal_header_callback' ),
                'swp_app_deals_options'
            );

            add_settings_field(  
                'swp_app_deals_check',                      
                'Deals On Mobile',               
                array( $this, 'swp_app_deals_check_callback' ),   
                'swp_app_deals_options',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_check' 
                ) 
            );
            
            add_settings_field(  
                'swp_app_deals_add_first_image',                      
                'First Image',               
                array( $this, 'swp_app_deals_add_first_image_callback' ),   
                'swp_app_deals_options',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_add_first_image' 
                ) 
            );
            
            add_settings_field(  
                'swp_app_deals_first_image_link',                      
                'First Image link',               
                array( $this, 'swp_app_deals_first_image_link_callback' ),   
                'swp_app_deals_options',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_first_image_link' 
                ) 
            );
            add_settings_field(  
                'swp_app_deals_add_second_image',                      
                'Second Image',               
                array( $this, 'swp_app_deals_add_second_image_callback' ),   
                'swp_app_deals_options',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_add_second_image' 
                ) 
            );
            add_settings_field(  
                'swp_app_deals_second_image_link',                      
                'Second Image Link',               
                array( $this, 'swp_app_deals_second_image_link_callback' ),   
                'swp_app_deals_options',                     
                'swp_app_deal_header',
                array(
                    'swp_app_deals_second_image_link' 
                ) 
            );
                                      
        }

        /***** Call Backs *****/
        /***** sanitize and then register fields *****/  
        public function swp_app_register_deal_settings( $input )
        {
            $new_input = array();

            if( isset( $input['swp_app_deals_check'] ) )
                    $new_input['swp_app_deals_check'] = sanitize_text_field( $input['swp_app_deals_check'] );
            
            if( isset( $input['swp_app_deals_add_first_image'] ) )
                    $new_input['swp_app_deals_add_first_image'] = sanitize_text_field( $input['swp_app_deals_add_first_image'] );

            if( isset( $input['swp_app_deals_first_image_link'] ) )
                    $new_input['swp_app_deals_first_image_link'] = sanitize_text_field( $input['swp_app_deals_first_image_link'] );

            if( isset( $input['swp_app_deals_add_second_image'] ) )
                    $new_input['swp_app_deals_add_second_image'] = sanitize_text_field( $input['swp_app_deals_add_second_image'] );

            if( isset( $input['swp_app_deals_second_image_link'] ) )
                    $new_input['swp_app_deals_second_image_link'] = sanitize_text_field( $input['swp_app_deals_second_image_link'] );

            return $new_input;
        }

        
         public function swp_app_deal_header_callback() { 
            echo '<p>Deals Detail :</p>'; 
        }
        
        /***** *****/
        public function swp_app_deals_check_callback( ){
        
                $options = get_option('swp_app_deals_options'); 
        ?>            
                 <input type="checkbox" id="swp_app_deals_check" name="swp_app_deals_options[swp_app_deals_check]" value="1" <?php echo ( $options['swp_app_deals_check'] ) ? checked( $options['swp_app_deals_check'], '1' ) : '' ; ?>  />
                
        <?php }
        
       
        public function swp_app_deals_add_first_image_callback($args) {

           $options = get_option('swp_app_deals_options'); 

            echo '<input type="file" class="tab-content" id="' . $args[0] . '" name="swp_app_deals_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_first_image_link_callback($args) { 

            $options = get_option('swp_app_deals_options'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_deals_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_add_second_image_callback($args) { 

            $options = get_option('swp_app_deals_options'); 

            echo '<input type="file" class="tab-content" id="'  . $args[0] . '" name="swp_app_deals_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_deals_second_image_link_callback($args) { 

            $options = get_option('swp_app_deals_options'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_deals_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
        }
                
        public function swp_deals_setting_page(){
            settings_fields( 'swp_app_deals_options' );
            do_settings_sections( 'swp_app_deals_options' );
            
        }
        
               
        
       
                
    }
   
    if( is_admin() )
        $SWPsettingdeals = new SWPsettingdeals();
}