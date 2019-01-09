<?php

if( !defined( 'ABSPATH' ) ){
    /***** EXIT if direct accessed file ******/ 
    exit;
}

/************************************************
* @user order details
* 
* return array
************************************************/

if( !class_exists( 'SWPappspecificusersorder' ) ){
    class SWPappspecificusersorder{
        
        private $rest_url = 'swp/v1/shipping';
        
        private $request = array();
        
        /***** create construct function *****/
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'swp_app_enpoints_users_order' ) );
        }
        
        /***** email details - endpoint *****/   
        function swp_app_enpoints_users_order(){
            
            /***** register specific orders by user *****/
            register_rest_route( $this->rest_url, '/getorderbyid', array(
                'methods' => 'GET',
                'callback' => array( $this, 'swp_get_order_by_id' ),
                //'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args' => array(
                    'order' => array(
                        'required' => true,
                        'sanitize_callback' => 'absint'
                    )	
                ),
            ) );
		
         }
        
       /***** callback function for orders details *****/    
       public function swp_get_order_by_id($request){
            $parameters = $request->get_params();
            $orderid = $parameters['order'];
            $order = new WC_Order($orderid);
            $auth = isset($_SERVER['HTTP_AUTHORIZATION']) ?  $_SERVER['HTTP_AUTHORIZATION'] : false;
            
            $customer = $order->get_user_id();
            $currentuser = get_current_user_id();
            if( $currentuser == true){
                $data              = $order->get_data();
                $currency = $data['currency'];
                //$currencys = WooConnectorGetCurrency(strtolower($currency));
                $number_of_decimals   = $currencys['number_of_decimals'];
                $currenty_symbol      = $currencys['symbol'];
                $ratecurrency         = $currencys['rate'];
                $currency_position    = $currencys['position'];
                $thousand_separator   = $currencys['thousand_separator'];
                $decimal_separator    = $currencys['decimal_separator'];
                $format_decimal       = array( 'discount', 'discount_total', 'discount_tax', 'shipping_total', 'shipping_tax', 'subtotal', 'shipping_tax', 'cart_tax', 'total', 'total_tax', 'prices_include_tax' );
                $format_date          = array( 'date_created', 'date_modified', 'date_completed', 'date_paid' );
                $format_line_items    = array( 'line_items', 'tax_lines', 'shipping_lines', 'fee_lines', 'coupon_lines' );

                $items = $order->get_items();
                $subtotal = 0;
                foreach($items as $item){
                    $subtotal += $item->get_subtotal();
                }
                $data['subtotal'] = $subtotal;
                // Format decimal values.
                foreach ( $format_decimal as $key ) {
                    if(!empty($data[ $key ])){
                        if($currency_position=='left'){
                            $data[ $key ] = $currenty_symbol.(wc_format_decimal( $data[ $key ], $number_of_decimals ));
                        }elseif($currency_position=='right'){
                            $data[ $key ] = (wc_format_decimal( $data[ $key ], $number_of_decimals )).$currenty_symbol;
                        }elseif($currency_position=='left_space'){
                            $data[ $key ] = $currenty_symbol.' '.(wc_format_decimal( $data[ $key ], $number_of_decimals ));
                        }elseif($currency_position=='right_space'){
                            $data[ $key ] = (wc_format_decimal( $data[ $key ], $number_of_decimals )).' '.$currenty_symbol;
                        }
                    }
                }

                // Format date values.
                foreach ( $format_date as $key ) {
                    $datetime              = $data[ $key ];
                    $data[ $key ]          = wc_rest_prepare_date_response( $datetime, false );
                    $data[ $key . '_gmt' ] = wc_rest_prepare_date_response( $datetime );
                }

                // Format the order status.
                $data['status'] = 'wc-' === substr( $data['status'], 0, 3 ) ? substr( $data['status'], 3 ) : $data['status'];

                // Format line items.
                foreach ( $format_line_items as $key ) {
                    $data[ $key ] = array_values( array_map( array( $this, 'get_order_item_data' ), $data[ $key ] ) );
                }

                if(!empty($data['coupon_lines'])){
                    foreach($data['coupon_lines'] as $coupon_lines){
                        foreach ( $format_decimal as $key ) {
                            if(!empty($coupon_lines[$key])){
                                if($currency_position == 'left'){
                                    $coupon_lines[$key] = $currenty_symbol.(wc_format_decimal( $coupon_lines[$key], $number_of_decimals ));
                                }elseif($currency_position == 'right'){
                                    $coupon_lines[$key] = (wc_format_decimal( $coupon_lines[$key], $number_of_decimals )).$currenty_symbol;
                                }elseif($currency_position =='left_space'){
                                    $coupon_lines[$key] = $currenty_symbol.' '.(wc_format_decimal( $coupon_lines[$key], $number_of_decimals ));
                                }elseif($currency_position == 'right_space'){
                                    $coupon_lines[$key] = (wc_format_decimal($coupon_lines[$key], $number_of_decimals )).' '.$currenty_symbol;
                                }
                            }
                        }
                        $listout[] = array(
                            'id' => $coupon_lines['id'],
                            'code' => $coupon_lines['code'],
                            'discount' => $coupon_lines['discount'],
                            'discount_tax' => $coupon_lines['discount_tax'],
                            'meta_data' => $coupon_lines['meta_data'],
                        );
                    }
                    $data['coupon_lines'] = $listout;
                }	

                // Refunds.
                $data['refunds'] = array();
                foreach ( $order->get_refunds() as $refund ) {
                    if($currency_position=='left'){
                            $data['refunds'][] = array(
                            'id'     => $refund->get_id(),
                            'refund' => $refund->get_reason() ? $refund->get_reason() : '',
                            'total'  =>'-' . $currenty_symbol.(wc_format_decimal( $refund->get_amount(), $number_of_decimals )),
                        );
                    }elseif($currency_position=='right'){
                        $data['refunds'][] = array(
                            'id'     => $refund->get_id(),
                            'refund' => $refund->get_reason() ? $refund->get_reason() : '',
                            'total'  => '-' . (wc_format_decimal( $refund->get_amount(), $number_of_decimals )). $currenty_symbol,
                        );
                    }elseif($currency_position=='left_space'){
                        $data['refunds'][] = array(
                            'id'     => $refund->get_id(),
                            'refund' => $refund->get_reason() ? $refund->get_reason() : '',
                            'total'  => '-' . $currenty_symbol. ' ' .(wc_format_decimal( $refund->get_amount(), $number_of_decimals )),
                        );
                    }elseif($currency_position=='right_space'){
                        $data['refunds'][] = array(
                            'id'     => $refund->get_id(),
                            'refund' => $refund->get_reason() ? $refund->get_reason() : '',
                            'total'  => '-' . (wc_format_decimal( $refund->get_amount(), $number_of_decimals )).' '.$currenty_symbol,
                        );
                    }
                }
                $paymentdes = '';
                $gateways = WC()->payment_gateways->payment_gateways();
                foreach($gateways as $gateway){
                    if($gateway->id == $data['payment_method']){
                        $paymentdes = $gateway->description;
                    }
                }
                $result = array(
                    'id'                   => $order->get_id(),
                    'parent_id'            => $data['parent_id'],
                    'number'               => $data['number'],
                    'order_key'            => $data['order_key'],
                    'created_via'          => $data['created_via'],
                    'version'              => $data['version'],
                    'status'               => $data['status'],
                    'currency'             => $data['currency'],
                    'date_created'         => $data['date_created'],
                    'date_created_gmt'     => $data['date_created_gmt'],
                    'date_modified'        => $data['date_modified'],
                    'date_modified_gmt'    => $data['date_modified_gmt'],
                    'discount_total'       => $data['discount_total'],
                    'discount_tax'         => $data['discount_tax'],
                    'shipping_total'       => $data['shipping_total'],
                    'shipping_tax'         => $data['shipping_tax'],
                    'cart_tax'             => $data['cart_tax'],
                    'total'                => $data['total'],
                    'subtotal'             => $data['subtotal'],
                    'total_tax'            => $data['total_tax'],
                    'prices_include_tax'   => $data['prices_include_tax'],
                    'customer_id'          => $data['customer_id'],
                    'customer_ip_address'  => $data['customer_ip_address'],
                    'customer_user_agent'  => $data['customer_user_agent'],
                    'customer_note'        => $data['customer_note'],
                    'billing'              => $data['billing'],
                    'shipping'             => $data['shipping'],
                    'payment_method'       => $data['payment_method'],
                    'payment_method_title' => $data['payment_method_title'],
                    'payment_method_description' => $paymentdes,
                    'transaction_id'       => $data['transaction_id'],
                    'date_paid'            => $data['date_paid'],
                    'date_paid_gmt'        => $data['date_paid_gmt'],
                    'date_completed'       => $data['date_completed'],
                    'date_completed_gmt'   => $data['date_completed_gmt'],
                    'cart_hash'            => $data['cart_hash'],
                    'meta_data'            => $data['meta_data'],
                    'line_items'           => $data['line_items'],
                    'tax_lines'            => $data['tax_lines'],
                    'shipping_lines'       => $data['shipping_lines'],
                    'fee_lines'            => $data['fee_lines'],
                    'coupon_lines'         => $data['coupon_lines'],
                    'refunds'              => $data['refunds'],
                );
                $laccs = array();
                if($data['payment_method'] == 'bacs'){
                    $accounts = get_option('woocommerce_bacs_accounts');
                    if(!empty($accounts)){
                        foreach($accounts as $account){
                            $laccs[] = array(
                                'account_name' => $account['account_name'],
                                'account_number' => $account['account_number'],
                                'bank_name' => $account['bank_name'],
                                'sort_code' => $account['sort_code'],
                                'iban' => $account['iban'],
                                'bic' => $account['bic'],
                            );
                        }
                    }
                    if(!empty($laccs)){
                        $result['bacs_accounts'] = $laccs;
                    }else{
                        $result['bacs_accounts'] = array();
                    }
                }
                return apply_filters('swpgetorderbyid',$result);
            }else{
                return $customer;
            }
        }


    }
    
    $SWPappspecificusersorder = new SWPappspecificusersorder();
}