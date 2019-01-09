<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/******************************************************
* @param deals function
* add sections, fields also register
* @return array and string
*******************************************************/

if( !class_exists( 'SWPappdeal' ) ){
    class SWPappdeal{
        
        private $deals;
            
        public function __construct(){
            add_action( 'admin_menu',array($this,'swp_app_deals_submenu'));
            $this->swp_app_register_deals_routes();
            add_action( 'save_post', array( $this, 'swp_app_update_thumbnail_deals'), 100, 2);
            add_action( 'admin_enqueue_scripts', array( $this , 'register_admin_scripts' ));
            add_action( 'admin_post_swp_delete_deal', array( $this, 'swp_app_delete_deal' ) );
            require_once('class-core.php');
        
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
                wp_localize_script( 'swp-app-deals-admin-script', 'swp_app_deals_script',  $script  );
                wp_enqueue_script( 'swp-app-deals-admin-script' );

                wp_register_style( 'swp-app-deals-admin-style', plugins_url('assets/css/swp-app-deals.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );
                wp_enqueue_style( 'swp-app-deals-admin-style' );	
            }
        }

        public function swp_app_render_deals_template(){
            $widthDB = get_option('swp_app_settings_width_deals');
            $heightDB = get_option('swp_app_settings_height_deals');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            ?>
            <div class="wrap appdeal">
                <h1><?php echo __('Deals Setting','swp')?></h1>
                <form accept-charset="UTF-8" action="?page=deals" method="post" id="settings_deal">
                <input type="hidden" name="deal-task" value="deal">
                    <div id='poststuff'>
                        <div id='post-body' class='metabox-holder columns-2'>
                            <div id='post-body-content'>
                                <div class="left">
                                    <table class="widefat sortable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <h3><?php _e( "Deals", "deals" ) ?></h3>
                                                    <p>Maximum 2 deals images can use</p>
                                                 </th>
                                                <th>
                                                    <a id="add-deal" class='button alignright add-deal' data-editor='content' title='<?php _e( "Add Deal", "deals" ) ?>'>
                                                        <span style="background:url('<?php echo admin_url( '/images/media-button.png') ?>') no-repeat top left;" class='wp-media-buttons-icon'></span> <?php _e( "Add Deal", "deals" ) ?>
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="woo-list-deal">
                                            <?php
                                                $this->swp_render_admin_deals();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div class='right'>
                            <div class="appdeal_configuration" id="appdeal_configuration_1">
                                <h4><?php _e('Resize Images for Deals','swp'); ?></h4>
                                <hr>
                                <div class="appdeal_publish">
                                    <div class="appdeal-element"><label for="width-deal" class="appdeal-label"><?php _e('Width','swp'); ?> : </label><input class="appdeal-input" type="number" id="width-deal" name="swp_app_settings_width_deals" value="<?php echo $widthDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                    <div class="appdeal-element"><label for="height-deal" class="appdeal-label"><?php _e('Height','swp'); ?> : </label><input class="appdeal-input" type="number" id="height-deal" name="swp_app_settings_height_deals" value="<?php echo $heightDB ?>"/><span><?php esc_html_e(__('Pixel')); ?></span></div>
                                </div>
                            </div>
                            <div class="appdeal_configuration">
                                <div class="appdeal_publish">
                                    <div class="misc-pub-section misc-pub-post-status"><?php _e( "Status", "swp" ) ?>: <span id="post-status-display"><?php _e( "Published", "swp" ) ?></span></div>
                                    <div class="misc-pub-section misc-pub-visibility" id="visibility"><?php _e( "Visibility", "swp" ) ?>: <span id="post-visibility-display"><?php _e( "Public", "swp" ) ?></span></div>										
                                    <div class="misc-pub-section" id="catalog-visibility"><?php _e( "Catalog visibility", "swp" ) ?>: <strong id="catalog-visibility-display"><?php _e( "Visible", "swp" ) ?></strong></div>
                                </div>
                                <div class='appdeal-configuration'>
                                    <input class='alignright button button-primary' type='submit' name='save' id='ms-save' value='<?php _e( "Save Deals", "swp" ) ?>' />								
                                    <span class="spinner"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }	

        private function swp_app_get_deals() {
            $args = array(
                'force_no_custom_order' => true,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'app_deals',
                'post_status' => 'publish',
                'suppress_filters' => 1,
                'posts_per_page' => -1,
            );
            $deals = get_posts($args);
            return $deals;
        }

        private function swp_render_admin_deals(){
            $deals = $this->swp_app_get_deals();
            foreach($deals as $deal){
                $id = $deal->ID;
                $attachment_id = get_post_thumbnail_id($id);
                $attachment = wp_get_attachment_image_src($attachment_id,'full');
                $deletelink = wp_nonce_url(admin_url('admin-post.php?action=swp_delete_deal&deal_id='.$id),'swp_delete_deal');
                $url = esc_attr( get_post_meta( $id, 'app_deals_link', true ) );
                ?>
            <tr class="app-deals">
                <td class="col-1">
                    <div class="thumb" style="background-image: url(<?php echo $attachment[0]; ?>)">
                        <a title="Delete Deal" class="tipsy-tooltip-top delete-deal dashicons dashicons-trash" href="<?php echo $deletelink; ?>"><?php _e('Delete Deals','swp'); ?></a>				
                    </div>
                    <div class="deals-url-section">
                        <input class="url app-deals-link" type="text" name="swp-app-deal-attachment[<?php echo $id; ?>][url]" placeholder="complate url: Ex- https://www.example.com/< page, post, product-cat, Product-id, external link >" value="<?php echo apply_filters('post_title',$url);?>">
                    </div>
                </td>
            </tr>
                <?php
            }
        }

        public function update_deal(){

        }

        public function swp_app_delete_deal() {
            // check nonce
            check_admin_referer( "swp_delete_deal" );
            $capability = apply_filters( 'appdeal_capability', 'edit_others_posts' );
            if ( ! current_user_can( $capability ) ) {
                return;
            }
            $deal_id = absint( $_GET['deal_id'] );
            // For new format deals - also trash the deal
            if ( get_post_type( $deal_id ) === 'app_deals' ) {
                $id = wp_update_post( array(
                        'ID' => $deal_id,
                        'post_status' => 'trash'
                    )
                );
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector')); 
            wp_redirect( admin_url( "admin.php?page=deals" ) );
        }

        public function swp_app_register_deals_routes() {
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
            $task = isset($_REQUEST['deal-task']) ? $_REQUEST['deal-task'] : '';			
            require_once(SWP_ABSPATH.'/settings/deals/class-app-connector-deals-function.php');
            if(!empty($task) || $task != ''){				
                $this->swp_app_save_deals();		
            }	
        }

        
        public function swp_app_get_deals_size_thumbnail(){
            $widthDB = get_option('swp_app_settings_width_deals');
            $heightDB = get_option('swp_app_settings_height_deals');
            if(empty($widthDB) && empty($heightDB)){
                $widthDB = 752;
                $heightDB = 564;
            }
            return array(
                'swp_app_deals_large' => array(
                    'width' => $widthDB,
                    'height' => $heightDB
                )
            );
        }

        public function swp_app_update_thumbnail_deals($post_ID, $post) {
            $post_thumbnail_id = get_post_thumbnail_id( $post_ID );
            if(empty($post_thumbnail_id))
                return true;
            
            $swp_app_deals_large = get_post_meta($post_thumbnail_id, 'swp_app_deals_large', true);
            if(!empty($swp_app_deals_large))
                return true; 
            
            $relative_pathto_file = get_post_meta( $post_thumbnail_id, '_wp_attached_file', true);
            $wp_upload_dir = wp_upload_dir();
            $absolute_pathto_file = $wp_upload_dir['basedir'].'/'.$relative_pathto_file;
            
            if(!file_exists($absolute_pathto_file))
                return true; 
            
            $path_parts = pathinfo($relative_pathto_file);
            $ext = strtolower($path_parts['extension']);
            $basename = strtolower($path_parts['basename']);
            $dirname = strtolower($path_parts['dirname']);
            $filename = strtolower($path_parts['filename']);
             
            $thumbnails = $this->swp_app_get_deals_size_thumbnail();
            foreach($thumbnails as $key => $value){
                $path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
                $dest = $wp_upload_dir['basedir'].'/'.$path;
                SWPresizeimage:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
                
                update_post_meta ($post_thumbnail_id, $key, $path);
            }
            return true;
        }

        public function swp_app_save_deals(){
            global $wpdb;
            $attachments = @$_POST['swp-app-deal-attachment'];
            foreach($attachments as $post_id => $value){
                $update_to_post = array(
                    'ID'           => $post_id,
                    'post_excerpt' => $value['post_excerpt'],
                );
                wp_update_post($update_to_post);
                update_post_meta($post_id,'app_deals_link',$value['url']);
            }
            $width = @$_POST['swp_app_settings_width_deals'];
            $height = @$_POST['swp_app_settings_height_deals'];
            $widthDB = get_option('swp_app_settings_width_deals');
            $heightDB = get_option('swp_app_settings_height_deals');
            $checkfirst = get_option('swp_app_settings-deal-first-settings');
            if($checkfirst == 1){
                if(!empty($width) && !empty($height)){
                    if($width != $widthDB || $height != $heightDB){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_deals_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_settings_width_deals',$width);
                        update_option('swp_app_settings_height_deals',$height);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }
                }else{
                    if($widthDB != 752 && $heightDB != 564){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_deals_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_settings_width_deals',752);
                        update_option('swp_app_settings_height_deals',564);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }
                }
            }else{
                if(empty($attachments)){
                    if(!empty($width) && !empty($height)){
                        update_option('swp_app_settings_width_deals',$width);
                        update_option('swp_app_settings_height_deals',$height);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }else{
                        update_option('swp_app_settings_width_deals',752);
                        update_option('swp_app_settings_height_deals',564);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }
                }else{
                    if(!empty($width) && !empty($height)){
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_deals_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_settings_width_deals',$width);
                        update_option('swp_app_settings_height_deals',$height);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }else{
                        $sql = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = "swp_app_deals_large"';
                        $wpdb->query($sql);
                        update_option('swp_app_settings_width_deals',752);
                        update_option('swp_app_settings_height_deals',564);
                        update_option('swp_app_settings-deal-first-settings',1);
                    }
                }
            }
    //		swp_app_add_notice(__('Successfully Update','mobiconnector'));   
            wp_redirect( admin_url( "admin.php?page=deals" ) );
        }
    }
    $SWPappdeal = new SWPappdeal();
}