<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class swp_testing {
    
    function swp_general_settings_testing_register_field(){
            
            add_settings_section("header_sections", "Header Options", array( $this, "display_header_options_content" ), "theme-option");

            add_settings_field("header_logo", "Logo Url", array( $this, "display_logo_form_element" ), "theme-option", "header_sections");
            register_setting("header_sections", "header_logo");
            
            add_settings_field("background_pictures", "Picture File Upload", array( $this, "background_form_element" ), "theme-option", "header_sections");
            register_setting("header_sections", "background_pictures", array( $this, "handle_file_upload" ) );
    }
    
   function handle_file_upload($options)
    {
        //check if user had uploaded a file and clicked save changes button
        if(!empty($_FILES["background_pictures"]["tmp_name"]))
        {
            $urls = wp_handle_upload($_FILES["background_pictures"], array('test_form' => FALSE));
            $temp = $urls["url"];
            return $temp;   
        }

        //no upload. old file url is the new value.
        return get_option("background_pictures");
    }


    function display_header_options_content(){echo "The header of the theme";}
    function background_form_element()
    {
        //echo form element for file upload
        ?>
            <input type="file" name="background_pictures" id="background_pictures" value="<?php echo get_option('background_pictures'); ?>" />
            <?php echo get_option("background_pictures"); ?>
        <?php
    }
    function display_logo_form_element()
    {
        ?>
            <input type="text" name="header_logo" id="header_logo" value="<?php echo get_option('header_logo'); ?>" />
        <?php
    }
    function display_ads_form_element()
    {
        ?>
            <input type="text" name="advertising_code" id="advertising_code" value="<?php echo get_option('advertising_code'); ?>" />
        <?php
    }

    
 
    public function swp_testing_setting_page(){
            settings_fields("header_sections");
            do_settings_sections("theme-option");
    }
    
}
$swp_testing = new swp_testing();
?>