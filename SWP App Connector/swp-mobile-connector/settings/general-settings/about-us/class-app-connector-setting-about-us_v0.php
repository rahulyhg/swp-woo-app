<?php

if( !defined( 'ABSPATH' ) ){
    /***** EXIT if access directly *****/
    exit;   
}

/*************************************************
* @param about us function
* add and register fields and section
* return
*************************************************/
if( !class_exists( 'SWPsettingaboutus' ) ){
    class SWPsettingaboutus{   
        
        private $options;

        function swp_general_settings_about_us_register_field(){
               register_setting(
                'swp_app_about_us_option',
                'swp_app_about_us_option'
            );

            /* Header Options Section */
            add_settings_section( 
                'swp_app_about_us_header',
                'About Us',
               array( $this,'swp_app_about_us_header_callback' ),
                'swp_app_about_us_option'
            );

            add_settings_field(  
                'swp_app_about_us_title',                      
                'About Us (Title)',               
                array( $this, 'swp_app_about_us_title_callback' ),   
                'swp_app_about_us_option',                     
                'swp_app_about_us_header',
                array(
                    'swp_app_about_us_title' 
                ) 
            );
            add_settings_field(  
                'swp_app_about_us_description',                      
                'About Us (Description)',               
                array( $this, 'swp_app_about_us_description_callback' ),   
                'swp_app_about_us_option',                     
                'swp_app_about_us_header',
                array(
                    'swp_app_about_us_description' 
                ) 
            );

            register_setting( 'pluginPage', 'pw_settings' );

             add_settings_section(
                'pw_pluginPage_section', 
                __( 'Live Credentials', 'pw' ), 
                array( $this, 'pw_settings_section_callback' ), 
                'pluginPage'
             );
            add_settings_field( 
                'pw_textarea_intro', 
                __( 'Header Intro Text', 'pw' ), 
               array( $this, 'pw_textarea_intro_render' ), 
                'pluginPage', 
                'pw_pluginPage_section',
                array(
                    'pw_textarea_intro'
                )
            );
            add_settings_field( 
                'pw_intro', 
                __( 'Intro', 'pw' ), 
               array( $this, 'pw_intro_render'), 
                'pluginPage', 
                'pw_pluginPage_section' 
            );
        }

        /****** Call Backs ******/
        public function swp_app_about_us_header_callback() { 
            echo '<p>Enter Your About Us Details :</p>'; 
        }

        public function swp_app_about_us_title_callback($args) { 

            $options = get_option('swp_app_about_us_option'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_about_us_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }
            public function swp_app_about_us_description_callback($args) { 

            $options = get_option('swp_app_about_us_option'); 
?>
            <textarea name='swp_app_about_us_option[swp_app_about_us_description]'> 
                <?php echo $options['swp_app_about_us_description']; ?>
            </textarea>;
<?php

        }

        public function swp_about_us_setting_page(){
            settings_fields( 'swp_app_about_us_option' );
            do_settings_sections( 'swp_app_about_us_option' ); 

            settings_fields( 'pw_settings' );
            do_settings_sections( 'pluginPage' ); 

        }
        
        public function pw_settings_section_callback() { 
            echo '<p>Test :</p>'; 
        }

        function pw_textarea_intro_render( ) { 

            $options = get_option( 'pw_settings' );
            ?>
            <textarea cols='40' rows='5' name='pw_settings[pw_textarea_intro]'> 
                <?php echo $options['pw_textarea_intro']; ?>
            </textarea>
            <?php

        }

        function pw_intro_render() {
            $options = get_option( 'pw_settings' );
            echo wp_editor( $options['pw_intro'], 'pw_intro', array('textarea_name' => 'pw_intro', 'media_buttons' => false)  );
        }

    }
    
    if( is_admin() )
    $SWPsettingaboutus = new SWPsettingaboutus();
}