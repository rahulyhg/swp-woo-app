<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class swp_product_cat_on_mobile{
    
    function __construct(){
        add_action('product_cat_add_form_fields', array( $this, 'swp_add_category_hide_on_mobile_meta_field' ), 10, 1);
        add_action('product_cat_edit_form_fields', array( $this, 'swp_edit_category_hide_on_mobile_meta_field' ), 10, 1);
       // add_action('create_category', array ( $this, 'swp_save_category_hide_on_mobile_meta_field' ), 10, 1);
        add_action('edited_product_cat', array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ), 10, 1);
        add_action('create_product_cat', array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ), 10, 1);
        add_action('create_term',array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ));
        
        add_action( 'product_cat_add_form_fields', array ( $this, 'swp_add_category_image' ), 10, 2 );
        add_action( 'created_product_cat', array ( $this, 'save_category_image' ), 10, 2 );
        add_action( 'product_cat_edit_form_fields', array ( $this, 'swp_update_category_image' ), 10, 2 );
        add_action( 'edited_product_cat', array ( $this, 'swp_updated_category_image' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'swp_load_media' ) );
        add_action( 'admin_footer', array ( $this, 'swp_add_script' ) );

        add_action( 'rest_api_init', array( $this , 'swp_hide_product_category' ) );
        
    }

    //Product Cat Create page
    function swp_add_category_hide_on_mobile_meta_field() {
        ?>   
        <div class="form-field">
            <label for="swp_cat_hide_on_mobile"><?php _e('Hide On Mobile App', 'swp'); ?></label>
            <input type="checkbox" name="swp_cat_hide_on_mobile" id="swp_cat_hide_on_mobile" value="1" >
            <p class="description"><?php _e(' Mobile App can access category if checkbox is enable ', 'swp'); ?></p>
        </div>
            
        <?php
    }
 
    //Product Cat Edit page
    function swp_edit_category_hide_on_mobile_meta_field($term) {
        //getting term ID
        $swp_cat_hide_on_mobile = get_term_meta( $term->term_id, 'swp_cat_hide_on_mobile',true);
         ?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="swp_cat_hide_on_mobile"><?php _e( 'Hide On Mobile App', 'swp' ); ?></label>
            </th>
            <td>
                <input type="checkbox" id="swp_cat_hide_on_mobile" name="swp_cat_hide_on_mobile" value="1" <?php echo ( $swp_cat_hide_on_mobile ) ? checked( $swp_cat_hide_on_mobile, '1' ) : ''; ?>/>
                
                <p class="description"><?php _e(' Mobile App can access category if checkbox is enable ', 'swp'); ?></p>
            </td>
        </tr>
        
        <?php  
    }
    
        
    function swp_save_category_hide_on_mobile_meta_field($term_id){
         
       // $swp_cat_hide_on_mobile = $_POST['swp_cat_hide_on_mobile'];
       // add_term_meta( $term_id, 'swp_cat_hide_on_mobile', $swp_cat_hide_on_mobile, true );
   
    }
    
// Save extra taxonomy fields callback function.
    function swp_save_category_taxonomy_hide_on_mobile( $term_id ) {
        
        if ( isset( $_POST[ 'swp_cat_hide_on_mobile' ] ) ) {
        update_term_meta( $term_id, 'swp_cat_hide_on_mobile', '1' );
        } else {
        update_term_meta( $term_id, 'swp_cat_hide_on_mobile', '' );
        }
        
    }
    

    // taxonomy fields for adding image thumbnail
    
    function swp_load_media() {
     wp_enqueue_media();
    }

     /*
      * Add a form field in the new category page
      * @since 1.0.0
     */
      function swp_add_category_image ( $taxonomy ) { ?>
       <div class="form-field term-group">
         <label for="category-image-id"><?php _e('Mobile App Thumbnail', 'swp'); ?></label>
         <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
         <div id="category-image-wrapper"></div>
         <p>
           <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'swp' ); ?>" />
           <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'swp' ); ?>" />
        </p>
       </div>
     <?php
     }

     /*
      * Save the form field
      * @since 1.0.0
     */
     function save_category_image ( $term_id, $tt_id ) {
       if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
         $image = $_POST['category-image-id'];
         add_term_meta( $term_id, 'category-image-id', $image, true );
       }
     }

     /*
      * Edit the form field
      * @since 1.0.0
     */
     function swp_update_category_image ( $term, $taxonomy ) { ?>
       <tr class="form-field term-group-wrap">
         <th scope="row">
           <label for="category-image-id"><?php _e( 'Mobile App Thumbnail', 'swp' ); ?></label>
         </th>
         <td>
           <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); 
            $image_url = get_term_meta ( $term -> term_id, 'category-image-url', true ); 
              $image_url =  wp_get_attachment_url( $image_id ); 
             ?>
           <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo $image_url; ?>">
           <div id="category-image-wrapper">
             <?php if ( $image_id ) { ?>
               <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
             <?php } ?>
           </div>
           <p>
             <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'swp' ); ?>" />
             <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'swp' ); ?>" />
           </p>
         </td>
       </tr>
     <?php
     }

    /*
     * Update the form field value
     * @since 1.0.0
     */
     function swp_updated_category_image ( $term_id, $tt_id ) {
       if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
         $image = $_POST['category-image-id'];
         $image_url =  wp_get_attachment_url( $image );
         update_term_meta ( $term_id, 'category-image-id', $image);
         update_term_meta ( $term_id, 'category-image-url', $image_url);
       } else {
         update_term_meta ( $term_id, 'category-image-id', '' );
         update_term_meta ( $term_id, 'category-image-url', '' );
       }
     }

    /*
     * Add script
     * @since 1.0.0
     */
     function swp_add_script() { ?>
       <script>
         jQuery(document).ready( function($) {
           function ct_media_upload(button_class) {
             var _custom_media = true,
             _orig_send_attachment = wp.media.editor.send.attachment;
             $('body').on('click', button_class, function(e) {
               var button_id = '#'+$(this).attr('id');
               var send_attachment_bkp = wp.media.editor.send.attachment;
               var button = $(button_id);
               _custom_media = true;
               wp.media.editor.send.attachment = function(props, attachment){
                 if ( _custom_media ) {
                   $('#category-image-id').val(attachment.id);
                   $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                   $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                 } else {
                   return _orig_send_attachment.apply( button_id, [props, attachment] );
                 }
                }
             wp.media.editor.open(button);
             return false;
           });
         }
         ct_media_upload('.ct_tax_media_button.button'); 
         $('body').on('click','.ct_tax_media_remove',function(){
           $('#category-image-id').val('');
           $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
         });
         $(document).ajaxComplete(function(event, xhr, settings) {
           var queryStringArr = settings.data.split('&');
           if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
             var xml = xhr.responseXML;
             $response = $(xml).find('term_id').text();
             if($response!=""){
               // Clear the thumb image
               $('#category-image-wrapper').html('');
             }
           }
         });
       });
     </script>
     <?php }

    
}
$swp_product_cat_on_mobile = new swp_product_cat_on_mobile();

?>