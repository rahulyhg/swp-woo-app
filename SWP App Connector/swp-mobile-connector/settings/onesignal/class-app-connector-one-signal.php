<?php


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
        
        /* Front Page Options Section */
        add_settings_section( 
            'swp_app_general_setting',
            'General Settings',
            array( $this, 'swp_app_general_setting_callback' ),
            'swp_app_general_options'
        );
        add_settings_field(
            'swp_product_title_for_buyers', // ID
            'Product Title', // Title 
            array( $this, 'swp_product_title_for_buyer_callback' ), // Callback
            'swp_app_general_options', // Page
            'swp_app_general_setting', // Section 
            array(
                'swp_product_title_for_buyers'
            )
        );
        add_settings_field(
            'swp_buyer_product_descriptions', 
            'Description of Buyer Product', 
            array( $this, 'swp_buyer_product_description_callback' ), 
            'swp_app_general_options', 
            'swp_app_general_setting',
            array(
                'swp_buyer_product_descriptions'
            )
        );
        add_settings_field(
            'swp_terms_of_use_title', 
            'Terms Of Use (Title)', 
            array( $this, 'swp_terms_of_use_title_callback' ), 
            'swp_app_general_options', 
            'swp_app_general_setting',
            array(
                'swp_terms_of_use_title'
            )
        );
        add_settings_field(
            'swp_terms_of_use_description', 
            'Terms Of Use (Description)', 
            array( $this, 'swp_terms_of_use_description_callback' ), 
            'swp_app_general_options', 
            'swp_app_general_setting',
            array(
                'swp_terms_of_use_description'
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
        
        add_settings_field(  
            'featured_post',                      
            'Featured Post',               
            array( $this,'ch_essentials_featured_post_callback'),   
            'swp_app_general_options',                     
            'swp_app_general_setting'            
        );
            
    }
    
      /* Call Backs
    -----------------------------------------------------------------*/
    public function swp_app_general_setting_callback() { 
        echo '<p>Enter your mobile app settings :</p>'; 
    }
    
     /** 
     * Get the settings option array and print one of its values
     */
    function swp_product_title_for_buyer_callback($args){
    
        
        $options = get_option('swp_app_general_options'); 

        echo '<input type="text" id="swp_product_title_for_buyers" name="swp_app_general_options[swp_product_title_for_buyers]" value="' . $options['swp_product_title_for_buyers'] . '"></input>';

    }

        
    /** 
     * Get the settings option array and print one of its values
     */
    public function swp_buyer_product_description_callback()
    {
        
        $options = get_option('swp_app_general_options'); 

        echo '<input type="text" id="swp_buyer_product_descriptions" name="swp_app_general_options[swp_buyer_product_descriptions]" value="' . $options['swp_buyer_product_descriptions'] . '"></input>';
          
    }
    
    /** 
     * Get the settings option array and print one of terms of use title
     */
    public function swp_terms_of_use_title_callback($args)
    {
        
        $options = get_option('swp_app_general_options'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
        
    }
   
    public function swp_terms_of_use_description_callback($args)
    {
       $options = get_option('swp_app_general_options'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
           
    }
    
     public function swp_privacy_policy_title_callback($args)
    {
        
        $options = get_option('swp_app_general_options'); 

        echo '<input type="text" id="'  . $args[0] . '" name="swp_app_general_options[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';
       
    }
    public function swp_privacy_policy_description_callback()
    {
        
        $settings = array(
        'tinymce' => false,
        'tabindex' => 1,
        'media_buttons' => false,
        'quicktags' => false,
        'textarea_rows' => 6
        );
        //$content = $_POST['swp_buyer_product_description'];
        wp_editor('', 'swp_privacy_policy_description', $settings);
        
    }

   
   public function ch_essentials_textbox_callback($args) { 

        $options = get_option('ch_essentials_header_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="ch_essentials_header_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }
        public function ch_essentials_textboxs_callback($args) { 

        $options = get_option('ch_essentials_header_option'); 

        echo '<input type="text" id="'  . $args[0] . '" name="ch_essentials_header_option[' . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '"></input>';

    }

    public function ch_essentials_featured_post_callback($options) { 

        $options = get_option('swp_app_general_options'); 

        query_posts( $args );


        echo '<select id="featured_post" name="swp_app_general_options[featured_post]">';
        while ( have_posts() ) : the_post();

            $selected = selected($options[featured_post], get_the_id(), false);
            printf('<option value="%s" %s>%s</option>', get_the_id(), $selected, get_the_title());

        endwhile;
        echo '</select>';


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
                <a href="?page=swp-app-general-setting-options&tab=swp_app_slider_settings" class="nav-tab <?php echo $active_tab == 'swp_app_slider_settings' ? 'nav-tab-active' : ''; ?>">Slider</a>
                <a href="?page=swp-app-general-setting-options&tab=swp_app_popup_settings" class="nav-tab <?php echo $active_tab == 'swp_app_popup_settings' ? 'nav-tab-active' : ''; ?>">Popup</a>
                <a href="?page=swp-app-general-setting-options&tab=swp_app_footer_settings" class="nav-tab <?php echo $active_tab == 'swp_app_footer_settings' ? 'nav-tab-active' : ''; ?>">Footer</a>
            </h2>  


            <form method="post" action="options.php">  

                <?php 
                if( $active_tab == 'swp_app_general_setting' ) {  
                    settings_fields( 'swp_app_general_options' );
                    do_settings_sections( 'swp_app_general_options' ); 
                } else if( $active_tab == 'header_options' ) {
                    $SWPsettingaboutus = new SWPsettingaboutus();
                    $SWPsettingaboutus->swp_about_us_setting_page();
                }else if( $active_tab == 'swp_app_slider_settings' ) {
                     $SWPsettingslider = new SWPsettingslider();
                    $SWPsettingslider->swp_about_us_settings_page();
                }else if( $active_tab == 'swp_app_popup_settings' ) {
                     $SWPsettingaboutus = new SWPsettingaboutus();
                    $SWPsettingaboutus->swp_about_us_setting_page();
                }else if( $active_tab == 'swp_app_footer_settings' ) {
                    $SWPsettingfooter = new SWPsettingfooter();
                    $SWPsettingfooter->swp_footer_setting_page();
                }
                ?>             
                <?php submit_button(); ?>  
            </form> 

        </div> 
    <?php
    }
}

if( is_admin() )
    $SWPsettingform = new SWPsettingform();