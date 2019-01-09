<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WooConnector_Brand_Images{

    public function __construct(){
        $this->add_custom_column_fields('wooconnector_product_brand');
        add_action('edit_term', array($this, 'save_image'));
        add_action('create_term', array($this, 'save_image'));
        add_action( 'admin_enqueue_scripts',array($this,'add_script'));
    }

    public function add_script($hook){
        if (is_admin() && $hook == 'edit-tags.php' || $hook == 'term.php') {
            wp_enqueue_script(
                'wooconnector_brands_avatar_js',
                plugins_url('/assets/js/wooconnector-brands.js', WOOCONNECTOR_PLUGIN_BASENAME),
                array('jquery'),
                WOOCONNECTOR_VERSION,
                true
            );
    
            wp_localize_script(
                'wooconnector_brands_avatar_js',
                'wooconnector_brands_avatar_params',
                array(
                    'label'      => array(
                        'title'  => __('Choose Brand Image'),
                        'button' => __('Choose Image')
                    )
                )
            );
            wp_enqueue_media();
        }
    }

    public static function get_brand_image($atts = array(), $onlysrc = false){
        $params = array_merge(array(
                'size'    => 'full',
                'term_id' => null,
                'alt'     => null
        ), $atts);

        $term_id = $params['term_id'];
        $size    = $params['size'];

        if (! $term_id) {
            if (is_brand()) {
                $term_id = get_query_var('cat');
            } elseif (is_tax()) {
                $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                $term_id = $current_term->term_id;
            }
        }

        if (!$term_id) {
            return;
        }

        $attachment_id   = get_term_meta($term_id,'wooconnector_brand_avatar');
        if(!empty($attachment_id)){
            $attachment_id = $attachment_id[0];
        }
        $attachment_meta = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $attachment_alt  = trim(strip_tags($attachment_meta));

        $attr = array(
            'alt'=> (is_null($params['alt']) ?  $attachment_alt : $params['alt'])
        );

        if ($onlysrc == true) {
            $src = wp_get_attachment_image_src($attachment_id, $size, false);
            return is_array($src) ? $src[0] : null;
        }

        return wp_get_attachment_image($attachment_id, $size, false, $attr);
    }

    public function add_custom_column_fields($taxonomy){
        add_action("{$taxonomy}_add_form_fields", array($this, 'add_taxonomy_field'));
        add_action("{$taxonomy}_edit_form_fields", array($this, 'edit_taxonomy_field'));

        // Add custom columns to custom taxonomies
        add_filter("manage_edit-{$taxonomy}_columns", array($this, 'manage_brand_columns'));
        add_filter("manage_{$taxonomy}_custom_column", array($this, 'manage_brand_columns_fields'), 10, 3);
    }

    public function manage_brand_columns($columns){
        $offset = 0;
        if(isset($columns['posts']))
            $offset++;
        
        if(isset($columns['slug']))
            $offset++;

        if(isset($columns['description']))
            $offset++;

        if(isset($columns['name']))
            $offset++;

        if ( $offset > 0 ) {
            $name = array_slice( $columns, -$offset, $offset, true );
            foreach ( $name as $column => $n ) {
                unset( $columns[$column] );
            }
            $columns['wooconnector_brand_image'] = __('Image');
            foreach ( $name as $column => $n ) {
                $columns[$column] = $n;
            }
        }else{
            $columns['wooconnector_brand_image'] = __('Image');
        }
        return $columns;
    }

    public function manage_brand_columns_fields($deprecated, $column_name, $term_id){
        if ($column_name == 'wooconnector_brand_image' && $this->has_image($term_id)) {
            echo self::get_brand_image(array(
                'term_id' => $term_id,
                'size'    => 'thumbnail',
            ));
        }
    }

    public function save_image($term_id){
        $attachment_id = isset($_POST['wooconnector_attachment']) ? (int) $_POST['wooconnector_attachment'] : null;
        if (! is_null($attachment_id) && $attachment_id > 0 && !empty($attachment_id)) {
            add_term_meta($term_id,'wooconnector_brand_avatar', $attachment_id);
        }else{
            delete_term_meta($term_id,'wooconnector_brand_avatar');
        }
    }

    public function get_attachment_id($term_id){
        $attachment_id = get_term_meta($term_id,'wooconnector_brand_avatar');
        $id = '';
        if(!empty($attachment_id)){
            $id = $attachment_id[0];
        }else{
            $id = '';
        }
        return $id;
    }

    public function has_image($term_id){
        return ($this->get_attachment_id($term_id) !== false);
    }

    public function add_taxonomy_field($taxonomy){
        echo $this->taxonomy_field('wooconnector-add-field-brand', $taxonomy);
    }

    public function edit_taxonomy_field($taxonomy){
        echo $this->taxonomy_field('wooconnector-edit-field-brand', $taxonomy);
    }

    public function taxonomy_field($template, $taxonomy){
        $params = array(
            'label'  => array(
                'image'        => __('Feature Image'),
                'upload_image' => __('Upload/Edit Image'),
                'remove_image' => __('Remove image')
            ),
            'wooconnector_attachment' => null
        );


        if (isset($taxonomy->term_id) && $this->has_image($taxonomy->term_id)) {
            $image = self::get_brand_image(array(
                'term_id' => $taxonomy->term_id
            ), true);
            
            $attachment_id = $this->get_attachment_id($taxonomy->term_id);

            $params = array_replace_recursive($params, array(
                'wooconnector_brand_avatar'  => $image,
                'wooconnector_attachment' => $attachment_id,
            ));
        }

        return wooconnector_get_brand_template($template, $params, false);
    }

}
$WooConnector_Brand_Images = new WooConnector_Brand_Images();
?>