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
                    <form method="post" class="connector-tab-settings" id="tab-settings" action="options.php" enctype="multipart/form-data">  
                        <div id="connector-settings-form-body">
                            <div id="connector-form-body">
                               <?php

                                    settings_fields("app_deals_section_content");

                                    do_settings_sections("app-deals-options");

                                    submit_button(); 
                                ?> 
                            </div>
                        </div>
                    </form>
                </div>
            <?php
            }
            
        function swp_app_deals_display_options()
            {
                add_settings_section("app_deals_section_content", "Header Options", array( $this, "swp_app_deals_header_options_content" ), "app-deals-options");

                add_settings_field("app_deals_first_image", "First Image", array ( $this, "swp_app_deals_first_image" ), "app-deals-options", "app_deals_section_content");
                register_setting("app_deals_section_content", "app_deals_first_image", array( $this, "handle_file_upload" ) );
                
                add_settings_field("app_deals_first_image_link", "First Image", array( $this, "swp_app_deals_first_image_link" ), "app-deals-options", "app_deals_section_content");
                register_setting("app_deals_section_content", "app_deals_first_image_link");
            
                add_settings_field("app_deals_second_image", "Second Image", array ( $this, "swp_app_deals_second_image" ), "app-deals-options", "app_deals_section_content");
                register_setting("app_deals_section_content", "app_deals_second_image", array( $this, "handle_file_upload1" ) );
                
                add_settings_field("app_deals_second_image_link", "Second Image Link", array( $this, "swp_app_deals_second_image_link" ), "app-deals-options", "app_deals_section_content");
                register_setting("app_deals_section_content", "app_deals_second_image_link");
                                          
            }
      
        function swp_app_deals_header_options_content(){
          echo __("The header of the theme", 'swp');
        }
     
        function handle_file_upload($options)
            {
                //check if user had uploaded a file and clicked save changes button
                if(!empty($_FILES["app_deals_first_image"]["tmp_name"]))
                {
                    $urls = wp_handle_upload($_FILES["app_deals_first_image"], array('test_form' => FALSE));
                    $temp = $urls["url"];
                    return $temp;   
                }

                //no upload. old file url is the new value.
                return get_option("app_deals_first_image");
            }
        
        function handle_file_upload1($options)
                {
                    //check if user had uploaded a file and clicked save changes button
                    if(!empty($_FILES["app_deals_second_image"]["tmp_name"]))
                    {
                        $urls = wp_handle_upload($_FILES["app_deals_second_image"], array('test_form' => FALSE));
                        $temp = $urls["url"];
                        return $temp;   
                    }

                    //no upload. old file url is the new value.
                    return get_option("app_deals_second_image");
                }

        function swp_app_deals_first_image()
            {
                //echo form element for file upload
                ?>
                    <input type="file" name="app_deals_first_image" id="app_deals_first_image" value="<?php echo get_option('app_deals_first_image'); ?>" />
                    <?php echo get_option("app_deals_first_image"); ?>
                <?php
            }
        
        function swp_app_deals_second_image()
            {
                //echo form element for file upload
                ?>
                    <input type="file" name="app_deals_second_image" id="app_deals_second_image" value="<?php echo get_option('app_deals_second_image'); ?>" />
                    <?php echo get_option("app_deals_second_image"); ?>
                <?php
            }
        function swp_app_deals_first_image_link()
            {
                ?>
                    <input type="text" class="tab-content" name="app_deals_first_image_link" id="app_deals_first_image_link" value="<?php echo get_option('app_deals_first_image_link'); ?>" />
                <?php
            }
      
        function swp_app_deals_second_image_link()
            {
                ?>
                    <input type="text" class="tab-content" name="app_deals_second_image_link" id="app_deals_second_image_link" value="<?php echo get_option('app_deals_second_image_link'); ?>" />
                <?php
            }
    
    }
   
    $SWPappdealsoptions = new SWPappdealsoptions();
}