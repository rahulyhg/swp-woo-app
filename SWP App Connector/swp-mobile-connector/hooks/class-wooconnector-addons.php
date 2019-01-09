<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Cart class.
 */
class WooConnectorProductAddons {

	/**
	 * Constructor.
	 */
	function __construct() {
		// Add to cart.
		//add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );

		// Load cart data per page load.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

		// Get item data to display.
		//add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Add item data to the cart.
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );       

		// Validate when adding to cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );

		// Add meta to order.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 10, 3 );

		// order again functionality.
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 're_add_cart_item_data' ), 10, 3 );
    }
    
	/*public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item['addons'] )) {

            $price = (float) $cart_item['data']->get_price( 'edit' );
            
			foreach ( $cart_item['addons'] as $addon ) {
				if ( $addon['price'] > 0 ) {
					$price += (float) $addon['price'];
				}
			}

			$cart_item['data']->set_price( $price );
		}

		return $cart_item;
	}*/

	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			$cart_item['addons'] = $values['addons'];
			$cart_item = $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}

	/*public function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['addons'] ) ) {
			foreach ( $cart_item['addons'] as $addon ) {
				$name = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', '__return_true' ) ) {
					$name .= ' (' . wc_price( get_product_addon_price_for_display( $addon['price'], $cart_item['data'], true ) ) . ')';
				}

				$other_data[] = array(
					'name'    => $name,
					'value'   => $addon['value'],
					'display' => isset( $addon['display'] ) ? $addon['display'] : '',
				);
			}
		}

		return $other_data;
	}*/

	public function is_grouped_product( $product_id ) {
		$product = wc_get_product( $product_id );
		return $product->is_type( 'grouped' );
	}

	public function add_cart_item_data( $cart_item_meta, $product_id, $variation_id = 0) {
        $get_data = null;
        $post_data = null;
        if(isset($_GET)){
            $get_data = $_GET;
        }
        if ( isset( $_POST ) ) {
			$post_data = $_POST;
        }
        $data = array();
        if ( ! empty( $post_data['add-to-cart'] ) && $this->is_grouped_product( $post_data['add-to-cart'] ) ) {
			$product_id = $post_data['add-to-cart'];
        }
        $key_data = $get_data['data_key'];
        global $wpdb;
        $table_name = $wpdb->prefix . "wooconnector_data";
        $key_data = esc_sql($key_data);
        $datas = $wpdb->get_results(
            "
            SELECT * 
            FROM $table_name
            WHERE data_key = '$key_data'
            "
        );		
        foreach($datas as $values){
            $val = $values->data;
            $orderid = $values->order_id;
            $data = (array) unserialize($val);			
        }	
        $pro = $data["products"];
        $products = json_decode($pro);
        foreach($products as $product){			
            $proid = absint($product->product_id);		
            if($proid == $product_id){
                $quantity = $product->quantity;									
                $variation_id = isset($product->variation_id) ? absint($product->variation_id) : 0;	
                $addons = isset($product->addons) ? $product->addons : array();
                if ( empty( $cart_item_meta['addons'] ) ) {
                    $cart_item_meta['addons'] = array();
                }
        
                $data = WooConnectorGetCartItemData($product_id,$addons);
                if ( is_wp_error( $data ) ) {
                    // Throw exception for add_to_cart to pickup.
                    throw new Exception( $data->get_error_message() );
                } elseif ( $data ) {
                    $cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], $data );
                }
            }else{
                continue;
            }
        }
		return $cart_item_meta;
	}

	public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = null ) {

		return $passed;
	}

	public function order_line_item( $item, $cart_item_key, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			foreach ( $values['addons'] as $addon ) {
				$key = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$key .= ' (' . strip_tags( wc_price( get_product_addon_price_for_display( $addon['price'], $values['data'], true ) ) ) . ')';
				}

				$item->add_meta_data( $key, $addon['value'] );
			}
		}
	}

	public function re_add_cart_item_data( $cart_item_meta, $product, $order ) {

		// Disable validation.
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 999, 3 );
      
        $get_data = null;
        if(isset($_GET)){
            $get_data = $_GET;
        }


        $proid = $product['product_id'];
        $datakey = $get_data['data_key'];
        global $wpdb;
        $table_name = $wpdb->prefix . "wooconnector_data";
        $key_data = esc_sql($key_data);
        $datas = $wpdb->get_results(
            "
            SELECT * 
            FROM $table_name
            WHERE data_key = '$key_data'
            "
        );		
        foreach($datas as $values){
            $val = $values->data;
            $orderid = $values->order_id;
            $data = (array) unserialize($val);			
        }	
      
        $pro = $data["products"];
        $products = json_decode($pro);

        foreach($products as $product){			
            $product_id = absint($product->product_id);		
            if($product_id == $proid){
                $quantity = $product->quantity;									
                $variation_id = isset($product->variation_id) ? absint($product->variation_id) : 0;	
                $addons = isset($product->addons) ? $product->addons : array();
                if ( empty( $cart_item_meta['addons'] ) ) {
                    $cart_item_meta['addons'] = array();
                }
        
                $data = $field->WooConnectorGetCartItemData($product_id,$addons);
        
                if ( is_wp_error( $data ) ) {
                    // Throw exception for add_to_cart to pickup.
                    throw new Exception( $data->get_error_message() );
                } elseif ( $data ) {
                    $cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
                }
            }	
        }

		return $cart_item_meta;
	}
}
$WooConnectorProductAddons = new WooConnectorProductAddons();
?>