<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WooConnectorHookCurrency{

    public $no_cents = array('JPY', 'TWD');
    public $basecurrency;
    public $current_currency;
    public $decimal_sep;
    public $thousand_sep;
    public $current_symbol;
    public $number_of_decimals;

    public function __construct(){
        if (!session_id()){
            @session_start();
        }
        $url = $_SERVER['REQUEST_URI'];
		$sUrl = substr($url,strpos($url,'wp-json'));
		$oUrl = $sUrl;
		if(strpos($sUrl,'?') != false){
			$oUrl = substr($sUrl,0,strpos($sUrl,'?'));
		}
		$tUrl = trim($oUrl,'/');
		$aUrl = explode('/',$tUrl);
        if(!is_admin() && wooconnector_is_rest_api()){
            $this->basecurrency = WooConnectorGetBaseCurrency();
            $currencycheck = get_woocommerce_currency();
            if($this->basecurrency != $currencycheck){
                update_option('wooconnector_get_base_currency',$currencycheck);
                $this->basecurrency = WooConnectorGetBaseCurrency();
            }
            $getcurrency = isset($_GET['woo_currency']) ? strtoupper($_GET['woo_currency']) : $this->basecurrency;
            if($this->wooconnector_check_exist_currency($getcurrency) == true){
                $this->genCurrencyBySession($getcurrency);
            }else{
                $this->current_currency = get_woocommerce_currency();
                $this->decimal_sep = wc_get_price_decimal_separator();
                $this->thousand_sep = wc_get_price_thousand_separator();
                $this->current_symbol = get_woocommerce_currency_symbol(get_woocommerce_currency());
                $this->number_of_decimals = wc_get_price_decimals();
            }
            $currencies = $this->wooconnector_get_currency_custom();
            if (isset($_REQUEST['action']) AND $_REQUEST['action'] == 'woocommerce_checkout') {
                $this->genCurrencyBySession($_SESSION['wooconnector_currency']);
            }
    
            if (isset($_REQUEST['woo_currency']) AND ! empty($_REQUEST['woo_currency'])) {
                if (array_key_exists($_REQUEST['woo_currency'], $currencies)) {
                    $this->genCurrencyBySession($_REQUEST['woo_currency']);
                }else{
                    $this->genCurrencyBySession($this->basecurrency);
                }
            }
    
            if (isset($_GET['woo_currency']) AND ! empty($_GET['woo_currency'])) {
                if (array_key_exists($_GET['woo_currency'], $currencies)) {
                    $_SESSION['wooconnector_currency'] = $_GET['woo_currency'];
                }
            }
            if(isset($_SESSION['wooconnector_currency'])){
                $this->genCurrencyBySession($_SESSION['wooconnector_currency']);
            }else{
                $this->current_currency = $this->basecurrency;
            }
            remove_all_filters('woocommerce_add_to_cart_hash');
            remove_all_filters('woocommerce_paypal_args');
            remove_all_filters('woocommerce_paypal_supported_currencies');
            remove_all_filters('woocommerce_currency_symbol');
            remove_all_filters('woocommerce_currency');
            remove_all_filters('woocommerce_product_get_price');
            remove_all_filters('woocommerce_product_variation_get_price');
            remove_all_filters('woocommerce_product_variation_get_regular_price');
            remove_all_filters('woocommerce_product_variation_get_sale_price');
            remove_all_filters('woocommerce_product_get_regular_price');
            remove_all_filters('woocommerce_product_get_sale_price');
            remove_all_filters('woocommerce_get_variation_regular_price');
            remove_all_filters('woocommerce_get_variation_sale_price');
            remove_all_filters('woocommerce_variation_prices');
            remove_all_filters('woocommerce_variation_prices_price');
            remove_all_filters('woocommerce_variation_prices_regular_price');
            remove_all_filters('woocommerce_variation_prices_sale_price');
            remove_all_filters('woocommerce_get_variation_prices_hash');
            remove_all_filters('woocommerce_price_format');
            remove_all_filters('woocommerce_package_rates');
            remove_all_filters('wc_price_args');
            remove_all_filters('woocommerce_before_mini_cart');
            remove_all_filters('woocommerce_after_mini_cart');
            remove_all_filters('woocommerce_shipping_free_shipping_is_available');
            remove_all_filters('woocommerce_shipping_legacy_free_shipping_is_available');
            remove_all_filters('woocommerce_coupon_get_discount_amount');
            remove_all_filters('woocommerce_coupon_validate_minimum_amount');
            remove_all_filters('woocommerce_coupon_validate_maximum_amount');
            remove_all_filters('woocommerce_evaluate_shipping_cost_args');
            remove_all_filters('woocommerce_thankyou_order_id');         
            add_filter('woocommerce_add_to_cart_hash', array($this, 'wooconnector_add_to_cart_hash'));
            add_filter('woocommerce_paypal_args',array($this,'wooconnector_apply_conversion'));
            add_filter('woocommerce_paypal_supported_currencies', array($this,'wooconnector_enable_currency'), 9999);
            add_filter('woocommerce_currency_symbol',array($this,'wooconnector_currency_symbol'), 9999);
            add_filter('woocommerce_currency', array($this,'wooconnector_get_current_currency'), 9999);
            add_filter('woocommerce_product_get_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 2);
            add_filter('woocommerce_product_variation_get_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 2);
            add_filter('woocommerce_product_variation_get_regular_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 2);
            add_filter('woocommerce_product_variation_get_sale_price', array($this, 'wooconnector_tmp_woocommerce_sale_price'), 9999, 2);
            add_filter('woocommerce_product_get_regular_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 2);
            add_filter('woocommerce_product_get_sale_price', array($this, 'wooconnector_tmp_woocommerce_sale_price'), 9999, 2);
            add_filter('woocommerce_get_variation_regular_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 4);
            add_filter('woocommerce_get_variation_sale_price', array($this, 'wooconnector_tmp_woocommerce_price'), 9999, 4);
            add_filter('woocommerce_variation_prices', array($this, 'wooconnector_variation_prices'), 9999, 3);
            add_filter('woocommerce_variation_prices_price', array($this, 'wooconnector_variation_prices'), 9999, 3);
            add_filter('woocommerce_variation_prices_regular_price', array($this, 'wooconnector_variation_prices'), 9999, 3);
            add_filter('woocommerce_variation_prices_sale_price', array($this, 'wooconnector_variation_prices'), 9999, 3);
            add_filter('woocommerce_get_variation_prices_hash', array($this, 'wooconnector_get_variation_prices_hash'), 9999, 3);
            add_filter('woocommerce_price_format', array($this, 'wooconnector_get_current_format'), 9999);
            add_filter('woocommerce_package_rates', array($this, 'wooconnector_package_rates'), 9999);
            add_filter('wc_price_args', array($this, 'wooconnector_price_args'), 9999);
            add_filter('woocommerce_before_mini_cart', array($this, 'wooconnector_before_mini_cart'), 9999);
            add_filter('woocommerce_after_mini_cart', array($this, 'wooconnector_after_mini_cart'), 9999);
            add_filter('woocommerce_shipping_free_shipping_is_available', array($this, 'wooconnector_shipping_free_shipping_is_available'), 99, 2);
            add_filter('woocommerce_shipping_legacy_free_shipping_is_available', array($this, 'wooconnector_shipping_free_shipping_is_available'), 99, 2);
            add_filter('woocommerce_coupon_get_discount_amount', array($this, 'wooconnector_coupon_get_discount_amount'), 9999, 5);
            add_filter('woocommerce_coupon_validate_minimum_amount', array($this, 'wooconnector_coupon_validate_minimum_amount'), 9999, 2);
            add_filter('woocommerce_coupon_validate_maximum_amount', array($this, 'wooconnector_coupon_validate_maximum_amount'), 9999, 2);
            add_filter("woocommerce_evaluate_shipping_cost_args", array($this, "wooconnector_fix_shipping_calc"), 10, 3);
            add_filter('woocommerce_thankyou_order_id', array($this, 'wooconnector_thankyou_order_id'), 9999);
        }
    }

    private function genCurrencyBySession($session){
        $this->current_currency = strtoupper($this->wooconnector_escape($session));
        $this->decimal_sep = $this->wooconnector_get_current_decimal_separator(); 
        $this->thousand_sep = $this->wooconnector_get_current_thousand_separator();
        $this->current_symbol = $this->wooconnector_get_current_currency_symbol();
        $this->number_of_decimals = $this->wooconnector_get_current_number_of_decimals();
    }

    public function wooconnector_check_exist_currency($currency){
        $currencys = get_option('wooconnector_currency_settings');
        if(empty($currencys)){
            return false;
        }
        $currencys = unserialize($currencys);
        if(empty($currencys[strtolower($currency)])){
            return false;
        }
        return true;
    }

    public function wooconnector_shipping_free_shipping_is_available($is_available, $package) {
      
        global $wpdb;
        global $woocommerce;
        $currencies = $this->wooconnector_get_currency_custom();
        $wc_shipping = WC_Shipping::instance();

        if ($wc_shipping->enabled) {
            if (!empty($wc_shipping->shipping_methods)) {
                foreach ($wc_shipping->shipping_methods as $key => $o) {
                    if (get_class($o) == 'WC_Shipping_Free_Shipping') {
                        $free_shipping_id = (int) $o->instance_id;
                        $free_shipping_settings = get_option('woocommerce_free_shipping_' . $free_shipping_id . '_settings');
                        $allows_array = array('min_amount', 'either', 'both');
                        if (in_array($free_shipping_settings['requires'], $allows_array)) {
                            $min_amount = $free_shipping_settings['min_amount'];
                            $amount = WC()->session->subtotal;

                            if (isset($package["cart_subtotal"]) AND $package["cart_subtotal"]) {
                                $amount = $package["cart_subtotal"];
                            }
                            if ($this->current_currency != $this->basecurrency) {
                                $amount = (float) $this->back_convert($amount, $currencies[strtolower($this->current_currency)]['rate']);
                            }
                            $range_float = 0.009;
                            if ($this->number_of_decimals > 2) {
                                $range_float = 0.00001;
                            }
                            if ($amount >= $min_amount OR abs($amount - $min_amount) <= $range_float) {
                                $is_available = true;
                            } else {
                                $is_available = false;
                            }
                            $free_shipping_coupon = false;
                            if (!empty($woocommerce->cart->applied_coupons)) {
                                $coupon = new WC_Coupon($woocommerce->cart->applied_coupons[0]);
                                $coupon_id = 0;
                                if (method_exists($coupon, 'get_id')) {
                                    $coupon_id = $coupon->get_id();
                                } else {
                                    $coupon_id = $coupon->id;
                                }
                                $free_shipping_coupon_val = get_post_meta($coupon_id, 'free_shipping', true);
                                if ($free_shipping_coupon_val == 'yes') {
                                    $free_shipping_coupon = true;
                                }
                            }

                            if ($free_shipping_settings['requires'] == 'both') {
                                if ($free_shipping_coupon AND ( $amount >= $min_amount OR abs($amount - $min_amount) <= $range_float )) {
                                    $is_available = true;
                                } else {
                                    $is_available = false;
                                }
                            }
                            if ($free_shipping_settings['requires'] == 'either') {
                                if ($free_shipping_coupon) {
                                    $is_available = true;
                                }

                                if ($amount >= $min_amount OR abs($amount - $min_amount) <= $range_float) {
                                    $is_available = true;
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        return $is_available;
    }

    public function wooconnector_package_rates($rates) {
        if ($this->current_currency != $this->basecurrency) {
            $currencies = $this->wooconnector_get_currency_custom();
            foreach ($rates as $rate) {
                $value = $rate->cost * $currencies[strtolower($this->current_currency)]['rate'];
                $position = $currencies[strtolower($this->current_currency)]['number_of_decimals'];
                $rate->cost = number_format(floatval($value), $position, $this->decimal_sep, '');
                if (isset($rate->taxes)) {
                    $taxes = $rate->taxes;
                    if (!empty($taxes)) {
                        foreach ($taxes as $order => $tax) {
                            $value_tax = $tax * $currencies[strtolower($this->current_currency)]['rate'];
                            $value_tax = number_format(floatval($value_tax), $position, $this->decimal_sep, '');
                            $taxes[$order] = $value_tax;
                        }
                    }
                    $rate->taxes = $taxes;
                }
            }
        }
        return $rates;
    }

    public function wooconnector_variation_prices($prices_array){
        if (!empty($prices_array) AND is_array($prices_array)) {
            foreach ($prices_array['regular_price'] as $key => $value) {
                if ($value === $prices_array['sale_price'][$key]) {
                    unset($prices_array['sale_price'][$key]);
                }
            }

            foreach ($prices_array as $key => $values) {
                if (!empty($values)) {
                    foreach ($values as $product_id => $price) {
                        $prices_array[$key][$product_id] = $this->wooconnector_calculator_custom_price(floatval($price));
                    }
                }
            }
        }
        if (!empty($prices_array) AND is_array($prices_array)) {
            foreach ($prices_array as $key => $arrvals) {
                asort($arrvals);
                $prices_array[$key] = $arrvals;
            }
        }
    
        if (empty($prices_array['sale_price'])) {
            if (isset($prices_array['regular_price'])) {
                $prices_array['price'] = $prices_array['regular_price'];
            }
        } 
        return $prices_array;
    }

    public function wooconnector_price_args($default_args) {
        if (in_array($this->current_currency, $this->no_cents)) {
            $default_args['decimals'] = 0;
        }
        return $default_args;
    }
    
    public function wooconnector_apply_conversion($paypal_args) {
        if (in_array($this->current_currency, $this->no_cents)) {
            $paypal_args['currency_code'] = $this->current_currency;
            foreach ($paypal_args as $key => $value) {
                if (strpos($key, 'amount_') !== false) {
                    $paypal_args[$key] = number_format($value, 0, $this->decimal_sep, '');
                } else {
                    if (strpos($key, 'tax_cart') !== false) {
                        $paypal_args[$key] = number_format($value, 0, $this->decimal_sep, '');
                    }
                }
            }
        }
        return $paypal_args;
    }

    public function wooconnector_enable_currency($currency_array) {
        $currency_array[] = 'USD';
        $currency_array[] = 'AUD';
        $currency_array[] = 'BRL';
        $currency_array[] = 'CAD';
        $currency_array[] = 'CZK';
        $currency_array[] = 'DKK';
        $currency_array[] = 'EUR';
        $currency_array[] = 'HKD';
        $currency_array[] = 'HUF';
        $currency_array[] = 'ILS';
        $currency_array[] = 'JPY';
        $currency_array[] = 'MYR';
        $currency_array[] = 'MXN';
        $currency_array[] = 'NOK';
        $currency_array[] = 'NZD';
        $currency_array[] = 'PHP';
        $currency_array[] = 'PLN';
        $currency_array[] = 'GBP';
        $currency_array[] = 'RUB';
        $currency_array[] = 'SGD';
        $currency_array[] = 'SEK';
        $currency_array[] = 'CHF';
        $currency_array[] = 'TWD';
        $currency_array[] = 'THB';
        $currency_array[] = 'TRY';
        return $currency_array;
    }
    
    public function wooconnector_currency_symbol($currency_symbol) {
        if(is_admin()){
           return $currency_symbol;
        }else{
            $currency_symbol = $this->current_symbol;
            return $currency_symbol;
        }
    }
    
    public function wooconnector_get_current_currency(){
        return $this->current_currency;
    }
    
    public function wooconnector_tmp_woocommerce_sale_price($price, $product = NULL){
        if ($product !== NULL) {
            if ($product->get_sale_price('edit') > 0) {
                return ($price == '') ? '' : $this->wooconnector_tmp_woocommerce_price($price, $product);
            }
        }
        return $price;
    }

    public function wooconnector_tmp_woocommerce_price($price, $product = NULL) {
        if (empty($price)) {
            
        }
        $price = $this->wooconnector_calculator_custom_price($price);
        return $price;
    }

    public function wooconnector_thankyou_order_id($order_id) {
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        update_post_meta($order_id, '_order_currency', $this->current_currency);
        update_post_meta($order_id, '_wooconnector_order_rate', $currencys[$key]['rate']);
        $this->wooconnector_reset_currency();
        return $order_id;
    }

    public function wooconnector_get_current_format() {
        if($this->current_currency == $this->basecurrency){
            $currency_pos = get_option( 'woocommerce_currency_pos');
        }else{
            $key = strtolower($this->current_currency);
            $currencys = $this->wooconnector_get_currency_custom();
            $currency_pos = $currencys[$key]['position'];
        }
        $format = '%1$s%2$s';
        switch ($currency_pos) {
            case 'left' :
                $format = '%1$s%2$s';
                break;
            case 'right' :
                $format = '%2$s%1$s';
                break;
            case 'left_space' :
                $format = '%1$s&nbsp;%2$s';
                break;
            case 'right_space' :
                $format = '%2$s&nbsp;%1$s';
                break;
        }
        return $format;
    }

    private function back_convert($amount, $rate, $position = 4) {
        return number_format((1 / $rate) * $amount, $position, '.', '');
    }

    public function wooconnector_coupon_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $coupon) {
        if (is_object($coupon) AND method_exists($coupon, 'is_type')) {
            if (!$coupon->is_type(array('percent_product', 'percent'))) {
                $discount = $this->wooconnector_exchange_value(floatval($discount));
            }
        }
        return $discount;
    }

    public function wooconnector_coupon_validate_minimum_amount($is, $coupon) {
        if ($this->current_currency != $this->basecurrency) {
            $currencies = $this->wooconnector_get_currency_custom();
            $cart_amount = $this->back_convert(WC()->cart->get_displayed_subtotal(), $currencies[strtolower($this->current_currency)]['rate']);
            return $coupon->get_minimum_amount() > $cart_amount;
        }
        return $is;
    }

    public function wooconnector_coupon_validate_maximum_amount($is, $coupon) {
        if ($this->current_currency != $this->basecurrency) {
            $currencies = $this->wooconnector_get_currency_custom();
            $cart_amount = $this->back_convert(WC()->cart->get_displayed_subtotal(), $currencies[strtolower($this->current_currency)]['rate']);
            return $coupon->get_maximum_amount() < $cart_amount;
        }
        return $is;
    }

    public function wooconnector_fix_shipping_calc($arg, $sum, $_this) {
        $currencies = $this->wooconnector_get_currency_custom();
        $rate = $currencies[strtolower($this->current_currency)]['rate'];
        $arg['cost'] = $arg['cost'] / $rate;
        return $arg;
    }

    public function wooconnector_get_variation_prices_hash($price_hash, $product, $display) {
    }

    public function wooconnector_add_to_cart_hash($hash) {
        return "";
    }
    
    public function wooconnector_before_mini_cart() {
        $_REQUEST['wooconnector_woocommerce_before_mini_cart'] = 'mini_cart_refreshing';
        WC()->cart->calculate_totals();
    }

    public function wooconnector_after_mini_cart() {
        unset($_REQUEST['wooconnector_woocommerce_before_mini_cart']);
    }

    public function wooconnector_reset_currency(){
        $_SESSION['wooconnector_currency'] = $this->basecurrency;
    }

    private function wooconnector_calculator_custom_price($price){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        if(!empty($currencys)){
            $rates = $currencys[$key]['rate'];
        }else{
            $rates = 1;
        }
        if(!is_numeric($price)){
            $price = 0;
        }
        $outprice = $price*$rates;
        return $outprice;
    }

    private function wooconnector_get_currency_custom(){
        $currencys = get_option('wooconnector_currency_settings');
        if(!empty($currencys)){
            if(is_string($currencys)){
                $currencys = unserialize($currencys);
            }           
        }else{
            $list[strtolower(get_woocommerce_currency())] = array(
                'currency' => get_woocommerce_currency(),
                'rate' => 1,
                'symbol' => get_woocommerce_currency_symbol(),
				'position' => get_option( 'woocommerce_currency_pos' ),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimal_separator' => wc_get_price_decimal_separator(),
				'number_of_decimals' => wc_get_price_decimals()
            );
            $currencys = $list;
        }
        return $currencys;
    }

    private function wooconnector_get_current_decimal_separator(){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        $decimal_separator = $currencys[$key]['decimal_separator'];
        return $decimal_separator;
    }

    private function wooconnector_get_current_thousand_separator(){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        $thousand_separator = $currencys[$key]['thousand_separator'];
        return $thousand_separator;
    }

    private function wooconnector_get_current_currency_symbol(){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        $symbol = $currencys[$key]['symbol'];
        return $symbol;
    }

    private function wooconnector_get_current_number_of_decimals(){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        $nod = $currencys[$key]['number_of_decimals'];
        return $nod;
    }

    private function wooconnector_escape($value) {
        return sanitize_text_field(esc_html($value));
    }

    private function wooconnector_exchange_value($value) {
        $currencies = $this->wooconnector_get_currency_custom();
        $value = $value * $currencies[strtolower($this->current_currency)]['rate'];
        $position = $this->number_of_decimals;
        $value = number_format($value, $position, $this->decimal_sep, '');
        return $value;
    }

    private function wooconnector_format_price($price){
        $key = strtolower($this->current_currency);
        $currencys = $this->wooconnector_get_currency_custom();
        $price = wc_format_decimal($price,$this->number_of_decimals);
        return $price;
    }
}
$WooConnectorHookCurrency = new WooConnectorHookCurrency();
?>