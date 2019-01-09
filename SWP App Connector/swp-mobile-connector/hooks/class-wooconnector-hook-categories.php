<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WooConnectorCategoriesHooks {
	
	public function __construct() {
		if(wooconnector_is_rest_api() && is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')){
	        remove_all_filters('get_terms_args');
	        remove_all_filters('terms_clauses');
	        remove_all_filters('get_terms');
	        remove_all_filters('get_term');
	    }
		add_filter( 'wooconnector_product_categories_data', array($this,'get_images_categories') , 10, 1);
		add_filter( 'wooconnector_product_categories_data_details', array($this,'get_listcategories_in_categories_details') , 10, 2);
		add_filter( 'wooconnector_product_brands_data', array($this,'get_images_brands') , 10, 1);
	}
	
	public function update_thumnail_woo($imagesID ) {
		$post_thumbnail_id = $imagesID;
		if(empty($post_thumbnail_id))
			return true;
		/// ki?m tra xem d� t?n t?i thumnail chua
		$wooconnector_large = get_post_meta($post_thumbnail_id, 'wooconnector_large', true);
		$wooconnector_medium = get_post_meta($post_thumbnail_id, 'wooconnector_medium', true);
		$wooconnector_x_large = get_post_meta($post_thumbnail_id, 'wooconnector_x_large', true);
		$wooconnector_small = get_post_meta($post_thumbnail_id, 'wooconnector_small', true);
		if(!empty($wooconnector_medium) && !empty($wooconnector_x_large) && !empty($wooconnector_large) && !empty($wooconnector_small))
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
		list($width, $height) = getimagesize($absolute_pathto_file);
		if($width > $height){
			foreach($this->thumnailsX as $key => $value){
				$path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
				$dest = $wp_upload_dir['basedir'].'/'.$path;
				WooConnectorCore:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
				// cập nhật post meta for thumnail
				update_post_meta ($post_thumbnail_id, $key, $path);
			}
		}elseif($width < $height){
			foreach($this->thumnailsY as $key => $value){
				$path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
				$dest = $wp_upload_dir['basedir'].'/'.$path;
				WooConnectorCore:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
				// cập nhật post meta for thumnail
				update_post_meta ($post_thumbnail_id, $key, $path);
			}
		}elseif($width == $height){
			foreach($this->thumnailsS as $key => $value){
				$path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
				$dest = $wp_upload_dir['basedir'].'/'.$path;
				WooConnectorCore:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);
				// cập nhật post meta for thumnail
				update_post_meta ($post_thumbnail_id, $key, $path);
			}
		}
		return true;
	}

	public function get_listcategories_in_categories_details($data,$request){
		$idterm = $data['id'];
		$childrens = array();
		$parameters = $request->get_params();
		$cat_per_page = $parameters['cat_per_page'];
		$num_page = $parameters['cat_num_page'];
		$cat_num_page = ($num_page - 1)*$cat_per_page;
		$cat_order_page = $parameters['cat_order_page'];
		$cat_order_by = $parameters['cat_order_by'];
		if(!empty($idterm)){
			$args = array(
				'taxonomy' => 'product_cat',
				'orderby' => 'id',
				'hide_empty'=> false,
				'parent'  => $idterm,
				'number' => $cat_per_page,
				'offset' => $cat_num_page,
				'orderby' => $cat_order_by,
				'order'  => $cat_order_page,
			);
			$stringids = '';
			if(is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')){
				$ids = $this->checkcategorieswithWPML($request);
				if(empty($ids)){
					return array();
				}
				if(!empty($args['include'])){
					$list_term_id = array();
					foreach($ids as $term_id){
						if(in_array($term_id,$args['include'])){
							$list_term_id[] = $term_id;
						}
					}
					$args['include'] = $list_term_id;
				}else{
					$args['include'] = $ids;
				}
				$stringids = implode(',',$ids);
			}
			if(is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')){
    		    $terms = $this->pre_sql_to_get_wpml_categories($stringids,$idterm);
    		}else{
    		    $terms = get_terms($args);
    		}
			foreach($terms as $child){
				$childrens[] = $this->getTheCategories($child,$request);
			}
		}
		$data['wooconnector_categories_childrens'] = $childrens;
		return $data;
	}

	private function get_default_wpml_languages(){
		$settings = get_option('icl_sitepress_settings');
		if(!empty($settings)){
			return $settings['default_language'];
		}else{
			return 'en';
		}
	}
	
	/**
	 * Check languages enable
	 */
	private function wooconnector_is_languages_enable($lang){
		global $wpdb;
		if(is_plugin_active('sitepress-multilingual-cms-master/sitepress.php')){ 		
			$enable = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.'icl_locale_map WHERE code = "'.$lang.'"');
			if($enable > 0){
				return true;
			}
			return false;
		}elseif(is_plugin_active('qtranslate-x/qtranslate.php')){
			$listenable = get_option('qtranslate_enabled_languages');
			if(in_array($lang,$listenable)){
				return true;
			}
			return false;
		}
		$enable = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.'icl_locale_map WHERE code = "'.$lang.'"');
		$listenable = get_option('qtranslate_enabled_languages');
		if(!empty($enable)){
			if($enable > 0){
				return true;
			}
			return false;	
		}elseif(!empty($listenable)){
			if(in_array($lang,$listenable)){
				return true;
			}
			return false;
		}else{
			return false;
		}
	}
	
	private function checkcategorieswithWPML(){
		global $wpdb;
		$current_lang = '';
		if(isset($_GET['mobile_lang']) && !isset($_GET['lang'])){
			$current_lang = (isset($_GET['mobile_lang']) && $this->wooconnector_is_languages_enable(sanitize_text_field($_GET['mobile_lang']))) ?  sanitize_text_field($_GET['mobile_lang']) : $this->get_default_wpml_languages();
		}elseif(!isset($_GET['mobile_lang']) && isset($_GET['lang'])){
			$current_lang = (isset($_GET['lang']) && $this->wooconnector_is_languages_enable(sanitize_text_field($_GET['lang']))) ?  sanitize_text_field($_GET['lang']) : $this->get_default_wpml_languages();
		}
		$prefix = $wpdb->prefix;
		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$prefix."term_taxonomy.* FROM ".$prefix."term_taxonomy INNER JOIN ".$prefix."icl_translations AS t ON ".$prefix."term_taxonomy.term_id = t.element_id AND t.element_type = CONCAT('tax_', ".$prefix."term_taxonomy.taxonomy) WHERE 1=1 AND ".$prefix."term_taxonomy.taxonomy = 'product_cat' AND  t.language_code = '".$current_lang."'";
		$listterms = $wpdb->get_results($sql,ARRAY_A);
		$listId = array();
		if(!empty($listterms)){
			foreach($listterms as $term){
				$listId[] = $term['term_id'];
			}
		}	
		return $listId;
	}

    public function get_images_categories( $data) {		
		$images = $data['image'];
		if(!empty($images)){	
			$image_id = $images['id'];
			$wp_upload_dir = wp_upload_dir();	
			$wooconnector_large = get_post_meta($image_id, 'wooconnector_large', true);
			$wooconnector_medium = get_post_meta($image_id, 'wooconnector_medium', true);
			$wooconnector_x_large = get_post_meta($image_id, 'wooconnector_x_large', true);
			$wooconnector_small = get_post_meta($image_id, 'wooconnector_small', true);
			if( empty($wooconnector_large) || empty($wooconnector_medium) || empty($wooconnector_x_large) || empty($wooconnector_small)) { 
					
				$this->update_thumnail_woo($image_id);
			}
			// g?n thumnail m?i
			foreach($this->thumnailsX as $key => $value){
				$imagesupload = get_post_meta($image_id, $key, true);
				if(empty($imagesupload)){
					$crop_image[$key] = null;
				}else{
					$crop_image[$key] = $wp_upload_dir['baseurl']."/". $imagesupload;
				}
			}
		}else{
			$crop_image = null;
		}
        $data['wooconnector_images_categories'] = $crop_image;
		return $data;		
	}

	public function get_images_brands( $data) {		
		$images = $data['image'];
		if(!empty($images)){	
			$image_id = $images['id'];
			$wp_upload_dir = wp_upload_dir();	
			$wooconnector_large = get_post_meta($image_id, 'wooconnector_large', true);
			$wooconnector_medium = get_post_meta($image_id, 'wooconnector_medium', true);
			$wooconnector_x_large = get_post_meta($image_id, 'wooconnector_x_large', true);
			$wooconnector_small = get_post_meta($image_id, 'wooconnector_small', true);
			if( empty($wooconnector_large) || empty($wooconnector_medium) || empty($wooconnector_x_large) || empty($wooconnector_small)) { 
					
				$this->update_thumnail_woo($image_id);
			}
			// g?n thumnail m?i
			foreach($this->thumnailsX as $key => $value){
				$imagesupload = get_post_meta($image_id, $key, true);
				if(empty($imagesupload)){
					$crop_image[$key] = null;
				}else{
					$crop_image[$key] = $wp_upload_dir['baseurl']."/". $imagesupload;
				}
			}
		}else{
			$crop_image = null;
		}
        $data['wooconnector_images_brands'] = $crop_image;
		return $data;		
	}

	private function getTheCategories($item,$request){
		$display_type = get_woocommerce_term_meta( $item->term_id, 'display_type' );

		// Get category order.
		$menu_order = get_woocommerce_term_meta( $item->term_id, 'order' );

		$data = array(
			'id'          => (int) $item->term_id,
			'name'        => apply_filters( 'post_title',$item->name),
			'slug'        => $item->slug,
			'parent'      => (int) $item->parent,
			'description' => apply_filters( 'the_content',$item->description),
			'display'     => $display_type ? $display_type : 'default',
			'image'       => array(),
			'menu_order'  => (int) $menu_order,
			'count'       => (int) $item->count,
		);

		// Get category image.
		if ( $image_id = get_woocommerce_term_meta( $item->term_id, 'thumbnail_id' ) ) {
			$attachment = get_post( $image_id );

			$data['image'] = array(
				'id'                => (int) $image_id,
				'date_created'      => wc_rest_prepare_date_response( $attachment->post_date ),
				'date_created_gmt'  => wc_rest_prepare_date_response( $attachment->post_date_gmt ),
				'date_modified'     => wc_rest_prepare_date_response( $attachment->post_modified ),
				'date_modified_gmt' => wc_rest_prepare_date_response( $attachment->post_modified_gmt ),
				'src'               => wp_get_attachment_url( $image_id ),
				'title'             => get_the_title( $attachment ),
				'alt'               => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
			);
		}
		$data = apply_filters('wooconnector_product_categories_data',$data);	
		$params = $request->get_params();
		if(isset($params['menu']) && $params['menu'] === 1 && isset($params['parent']) && $params['parent'] === 0){
			$data = apply_filters('wooconnector_product_categories_data_details',$data,$request);	
		}
		return $data;
	}

	private function pre_sql_to_get_wpml_categories($ids,$parent){
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sql = "SELECT  t.*, tt.* FROM ".$prefix."terms AS t  INNER JOIN ".$prefix."term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('product_cat') AND t.term_id IN (".$ids.") AND tt.parent = '".$parent."' ORDER BY t.name ASC";
		$result = $wpdb->get_results($sql);
		return $result;
	}
	
	public $thumnailsX = array(
		'wooconnector_small' => array(
			'width' => 320,
			'height' => 240
		),
		'wooconnector_medium' => array(
			'width' => 480,
			'height' => 360
		),
		'wooconnector_large' => array(
			'width' => 752,
			'height' => 564
		),
		'wooconnector_x_large' => array(
			'width' => 1080,
			'height' => 810
		),
	);

	public $thumnailsS = array(
		'wooconnector_small' => array(
			'width' => 320,
			'height' => 320
		),
		'wooconnector_medium' => array(
			'width' => 480,
			'height' => 480
		),
		'wooconnector_large' => array(
			'width' => 752,
			'height' => 752
		),
		'wooconnector_x_large' => array(
			'width' => 1080,
			'height' => 1080
		),
	);	
	
	public $thumnailsY = array(
		'wooconnector_small' => array(
			'width' => 240,
			'height' => 320
		),
		'wooconnector_medium' => array(
			'width' => 360,
			'height' => 480
		),
		'wooconnector_large' => array(
			'width' => 564,
			'height' => 752
		),
		'wooconnector_x_large' => array(
			'width' => 810,
			'height' => 1080
		),
	);		
	
	
}
$WooConnectorCategoriesHooks = new WooConnectorCategoriesHooks();
?>