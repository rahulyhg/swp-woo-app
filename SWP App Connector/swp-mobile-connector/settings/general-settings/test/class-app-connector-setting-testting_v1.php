<?php

if ( ! defined( 'ABSPATH' ) ) {
	/***** Exit if accessed directly. *****/
    exit; 
}

/************************************************
* @class SWPapppopup created 
* register menu, and submenu
* @return
************************************************/
 
if( !class_exists( 'SWPappdealsoptions' ) ){
    class SWPappdealsoptions{
       
        public function __construct(){
            add_action("admin_menu", array( $this, "swp_app_deals_submenu" ) );
            add_action("admin_init", array( $this, "swp_app_deals_display_options" ) );
        }
        
        function swp_app_deals_submenu()
            {   
                $parent_slug = 'appconnector-settings';

                add_submenu_page(
                    $parent_slug,
                        __(' Deals Setting '),
                        __(' Deals Setting '),
                        'manage_options',
                        'deals',
                        array( $this, 'swp_app_deals_options_page' )
                );
            
               
            }

        function swp_app_deals_options_page(){
            ?>
                <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>
                <h1>Theme Options</h1>

                <form method="post" action="options.php" enctype="multipart/form-data">
                    <?php

                        settings_fields("header_section");

                        do_settings_sections("theme-options");

                        submit_button(); 

                    ?>          
                </form>
            </div>
            <?php
            }
            
        function swp_app_deals_display_options()
            {
                add_settings_section("header_section", "Header Options", array( $this, "swp_app_deals_header_options_content" ), "theme-options");

                add_settings_field("background_picture", "First Image", array ( $this, "swp_app_deals_first_image" ), "theme-options", "header_section");
                register_setting("header_section", "background_picture", array( $this, "handle_file_upload" ) );
                
                add_settings_field("header_logo", "First Image", array( $this, "swp_app_deals_first_image_link" ), "theme-options", "header_section");
                register_setting("header_section", "header_logo");
            
                add_settings_field("background_picture1", "Second Image", array ( $this, "swp_app_deals_second_image" ), "theme-options", "header_section");
                register_setting("header_section", "background_picture1", array( $this, "handle_file_upload1" ) );
                
                add_settings_field("header_logo1", "Second Image Link", array( $this, "swp_app_deals_second_image_link" ), "theme-options", "header_section");
                register_setting("header_section", "header_logo1");
                                          
            }
      
        function swp_app_deals_header_options_content(){
          echo __("The header of the theme", 'swp');
        }
     
        function handle_file_upload($options)
            {
                //check if user had uploaded a file and clicked save changes button
                if(!empty($_FILES["background_picture"]["tmp_name"]))
                {
                    $urls = wp_handle_upload($_FILES["background_picture"], array('test_form' => FALSE));
                    $temp = $urls["url"];
                    return $temp;   
                }

                //no upload. old file url is the new value.
                return get_option("background_picture");
            }
        
        function handle_file_upload1($options)
                {
                    //check if user had uploaded a file and clicked save changes button
                    if(!empty($_FILES["background_picture1"]["tmp_name"]))
                    {
                        $urls = wp_handle_upload($_FILES["background_picture1"], array('test_form' => FALSE));
                        $temp = $urls["url"];
                        return $temp;   
                    }

                    //no upload. old file url is the new value.
                    return get_option("background_picture1");
                }

        function swp_app_deals_first_image()
            {
                //echo form element for file upload
                ?>
                    <input type="file" name="background_picture" id="background_picture" value="<?php echo get_option('background_picture'); ?>" />
                    <?php echo get_option("background_picture"); ?>
                <?php
            }
        
        function swp_app_deals_second_image()
            {
                //echo form element for file upload
                ?>
                    <input type="file" name="background_picture1" id="background_picture1" value="<?php echo get_option('background_picture1'); ?>" />
                    <?php echo get_option("background_picture1"); ?>
                <?php
            }
        function swp_app_deals_first_image_link()
            {
                ?>
                    <input type="text" class="tab-content" name="header_logo" id="header_logo" value="<?php echo get_option('header_logo'); ?>" />
                <?php
            }
      
        function swp_app_deals_second_image_link()
            {
                ?>
                    <input type="text" class="tab-content" name="header_logo1" id="header_logo1" value="<?php echo get_option('header_logo1'); ?>" />
                <?php
            }
    
    }
   
    $SWPappdealsoptions = new SWPappdealsoptions();
}