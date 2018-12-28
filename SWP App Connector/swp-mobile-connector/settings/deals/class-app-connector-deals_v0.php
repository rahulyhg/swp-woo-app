<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/******************************************************
* @param slider function
* add sections, fields also register
* @return
*******************************************************/

if( !class_exists( 'SWPappdeal' ) ){
    class SWPappdeal{
        
        private $slides;
        
        public function __construct(){
            add_action( 'admin_menu',array($this,'swp_app_deals_submenu'));
            $this->swp_app_register_slider_routes();
            add_action( 'save_post', array( $this, 'swp_app_update_thumnail_deals'), 100, 2);
            add_action( 'admin_enqueue_scripts', array( $this , 'register_admin_scripts' ));
            add_action( 'admin_post_woo_delete_slide', array( $this, 'swp_app_delete_deal' ) );
        }

        public function register_admin_scripts() {
            if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'deals'){
                wp_enqueue_media();
                wp_enqueue_script( 'swp-app-deals-admin-script', plugins_url('assets/js/swp-app-deals.js',SWP_PLUGIN_FILE), array( 'jquery' ), SWP_VERSION, true );
                $script = array(
                    'domain' => site_url(),
                    'baseurl' => ABSPATH,
                    'ajax_url' => plugins_url('/settings/deals/swp-app-connector-deals-ajax.php',SWP_PLUGIN_FILE),
                );
                wp_localize_script( 'swp-app-deals-admin-script', 'wooconnector_slider_script_params',  $script  );
                wp_enqueue_script( 'swp-app-deals-admin-script' );

                wp_register_style( 'swp-app-deals-admin-style', plugins_url('assets/css/swp-app-deals.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
                wp_enqueue_style( 'swp-app-deals-admin-style' );	
            }
        }

        public function swp_app_render_deals_template(){
            $widthDB = get_option('wooconnector_settings-width-slider');
            $heightDB = get_option('wooconnector_settings-height-slider');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            ?>
            <div class="wrap wooslider">
                <h1><?php echo __('Deals Setting','wooconnector')?></h1>
                <form accept-charset="UTF-8" action="?page=deals" method="post" id="settings_slider">
                <input type="hidden" name="wootask" value="slide">
                    <div id='poststuff'>
                        <div id='post-body' class='metabox-holder columns-2'>
                            <div id='post-body-content'>
                                <div class="left">
                                    <table class="widefat sortable">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">
                                                    <h3><?php _e( "Deals", "deals" ) ?></h3>
                                                </th>
                                                <th>
                                                    <a id="add-slide" class='button alignright add-slide' data-editor='content' title='<?php _e( "Add Slide", "deals" ) ?>'>
                                                        <span style="background:url('<?php echo admin_url( '/images/media-button.png') ?>') no-repeat top left;" class='wp-media-buttons-icon'></span> <?php _e( "Add Slide", "deals" ) ?>
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="woo-list-slide">
                                            <?php
                                                $this->render_admin_slides();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div class='right'>
                            <div class="wooslider_configuration" id="wooslider_configuration_1">
                                <h4><?php _e('Resize Images for Slider','wooconnector'); ?></h4>
                                <hr>
                                <div class="wooslider_publish">
                                    <div class="wooslider-element"><label for="width-slider" class="wooslider-label"><?php _e('Width','wooconnector'); ?> : </label><input class="wooslider-input" type="number" id="width-slider" name="wooconnector_settings-width-slider" value="<?php echo $widthDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                    <div class="wooslider-element"><label for="height-slider" class="wooslider-label"><?php _e('Height','wooconnector'); ?> : </label><input class="wooslider-input" type="number" id="height-slider" name="wooconnector_settings-height-slider" value="<?php echo $heightDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                </div>
                            </div>
                            <div class="wooslider_configuration">
                                <div class="wooslider_publish">
                                    <div class="misc-pub-section misc-pub-post-status"><?php _e( "Status", "wooconnector" ) ?>: <span id="post-status-display"><?php _e( "Published", "wooconnector" ) ?></span></div>
                                    <div class="misc-pub-section misc-pub-visibility" id="visibility"><?php _e( "Visibility", "wooconnector" ) ?>: <span id="post-visibility-display"><?php _e( "Public", "wooconnector" ) ?></span></div>										
                                    <div class="misc-pub-section" id="catalog-visibility"><?php _e( "Catalog visibility", "wooconnector" ) ?>: <strong id="catalog-visibility-display"><?php _e( "Visible", "wooconnector" ) ?></strong></div>
                                </div>
                                <div class='wooslider-configuration'>
                                    <input class='alignright button button-primary' type='submit' name='save' id='ms-save' value='<?php _e( "Save Sliders", "wooconnector" ) ?>' />								
                                    <span class="spinner"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }	

        private function swp_app_get_slides() {
            $args = array(
                'force_no_custom_order' => true,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'wooslide',
                'post_status' => 'publish',
                'suppress_filters' => 1,
                'posts_per_page' => -1,
            );
            $slides = get_posts($args);
            return $slides;
        }

        private function render_admin_slides(){
            $slides = $this->swp_app_get_slides();
            foreach($slides as $slide){
                $id = $slide->ID;
                $attachment_id = get_post_thumbnail_id($id);
                $attachment = wp_get_attachment_image_src($attachment_id,'thumbnail');
                $deletelink = wp_nonce_url(admin_url('admin-post.php?action=woo_delete_slide&slide_id='.$id),'woo_delete_slide');
                $url = esc_attr( get_post_meta( $id, 'woo-slider_url', true ) );
                $caption = esc_textarea( $slide->post_excerpt );
                ?>
            <tr class="woo-slide">
                
                <td class="col-2 col-1">
                    <div class="thumb" style="background-image: url(<?php echo $attachment[0]; ?>)">
                        <a title="Delete slide" class="tipsy-tooltip-top delete-slide dashicons dashicons-trash" href="<?php echo $deletelink; ?>"><?php _e('Delete slide','wooconnector'); ?></a>				
                    </div>
                    <div class="deals-url-section">
                        <input class="url wooconnector_slider_url" type="text" name="woo-slide-attachment[<?php echo $id; ?>][url]" placeholder="link://product/<product_id>" value="<?php echo apply_filters('post_title',$url);?>">
                    </div>
                </td>
            </tr>
                <?php
            }
        }

        public function update_slide(){

        }

        public function swp_app_delete_deal() {
            // check nonce
            check_admin_referer( "woo_delete_slide" );
            $capability = apply_filters( 'wooslider_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return;
            }
            $slide_id = absint( $_GET['slide_id'] );
            // For new format slides - also trash the slide
            if ( get_post_type( $slide_id ) === 'wooslide' ) {
                $id = wp_update_post( array(
                        'ID' => $slide_id,
                        'post_status' => 'trash'
                    )
                );
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector')); 
            wp_redirect( admin_url( "admin.php?page=deals" ) );
        }

        public function swp_app_register_slider_routes() {
              }

        
        /**
         * Check if a given request has access to read items.
         *
         * @param  WP_REST_Request $request Full details about the request.
         * @return WP_Error|boolean
         */
        public function get_items_permissions_check( $request ) {
            if(is_plugin_active('mobiconnector/mobiconnector.php')){
                $usekey = get_option('mobiconnector_settings-use-security-key');
                if ($usekey == 1 && ! bamobile_mobiconnector_rest_check_post_permissions( $request ) ) {
                    return new WP_Error( 'mobiconnector_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'mobiconnector' ), array( 'status' => rest_authorization_required_code() ) );
                }
            }
            return true;
        }

        public function swp_app_deals_submenu(){
            
            $parent_slug = 'appconnector-settings';
            add_submenu_page(
                $parent_slug,
                __('Deals Setting'),
                __('Deals Setting'),
                'manage_options',
                'deals',
                array($this,'swp_app_deals_action')
            );
        }

        public function swp_app_deals_action(){
            $task = isset($_REQUEST['wootask']) ? $_REQUEST['wootask'] : '';			
            require_once(SWP_ABSPATH.'/settings/deals/class-app-connector-deals-function.php');
            if(!empty($task) || $task != ''){				
                $this->swp_app_save_deals();		
            }	
        }

        
        public function swp_app_get_deals_size_thumbnail(){
            $widthDB = get_option('wooconnector_settings-width-slider');
            $heightDB = get_option('wooconnector_settings-height-slider');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            return array(
                'wooconnector_slider_large' => array(
                    'width' => $widthDB,
                    'height' => $heightDB
                )
            );
        }

        public function swp_app_update_thumnail_deals($post_ID, $post) {
            $post_thumbnail_id = get_post_thumbnail_id( $post_ID );
            if(empty($post_thumbnail_id))
                return true;
            /// ki?m tra xem d� t?n t?i thumnail chua
            $wooconnector_slider_large = get_post_meta($post_thumbnail_id, 'wooconnector_slider_large', true);
            if(!empty($wooconnector_slider_large))
                return true; // d� t?n t?i r?i ko t?o n?a
            // l?y th�ng tin c?a ?nh
            $relative_pathto_file = get_post_meta( $post_thumbnail_id, '_wp_attached_file', true);
            $wp_upload_dir = wp_upload_dir();
            $absolute_pathto_file = $wp_upload_dir['basedir'].'/'.$relative_pathto_file;
            // ki?m tra file g?c c� t?n t?i hay kh�ng?
            if(!file_exists($absolute_pathto_file))
                return true; // file ko t?n t?i
            ////////////////

            $path_parts = pathinfo($relative_pathto_file);
            $ext = strtolower($path_parts['extension']);
            $basename = strtolower($path_parts['basename']);
            $dirname = strtolower($path_parts['dirname']);
            $filename = strtolower($path_parts['filename']);
            // t?o ?nh 
            $thumbnails = $this->swp_app_get_deals_size_thumbnail();
            foreach($thumbnails as $key => $value){
                $path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
                $dest = $wp_upload_dir['basedir'].'/'.$path;
                WooConnectorCore:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
                // c?p nh?t post meta for thumnail
                update_post_meta ($post_thumbnail_id, $key, $path);
            }
            return true;
        }

        public function swp_app_save_deals(){
            global $wpdb;
            $attachments = @$_POST['woo-slide-attachment'];
            foreach($attachments as $post_id => $value){
                $update_to_post = array(
                    'ID'           => $post_id,
                    'post_excerpt' => $value['post_excerpt'],
                );
                wp_update_post($update_to_post);
                update_post_meta($post_id,'woo-slider_url',$value['url']);
            }
            $width = @$_POST['wooconnector_settings-width-slider'];
            $height = @$_POST['wooconnector_settings-height-slider'];
            $widthDB = get_option('wooconnector_settings-width-slider');
            $heightDB = get_option('wooconnector_settings-height-slider');
            $checkfirst = get_option('wooconnector_settings-slider-first-settings');
            if($checkfirst == 1){
                if(!empty($width) && !empty($height)){
                    if($width != $widthDB || $height != $heightDB){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "wooconnector_slider_large"';
                        $wpdb->query($sql);
                        update_option('wooconnector_settings-width-slider',$width);
                        update_option('wooconnector_settings-height-slider',$height);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }
                }else{
                    if($widthDB != 752 && $heightDB != 564){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "wooconnector_slider_large"';
                        $wpdb->query($sql);
                        update_option('wooconnector_settings-width-slider',752);
                        update_option('wooconnector_settings-height-slider',564);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }
                }
            }else{
                if(empty($attachments)){
                    if(!empty($width) && !empty($height)){
                        update_option('wooconnector_settings-width-slider',$width);
                        update_option('wooconnector_settings-height-slider',$height);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }else{
                        update_option('wooconnector_settings-width-slider',752);
                        update_option('wooconnector_settings-height-slider',564);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }
                }else{
                    if(!empty($width) && !empty($height)){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "wooconnector_slider_large"';
                        $wpdb->query($sql);
                        update_option('wooconnector_settings-width-slider',$width);
                        update_option('wooconnector_settings-height-slider',$height);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }else{
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "wooconnector_slider_large"';
                        $wpdb->query($sql);
                        update_option('wooconnector_settings-width-slider',752);
                        update_option('wooconnector_settings-height-slider',564);
                        update_option('wooconnector_settings-slider-first-settings',1);
                    }
                }
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector'));   
            wp_redirect( admin_url( "admin.php?page=deals" ) );
        }
    }
    $SWPappdeal = new SWPappdeal();
}