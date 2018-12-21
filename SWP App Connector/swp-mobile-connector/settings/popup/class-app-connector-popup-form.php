<?php

if ( ! defined( 'ABSPATH' ) ) {
    /***** EXIT if access directly *****/
    exit;
}

/*****************************************************
* @param setting for popup front view
* create left and right table form
* @retun array and string
* 
*****************************************************/
        
    $popup =  get_option('wooconnector-popup-homepage');
    $link = get_option('wooconnector-popup-homepage-link');
    ?>
    <div class="wrap connector-popup">
        <h1><?php echo __('Mobile Popup','swp')?></h1>
        <?php
    //		bamobile_mobiconnector_print_notices();
        ?>
        <form accept-charset="UTF-8" action="<?php echo admin_url().'/admin-post.php?action=woo_update_popup'; ?>" class="form-horizontal" method="post" id="settings_popup">
            <input type="hidden" name="action" value="woo_update_popup">
            <?php wp_nonce_field( 'woo_update_popup' ); ?>
            <hr>        
            <div id='poststuff'>
                <div id='post-body' class=''>
                    <div id='post-body-content'>

                        <div class='connector-hold'>
                            <div id="connector-content-popup" class="<?php if(!empty($popup)){echo '';}else{echo 'connector-hidden';} ?>">
                                <div id="app-overlay-popup"></div>
                                <div id="app-content-popup-div"><div id="connector-content-popup-close"><span class="app-content-close-button"></span></div><img id="app-content-popup" src="<?php if(!empty($popup)){echo $popup; }else{echo '';} ?>"/></div>
                                <input type="hidden" name="connector-popup-url" id="connector-popup-url" value="<?php if(!empty($popup)){echo $popup; }else{echo '';} ?>"/>
                            </div>
                        </div><!-- 'connector-hold' -->
                    </div>
                </div>
            </div>
            <div id="app-postbox-container-2" class="app-postbox-container">
                <div class="right">
                    <div class="connector-popup_configuration">
                        <h3 id="connector-settings-popup"><?php _e('Popup Settings','swp'); ?></h3>
                        <hr>
                        <div class="connector-popup_settings_field">
                            <div class="connector-form-control" id="popup-options">
                                <a id="app-add-popup" class="button"><span style="background:url('<?php echo admin_url( '/images/media-button.png') ?>') no-repeat top left;" class="wp-media-buttons-icon"></span> <?php _e('Add Image','swp'); ?></a>
                                <a id="connector-delete-popup" class="button <?php if(!empty($popup)){echo '';}else{echo 'connector-hidden';} ?>"><?php _e('Delete Photo','swp'); ?></a>
                            </div>
                            <div class="connector-form-control">
                                <label for="connector_check_popup_link" id="connector_check_popup_link_label"><?php _e('Link','swp'); ?></label>
                                <input type="text" name="wooconnector_popup_link" id="connector_check_popup_link" value="<?php echo $link; ?>"/>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div id="app-postbox-container-1" class="app-postbox-container">
                <div class='right'>
                    <div class="connector-popup_configuration">
                        <h3 id="connector-settings-popup"><?php _e('Publish','swp'); ?></h3>
                        <hr>
                        <div class="connector-popup_publish">
                            <div class="misc-pub-section misc-pub-post-status"><?php _e( "Status", "swp" ) ?>: <span id="post-status-display"><?php _e( "Published", "swp" ) ?></span></div>
                            <div class="misc-pub-section misc-pub-visibility" id="visibility"><?php _e( "Visibility", "swp" ) ?>: <span id="post-visibility-display"><?php _e( "Public", "swp" ) ?></span></div>										
                            <div class="misc-pub-section" id="catalog-visibility"><?php _e( "Catalog visibility", "swp" ) ?>: <strong id="catalog-visibility-display"><?php _e( "Visible", "swp" ) ?></strong></div>
                        </div>
                        <div class='connector-popup-configuration'>
                            <input class='alignright button button-primary' type='submit' name='save' id='ms-save' value='<?php _e( "Save", "swp" ) ?>' />								
                            <span class="spinner"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
