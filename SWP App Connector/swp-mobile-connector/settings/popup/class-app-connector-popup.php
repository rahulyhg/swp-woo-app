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
if( !class_exists( 'SWPapppopup' ) ){
    class SWPapppopup{
        
        private $endpoint_url = 'swp/v1';
        
        /***** create construct function *****/
        public function __construct(){
            add_action( 'admin_menu',array( $this, 'swp_app_create_submenu_popup' ) );       
            add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
            add_action( 'admin_post_woo_update_popup', array( $this, 'swp_popup_update' ) );
            $this->swp_app_register_popup_route();
            add_action( 'rest_api_init', array( $this, 'swp_app_register_popup_api' ) );   
        }
        
        /**** add update function updating new changes *****/
        public function swp_popup_update(){
            global $wpdb;
            check_admin_referer( "woo_update_popup" );
            $capability = apply_filters( 'woopopup_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return;
            }
            $link = @$_POST['wooconnector_popup_link'];
            if(!empty($link)){
                update_option('wooconnector-popup-homepage-link',$link);
            }else{
                update_option('wooconnector-popup-homepage-link','');
            }
            $check = @$_POST['wooconnector_check_popup_datetime'];
            if(!empty($check) && $check == 1){
                update_option('wooconnector-popup-homepage-check',$check);
                $from = @$_POST['wooconnector_popup_datepicker_from'];
                $to = @$_POST['wooconnector_popup_datepicker_to'];
                update_option('wooconnector-popup-homepage-date-from',$from);
                update_option('wooconnector-popup-homepage-date-to',$to);
            }else{
                update_option('wooconnector-popup-homepage-check','');
                update_option('wooconnector-popup-homepage-date-from','');
                update_option('wooconnector-popup-homepage-date-to','');
            }
            $attachments = @$_POST['connector-popup-url'];
            if(!empty($attachments)){
                update_option('wooconnector-popup-homepage',$attachments);
            }else{
                update_option('wooconnector-popup-homepage','');
            }
            //bamobile_mobiconnector_add_notice(__('Successfully Update','mobiconnector'));   
            wp_redirect( admin_url( "admin.php?page=popup" ) );
        }
        
        /***** create submenu function *****/
        public function swp_app_create_submenu_popup(){
            $parent_slug = 'appconnector-settings';
            add_submenu_page(
                $parent_slug,
                __(' Popup Setting '),
                __(' Popup Setting '),
                'manage_options',
                'popup',
                array($this,'swp_app_popup_action')
            );
        }
        
        /***** create action popup function *****/
        public function swp_app_popup_action(){
            $task = isset($_REQUEST['wootask']) ? $_REQUEST['wootask'] : '';			
            require_once(SWP_ABSPATH.'/settings/popup/class-app-connector-popup-form.php');		
            if(!empty($task) || $task != ''){				
                $this->swp_app_popup_save();		
            }
        }
        
        /***** crete popup function saving details *****/
        public function swp_app_popup_save(){        
        }
        
        /***** register javasript, css using admin scripts functions *****/
        public function register_admin_scripts(){
            if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'popup'){
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-ui-timepicker-addon',plugins_url('assets/js/jquery-ui-timepicker-addon.js',SWP_PLUGIN_FILE),array(),SWP_VERSION,true);
                wp_enqueue_style('jquery-ui-timepicker-addon-style',plugins_url('assets/css/jquery-ui-timepicker-addon.css',SWP_PLUGIN_FILE),array(),SWP_VERSION,'all');
                wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');     

                wp_enqueue_script( 'connector-popup-script', plugins_url('assets/js/swp-app-popup.js',SWP_PLUGIN_FILE), array( 'jquery' ), SWP_VERSION, true );
                $script = array(
                    'domain' => site_url(),
                );
                wp_localize_script( 'connector-popup-script', 'connector_popup_script_params',  $script  );
                wp_enqueue_script( 'connector-popup-script' );

                wp_register_style( 'connector-admin-popup-style', plugins_url('assets/css/swp-app-popup.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
                wp_enqueue_style( 'connector-admin-popup-style' );	
                wp_enqueue_media();
            }
        }
        
        /***** function for register popup route *****/
        public function swp_app_register_popup_route(){
                     
        }
        
        
        
        /*****************************************************
        * Check if a given request has access to read items.
        * @param  WP_REST_Request $request Full details about the request.
        * @return WP_Error|boolean
        *****************************************************/
        public function swp_app_check_permissions_for_get_items( $request ) {
            if(is_plugin_active('mobiconnector/mobiconnector.php')){
                $usekey = get_option('mobiconnector_settings-use-security-key');
                if ($usekey == 1 && ! bamobile_mobiconnector_rest_check_post_permissions( $request ) ) {
                    return new WP_Error( 'mobiconnector_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'mobiconnector' ), array( 'status' => rest_authorization_required_code() ) );
                }
            }
            return true;
        }
        
                
        /***** function for retrive value by product category *****/
        public function swp_get_product_category_by_slug( $slug  ) {
            $category = get_term_by( 'slug', $slug, 'product_cat' );
            if ( $category )
                _make_cat_compat( $category );
            return $category;
        }
        
    /***** end class *****/
    }
    $SWPapppopup = new SWPapppopup();
}
