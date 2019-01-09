<?php
if( !defined( 'ABSPATH' ) ){
    /***** EXIT if access derectly *****/
    exit;
}

/***********************************************************
* @param setting form function
* add fields, sections and register fields and sections
* @return
************************************************************/
if( !class_exists( 'SWPsettingform' ) ){
    
    /****** create setting form ******/
    class SWPsettingform
    {   
        public function __construct(){
            //require_once('swp-mobile-connector/settings/general-settings/about-us/class-app-connector-setting-about-us.php');
            
        }
        function swp_general_settings_register_section_and_add_field(){
        register_setting(
                'swp_app_general_options',
                'swp_app_general_options'
            );

            /****** Front Page Options Section *****/
            add_settings_section( 
                'swp_app_general_setting',
                'General Settings',
                array( $this, 'swp_app_general_setting_callback' ),
                'swp_app_general_options'
            );
            add_settings_field(
                'swp_general_settings_contact_us', // ID
                'Contact Us', // Title 
                array( $this, 'swp_general_settings_contact_us_callback' ), // Callback
                'swp_app_general_options', // Page
                'swp_app_general_setting', // Section 
                array(
                    'swp_general_settings_contact_us'
                )
            );
            add_settings_field(
                'swp_general_settings_google_play_store_review', 
                'Review App On Google Play Store', 
                array( $this, 'swp_general_settings_google_play_store_review_callback' ), 
                'swp_app_general_options', 
                'swp_app_general_setting',
                array(
                    'swp_general_settings_google_play_store_review'
                )
            );
            add_settings_field(
                'swp_general_settings_itune_play_store_review', 
                'Review App On itunes Play Store', 
                array( $this, 'swp_general_settings_itune_play_store_review_callback' ), 
                'swp_app_general_options', 
                'swp_app_general_setting',
                array(
                    'swp_general_settings_itune_play_store_review'
                )
            );
            add_settings_field(
                'swp_privacy_policy_title', 
                'Privacy Policy (Title)', 
                array( $this, 'swp_privacy_policy_title_callback' ), 
                'swp_app_general_options', 
                'swp_app_general_setting',
                array(
                    'swp_privacy_policy_title'
                )
            );
            add_settings_field(
                'swp_privacy_policy_description', 
                'Privacy Policy (Description)', 
                array( $this, 'swp_privacy_policy_description_callback' ), 
                'swp_app_general_options', 
                'swp_app_general_setting',
                array(
                    'swp_privacy_policy_description'
                )
            ); 

            
//            add_settings_field(  
//                'swp_privacy_policy_test',                      
//                'Test',               
//                array( $this,'swp_privacy_policy_test_callback'),   
//                'swp_app_general_options',                     
//                'swp_app_general_setting',
//                array(
//                    'swp_privacy_policy_test'
//                )
//            );
            
            wp_register_style( 'swp-tab-settings-form-style', plugins_url('assets/css/swp-app-tabs-settings-form-style.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
			wp_enqueue_style( 'swp-tab-settings-form-style' );	


        }

          /***** Call Backs ******/
        public function swp_app_general_setting_callback() { 
            echo '<p>Enter your mobile app general settings details</p>'; 
        }

         /***** Get the settings option array and print one of its values ******/
        function swp_general_settings_contact_us_callback($args){


            $options = get_option('swp_app_general_options'); 
            
            $email = $options['swp_general_settings_contact_us'];
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
               $mail ='Invalid Email';
            } else {
                $mail = $email;
            }

            echo '<input type="text" class="tab-content" id="swp_general_settings_contact_us" name="swp_app_general_options[swp_general_settings_contact_us]" value="' .$mail . '"></input>';

        }


        /******  Get the settings option array and print one of its values  ******/
        public function swp_general_settings_google_play_store_review_callback($args)
        {
            $options =  get_option('swp_app_general_options' ) ; 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
        }

        /****** Get the settings option array and print one of terms of use title *****/
        public function swp_general_settings_itune_play_store_review_callback($args)
        {

            $options = get_option('swp_app_general_options');
            $site_changes = array('http' =>'http:', 'https' => 'https:');
            
            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
                    
        }

        public function swp_privacy_policy_title_callback($args)
        {

            $options = get_option('swp_app_general_options'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }
        
        public function swp_privacy_policy_test_callback()
        {

            $settings = array(
            'tinymce' => true,
            'teeny' => true,
            'tabindex' => 1,
            'media_buttons' => true,
            'quicktags' => true,
            'textarea_rows' => 6
            );
            
            $content = '';
            $options = get_option('swp_app_general_options');                
            //$content = $_POST['swp_buyer_product_description'];
            wp_editor($content, 'swp_privacy_policy_test', $settings);
        }

        public function swp_privacy_policy_description_callback($args) {
            $options = get_option('swp_app_general_options');                
            ?>
            <textarea rows='10' class="tab-content" name='swp_app_general_options[swp_privacy_policy_description]'> 
                <?php echo stripslashes($options['swp_privacy_policy_description']); ?>
            </textarea>
        <?php
        }
        
        public function ch_essentials_textboxs_callback($args) { 

            $options = get_option('ch_essentials_header_option'); 

            echo '<input type="text" class="tab-content" id="'  . $args[0] . '" name="ch_essentials_header_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

        }

        function swp_setting_form(){
        ?>
            <div class="wrap">  
                <div id="icon-themes" class="icon32"></div>  
                <?php settings_errors(); ?>  

                <?php  
                        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'swp_app_general_setting';  
                ?>  

                <h2 class="nav-tab-wrapper">  
                    <a href="?page=swp-app-general-setting-options&tab=swp_app_general_setting" class="nav-tab <?php echo $active_tab == 'swp_app_general_setting' ? 'nav-tab-active' : ''; ?>">General Setting</a>  
                    <a href="?page=swp-app-general-setting-options&tab=header_options" class="nav-tab <?php echo $active_tab == 'header_options' ? 'nav-tab-active' : ''; ?>">About Us</a>
                    <a href="?page=swp-app-general-setting-options&tab=swp_app_footer_settings" class="nav-tab <?php echo $active_tab == 'swp_app_footer_settings' ? 'nav-tab-active' : ''; ?>">Footer</a>
                </h2>  

                
                <form method="post" class="connector-tab-settings" id="tab-settings" action="options.php">  
                    <div id="connector-settings-form-body">
                        <div id="connector-form-body">
                            <?php
                            /***** create setting form *****/

                            if( $active_tab == 'swp_app_general_setting' ) {  
                                settings_fields( 'swp_app_general_options') ;
                                do_settings_sections( 'swp_app_general_options' ); 
                            } else if( $active_tab == 'header_options' ) {
                                $SWPsettingaboutus = new SWPsettingaboutus();
                                $SWPsettingaboutus->swp_about_us_setting_page();
                            }else if( $active_tab == 'swp_app_slider_settings' ) {
                                 $SWPsettingslider = new SWPsettingslider();
                                 $SWPsettingslider->swp_slider_setting_page();
                            }else if( $active_tab == 'swp_app_popup_settings' ) {
                                 $SWPsettingpopup = new SWPsettingpopup();
                                 $SWPsettingpopup->swp_popup_setting_page();
                            }else if( $active_tab == 'swp_app_deals_settings' ) {
                                 $SWPsettingdeals = new SWPsettingdeals();
                                 $SWPsettingdeals->swp_deals_setting_page();
                            }else if( $active_tab == 'swp_app_footer_settings' ) {
                                $SWPsettingfooter = new SWPsettingfooter();
                                $SWPsettingfooter->swp_footer_setting_page();
                            }else if( $active_tab == 'swp_app_test_settings' ) {
                                $swp_testing = new swp_testing();
                                $swp_testing->swp_testing_setting_page();
                            }
                            ?>             
                            <?php submit_button(); ?> 
                        </div>
                    </div>
                </form> 

            </div> 
        <?php
        }
    }

    if( is_admin() )
    $SWPsettingform = new SWPsettingform();
}