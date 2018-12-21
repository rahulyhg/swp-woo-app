<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/******************************************************
* @param slider function
* add sections, fields also register
* @return
*******************************************************/

if( !class_exists( 'SWPappslider' ) ){
    class SWPappslider{
        
        private $slides;
        
        public function __construct(){
            add_action( 'admin_menu',array($this,'swp_app_slider_submenu'));
            $this->swp_app_register_slider_routes();
            add_action( 'save_post', array( $this, 'swp_app_update_thumnail_slider'), 100, 2);
            add_action( 'admin_enqueue_scripts', array( $this , 'register_admin_scripts' ));
            add_action( 'admin_post_app_delete_slide', array( $this, 'swp_app_delete_slide' ) );
            require_once('class-core.php');
        }

        public function register_admin_scripts() {
            if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'app-slider'){
                wp_enqueue_media();
                wp_enqueue_script( 'swp-app-slider-script', plugins_url('assets/js/swp-app-slider.js',SWP_PLUGIN_FILE), array( 'jquery' ), SWP_VERSION, true );
                $script = array(
                    'domain' => site_url(),
                    'baseurl' => ABSPATH,
                    'ajax_url' => plugins_url('/settings/slider/class-app-slider-ajax.php',SWP_PLUGIN_FILE),
                );
                wp_localize_script( 'swp-app-slider-script', 'swp_app_slider_script_params',  $script  );
                wp_enqueue_script( 'swp-app-slider-script' );

                wp_register_style( 'swp-app-slider-style', plugins_url('assets/css/swp-app-slider.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
                wp_enqueue_style( 'swp-app-slider-style' );	
            }
        }

        public function render_template_admin(){
            $widthDB = get_option('swp_app_slider-width-settings');
            $heightDB = get_option('swp_app_slider_height_settings');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            ?>
            <div class="wrap appslider">
                <h1><?php echo __('Slider Setting','swp')?></h1>
                <form accept-charset="UTF-8" action="?page=app-slider" method="post" id="settings_slider">
                <input type="hidden" name="slidertask" value="slide">
                    <div id='poststuff'>
                        <div id='post-body' class='metabox-holder columns-2'>
                            <div id='post-body-content'>
                                <div class="left">
                                    <table class="widefat sortable">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">
                                                    <h3><?php _e( "Slides", "app-slider" ) ?></h3>
                                                </th>
                                                <th>
                                                    <a id="add-slide" class='button alignright add-slide' data-editor='content' title='<?php _e( "Add Slide", "app-slider" ) ?>'>
                                                        <span style="background:url('<?php echo admin_url( '/images/media-button.png') ?>') no-repeat top left;" class='wp-media-buttons-icon'></span> <?php _e( "Add Slide", "app-slider" ) ?>
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="app-list-slide">
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
                            <div class="appslider_configuration" id="appslider_configuration_1">
                                <h4><?php _e('Resize Images for Slider','swp'); ?></h4>
                                <hr>
                                <div class="appslider_publish">
                                    <div class="appslider-element"><label for="width-slider" class="appslider-label"><?php _e('Width','swp'); ?> : </label><input class="appslider-input" type="number" id="width-slider" name="swp_app_slider-width-settings" value="<?php echo $widthDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                    <div class="appslider-element"><label for="height-slider" class="appslider-label"><?php _e('Height','swp'); ?> : </label><input class="appslider-input" type="number" id="height-slider" name="swp_app_slider_height_settings" value="<?php echo $heightDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                </div>
                            </div>
                            <div class="appslider_configuration">
                                <div class="appslider_publish">
                                    <div class="misc-pub-section misc-pub-post-status"><?php _e( "Status", "swp" ) ?>: <span id="post-status-display"><?php _e( "Published", "swp" ) ?></span></div>
                                    <div class="misc-pub-section misc-pub-visibility" id="visibility"><?php _e( "Visibility", "swp" ) ?>: <span id="post-visibility-display"><?php _e( "Public", "swp" ) ?></span></div>										
                                    <div class="misc-pub-section" id="catalog-visibility"><?php _e( "Catalog visibility", "swp" ) ?>: <strong id="catalog-visibility-display"><?php _e( "Visible", "swp" ) ?></strong></div>
                                </div>
                                <div class='appslider-configuration'>
                                    <input class='alignright button button-primary' type='submit' name='save' id='ms-save' value='<?php _e( "Save Sliders", "swp" ) ?>' />								
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
                'post_type' => 'slider_slide',
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
                $deletelink = wp_nonce_url(admin_url('admin-post.php?action=app_delete_slide&slide_id='.$id),'app_delete_slide');
                $url = esc_attr( get_post_meta( $id, 'app_slider_url', true ) );
                $caption = esc_textarea( $slide->post_excerpt );
                ?>
            <tr class="app-slide">
                <td class="col-1 ui-sortable-handle">
                    <div class="thumb" style="background-image: url(<?php echo $attachment[0]; ?>)">
                        <a title="Delete slide" class="tipsy-tooltip-top delete-slide dashicons dashicons-trash" href="<?php echo $deletelink; ?>"><?php _e('Delete slide','swp'); ?></a>				
                    </div>
                </td>
                <td class="col-2">
                    <ul class="tabs"><li class="selected" rel="tab-0"><?php _e('General','swp'); ?></li></ul>
                    <div class="tabs-content">
                        <div class="tab tab-0">
                            <textarea class="app_slider_textarea" name="app-slide-attachment[<?php echo $id; ?>][post_excerpt]" placeholder="Caption"><?php echo apply_filters('post_title',$caption); ?></textarea>
                            <input class="url app_slider_url" type="text" name="app-slide-attachment[<?php echo $id; ?>][url]" placeholder="link://product/<product_id>" value="<?php echo apply_filters('post_title',$url);?>">
                            <div class="support-input">
                                <div class="symb-support-input">
                                    <span>?</span>
                                </div>
                                <div class="tooltip-support-input">
                                    <?php echo sprintf(__('When user click this picture, the URL will be openned. %s','mobiconnector'),'<a target="_blank" href="https://taydoapp.com/knowledge-base/how-to-add-link-for-a-image-in-slideshow/">Read More</a>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
                <?php
            }
        }

        public function update_slide(){

        }

        public function swp_app_delete_slide() {
            // check nonce
            check_admin_referer( "app_delete_slide" );
            $capability = apply_filters( 'appslider_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return;
            }
            $slide_id = absint( $_GET['slide_id'] );
            // For new format slides - also trash the slide
            if ( get_post_type( $slide_id ) === 'slider_slide' ) {
                $id = wp_update_post( array(
                        'ID' => $slide_id,
                        'post_status' => 'trash'
                    )
                );
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector')); 
            wp_redirect( admin_url( "admin.php?page=app-slider" ) );
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

        public function swp_app_slider_submenu(){
            
            $parent_slug = 'appconnector-settings';
            add_submenu_page(
                $parent_slug,
                __('Slider Setting'),
                __('Slider Setting'),
                'manage_options',
                'app-slider',
                array($this,'swp_app_slider_action')
            );
        }

        public function swp_app_slider_action(){
            $task = isset($_REQUEST['slidertask']) ? $_REQUEST['slidertask'] : '';			
            require_once(SWP_ABSPATH.'/settings/slider/class-app-slider-function.php');
            if(!empty($task) || $task != ''){				
                $this->swp_app_save_slider();		
            }	
        }

        
        public function swp_app_get_size_thumbnail(){
            $widthDB = get_option('swp_app_slider-width-settings');
            $heightDB = get_option('swp_app_slider_height_settings');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            return array(
                'swp_app_slider_large' => array(
                    'width' => $widthDB,
                    'height' => $heightDB
                )
            );
        }

        public function swp_app_update_thumnail_slider($post_ID, $post) {
            $post_thumbnail_id = get_post_thumbnail_id( $post_ID );
            if(empty($post_thumbnail_id))
                return true;
            /// ki?m tra xem d� t?n t?i thumnail chua
            $swp_app_slider_large = get_post_meta($post_thumbnail_id, 'swp_app_slider_large', true);
            if(!empty($swp_app_slider_large))
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
            $thumbnails = $this->swp_app_get_size_thumbnail();
            foreach($thumbnails as $key => $value){
                $path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
                $dest = $wp_upload_dir['basedir'].'/'.$path;
                SWPresizesliderimage:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
                // c?p nh?t post meta for thumnail
                update_post_meta ($post_thumbnail_id, $key, $path);
            }
            return true;
        }

        public function swp_app_save_slider(){
            global $wpdb;
            $attachments = @$_POST['app-slide-attachment'];
            foreach($attachments as $post_id => $value){
                $update_to_post = array(
                    'ID'           => $post_id,
                    'post_excerpt' => $value['post_excerpt'],
                );
                wp_update_post($update_to_post);
                update_post_meta($post_id,'app_slider_url',$value['url']);
            }
            $width = @$_POST['swp_app_slider-width-settings'];
            $height = @$_POST['swp_app_slider_height_settings'];
            $widthDB = get_option('swp_app_slider-width-settings');
            $heightDB = get_option('swp_app_slider_height_settings');
            $checkfirst = get_option('swp_app_slider_first_settings');
            if($checkfirst == 1){
                if(!empty($width) && !empty($height)){
                    if($width != $widthDB || $height != $heightDB){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_slider_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_slider-width-settings',$width);
                        update_option('swp_app_slider_height_settings',$height);
                        update_option('swp_app_slider_first_settings',1);
                    }
                }else{
                    if($widthDB != 752 && $heightDB != 564){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_slider_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_slider-width-settings',752);
                        update_option('swp_app_slider_height_settings',564);
                        update_option('swp_app_slider_first_settings',1);
                    }
                }
            }else{
                if(empty($attachments)){
                    if(!empty($width) && !empty($height)){
                        update_option('swp_app_slider-width-settings',$width);
                        update_option('swp_app_slider_height_settings',$height);
                        update_option('swp_app_slider_first_settings',1);
                    }else{
                        update_option('swp_app_slider-width-settings',752);
                        update_option('swp_app_slider_height_settings',564);
                        update_option('swp_app_slider_first_settings',1);
                    }
                }else{
                    if(!empty($width) && !empty($height)){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_slider_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_slider-width-settings',$width);
                        update_option('swp_app_slider_height_settings',$height);
                        update_option('swp_app_slider_first_settings',1);
                    }else{
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_slider_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_slider-width-settings',752);
                        update_option('swp_app_slider_height_settings',564);
                        update_option('swp_app_slider_first_settings',1);
                    }
                }
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector'));   
            wp_redirect( admin_url( "admin.php?page=app-slider" ) );
        }
    }
    $SWPappslider = new SWPappslider();
}