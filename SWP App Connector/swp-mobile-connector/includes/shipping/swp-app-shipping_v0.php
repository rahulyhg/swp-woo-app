<?php
/***********************************************************
* referance by : wooconnector
************************************************************/
/*****  *****/

if (! defined ( 'ABSPATH' ) ){
    /***** Exit If access Directly *****/ 
    exit;
}

if ( ! class_exists( 'SWPappshipping' ) ){
    
    /***** Main Class of shipping *****/
    class SWPappshipping{

        /***** Total of cart *****/
        private $total = array();

        /***** shipping of cart *****/
        private $shipping = array();
        
        /***** coupon of cart *****/
        private $coupons = array();
        
        /*****  *****/
        private $rest_url = 'swp/v1/shipping';

        /*****create construct function *****/
        public function __construct() {
            /***** register routs ****/
            $this->swp_register_shipping_rest_api(); 
	    }
        
        /***** Hook into actions and filters *****/
        public function swp_register_shipping_rest_api(){
        
            /***** call route api *****/
            add_action( 'rest_api_init', array( $this, 'swp_register_shipping_routes' ) );
        }
        
        /***** create rest api for shipping, total and coupons *****/
        public function swp_register_shipping_routes(){
            
            /*****  API for get all information *****/
            register_rest_route(
                'swp/v1/shipping', '/get-all',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_get_all' ),
                    'permission_callback' => array( $this, 'swp_get_item_permission_check' ),
                    'args' =>
                    array(
                        'products' =>array(
                            'required' => true
                        ),
                        'coupons' => array(						
                        ),
                        'address' => array(
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'city' => array(
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'country' => array(
                            'required' => true,
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'postcode' => array(
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'states' => array(
                            'sanitize_callback' => 'esc_sql'
                        )
                    ),
                ) 
            );
            
            /***** API for get total  *****/
            register_rest_route(
                'swp/v1/shipping', '/total',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_get_total' ),
                   // 'permission_callback' => array( $this, 'swp_get_item_permission_check' ),
                    'args' => array(
                        'products' => array(
                           'required' => true
                        )					
                    ),
		        )
            );
            
            /***** API for get shipping *****/
            register_rest_route(
                'swp/v1/shipping', '/get-shippings',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_get_shipping' ),
                    'permission_callback' => array( $this, 'swp_get_item_permission_check' ),
                    'args' => array(					
                        'postcode' => array(
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'country' => array(
                            'sanitize_callback' => 'esc_sql'
                        ),
                        'states' => array(
                            'sanitize_callback' => 'esc_sql'
                        )
                    ),
                )
            );
            
            /***** API for get payment *****/
            register_rest_route(
                'swp/v1/shipping', '/get-payment',
                array(
                    'methods' => 'GET',
                    'callback' => array( $this, 'swp_get_payment' ),
                    'permission_callback' => array( $this, 'swp_get_item_permission_check' ),
                    'args' => array(					
                    ),
                )
            );
            
            /***** API for get coupon *****/
            register_rest_route(
                'swp/v1/shipping', '/coupon', 
                array(
                    'methods' => 'POST',
                    'callback' => array( $this, 'swp_add_coupon' ),
                    'permission_callback' => array( $this, 'swp_get_item_permission_check' ),
                    'args' => array(
                        'products' =>array(
                            'required' => true
                        ),
                        'coupons' => array(

                        )
                    ),
                ) 
            );
            
            /***** API for get currency  *****/
            register_rest_route( 'swp/v1/shipping', '/currency', array(
					'methods'         => 'GET',
					'callback'        => array( $this, 'swp_currency_options' ),	
					//'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'            => array(
						
					),					
                ) 
            );
		}
        
        /**************************************
        * api process if given request has access 
        * $request full details about request
        * @return wp_error/boolean
        ***************************************/
        public function swp_get_item_permission_check( $request ) {
            //if(is_plugin_active('swp-mobile-connector/swp-app-connector.php')){
                $usekey = get_option('mobiconnector_settings-use-security-key');
                if ($usekey == 1 && ! bamobile_mobiconnector_rest_check_post_permissions( $request ) ) {
                    return new WP_Error( 'mobiconnector_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'SWP' ), array( 'status' => rest_authorization_required_code() ) );
                //}
            }
            return true;
        }
        
        /*************************************
        * get total of cart 
        * @param WP_REST_Request $request current Request
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_all($request){
            
        
        }
        
        /*************************************
        * get total of cart 
        * @param WP_REST_Request $request current Request
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_total($request){
            $parameters = $request->get_params();
            $pro = $parameters['products'];	
            $list = array();
            if(!empty($pro)){
                $product = json_decode($pro);	
                foreach($product as $post){	
                    $product_id = absint($post->product_id);			
                    $quantity = $post->quantity;		
                    $variation_id = !empty($post->variation_id) ? absint($post->variation_id) : false;
                    $addons = !empty($post->addons) ? $post->addons : array();
                    $error = $this->swp_validate_input($product_id,$variation_id,$quantity);	
                    if(!empty($error)){
                        if($error['code'] !== 'rest_order_stock_error'){
                            continue;
                        }
                    }
                    $list[] = $this->swp_get_total_no_coupon($post,$request,$addons);	
                }
            }		
            return $pro;
        }
        
        /*************************************
        * get shipping  
        * @param WP_REST_Request $request current Request
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_shipping( $request ){
            $requestvalues = $request->get_params();
            $country = (isset($requestvalues['country'])) ? strtoupper($requestvalues['country']) : false;		
            $postcode = (isset($requestvalues['postcode'])) ? $requestvalues['postcode'] : false;
            $states = (isset($requestvalues['states'])) ? $requestvalues['country']. ":". $requestvalues['states'] : false;
            $shipping_country = get_option('woocommerce_ship_to_countries');		
            $shipping_specific_country = get_option('woocommerce_specific_ship_to_countries');
            $checkspecial = 0;
            if($shipping_country == 'specific' && empty($shipping_specific_country)){			
                return null;
            }elseif($shipping_country == 'specific' && !empty($shipping_specific_country)){
                foreach($shipping_specific_country as $special){
                    if($special == $country){
                        $checkspecial++;
                    }
                }
            }
            if($shipping_country == 'specific' && $checkspecial === 0){
                return null;
            }
            //get all zones shipping in woocommerce
            $zones = WC_Shipping_Zones::get_zones();
            if(!empty($zones)){
                krsort($zones);
                $count = count($zones);
                $listmethods = array();
                $listzone_id = array();
                $checknotexist = array();		
                foreach($zones as $zone){	
                    if(empty($zone)){
                        continue;
                    }
                    $ship = $zone['formatted_zone_location'];
                    $zonelocations = $zone['zone_locations'];
                    $listzone = array();
                    $listpost = array();
                    $liststate = array();
                    $listcontinent = array();
                    if(empty($zonelocations) && empty($ship)){
                        continue;
                    }	
                    foreach($zonelocations as $zonelocation){
                        if($zonelocation->type == 'country'){
                            $listzone[] = $zonelocation->code;
                        }
                        if($zonelocation->type == 'postcode'){
                            $listpost[] = $zonelocation->code;
                        }	
                        if($zonelocation->type == 'state'){
                            $liststate[] = $zonelocation->code;
                        }	
                        if($zonelocation->type == 'continent'){
                            $listcontinent[] = $zonelocation->code;
                        }
                    }	
                    $name = $zone['zone_name'];	
                    $zoneid = $zone['zone_id'];	
                    if(isset($requestvalues['country'])){	
                        if(!empty($listzone)){
                            if (in_array($country,$listzone)){							
                                $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                                $listmethods = $shippingaftermethod['listmethods'];
                                $checknotexist = $shippingaftermethod['checknotexist'];
                                $listzone_id = $shippingaftermethod['listzone_id'];						
                            }elseif(!empty($listpost) && !empty($liststate)){
                                if(empty($states) || empty($postcode)){
                                    continue;
                                }
                                if(in_array($states,$liststate) && $this->checkInPostcode($listpost,$postcode)){
                                    $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                                    $listmethods = $shippingaftermethod['listmethods'];
                                    $checknotexist = $shippingaftermethod['checknotexist'];
                                    $listzone_id = $shippingaftermethod['listzone_id'];
                                }else{
                                    continue;
                                }
                            }elseif(!empty($liststate) && empty($listpost)){
                                if(empty($states)){
                                    continue;
                                }
                                if(in_array($states,$liststate)){
                                    $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                                    $listmethods = $shippingaftermethod['listmethods'];
                                    $checknotexist = $shippingaftermethod['checknotexist'];
                                    $listzone_id = $shippingaftermethod['listzone_id'];
                                }else{
                                    continue;
                                }
                            }elseif(!empty($listpost) && empty($liststate)){
                                if(empty($postcode)){
                                    continue;
                                }			
                                if($this->checkInPostcode($listpost,$postcode)){
                                    $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id,$postcode,$listpost,true);
                                    $listmethods = $shippingaftermethod['listmethods'];
                                    $checknotexist = $shippingaftermethod['checknotexist'];
                                    $listzone_id = $shippingaftermethod['listzone_id'];
                                }else{
                                    continue;
                                }		
                            }else{
                                continue;
                            }
                        }elseif(empty($listzone) && !empty($listpost) && !empty($liststate)){
                            if(empty($states) || empty($postcode)){
                                continue;
                            }
                            if(in_array($states,$liststate) && $this->checkInPostcode($listpost,$postcode)){	
                                $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);							
                                $listmethods = $shippingaftermethod['listmethods'];
                                $checknotexist = $shippingaftermethod['checknotexist'];
                                $listzone_id = $shippingaftermethod['listzone_id'];
                            }else{
                                continue;
                            }			
                        }elseif(empty($listzone) && !empty($liststate) && empty($listpost)){
                            if(empty($states)){
                                continue;
                            }
                            if(in_array($states,$liststate)){
                                $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                                $listmethods = $shippingaftermethod['listmethods'];
                                $checknotexist = $shippingaftermethod['checknotexist'];
                                $listzone_id = $shippingaftermethod['listzone_id'];
                            }else{
                                continue;
                            }
                        }elseif(empty($listzone) && !empty($listpost) && empty($liststate)){	
                            if(empty($postcode)){
                                continue;
                            }						
                            if($this->checkInPostcode($listpost,$postcode)){	
                                $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id,$postcode,$listpost,true);
                                $listmethods = $shippingaftermethod['listmethods'];
                                $checknotexist = $shippingaftermethod['checknotexist'];
                                $listzone_id = $shippingaftermethod['listzone_id'];
                            }else{
                                continue;
                            }
                        }else{
                            $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                            $listmethods = $shippingaftermethod['listmethods'];
                            $checknotexist = $shippingaftermethod['checknotexist'];
                            $listzone_id = $shippingaftermethod['listzone_id'];
                        }
                    }else{

                        $shippingaftermethod = $this->swp_get_shipping_by_method($zone,$listmethods,$checknotexist,$listzone_id);
                        $listmethods = $shippingaftermethod['listmethods'];
                        $checknotexist = $shippingaftermethod['checknotexist'];
                        $listzone_id = $shippingaftermethod['listzone_id'];
                    }
                }
                global $wpdb;
                $table = $wpdb->prefix."terms";	
                $intable = $wpdb->prefix."term_taxonomy";			
                $shippingsclass = $wpdb->get_results("
                    SELECT * FROM $table as t INNER JOIN $intable as taxo ON t.term_id = taxo.term_id  WHERE taxonomy = 'product_shipping_class'
                ");	
                if(empty($listmethods)){
                    $default = new WC_Shipping_Zone(0);
                    $default_name = $default->get_zone_name();
                    $default_methods = $default->get_shipping_methods();
                    foreach($default_methods as $default_method){				
                        $instance_settings = $default_method->instance_settings;
                        $id = $default_method->id;
                        $idinstance = $default_method->instance_id;
                        $listmethods[] = array(
                            'id' => $id.':'.$idinstance,
                            'country' => array('default'),
                            'states' => array('default'),
                            'format_zone' => 'default',
                            'postcode' => array('default'),
                            'name' => $default_name,
                            'enabled' => $default_method->enabled,
                            'instance_settings' => $instance_settings,
                            'zone_id' => 0,
                            'zone_method' => '0_0'
                        );	

                    }
                }
                if(!empty($listmethods)){
                    $list = array();
                    $instance_settings = array();
                    foreach($listmethods as $listmethod){
                        if(isset($listmethod['instance_settings'])){
                            $instance_settings = $listmethod['instance_settings'];
                            $id = $listmethod['id'];
                            $name = $listmethod['name'];								
                            if(strpos($id, 'flat_rate')!== false || strpos($id, 'local_pickup')!== false){
                                $coststring = $instance_settings['cost'];				
                                $cost = $coststring;				
                            }
                            else{
                                $cost = 0;
                            }	
                            $classshipping = array();
                            if(!empty($shippingsclass)){
                                foreach($shippingsclass as $shippingclass){
                                    $index = 'class_cost_'.$shippingclass->term_id;				
                                    if(isset($instance_settings[$index])){
                                        $classshipping[$shippingclass->term_id] = $instance_settings[$index];
                                    }
                                }	
                            }				
                            $list[] = array(
                                'id' => $id,
                                'name' => $name,
                                'enabled' => $listmethod['enabled'],
                                'country' => $listmethod['country'],
                                'states' => $listmethod['states'],
                                'postcode' => $listmethod['postcode'],
                                'format_zone' => $listmethod['format_zone'],
                                'title' => isset($instance_settings['title']) ? $instance_settings['title'] : "",					
                                'class_shipping' => !empty($classshipping) ? $classshipping : "",
                                'no_class_cost' => isset($instance_settings['no_class_cost']) ? $instance_settings['no_class_cost'] : "",
                                'tax_status' => isset($instance_settings['tax_status']) ? $instance_settings['tax_status'] : "",
                                'cost' => isset($instance_settings['cost']) ? $cost : 0,
                                'requires'=> isset($instance_settings['requires']) ? $instance_settings['requires'] : "",
                                'min_amount'=> isset($instance_settings['min_amount']) ? $instance_settings['min_amount'] : "",
                                'zone_id' => $listmethod['zone_id'],
                                'zone_method' => $listmethod['zone_method']
                            );
                        }else{
                            continue;
                        }				
                    }			
                    return apply_filters('swp_app_get_shipping',$list );	
                }
            }else{
                return null;
            }		
        }
        
        /*************************************
        * get payment  
        * @param WP_REST_Request $request current Request
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_payment(){
        
        }
        
        /*************************************
        * add coupons  
        * @param WP_REST_Request $request current Request
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_add_coupon( $request ){
            $parameters = $request->get_params();
            $pro = $parameters['product'];	
            if(isset($parameters['woo_currency'])){
                $currentkey = $parameters['woo_currency'];					
            }else{
                $currentkey = strtolower(get_woocommerce_currency());
            }
            $currencys = WooConnectorGetCurrency($currentkey);
            $number_of_decimals   = $currencys['number_of_decimals'];
            $currencysymbol       = $currencys['symbol'];
            $ratecurrency         = $currencys['rate'];
            $currency_position    = $currencys['position'];
            $thousand_separator   = $currencys['thousand_separator'];
            $decimal_separator    = $currencys['decimal_separator'];
            $coupons = isset($parameters['coupons']) ? json_decode($parameters['coupons']) : false;			
            $product = json_decode($pro);
            $product = $this->sorttotalsbyquantityobject($product);
            $list = array();
            $listdiscount = array();		
            foreach($product as $post){	
                $product_id = absint($post->product_id);			
                $quantity = $post->quantity;					
                $variation_id = !empty($post->variation_id) ? absint($post->variation_id) : null;
                $addons = !empty($post->addons) ? $post->addons : array();
                $error = $this->validateInput($product_id,$variation_id,$quantity);			
                if(!empty($error)){
                    $list['error'][$product_id] = $error;
                    if($error['code'] !== 'rest_order_stock_error'){
                        continue;
                    }
                }
                $list['product'][] = $this->getTotalnoCoupon($post,$request,$addons);						
            }	
            if(!empty($list['error'])){
                foreach($list['error'] as $postid => $listerror){
                    $listdiscount['errors'][$postid] = $listerror;
                }
            }
            if(empty($list['product'])){
                return $listdiscount;
            }else{
                $totals = $list['product'];
            }
            $totalsubprice = 0;	
            $totaltax = 0;
            $totalquantity = 0;
            $basetotal = 0;
            $basetax = 0;
            $quantitytotals = 0;
            foreach($totals as $total){			
                $totalsubprice += $total['subtotal'];
                $basetotal += $total['subtotal'];				
                $totaltax += $total['tax'];	
                $basetax += $total['tax'];					
                $totalquantity += $total['quantity'];
                $quantitytotals++;							
            }	
            $baselisttotals = $totals;			
            $checkidden = 0;
            $checkcoupon = array();	
            if(!empty($coupons)){
                $totalcoupons = 0;	
                foreach($coupons as $coupon){
                    $totalcoupons++;		
                }
            }
            $totalsafterdiscount = array();						
            if(empty($coupons))	{
                $listdiscount['total'] = $this->gettotal($request);
            }
            else{	
                $checkerrorcoupons = array();	
                $listbeforetotal = array();		
                $listtotal = array();		
                foreach($coupons as $coupon){
                    $cp = new WC_Coupon( $coupon );	
                    $data = $cp->get_data();										
                    $format_decimal = array( 'amount', 'minimum_amount', 'maximum_amount' );
                    foreach ( $format_decimal as $key ) {
                        $data[ $key ] = (float)wc_format_decimal( $data[ $key ], $number_of_decimals );
                    }					
                    $checkerrorcoupons = $this->validateCoupon($data,$basetotal);
                    if(empty($checkerrorcoupons)){
                        $checkerrorcoupons['resulterror'] = 0;
                    }
                    if(empty($checkerrorcoupons['typeerror'])){
                        $checkerrorcoupons['typeerror'] = 'default';
                    }
                    if(empty($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']])){
                        $checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] = 0;
                    }
                    $checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] = $checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] + $checkerrorcoupons['resulterror'];
                    if($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] <= $totalcoupons && !empty($checkerrorcoupons['typeerror']) && $checkerrorcoupons['typeerror'] == 'notexist'){
                        $listdiscount['errors'][$data['code']] =  new WP_Error( 'rest_coupon_error', __( 'Sorry, Coupon not exist.','wooconnector' ), array( 'status' => 401 ));
                        continue;
                    }elseif($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] <= $totalcoupons && !empty($checkerrorcoupons['typeerror']) && $checkerrorcoupons['typeerror'] == 'minmax'){
                        $listdiscount['errors'][$data['code']] =  new WP_Error( 'rest_coupon_error', __( 'Sorry, Amount not suitable for Coupon. '.$coupon ,'wooconnector' ), array( 'status' => 401 ) );
                        continue;
                    }elseif($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] <= $totalcoupons && !empty($checkerrorcoupons['typeerror']) && $checkerrorcoupons['typeerror'] == 'expires'){
                        $listdiscount['errors'][$data['code']] =  new WP_Error( 'rest_coupon_error', __( 'Sorry, Coupon has expired.','wooconnector' ), array( 'status' => 401 ) );
                        continue;
                    }elseif($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] <= $totalcoupons && !empty($checkerrorcoupons['typeerror']) && $checkerrorcoupons['typeerror'] == 'usagelimit'){
                        $listdiscount['errors'][$data['code']] =  new WP_Error( 'rest_coupon_error', __( 'Sorry, Coupon usage limit has been reached.','wooconnector' ), array( 'status' => 401 ) );
                        continue;
                    }elseif($checkerrorcoupons[$data['code']][$checkerrorcoupons['typeerror']] <= $totalcoupons && !empty($checkerrorcoupons['typeerror']) && $checkerrorcoupons['typeerror'] == 'usagelimitperuser'){
                        $listdiscount['errors'][$data['code']] = new WP_Error( 'rest_coupon_error', __( 'Sorry, Coupon usage limit per user has been reached.','wooconnector' ), array( 'status' => 401 ) );
                        continue;
                    }
                    if(!in_array($coupon,$checkcoupon)){
                        if(!empty($checkcoupon)){
                            $checkerroridden = 0;
                            foreach($checkcoupon as $ckcoup){
                                $ckcp = new WC_Coupon( $ckcoup );									
                                $checkdata = $ckcp->get_data();												
                                if($checkdata['individual_use'] == true && $checkidden > 0)	{
                                    $checkerroridden++;
                                }
                            }
                            if($checkerroridden > 0){
                                $listdiscount['errors'][$data['code']] = new WP_Error( 'rest_coupon_error_exists', __( 'Sorry, Coupon only one.','wooconnector' ), array( 'status' => 401 ) );
                                continue;
                            }	
                        }																										
                        if($data['individual_use'] == true && $checkidden > 0)	{
                            $listdiscount['errors'][$data['code']] = new WP_Error( 'rest_coupon_error_delete', __( 'Sorry, Coupon only one.','wooconnector' ), array( 'status' => 401 ) );
                            continue;
                        }						
                        array_push($checkcoupon,$coupon);
                        $checkidden++;												
                    }
                    else{				
                        $listdiscount['errors'][$data['code']] = new WP_Error( 'rest_coupon_error', __( 'Sorry, Coupon already applied!.','wooconnector' ), array( 'status' => 401 ) );
                        continue;			
                    }
                    if($data['discount_type'] === 'percent' ){						
                        if($data['limit_usage_to_x_items'] > 0 && $totalquantity > $data['limit_usage_to_x_items'])	{
                            $totalperafterchangequantity = array();
                            $calculatorDetails = $this->getProductXquantity($totals,$data,$checkerrorcoupons,$listdiscount,0,$totalsubprice,$listtotal,$listbeforetotal,$ratecurrency,$totalsafterdiscount,$quantitytotals,$request,$baselisttotals,$basetotal);	
                            $totalperafterchangequantity = $calculatorDetails['totalperafterchangequantity'];
                            $listtotal = $calculatorDetails['listtotal'];										
                            $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                            $totalsubprice = $calculatorDetails['totalsubprice'];
                            $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                            $listdiscount = $calculatorDetails['listdiscount'];	
                            if(!empty($totalperafterchangequantity)){
                                $checktotals = ($basetotal*$data['amount'])/100;
                                $calculatorDetails = $this->calculatorDetailPercentCoupon($listtotal,$data,$totalperafterchangequantity,$totalsubprice,$basetotal,$ratecurrency,$checktotals,$baselisttotals,$quantitytotals,$totalsafterdiscount,$listbeforetotal,$listdiscount,$checkerrorcoupons);
                                $listtotal = $calculatorDetails['listtotal'];										
                                $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                                $totalsubprice = $calculatorDetails['totalsubprice'];
                                $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                                $listdiscount = $calculatorDetails['listdiscount'];
                            }						
                        }
                        else{	
                            $checktotals = ($basetotal*$data['amount'])/100;
                            $calculatorDetails = $this->calculatorDetailPercentCoupon($listtotal,$data,$baselisttotals,$totalsubprice,$basetotal,$ratecurrency,$checktotals,$baselisttotals,$quantitytotals,$totalsafterdiscount,$listbeforetotal,$listdiscount,$checkerrorcoupons);																																	
                            $listtotal = $calculatorDetails['listtotal'];										
                            $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                            $totalsubprice = $calculatorDetails['totalsubprice'];
                            $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                            $listdiscount = $calculatorDetails['listdiscount'];		
                        }						
                    }elseif($data['discount_type'] === 'fixed_cart'){					
                        $checktotals = $data['amount'];
                        $calculatorDetails = $this->calculatorDetailCartCoupon($listtotal,$data,$baselisttotals,$totalsubprice,$basetotal,$ratecurrency,$checktotals,$baselisttotals,$quantitytotals,$totalsafterdiscount,$listbeforetotal,$listdiscount,$checkerrorcoupons);																													
                        $listtotal = $calculatorDetails['listtotal'];										
                        $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                        $totalsubprice = $calculatorDetails['totalsubprice'];
                        $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                        $listdiscount = $calculatorDetails['listdiscount'];																					
                    }elseif($data['discount_type'] === 'fixed_product'){
                        if($data['limit_usage_to_x_items'] > 0 && $totalquantity > $data['limit_usage_to_x_items'])	{	
                            $totalproafterchangequantity = array();
                            $calculatorDetails = $this->getProductXquantity($totals,$data,$checkerrorcoupons,$listdiscount,0,$totalsubprice,$listtotal,$listbeforetotal,$ratecurrency,$totalsafterdiscount,$quantitytotals,$request,$baselisttotals,$basetotal);
                            $totalproafterchangequantity = $calculatorDetails['totalperafterchangequantity'];
                            $listtotal = $calculatorDetails['listtotal'];										
                            $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                            $totalsubprice = $calculatorDetails['totalsubprice'];
                            $listdiscount = $calculatorDetails['listdiscount'];	
                            $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                            if(!empty($totalproafterchangequantity)){
                                $calculatorDetails = $this->calculatorDetailProductCoupon($listtotal,$totalproafterchangequantity,$data,$listbeforetotal,$totalsubprice,$ratecurrency,$baselisttotals,$quantitytotals,$totalsafterdiscount,$listdiscount,$checkerrorcoupons);					
                                $listtotal = $calculatorDetails['listtotal'];																	
                                $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                                $totalsubprice = $calculatorDetails['totalsubprice'];
                                $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                                $listdiscount = $calculatorDetails['listdiscount'];	
                            }
                        }else{
                            $calculatorDetails = $this->calculatorDetailProductCoupon($listtotal,$baselisttotals,$data,$listbeforetotal,$totalsubprice,$ratecurrency,$baselisttotals,$quantitytotals,$totalsafterdiscount,$listdiscount,$checkerrorcoupons);					
                            $listtotal = $calculatorDetails['listtotal'];																	
                            $totalsafterdiscount = $calculatorDetails['totalsafterdiscount'];	
                            $totalsubprice = $calculatorDetails['totalsubprice'];
                            $checkerrorcoupons = $calculatorDetails['checkerrorcoupons'];
                            $listdiscount = $calculatorDetails['listdiscount'];
                        }										
                    }					
                }								
                $checkcode = array();
                $totaldiscountaftercalcu = 0;
                if(empty($listtotal)){
                    $listdiscount['total'] = $this->gettotal($request);
                    return $listdiscount;
                }else{
                    foreach($listtotal as $code){
                        $checkcode[] = $code['couponcode'];
                    }
                }			
                $codes = array_unique( $checkcode );									
                foreach($codes as $code){
                    $totaldiscount = 0;
                    $totaltaxdiscount = 0;				
                    foreach($listtotal as $calcu){
                        if($calcu['couponcode'] == $code){							
                            $totaldiscount += $calcu['totaldiscount'];														
                            $output[] = (float)$totaldiscount;
                            $outputpop = array_pop($output);
                        }					
                    }
                    $listdiscountoutput[$code] = $outputpop;	
                }
                $outdiscounts = array();
                foreach($listdiscountoutput as $outdiscount => $values){
                    foreach($coupons as $coupon){
                        $cp = new WC_Coupon($coupon);
                        $data = $cp->get_data();
                        if($data['code'] == $outdiscount){
                            $listout = array(
                                'code' => $outdiscount,
                                'value' => number_format(floatval($values), $number_of_decimals, $decimal_separator, $thousand_separator)
                            );
                            if($data['discount_type'] == 'percent'){
                                $listout['amount'] = $data['amount'].'%';
                            }else{
                                $listout['amount'] = number_format(floatval($data['amount']*$ratecurrency), $number_of_decimals, $decimal_separator, $thousand_separator);
                            }
                            $outdiscounts[] = $listout;
                        }
                    }
                }		
                foreach($listdiscountoutput as $calcudiscount){
                    $totaldiscountaftercalcu += $calcudiscount;
                }
                $totalafteraddcoupon = $basetotal - $totaldiscountaftercalcu;
                $taxs = new WC_Tax();
                $listrates = array();				
                foreach($totalsafterdiscount as $afterpost){	
                    $product_id = absint($afterpost['product_id']);
                    $variation_id = !empty($afterpost['variation_id']) ? absint($afterpost['variation_id']) : null;					
                    $subtotal = $afterpost['subtotal'];
                    if(!empty($variation_id) || $variation_id != null){
                        $pros = wc_get_product($product_id);
                        $taxstatus = $pros->get_tax_status();
                        $provari = wc_get_product($variation_id);
                        $taxclass = $provari->get_tax_class();
                        if($taxstatus == 'taxable'){
                            $rates = $taxs->get_rates($taxclass);
                            $listrates[] = $taxs->get_rates($taxclass);
                            $variations = $pros->get_available_variations();
                            foreach($variations as $variation => $value){
                                if($value['variation_id'] == $variation_id){
                                    $tax[] = $taxs->calc_tax($subtotal,$rates);
                                }
                            }
                        }
                    }else{
                        $pros = wc_get_product($product_id);
                        $taxclass = $pros->get_tax_class();
                        $taxstatus = $pros->get_tax_status();
                        if($taxstatus == 'taxable'){
                            $rates = $taxs->get_rates($taxclass);
                            $listrates[] = $taxs->get_rates($taxclass);
                            $tax[] = $taxs->calc_tax($subtotal,$rates);
                        }

                    }								
                }
                $listratesafter = array();
                foreach($listrates as $rate => $vals){
                    foreach($vals as $val => $v){
                        $listratesafter[$val] = $v;
                    }					
                }
                $listtaxouts = array();	
                $totalstaxaftercoupon = 0;
                if(!empty($tax)){
                    foreach($tax as $ttax => $values){
                        foreach($values as $totalstax => $valx){
                            $label = $taxs->get_rate_label($totalstax);
                            if(!empty($listtaxouts[$label])){
                                $listtaxouts[$label] += $valx;
                            }else{
                                $listtaxouts[$label] = $valx;
                            }
                            $totalstaxaftercoupon += $valx;
                        }
                    }	
                    $outtax = array();
                    foreach($listtaxouts as $outtaxs => $values){
                        $outtax[] = array(
                            'code' => $outtaxs,
                            'value' => (float)wc_format_decimal($values,$number_of_decimals)
                        );
                    }
                }else{
                    $outtax = null;
                }
                $formatcurrencyafterdiscount = array();
                $baseitems = $this->getListproduct($request);			
                foreach($totalsafterdiscount as $total){					
                    $formatcurrencyafterdiscount[] = array(
                        'product_id' => $total['product_id'],
                        'quantity' => $total['quantity'],
                        'variation_id' => $total['variation_id'],
                        'subtotal' => (float)wc_format_decimal($total['subtotal'], $number_of_decimals),
                        'class_shipping_id' => $total['class_shipping_id']
                    );					
                }
                $totalsaftercou = $totalafteraddcoupon + $totalstaxaftercoupon;
                $listdiscount['baseitem'] = $baseitems;
                $listdiscount['subtotal'] = (float)wc_format_decimal($basetotal,$number_of_decimals);				
                $listdiscount['discount'] = $outdiscounts;
                $listdiscount['baseitemaftercoupon'] = $formatcurrencyafterdiscount;
                $listdiscount['subtotalaftercoupon'] = (float)wc_format_decimal($totalafteraddcoupon,$number_of_decimals);	
                $listdiscount['tax'] = $outtax;
                $listdiscount['total'] = (float)wc_format_decimal($totalsaftercou,$number_of_decimals);
            }		
            return apply_filters('swp_app_add_coupon',$listdiscount,$product,$coupons);

        }
        
        /*************************************
        * get valid input
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_valid_input( $product_id, $variation_id, $quantity ){
        
        }
        
        /*************************************
        * get valid input
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_check_in_postcode( ){
            
        }
            
        /*************************************
        * get valid input
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        private function swp_get_shipping_by_method( $zone,$listmethods,$checknotexist,$listzone_id,$postcode = '',$listpost = array(),$checkpost = false ){
            $zoneid = $zone['zone_id'];
            if($checkpost){
			$listshippingafterpostcode = $this->swp_get_shipping_by_postcode($zone,$listmethods,$listpost,$postcode,$checknotexist,$listzone_id);
			$listmethods = $listshippingafterpostcode['listmethods'];
			$checknotexist = $listshippingafterpostcode['checknotexist'];
			$listzone_id = $listshippingafterpostcode['listzone_id'];
            }else{
			if(empty($listmethods)){
				$methods = $zone['shipping_methods'];
				$methodsafter = $this->swp_get_shipping_content_by_method($zone,$checknotexist);
				$listmethods = $methodsafter['listmethods'];
				$checknotexist = $methodsafter['checknotexist'];
				array_push($listzone_id,$zoneid);
			}else{
				if(in_array($zoneid,$listzone_id)){
					$methods = $zone['shipping_methods'];
					$methodsafter = $this->swp_get_shipping_content_by_method($zone,$checknotexist);
					$listmethods = $methodsafter['listmethods'];
					$checknotexist = $methodsafter['checknotexist'];
				    }
                }
            }	
            $content = 	array(
                'listmethods' => $listmethods,
                'checknotexist' => $checknotexist,
                'listzone_id' => $listzone_id
            );
		
            return apply_filters('wooconnector_get_shipping_method',$content,$zone,$postcode,$listpost);
        }
        
        /*************************************
        * get shipping postcode
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_shipping_by_postcode( ){
            
        }
        
        /*************************************
        * get product total no coupon add
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_total_no_coupon( $post,$request,$addons = array() ){
            $params = $request->get_params();
            if(isset($params['woo_currency'])){
                $currentkey = $params['woo_currency'];					
            }else{
                $currentkey = strtolower(get_woocommerce_currency());
            }
            $currencys = WooConnectorGetCurrency($currentkey);
            $ratecurrency = $currencys['rate'];
            $number_of_decimals = $currencys['number_of_decimals'];
            $postid = absint($post->product_id);
            $quantity = $post->quantity;
            $idvar = !empty($post->variation_id) ? absint($post->variation_id) : null;
            $pros = wc_get_product($postid);
            $status = $pros->get_tax_status();
            $pro_v = new WC_Product_Variable($postid);
            $productvari = $pro_v->get_available_variations();
            if($idvar != null || !empty($idvar)){
                $ratec = $this->getRates($idvar);	
                $provari = 	wc_get_product($idvar);
                $shippingid = $provari->get_shipping_class_id();
                foreach($productvari as $value => $val){
                    if($idvar == $val['variation_id']){
                        $subtotal = 0;
                        $tax = 0;
                        if(WC_VERSION < 3.1){
                            $subtotal = ($val['price'])*$quantity;
                            if($status == 'taxable'){					
                                $tax = (($val['price'])*$ratec/100)*$quantity;
                            }else{
                                $tax = 0;
                            }
                        }else{
                            $subtotal = ($val['display_price'])*$quantity;
                            if($status == 'taxable'){					
                            $tax = (($val['display_price'])*$ratec/100)*$quantity;
                            }else{
                                $tax = 0;
                            }
                        }		
                        $total = $subtotal + $tax;		
                        $list = array(
                            'subtotal' => (float)wc_format_decimal($subtotal,$number_of_decimals),
                            'tax'      => (float)wc_format_decimal($tax,$number_of_decimals),
                            'total'    => (float)wc_format_decimal($total,$number_of_decimals),
                            'product_id'   => $postid,
                            'quantity'   => $quantity,
                            'variation_id'   => $idvar,
                            'class_shipping_id' => $shippingid
                        );
                    }else{
                        continue;
                    }	
                }
            }
            else{
                $shippingid = $pros->get_shipping_class_id();
                $ratec = $this->getRates($postid);	
                $subtotal = ($pros->get_price())*$quantity;
                if($status == 'taxable'){
                    $tax = (($pros->get_price())*$ratec/100)*$quantity;
                }else{
                    $tax = 0;
                }
                $total = $subtotal + $tax;
                $list = array(
                    'subtotal' => (float)wc_format_decimal($subtotal,$number_of_decimals),
                    'tax'      => (float)wc_format_decimal($tax,$number_of_decimals),
                    'total'    => (float)wc_format_decimal($total,$number_of_decimals),
                    'product_id'   => $postid,
                    'quantity'   => $quantity,
                    'variation_id'   => $idvar,
                    'class_shipping_id' => $shippingid
                );
            }
            if(!empty($addons) && is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php')){
                $i = 0;
                $idaddons = $addons->id;
                $positionaddons = $addons->positions;
                $meta_data = $pros->get_meta_data();
                $listoption = array();
                $optionprices = 0;
                foreach($meta_data as $meta){
                    if($meta->id == $idaddons && $meta->key == '_product_addons'){
                        $optionprice = 0;
                        $valueaddons = $meta->value;
                        foreach($positionaddons as $position){
                            $positionindex = $position->position;
                            $positionvalues = $position->value;
                            foreach($valueaddons as $val){
                                if($val['position'] == $positionindex){
                                    $options = $val['options'];
                                    for($i; $i < count($options); $i++){
                                        foreach($positionvalues as $valuep){
                                            if($i === $valuep){
                                                $price = (float)wc_format_decimal( ($options[$i]['price'] * $ratecurrency), $number_of_decimals );
                                                $optionprice += $price;
                                                $listoption[] = array(
                                                    'name'  => $val['name'],
                                                    'value' => $options[$i]['label'],
                                                    'price' => $price,
                                                );
                                            }else{
                                                continue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $optionprices += $optionprice;
                    }
                }
                $list['subtotal'] = $list['subtotal'] + $optionprices;
                if($idvar != null || !empty($idvar)){
                    $ratec = $this->getRates($idvar);
                }else{
                    $ratec = $this->getRates($postid);
                }
                if($status == 'taxable'){					
                    $list['tax'] = $list['subtotal']*($ratec/100);
                }else{
                    $tax = 0;
                }
                $list['total'] = $list['subtotal'] + $list['tax'];
                $list['addons'] = $listoption;
            }
            return apply_filters('swp_get_total',$list,$post,$addons);

        }
        
        /*************************************
        * get shipping postcode
        * @param WP_REST_Request $tot_product_id, $tot_variation_id, $tot_quantity
        * products is required in Request
        * @return mixed
        **************************************/
        public function swp_get_shipping_content_by_method( $zone,$checknotexist ){
            
            $name = $zone['zone_name'];	
            $methods = $zone['shipping_methods'];
            $zonelocations = $zone['zone_locations'];
            $ship = $zone['formatted_zone_location'];
            $listzone = array();
            $listpost = array();
            $liststate = array();
            foreach($zonelocations as $zonelocation){
                if($zonelocation->type == 'country'){
                    $listzone[] = $zonelocation->code;
                }
                if($zonelocation->type == 'postcode'){
                    $listpost[] = $zonelocation->code;
                }	
                if($zonelocation->type == 'state'){
                    $liststate[] = $zonelocation->code;
                }		
            }
            $listmethods = array();
            foreach($methods as $method){
                $id = $method->id;
                $idinstance = $method->instance_id;
                $idshipping = $id.':'.$idinstance;
                if(!in_array($idshipping,$checknotexist)){
                    $instance_settings = $method->instance_settings;				
                    $listmethods[] = array(
                        'id' => $idshipping,
                        'name' => $name,
                        'country' => $listzone,
                        'states' => $liststate,
                        'format_zone' => $ship,
                        'postcode' => $listpost,
                        'enabled' => $method->enabled,
                        'instance_settings' => $instance_settings,
                        'zone_id' => $zone['zone_id'],
                        'zone_method' => $zone['zone_id'].'_'.$idinstance
                    );	
                    array_push($checknotexist,$idshipping);				
                }
            }
            $content = array(
                'listmethods' => $listmethods,
                'checknotexist' => $checknotexist
            );
            return apply_filters('wooconnector_get_content_shipping',$content,$zone);
        }
        
        private function swp_validate_input( $product_id,$variation_id,$quantity ){
            $getpro = wc_get_product($product_id);
            $getpost = get_post($product_id);
            if(!is_object($getpro) || !empty($getpro) && $getpro->get_id() <= 0 || empty($getpro) || !empty($getpost) && $getpost->post_status == 'trash'){
                return $this->returnErrorInput( 'rest_product_error', __( 'Sorry, Product not exits.','wooconnector' ),$product_id, array( 'status' => 401 ) );
            }	
            $checkorder = $getpro;
            if($variation_id != 0){
                $checkorder = wc_get_product($variation_id);
            }
            if ( get_option( 'woocommerce_hold_stock_minutes' ) > 0 && ! $checkorder->backorders_allowed() && $checkorder->get_manage_stock() !== false) {
                global $wpdb;
                $order_id   = isset( WC()->session->order_awaiting_payment ) ? absint( WC()->session->order_awaiting_payment ) : 0;
                $held_stock = $wpdb->get_var(
                    $wpdb->prepare(
                        "
                        SELECT SUM( order_item_meta.meta_value ) AS held_qty
                        FROM {$wpdb->posts} AS posts
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_items as order_items ON posts.ID = order_items.order_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta2 ON order_items.order_item_id = order_item_meta2.order_item_id
                        WHERE 	order_item_meta.meta_key   = '_qty'
                        AND 	order_item_meta2.meta_key  = %s AND order_item_meta2.meta_value  = %d
                        AND 	posts.post_type            IN ( '" . implode( "','", wc_get_order_types() ) . "' )
                        AND 	posts.post_status          = 'wc-pending'
                        AND		posts.ID                   != %d;",
                        'variation' === get_post_type( $checkorder->get_stock_managed_by_id() ) ? '_variation_id' : '_product_id',
                        $checkorder->get_stock_managed_by_id(),
                        $order_id
                    )
                );

                if ( $checkorder->get_stock_quantity() < ( $held_stock + $quantity ) ) {
                    return $this->returnErrorInput( 'rest_order_stock_error', sprintf( __( 'Sorry, we do not have enough %1$s in stock to fulfill your order right now. Please try again in %2$d minutes or edit your cart and try again. We apologize for any inconvenience caused.', 'wooconnector' ),  apply_filters('mobiconnector_languages',$checkorder->get_name()), get_option( 'woocommerce_hold_stock_minutes' ) ),$product_id, array( 'status' => 401 ) );
                }
            }		
            $name = apply_filters('mobiconnector_languages',$getpro->get_name());
            $parent = $getpro->get_parent_id();
            $post = get_post($product_id);
            $type = $post->post_type;
            if($parent !== 0 || $type != 'product'){
                return $this->returnErrorInput( 'rest_product_error', __( 'Sorry, This is not product.','wooconnector' ),$product_id, array( 'status' => 401 ) );
            }	
            if($quantity <= 0){
                return $this->returnErrorInput( 'rest_quantity_error', sprintf(__( 'Sorry, Product %s quantity for must be greater than 0.','wooconnector' ),$name),$product_id, array( 'status' => 401 ) );
            }	
            $product_vari = null;
            $namevariation = '';	
            if($variation_id != 0){
                $product_vari = wc_get_product($variation_id);
                $namevariation = apply_filters('post_title',$product_vari->get_name());
                $checkids = $getpro->get_children();
                if(!in_array($variation_id,$checkids)){
                    return $this->returnErrorInput( 'rest_variation_error', sprintf(__( 'Sorry, Variation %1$s does not belong to Products %2$s.','wooconnector' ),$namevariation,$name),$product_id, array( 'status' => 401 ) );
                }
            }
            if($variation_id != 0){
                $sold_individually = $product_vari->get_sold_individually();
                if($sold_individually){
                    if($quantity != 1){
                        return $this->returnErrorInput( 'rest_quantity_error', sprintf(__( 'Sorry, Product %s only sells quantity 1 .','wooconnector' ),$namevariation),$product_id, array( 'status' => 401 ) );
                    }
                }
            }else{
                $sold_individually = $getpro->get_sold_individually();
                if($sold_individually){
                    if($quantity != 1){
                        return $this->returnErrorInput( 'rest_quantity_error', sprintf(__( 'Sorry, Product %s only sells quantity 1 .','wooconnector' ),$name),$product_id, array( 'status' => 401 ) );
                    }
                }
            }
            $stock = 'outofstock';
            $stockquantity = 0;
            $backorderAllow = false;
            $backorders = 'no';
            $managestock = false;
            $iderror = 0;
            $nameout = '';
            if($getpro->is_type( 'variable' ) && $getpro->has_child()){
                $variation = wc_get_product($variation_id);
                $stock = $variation->get_stock_status();
                $stockquantity = $variation->get_stock_quantity();
                $managestock = $variation->get_manage_stock();
                $backorders = $variation->get_backorders();
                $backorderAllow = $variation->backorders_allowed();
                $iderror = $variation_id;
                $nameout = apply_filters('post_title',$variation->get_name());
            }else{
                $stock = $getpro->get_stock_status();
                $stockquantity = $getpro->get_stock_quantity();
                $managestock = $getpro->get_manage_stock();
                $backorders = $getpro->get_backorders();
                $backorderAllow = $getpro->backorders_allowed();	
                $iderror = $product_id;		
                $nameout = apply_filters('post_title',$getpro->get_name());
            }
            if($stock == 'outofstock' && $managestock == false){
                return $this->returnErrorInput( 'rest_stock_error', sprintf(__( 'Sorry, Product %s out of stock.', 'wooconnector' ),$nameout),$iderror, array( 'status' => 401 ) );
            }
            if($stock == 'outofstock' && $managestock == true && $backorders == 'no' && $backorderAllow == false){
                return $this->returnErrorInput( 'rest_stock_error', sprintf(__( 'Sorry, Product %s out of stock.', 'wooconnector' ),$nameout),$iderror, array( 'status' => 401 ) );
            }
            if($stock == 'instock' && $managestock == true && $quantity > $stockquantity && ($backorders == 'no' || $backorderAllow == false)){
                return $this->returnErrorInput( 'rest_stock_error', sprintf(__( 'Sorry, Product not enough.', 'wooconnector' ),$nameout),$iderror, array( 'status' => 401 ) );
            }
        }
        
        /*************************************
        * get currency options
        * @param wp currency request
        * products is required in Request
        * @return mixed
        **************************************/
       
        public function swp_currency_options($request){
            $list = array(
                'currency'              => get_woocommerce_currency(),
                'currency_symbol'       => get_woocommerce_currency_symbol(),
                'currency_position'     => get_option( 'woocommerce_currency_pos' ),
                'thousand_separator'    => wc_get_price_thousand_separator(),
                'decimal_separator'     => wc_get_price_decimal_separator(),
                'number_of_decimals'    => wc_get_price_decimals(),
            );
            return $list;
        }
        
    /***** end swpappshipping class  *****/    
    }
/***** end if exists class *****/
}