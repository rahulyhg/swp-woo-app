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
            
            // Load the Admin Options page
            add_action('admin_menu', array($this,'wptuts_menu_options' ));

            // Initialize Theme options
            add_action( 'amin_init', array( $this, 'wptuts_options_init') );
            add_action( 'admin_init', array( $this, 'wptuts_options_settings_init' ) );
            add_action( 'admin_init', array( $this, 'wptuts_get_default_options' ) );
            add_action('admin_enqueue_scripts', array($this,'wptuts_options_enqueue_scripts' ) );
            add_action( 'admin_init', array($this, 'wptuts_options_setup') );
            


        }
        
        
        
                // Add "WPTuts Options" link to the "Appearance" menu
        function wptuts_menu_options() {
            // add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function);
            $parent_slug = 'appconnector-settings';
            add_submenu_page($parent_slug, 'WPTuts Options', 'WPTuts Options', 'manage_options', 'wptuts-settings', array( $this, 'wptuts_admin_options_page') );
        }
        
        function wptuts_get_default_options() {
            $options = array(
            'logo' => ''
            );
            return $options;
        }
        
        function wptuts_options_init() {
        $wptuts_options = get_option( 'theme_wptuts_options' );

            // Are our options saved in the DB?
            if ( false === $wptuts_options ) {
                // If not, we'll save our default options
                $wptuts_options = $this->wptuts_get_default_options();
                add_option( 'theme_wptuts_options', $wptuts_options );
            }

            // In other case we don't need to update the DB
        }
        function wptuts_admin_options_page() {
            ?>
                <!-- 'wrap','submit','icon32','button-primary' and 'button-secondary' are classes
                for a good WP Admin Panel viewing and are predefined by WP CSS -->

                <div class="wrap">

                    <div id="icon-themes" class="icon32"><br /></div>

                    <h2><?php _e( 'WPTuts Options', 'wptuts' ); ?></h2>

                    <!-- If we have any error by submiting the form, they will appear here -->
                    <?php settings_errors( 'wptuts-settings-errors' ); ?>

                    <form id="form-wptuts-options" action="options.php" method="post" enctype="multipart/form-data">

                        <?php
                            settings_fields('theme_wptuts_options');
                            do_settings_sections('wptuts');
                        ?>

                        <p class="submit">
                            <input name="theme_wptuts_options[submit]" id="submit_options_form" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'wptuts'); ?>" />
                            <input name="theme_wptuts_options[reset]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'wptuts'); ?>" />
                        </p>

                    </form>

                </div>
            <?php
        }
        
      function wptuts_options_settings_init() {
        register_setting( 'theme_wptuts_options', 'theme_wptuts_options', array( $this, 'wptuts_options_validate' ) );

        // Add a form section for the Logo
        add_settings_section('wptuts_settings_header', __( 'Logo Options', 'wptuts' ),array( $this, 'wptuts_settings_header_text' ), 'wptuts');

        // Add Logo uploader
        add_settings_field('wptuts_setting_logo',  __( 'Logo', 'wptuts' ),array($this, 'wptuts_setting_logo' ), 'wptuts', 'wptuts_settings_header');
        
        add_settings_field('wptuts_setting_logo_preview',  __( 'Logo Preview', 'wptuts' ), array( $this, 'wptuts_setting_logo_preview' ), 'wptuts', 'wptuts_settings_header');
    }

        function wptuts_settings_header_text() {
            ?>
                <p><?php _e( 'Manage Logo Options for WpTuts Theme.', 'wptuts' ); ?></p>
            <?php
        }

        function wptuts_setting_logo() {
            $wptuts_options = get_option( 'theme_wptuts_options' );
            ?>
                <input type="text" id="logo_url" name="theme_wptuts_options[logo]" value="<?php echo esc_url( $wptuts_options['logo'] ); ?>" />
                <input id="upload_logo_button" type="button" class="button" value="<?php _e( 'Upload Logo', 'wptuts' ); ?>" />
                <span class="description"><?php _e('Upload an image for the banner.', 'wptuts' ); ?></span>
            <?php
        }
        
        function wptuts_options_validate( $input ) {
            $default_options = $this->wptuts_get_default_options();
            $valid_input = $default_options;

            $submit = ! empty($input['submit']) ? true : false;
            $reset = ! empty($input['reset']) ? true : false;

            if ( $submit )
                $valid_input['logo'] = $input['logo'];
            elseif ( $reset )
                $valid_input['logo'] = $default_options['logo'];

            return $valid_input;
        }
        
        
        function wptuts_options_enqueue_scripts() {
            wp_register_script( 'wptuts-upload', plugins_url('assets/js/wptuts-upload.js', SWP_PLUGIN_FILE ), array('jquery','media-upload','thickbox') );

            if ( 'appearance_page_wptuts-settings' == get_current_screen() -> id ) {
                wp_enqueue_script('jquery');

                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');

                wp_enqueue_script('media-upload');
                wp_enqueue_script('wptuts-upload');

            }

        }
        
        function wptuts_options_setup() {
            global $pagenow;

            if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
                // Now we'll replace the 'Insert into Post Button' inside Thickbox
                add_filter( 'gettext', 'replace_thickbox_text'  , 1, 3 );
            }
        }
        
        function replace_thickbox_text($translated_text, $text, $domain) {
            if ('Insert into Post' == $text) {
                $referer = strpos( wp_get_referer(), 'wptuts-settings' );
                if ( $referer != '' ) {
                    return __('I want this to be my logo!', 'wptuts' );
                }
            }
            return $translated_text;
        }
        
        function wptuts_setting_logo_preview() {
            $wptuts_options = get_option( 'theme_wptuts_options' );  ?>
            <div id="upload_logo_preview" style="min-height: 100px;">
                <img style="max-width:100%;" src="<?php echo esc_url( $wptuts_options['logo'] ); ?>" />
            </div>
            <?php
        }
     }
   
    $SWPappdealsoptions = new SWPappdealsoptions();
}