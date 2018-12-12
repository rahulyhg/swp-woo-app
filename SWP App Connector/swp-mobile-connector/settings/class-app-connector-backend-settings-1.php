<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.	
}
/**
 * Process Seettings BAMobile Settings
 */
Class SWPappsettings{

    /**
     * MobiConnectorSettings construct
     */
    public function __construct(){
        $this->init_hooks();
    }

    /**
	 * Hook into actions and filters.
	 */
    public function init_hooks(){
        add_action( 'admin_menu',array($this,'swpapp_create_menu'));
        add_action( 'admin_enqueue_scripts', array( $this, 'swpapp_admin_style' ));
        //add_action( 'wp_ajax_mobiconnector_show_url', array($this,'bamobile_mobiconnector_process_show_url_function' ));
    }

    public function swpapp_create_menu(){
		add_menu_page(
			__('App Connector','appconnector'),
			__('App Connector','appconnector'),
			'manage_options',
			'appconnector-settings',
			array($this,'swpapp_redirect_to_app_connector'),
            '',
            50
        );
        add_submenu_page(
            'appconnector-settings',
            __('General Setting','appconnector'),
            __('General Setting','appconnector'),
            'manage_options',
            'swpapp-settings',
            array($this,'swpapp_redirect_to_general_setting')
        );
        add_submenu_page(
            'appconnector-settings',
            __('Push Notification','appconnector'),
            __('Push Notification','appconnector'),
            'manage_options',
            'swpapp-notification',
            array($this,'swpapp_redirect_to_notification')
        );
        add_submenu_page(
            'appconnector-settings',
            __('One Signal API','appconnector'),
            __('One Signal API','appconnector'),
            'manage_options',
            'swpapp-popup',
            array($this,'swpapp_redirect_to_one_signal')
        );
        add_submenu_page(
            'appconnector-settings',
            __('Product Review','appconnector'),
            __('Product Review','appconnector'),
            'manage_options',
            'swpapp-slider',
            array($this,'swpapp_redirect_to_product_review')
        );
       
    }
    
    public function swpapp_redirect_to_app_connector(){ ?>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab">Setting</a>
            <a href="#" class="nav-tab">Cache</a>
        </h2>
        
    <?php }
    
    public function swpapp_admin_style(){ ?>
            <?php }
    
    public function swpapp_redirect_to_general_setting(){ ?>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab">General Setting</a>
            <a href="#" class="nav-tab">Slider</a>
            <a href="#" class="nav-tab">Popup</a>
            <a href="#" class="nav-tab">Footer</a>
        </h2>
    
        <?php
            function sandbox_theme_display() {
            ?>
                <!-- Create a header in the default WordPress 'wrap' container -->
                <div class="wrap">

                    <div id="icon-themes" class="icon32"></div>
                    <h2>Sandbox Theme Options</h2>
                    <?php settings_errors(); ?>

                                        
                    <?php
            } ?>
            <form method="post" action="options.php">
                <?php
                        if( isset( $_GET[ 'tab' ] ) ) {
                            $active_tab = $_GET[ 'tab' ];
                        } // end if
                    
                    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'swpapp-settings-tab';
                ?>
        <div id="modern-settings-body" style="width:100%;">
			<div id="modern-body" >
				<div id="modern-body-content">	
					<table id="table-modern" class="i18n-multilingual-display" style="width:100%;">
		
                    <tr>
                        <td> Product Title
                        </td>
                        <td>
                            <input type="text" id="swp-product-title" name="Product Title">

                        </td>
                    </tr>
                    <tr>
                        <td> Decription Of Product
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_product_description', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td> Terms Of Use(Title )
                        </td>
                        <td><input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Terms Of Use(Description )
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_term_of_use', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Privacy And Policy(Heading)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Privacy And Policy(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_policy_and_privacy', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            About Us(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            About Us(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_about_us', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-1(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-1(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_home_page_block_one', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-2(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-2(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_home_page_block_two', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-3(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-3(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_home_page_block_three', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-4(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Home Page Block-4(Description)
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_home_page_block_four', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Footer(Title)
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Footer Address
                        </td>
                        <td>
                            <?php
                            $settings = array(
                            'teeny' => true,
                            'textarea_rows' => 10,
                            'tabindex' => 1
                            );
                            wp_editor(esc_html( __(get_option('product_description', 'Product Discripion'))), 'swp_footer_address', $settings);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Footer Contact/Phone Number
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Footer Email Id
                        </td>
                        <td>
                            <input type="text" id="text" name="textt">
                        </td>
                    </tr>
<!--
                    <tr><h2>Footer Social Links</h2></tr>
                    <tr>
                        <td>
                            Facebook Link
                        </td>
                        <td>
                            <input type="text" id="footer-facebook" name="Facebook">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Twitter Link
                        </td>
                        <td>
                            <input type="text" id="footer-twitter" name="Twitter">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Instagram Link
                        </td>
                        <td>
                            <input type="text" id="footer-instagram" name="Instagram">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            LinkedIn Link
                        </td>
                        <td>
                            <input type="text" id="footer-linkedin" name="LinkedIn">
                        </td>
                    </tr>
-->
                </table>
                </div>
            </div>
        </div>
                <?php
                if( $active_tab == 'swpapp-settings-tab' ) {
                    settings_fields( 'swpapp-settings' );
                    do_settings_sections( 'swpapp-settings' );
                }
        register_setting('appconnector-settings','manage-option');        

                submit_button();

            ?>
            </form>
    <?php }
    
    public function swpapp_redirect_to_notification(){ ?>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab">General Setting</a>
            <a href="#" class="nav-tab">Notification</a>
            <a href="#" class="nav-tab">Popup Setting</a>
            <a href="#" class="nav-tab">Slider Setting</a>
        </h2>
    <?php }
    
    public function swpapp_redirect_to_one_signal(){ ?>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab">General Setting</a>
            <a href="#" class="nav-tab">Notification</a>
            <a href="#" class="nav-tab">Popup Setting</a>
            <a href="#" class="nav-tab">Slider Setting</a>
        </h2>
    <?php }
    
    public function swpapp_redirect_to_product_review(){ ?>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab">General Setting</a>
            <a href="#" class="nav-tab">Notification</a>
            <a href="#" class="nav-tab">Popup Setting</a>
            <a href="#" class="nav-tab">Slider Setting</a>
        </h2>
    <?php }
        
    
 }
 new SWPAppsettings();
?>