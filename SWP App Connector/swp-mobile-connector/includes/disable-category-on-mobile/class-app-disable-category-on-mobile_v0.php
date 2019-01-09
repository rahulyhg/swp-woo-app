<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class swp_product_cat_on_mobile{
    
    function __construct(){
        add_action('product_cat_add_form_fields', array( $this, 'swp_add_category_hide_on_mobile_meta_field' ), 10, 1);
        add_action('product_cat_edit_form_fields', array( $this, 'swp_edit_category_hide_on_mobile_meta_field' ), 10, 1);
        add_action('create_category', array ( $this, 'swp_save_category_hide_on_mobile_meta_field' ), 10, 1);
        add_action('edited_product_cat', array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ), 10, 1);
        add_action('create_product_cat', array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ), 10, 1);
        add_action('create_term',array( $this, 'swp_save_category_taxonomy_hide_on_mobile' ));
        add_action( 'rest_api_init', array( $this , 'swp_hide_product_category' ) );
            
    }

    //Product Cat Create page
    function swp_add_category_hide_on_mobile_meta_field() {
        ?>   
        <div class="form-field">
            <label for="app_mobile_category_meta"><?php _e('Hide On Mobile App', 'swp'); ?></label>
            <input type="checkbox" name="app_mobile_category_meta" id="app_mobile_category_meta" value="1" >
            <p class="description"><?php _e(' Mobile App can access category if checkbox is enable ', 'swp'); ?></p>
        </div>
            
        <?php
    }
 
    //Product Cat Edit page
    function swp_edit_category_hide_on_mobile_meta_field($term) {
        //getting term ID
        $app_mobile_category_meta = get_term_meta( $term->term_id, 'app_mobile_category_meta',true); 
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="app_mobile_category_meta"><?php _e( 'Hide On Mobile App', 'swp' ); ?></label>
            </th>
            <td>
                <input type="checkbox" id="app_mobile_category_meta" name="app_mobile_category_meta" value="1" <?php echo ( $app_mobile_category_meta ) ? checked( $app_mobile_category_meta, '1' ) : ''; ?>/>
                
                <p class="description"><?php _e(' Mobile App can access category if checkbox is enable ', 'swp'); ?></p>
            </td>
        </tr>

        <?php var_dump($app_mobile_category_meta);
    }
    function swp_save_category_hide_on_mobile_meta_field($term_id){
         
        $app_mobile_category_meta = $_POST['app_mobile_category_meta'];
   //    add_term_meta( $term_id, 'app_mobile_category_meta', $app_mobile_category_meta, true );
   
    }
    
// Save extra taxonomy fields callback function.
    function swp_save_category_taxonomy_hide_on_mobile( $term_id ) {
        
        if ( isset( $_POST[ 'app_mobile_category_meta' ] ) ) {
        update_term_meta( $term_id, 'app_mobile_category_meta', '1' );
        } else {
        update_term_meta( $term_id, 'app_mobile_category_meta', '' );
        }
    }
    
}
$swp_product_cat_on_mobile = new swp_product_cat_on_mobile();

?>