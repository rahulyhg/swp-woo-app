<?php
if( !defined( 'ABSPATH' ) ){
    /***** EXIT if access direct *****/
    exit;
}
/******************************************************
* @param slider function
* add sections, fields also register
* @return
*******************************************************/

if( !class_exists( 'SWPsettingslider' ) ){
    
    class SWPsettingslider{
        
        public function __construct(){
         //   $this->swp_settings_slider_register_field();
        }
        
        function swp_settings_slider_register_field(){
               register_setting(
                'swp_app_slider_option',
                'swp_app_slider_option',
                array( $this, 'swp_app_register_slider_settings' )
            );

            /* Header Options Section */
            add_settings_section( 
                'swp_app_slider_header',
                'Slider',
               array( $this,'swp_app_slider_header_callback' ),
                'swp_app_slider_option'
            );

            add_settings_field(  
                'swp_app_slider_add_slide',                      
                'Add Slide',               
                array( $this, 'swp_app_slider_add_slide_callback' ),   
                'swp_app_slider_option',                     
                'swp_app_slider_header',
                array(
                    'swp_app_slider_add_slide' 
                ) 
            );
            add_settings_field(  
                'swp_app_slider_slide_link',                      
                'Slide link',               
                array( $this, 'swp_app_slider_slide_link_callback' ),   
                'swp_app_slider_option',                     
                'swp_app_slider_header',
                array(
                    'swp_app_slider_slide_link' 
                ) 
            );
            add_settings_field(  
                'swp_app_slider_width',                      
                'Slider Width (px)',               
                array( $this, 'swp_app_slider_width_callback' ),   
                'swp_app_slider_option',                     
                'swp_app_slider_header',
                array(
                    'swp_app_slider_width' 
                ) 
            );
            add_settings_field(  
                'swp_app_slider_heights',                      
                'Slider Height (px)',               
                array( $this, 'swp_app_slider_heights_callback' ),   
                'swp_app_slider_option',                     
                'swp_app_slider_header',
                array(
                    'swp_app_slider_heights' 
                ) 
            );
            
        }

          /***** Call Backs ******/
        //sanitize and then register fields  
        public function swp_app_register_slider_settings( $input )
        {
            $new_input = array();

            if( isset( $input['swp_app_slider_add_slide'] ) )
                    $new_input['swp_app_slider_add_slide'] = sanitize_text_field( $input['swp_app_slider_add_slide'] );

            if( isset( $input['swp_app_slider_slide_link'] ) )
                    $new_input['swp_app_slider_slide_link'] = sanitize_text_field( $input['swp_app_slider_slide_link'] );

            if( isset( $input['swp_app_slider_width'] ) )
                    $new_input['swp_app_slider_width'] = sanitize_text_field( $input['swp_app_slider_width'] );

            if( isset( $input['swp_app_slider_heights'] ) )
                    $new_input['swp_app_slider_heights'] = sanitize_text_field( $input['swp_app_slider_heights'] );

            return $new_input;
        }

        public function swp_app_slider_header_callback() { 
            echo '<p>Enter Slider Details :</p>'; 
        }


        public function swp_app_slider_add_slide_callback($args) {

           $options = get_option('swp_app_slider_option'); 

            echo '<input type="file" id="' . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_slider_slide_link_callback($args) { 

            $options = get_option('swp_app_slider_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_slider_width_callback($args) { 

            $options = get_option('swp_app_slider_option'); 

            echo '<input type="text" id="'  . $args[0] . '" name="swp_app_slider_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        public function swp_app_slider_heights_callback($args) { 

            $options = get_option('swp_app_slider_option'); 
?>
            <textarea cols='40' rows='5' name='swp_app_slider_option[swp_app_slider_heights]'> 
                <?php echo $options['swp_app_slider_heights']; ?>
            </textarea>;
<?php

        }
        
       
        public function swp_slider_setting_page(){
            settings_fields( 'swp_app_slider_option' );
            do_settings_sections( 'swp_app_slider_option' );
         }
        
       
    }

    if( is_admin() )
    $SWPsettingslider = new SWPsettingslider();
}