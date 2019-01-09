<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function wooconnector_convert_languagesCode_to_isoCode($languageCode){
	$listConvert = array(
		'en' => array(
			'en_US',
			'en_AU',
			'en_CA',
			'en_GB',
			'is_IS',
			'haw_US',
		),
		'vn' => array(
			'vi'
		),
		'zh-Hant' => array(
			'zh_HK',
			'zh_TW'
		),
		'zh-Hans' => array(
			'zh_CN'
		),
		'nl' => array(
			'nl_NL',
			'nl_BE',
			'fy'
		),
		'ka' => array(
			'ka_GE'
		),
		'hi' => array(
			'hi_IN',
			'gu_IN',
			'ml_IN'
		),
		'it' => array(
			'it_IT'
		),
		'ja' => array(
			'ja'
		),
		'ko' => array(
			'ko_KR'
		),
		'lv' => array(
			'lv'
		),
		'lt' => array(
			'lt_LT'
		),
		'fa' => array(
			'fa_IR',
			'fa_AF',
			'haz'
		),
		'sr' => array(
			'sr_RS'
		),
		'th' => array(
			'th'
		),
		'ar' => array(
			'ar'
		),
		'hr' => array(
			'hr',
			'bs_BA'
		), 
		'et' => array(
			'et'
		), 
		'bg' => array(
			'bg_BG'
		), 
		'he' => array(
			'he_IL'
		), 
		'ms' => array(
			'ms_MY'
		),
		'pt' => array(
			'pt_BR',
			'pt_PT'
		), 
		'sk' => array(
			'sk_SK'
		), 
		'tr' => array(
			'tr_TR'
		), 
		'ca' => array(
			'ca',
			'bal'
		), 
		'cs' => array(
			'cs_CZ'
		), 
		'fi' => array(
			'fi'
		), 
		'de' => array(
			'de_DE',
			'de_CH'
		), 
		'hu' => array(
			'hu_HU'
		), 
		'nb' => array(
			'nb_NO'
		), 
		'ro' => array(
			'ro_RO'
		), 
		'es' => array(
			'es_AR',
			'es_CL',
			'es_CO',
			'es_MX',
			'es_PE',
			'es_PR',
			'es_ES',
			'es_VE',
			'gn',
			'gl_ES'
		), 
		'uk' => array(
			'uk'
		), 
		'da' => array(
			'da_DK'
		), 
		'fr' => array(
			'fr_BE',
			'fr_FR',
			'co'
		), 
		'el' => array(
			'el'
		), 
		'id' => array(
			'id_ID',
			'su_ID',
			'jv_ID'
		), 
		'pl' => array(
			'pl_PL'
		), 
		'ru' => array(
			'ru_RU',
			'ru_UA',
			'ky_KY'
		),
		'sv' => array(
			'sv_SE'
		)
	);
	foreach($listConvert as $iso => $languages){
		foreach($languages as $language){
			if($language == $languageCode){
				$isoCode = $iso;
			}
		}
	}
	if(empty($isoCode)){
		return 'en';
	}else{
		return $isoCode;
	}
}

function WooconnectorChangePosition($old,$currency){
	$currency = strtoupper($currency);
	$oucurrentposition = '';
	if($old == 'left'){
		$oucurrentposition = __('Left','wooconnector').' ('.get_woocommerce_currency_symbol( $currency ).'99.99)';
	}elseif($old == 'right'){
		$oucurrentposition = __('Right','wooconnector').' (99.99'.get_woocommerce_currency_symbol( $currency ).')';
	}elseif($old == 'left_space'){
		$oucurrentposition = __('Left with space','wooconnector').' ('.get_woocommerce_currency_symbol( $currency ).' 99.99)';
	}elseif($old == 'right_space'){
		$oucurrentposition = __('Right with space','wooconnector').' (99.99 '.get_woocommerce_currency_symbol( $currency ).')';
	}
	return $oucurrentposition;
}

function WooConnectorListSymbolCurrency(){
	$list = array(
		'&#36;', '&euro;', '&yen;', '&#1088;&#1091;&#1073;.', '&#1075;&#1088;&#1085;.', '&#8361;',
		'&#84;&#76;', 'د.إ', '&#2547;', '&#82;&#36;', '&#1083;&#1074;.',
		'&#107;&#114;', '&#82;', '&#75;&#269;', '&#82;&#77;', 'kr.', '&#70;&#116;',
		'Rp', 'Rs', '&#8377;', 'Kr.', '&#8362;', '&#8369;', '&#122;&#322;', '&#107;&#114;',
		'&#67;&#72;&#70;', '&#78;&#84;&#36;', '&#3647;', '&pound;', 'lei', '&#8363;',
		'&#8358;', 'Kn', '-----'
	);
	return $list;
}

function WooConnectorSetBaseCurrency(){
	$currency = WooConnectorGetBaseCurrency();
	$checkcurrency = get_option('wooconnector_get_base_currency');
	if(empty($checkcurrency) || $checkcurrency == ''){
		update_option('wooconnector_get_base_currency',$currency);
	}
}
add_action('plugins_loaded','WooConnectorSetBaseCurrency');

function WooConnectorGetBaseCurrency(){
	$currency = get_option('woocommerce_currency');
	return $currency;
}

function WooConnectorConvertRateOnGoogle($from,$to){
	$amount = urlencode(1);
    $from = urlencode($from);
	$to = urlencode($to);
	$query_str = sprintf("%s_%s", $from, $to);
	$url = "http://free.currencyconverterapi.com/api/v3/convert?q={$query_str}&compact=y";

	if (function_exists('curl_init')) {
		$res = wooconnector_file_get_contents_curl($url);
	} else {
		$res = file_get_contents($url);
	}

	$currency_data = json_decode($res, true);

	if (!empty($currency_data[$query_str]['val'])) {
		$request = $currency_data[$query_str]['val'];
	} else {
		$request = sprintf(__("no data for %s", 'wooconnector'), $to);
	}

	//***

	if (!$request) {
		$request = sprintf(__("no data for %s", 'wooconnector'), $to);
	}
	return $request;
}

function wooconnector_file_get_contents_curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function WooConnectorListCurrency(){
	$list = array('AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BOV','BRL','BSD','BTN','BWP','BYN','BZD','CAD','CDF','CHE','CHF','CHW','CLF','CLP','CNY','COP','COU','CRC','CUC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','IQD','IRR','ISK','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MXV','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SRD','SSP','STD','SVC','SYP','SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','USN','UYI','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU','XBA','XBB','XBC','XBD','XCD','XDR','XOF','XPD','XPF','XPT','XSU','XTS','XUA','YER','ZAR','ZMW','ZWL');
	return $list;
}

function swp_get_currency($key){
	$currencys = get_option('wooconnector_currency_settings');
	if(!empty($currencys)){
		$currencys = unserialize($currencys);
		$listcurrency = array();
		foreach($currencys as $currency => $value){
			if($currency == $key){
				$listcurrency = array(
					'code' => strtolower($value['currency']),
					'name' => strtoupper($value['currency']),
					'rate' => $value['rate'],
					'symbol' => $value['symbol'],
					'position' => $value['position'],
					'thousand_separator' => $value['thousand_separator'],
					'decimal_separator' => $value['decimal_separator'],
					'number_of_decimals' => $value['number_of_decimals']
				);
			}
		}
		if(empty($listcurrency)){
			$listcurrency = array(
				'code' => strtolower(get_woocommerce_currency()),
				'name' => get_woocommerce_currency(),
				'rate' => 1,
				'symbol' => get_woocommerce_currency_symbol(),
				'position' => get_option( 'woocommerce_currency_pos' ),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimal_separator' => wc_get_price_decimal_separator(),
				'number_of_decimals' => wc_get_price_decimals()
			);
		}
	}else{
		$listcurrency = array(
			'code' => strtolower(get_woocommerce_currency()),
			'name' => get_woocommerce_currency(),
			'rate' => 1,
			'symbol' => get_woocommerce_currency_symbol(),
			'position' => get_option( 'woocommerce_currency_pos' ),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimal_separator' => wc_get_price_decimal_separator(),
			'number_of_decimals' => wc_get_price_decimals()
		);
	}
	return $listcurrency;
}
?>