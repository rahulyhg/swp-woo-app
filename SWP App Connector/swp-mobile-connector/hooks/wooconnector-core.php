<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/*

* Check woocommerce checkout

*/

if ( ! function_exists( 'is_swp_checkout' ) ) {

	function is_swp_checkout(){			

		$pages = get_option( 'swp_checkout_page_id' );

		$idpage = $pages ? $pages : -1;					

		if(is_page($idpage)){

			return true;

		}else{

			return false;

		}

	}

}	

/**

* Update the order meta with field value in shop order

*/

add_action( 'add_meta_boxes', 'swp_add_meta_boxes' );

if ( ! function_exists( 'swp_add_meta_boxes' ) ){

	function swp_add_meta_boxes(){

		global $woocommerce, $order, $post;

		$order_id = $post->ID;

		$checkwoo = get_post_meta($order_id,'check_swp',true);

		if($checkwoo == 1 ){

			add_meta_box( 'swp_fields', __('Order meta','swp'), 'swp_add_other_fields_for_packaging', 'shop_order', 'side', 'core' );

		}			

	}

}



/**

* Update the order meta with field value in product

*/

add_action( 'add_meta_boxes', 'swp_add_meta_boxes_product' );

if ( ! function_exists( 'swp_add_meta_boxes_product' ) ){

	function swp_add_meta_boxes_product(){

		global $woocommerce, $post;

		$product = $post->ID;			

		add_meta_box( 'swp_product_fields', __('Mobile App Data','swp'), 'swp_add_product_meta_box', 'product', 'side', 'high' );

				

	}

}





/*

* adding Meta field in the meta container admin shop_order pages

*/

if ( ! function_exists( 'swp_add_other_fields_for_packaging' ) ){

	function swp_add_other_fields_for_packaging(){

		global $woocommerce, $order, $post;

		$order_id = $post->ID;

		$detect = new Wooconnector_Detect;

		$order = wc_get_order($order_id);

		$userAgent = $order->get_customer_user_agent();

		$listdetailt = $detect->output($userAgent);

		$tb = isset($listdetailt['tb']) ? $listdetailt['tb'] : "PC";

		$browser = $listdetailt['browser'];

		$device = $listdetailt['device'];

		$os = isset($listdetailt['os']) ? $listdetailt['os'] : "";

		$versionbrowser = $listdetailt['version'];	

		$versionos = isset($listdetailt['version-os']) ? $listdetailt['version-os'] : "";

		echo '<strong>Machine : </strong>	<span>'.$tb.'</span><br><strong>Device : </strong>	<span>'.$device.'</span><br><strong>Os : </strong>	<span>'.$os.'</span><br>	<strong>Version Os : </strong>	<span>'.$versionos.'</span><br> <strong>Browser : </strong>	<span>'.$browser.'</span><br>	<strong>Version : </strong>	<span>'.$versionbrowser.'</span><br> ';



	}

}

/*

* adding Meta field in the meta container admin product pages

*/

if ( ! function_exists( 'swp_add_product_meta_box' ) ){

	function swp_add_product_meta_box(){

		global $woocommerce, $post;

		$product_id = $post->ID;

		$product = wc_get_product($product_id );	

		$checkdeal = get_post_meta($product_id ,'_swp_data-dod',true);

		if($checkdeal == 1){

			$check = 'checked="checked"';

		}else{

			$check = '';

		}		

		if(!empty($product)){

			$valueproduct = $product->get_name();

		}else{

			$valueproduct = '';

		}

		$checkdisios = get_post_meta($product_id ,'swp_data-disable-ios-app',true);

		if($checkdisios == 1){

			$checkdi = 'checked="checked"';

		}else{

			$checkdi = '';

		}

		$checkdisadr = get_post_meta($product_id ,'swp_data-disable-android-app',true);

		if($checkdisadr == 1){

			$checkadr = 'checked="checked"';

		}else{

			$checkadr = '';

		}

		$html =  ('	<input style="margin-top:0px;" type="checkbox" id="woo_push_notification" name="swp_data-push-notification" value="1"  />');

		$html .= (' <label for="woo_push_notification"> Notice this product to customer</label><br>' );			

		$html .= ('	<div class="swp-after-checked-notification">

					<label for="woo_push_notification_title"> Title : </label>

					<input type="text" id="woo_push_notification_title" name="swp_data-push-notification-title" value="'.$valueproduct.'"  />

					<label for="woo_push_notification_content"> Content : </label>

					<textarea id="woo_push_notification_content" name="swp_data-push-notification-content"></textarea>

					<span class="notti-images-notifaction" style="color:#666">* Feature image used to notification</span>

				</div>');

		$html .= ('<hr style="float: left;width: 100%;">');

		$html .=('<div>');

		$html .= ('	<input type="checkbox" id="woo_deal_of_day" name="swp_data-dod" value="1"  '.$check.'   />');

		$html .= ( '<label for="woo_deal_of_day"> Deal Of Day</label>' );

		$html .= ('</div>');

		$echohtml = apply_filters('swp_meta_box_product',$html,$post);

		echo $echohtml;

	}

}	



function swp_product_meta_save( $post_id ){

	global $wpdb;

	if(isset($_POST['swp_data-dod'])){

		$dataDod = sanitize_text_field( $_POST['swp_data-dod'] );		

		update_post_meta( $post_id, '_swp_data-dod', $dataDod );

	}

	else{

		update_post_meta( $post_id, '_swp_data-dod', '' );

	}	

	if(isset($_POST['swp_data-push-notification']) && isset($_POST['hidden_post_status']) && $_POST['hidden_post_status'] == 'publish'){

		$api = get_option('swp_settings-api');

		$rest = get_option('swp_settings-restkey');

		if(empty($api)){

			print("<div class='notti-settings-onsignal setting-error'>". __('Please input your api key!','swp') ."</div>");

			exit;				

		}

		elseif(empty($rest)){

			print("<div class='notti-settings-onsignal setting-error'>". __('Please input your rest api key!','swp') ."</div>");

			exit;			

		}

		$table_name = $wpdb->prefix . "swp_data_api";

		$api = esc_sql($api);

		$datas = $wpdb->get_results(

			"

			SELECT * 

			FROM $table_name

			WHERE api_key = '$api'

			"

		);

		if(!empty($datas)){

			foreach($datas as $data){

				$idswpapi = $data->api_id;

			}	

			$title = @$_POST['swp_data-push-notification-title'];

			$title = apply_filters( 'post_title',stripslashes(strip_tags($title)));

			$content = @$_POST['swp_data-push-notification-content'];

			$content = apply_filters( 'post_title',stripslashes(strip_tags($content)));

			$notification = sendWooconnectorMessageOnProduct($post_id,$title,$content);

			$noti = json_decode($notification);					

			$errornoti = $noti->errors;					

			if(!empty($errornoti)){

				$invalids = $errornoti->invalid_player_ids;				

				if(!empty($invalids)){

					foreach($invalids as $invalid){

						$iderrors[] = $invalid;

					}

					$iderror = implode(',',$iderrors);

					$iderror = trim($iderror,',');

					print("<div class='notti-settings-onsignal setting-error'>". __("Invalid player ids".$iderror . 'swp') ."</div>");

					exit;

				}else{

					print("<div class='notti-settings-onsignal setting-error'>".__('All included players are not subscribed','swp')."</div>");

					exit;				

				}				

			}			

			$notificationId = $noti->id;

			$notificationRecipients = $noti->recipients;						

			$return = getNotificationById($notificationId);			

			$failed = $return->failed;

			$remaining = $return->remaining;

			$successful = $return->successful;

			$total = ($failed + $remaining + $successful);

			$converted = $return->converted;

			$datenow = new DateTime();

			$date = $datenow->format('Y-m-d H:i:s');			

			$table_name = $wpdb->prefix . "swp_data_notification";			

			$wpdb->insert(

				"$table_name",array(

					"notification_id" => $notificationId,

					"api_id" => $idswpapi,

					"recipients" => $notificationRecipients,

					"failed" => $failed,

					"remaining" => $remaining,

					"converted" => $converted,  	

					"successful" => $successful,	

					"total" => $total,

					"create_date" => $date	

				),

				array( 

					'%s',

					'%d',	

					'%d',

					'%d',

					'%d',

					'%d',

					'%d',

					'%d',

					'%s'	

				) 

			);

		}	

	}

}

add_action( 'save_post_product', 'swp_product_meta_save' );



/*

* Processing before order creation

*/

function swp_custom_details_device_mobile( $order_id ) {	

	global $wpdb;

	if ( !empty( $_POST['check_swp'] ) ) {

		update_post_meta( $order_id, 'check_swp', sanitize_text_field( $_POST['check_swp'] ) );		

		update_post_meta( $order_id, 'swp_device_checkout', 'mobile' );

	}else{

		update_post_meta( $order_id, 'swp_device_checkout', 'nonmobile' );

	}

	if( !empty($_POST['swp_check_user_agent'])){

		update_post_meta( $order_id, 'swp_check_user_agent', sanitize_text_field( $_POST['swp_check_user_agent'] ) );	

	}

	if( !empty($_POST['onesignal_player_id'])){

		update_post_meta( $order_id, 'onesignal_player_id', sanitize_text_field( $_POST['onesignal_player_id'] ) );	

	}

	if( !empty($_POST['swp_key_order']) && $_POST['swp_key_order'] != ''){

		$session_key_order = sanitize_text_field($_POST['swp_key_order']);

		$wpdb->query( "UPDATE " . $wpdb->prefix."swp_data SET order_id = ".$order_id." WHERE data_key = '".$session_key_order."'" );				

	}							

}	

add_action( 'woocommerce_checkout_update_order_meta', 'swp_custom_details_device_mobile' );		



function swp_customer_id_order(){

	if(!empty ($_POST['swp_setting_customer'])){

		$cus = $_POST['swp_setting_customer'];

		if(is_int($cus) || (ctype_digit($cus))){

			$customer = $cus;

		}else{

			$customer = 0;

		}	

	}	

	else{

		$customer = get_current_user_id();

	}

	return $customer;

}

add_filter('woocommerce_checkout_customer_id', 'swp_customer_id_order');

/*

* Create column in shop order

*/

function swp_custom_shop_order_column($columns){	   

	$columns['from_mobile'] = __( 'From Mobile App','woocommercec');

	return $columns;

}

add_filter( 'manage_edit-shop_order_columns', 'swp_custom_shop_order_column',11);

/*

* Content custom order list

*/

function swp_render_shop_order_columns( $column ){

	global $post, $woocommerce;

	$order_id = $post->ID;

	switch($column)	{

		case 'from_mobile' :				

			$device = get_post_meta($order_id,'swp_device_checkout',true);

			if(!empty($device) && $device == 'mobile'){

				print "Yes";

			}

			else{

				print "-";

			}

			break;

	}

}

add_action( 'manage_shop_order_posts_custom_column' , 'swp_render_shop_order_columns', 10, 2 );

/*

* Create filter in shop_order

*/

function swp_filter_to_shop_order_administration(){		

	global $post_type;				

	if($post_type == 'shop_order'){	

		$current = isset($_GET['swp_order_form_by']) ? $_GET['swp_order_form_by'] : '';	

		?>

		<select id="select_from_by" name="swp_order_form_by">

			<option value="">Show All</option>

			<option value="nonmobile"

			<?php if($current == 'nonmobile'){ echo 'selected="selected"'; }else{ echo '';}  ?>><?php echo __('Non Mobile App','swp'); ?>

			</option>

			<option value="mobile"

			<?php if($current == 'mobile') { echo 'selected="selected"'; }else{ echo '';}  ?>><?php echo __('Mobile App','swp'); ?>

			</option>

		</select>

		<?php

	}

}

add_action('restrict_manage_posts','swp_filter_to_shop_order_administration');	



/*

* Add metavalue in filter

*/	

function swp_shop_order_metavalue_in_query($query) {

	global $pagenow;

	$post_type = 'shop_order'; 	

	$q_vars   = &$query->query_vars;

	if( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($_GET['swp_order_form_by']) && $_GET['swp_order_form_by'] != ''){			

		$query->query_vars['meta_key'] = 'swp_device_checkout';

		$query->query_vars['meta_value'] = $_GET['swp_order_form_by'];					

	}

}

add_filter('parse_query', 'swp_shop_order_metavalue_in_query');

/*

* Create database

*/	

function create_swp_table() {

	global $wpdb;		

	$table_name = $wpdb->prefix . "swp_data";

	$charset_collate = $wpdb->get_charset_collate();

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){	

		$sql = "CREATE TABLE $table_name (

			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

			data_key varchar(100) DEFAULT '' NOT NULL,

			create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

			data longtext NOT NULL,

			order_id BIGINT NULL,			  

			PRIMARY KEY (id),

			UNIQUE KEY data_key (data_key)

		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );		

		update_option('swp_database_version',SWP_VERSION);

	}		

}	

/*

* Create database with plugins load

*/

function swp_myplugin_update_db_check() {

	create_swp_table();	

}

add_action( 'plugins_loaded', 'swp_myplugin_update_db_check' );		



/*

* Set current email to settings

*/

function swp_set_current_email_to_settings(){

	$current_user = wp_get_current_user();

	$checkmail = get_option('swp_settings-mail');

	if(empty($checkmail) || $checkmail == ''){

		update_option('swp_settings-mail',esc_sql($current_user->user_email));	

	}		

	global $wpdb;

	$prefix = $wpdb->prefix;

	$db_options = $prefix.'options';

	$sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name = "swp_settings-custom-attribute"';

	$results = $wpdb->get_results( $sql_query, OBJECT );

	if(count($results) === 0){

		add_option('swp_settings-custom-attribute',1);

	}	

}

add_action('plugins_loaded','swp_set_current_email_to_settings');



function swp_settings_admin_style(){

	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'swp-one-signal'){

		wp_register_style( 'swp-admin-onesignal-style', plugins_url('assets/css/wooconnector-onesignal-style.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );

		wp_enqueue_style( 'swp-admin-onesignal-style' );			

	}	

	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'swp' && isset($_GET['wootab']) && $_GET['wootab'] == 'currency'){

		wp_register_style( 'swp-admin-currency-style', plugins_url('assets/css/wooconnector-currency-style.css',SWP_PLUGIN_FILE), array(), SWP_VERSION, 'all' );

		wp_enqueue_style( 'swp-admin-currency-style' );			

	}	

}

add_action( 'admin_enqueue_scripts','swp_settings_admin_style');



function swp_include_media_button_js_file() {

	$ct = new WC_Countries();

	$coutries = $ct->get_countries();

	$states = $ct->get_states();

	$lists = array();

	foreach($states as $states => $value){

		if(!empty($value)){

			foreach($value as $lstates => $values){

				$lists[] = $states.'-'.$lstates;					

			}

		}

	}

	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'swp-one-signal'){

		wp_register_script('swp_settings_onesignal_js', plugins_url('assets/js/settingsonesignal.js',SWP_PLUGIN_FILE), array('jquery'), SWP_VERSION);	

		$setting = array(

			'tab' => isset($_GET['wootab']) ? $_GET['wootab'] : 'settings',

			'onesignal' => isset($_GET['onesignal']) ? $_GET['onesignal'] : 'api',

			'baseurl' => plugins_url('',SWP_PLUGIN_FILE),

			'urldefaultimage' => plugins_url('/assets/images/default.jpg',SWP_PLUGIN_FILE),

			'countries' => $coutries,

			'states' => $lists,

			'ajax_url' => plugins_url('/settings/slider/class-app-slider-ajax.php',SWP_PLUGIN_FILE)

		);	

		wp_localize_script( 'swp_settings_onesignal_js', 'swp_settings_js_params',  $setting  );

		wp_enqueue_script( 'swp_settings_onesignal_js' );

		

		wp_register_script('swp_colorpicker_js', plugins_url('assets/js/jscolor.min.js',SWP_PLUGIN_FILE), array('jquery'), SWP_VERSION);	

		$color = array(								

		);	

		wp_localize_script( 'swp_colorpicker_js', 'swp_colorpicker_js_params',  $color  );

		wp_enqueue_script( 'swp_colorpicker_js' );		

	}



	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'swp'){

		wp_register_script('swp_settings_js', plugins_url('assets/js/wooconnector-settings.js',SWP_PLUGIN_FILE), array('jquery'), SWP_VERSION);	

		$setting = array(

			'countries' => $coutries,

			'states' => $lists

		);	

		wp_localize_script( 'swp_settings_js', 'wooconnector_settings_js_params',  $setting  );

		wp_enqueue_script( 'swp_settings_js' );		

	}



	if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'swp' && isset($_GET['wootab']) && $_GET['wootab'] == 'currency'){

		$currency_code_options = get_woocommerce_currencies();

		foreach ( $currency_code_options as $code => $name ) {

			$currency_code_options[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';

		}

		$listtext = array(

			'currency' 			=> __('Sorry, Currency can only enter the character a - z.','swp'),

			'rate'     			=> __('Rate is invalid.','swp'),

			'ndecima'  			=> __('Number of decimals is invalid. Please specify a valid number [0-9].','swp'),

			'currencyexist' 	=> __('The currencies is duplicated.','swp'),

			'currencynotfound' 	=> __('Currency Code is invalid.','swp'),

			'ratenotfound' 		=> __('Rate is required.','swp'),

			'editrate'    		=> __('You want to fix the exchange rate yourself?','swp'),

			'delete'       		=> __('Successful deletion','swp'),

			'errorrate'         => __('Google Finance does not support this currency','swp'),

			'dontselect'        => __('You must select a currency','swp')

		);

		wp_register_script('swp_settings_currency_js', plugins_url('assets/js/settingscurrency.js',SWP_PLUGIN_FILE), array('jquery'), SWP_VERSION);	

		$ajax_nonce = wp_create_nonce( "swp-security-get-rates-ajax" );

		$setting = array(

			'listcurrencys'     => $currency_code_options,

			'currency'          => strtolower(get_woocommerce_currency()),

			'ajax_url'          => admin_url( 'admin-ajax.php', 'relative' ),

			'security'          => $ajax_nonce,

			'symbols'           => WooConnectorListSymbolCurrency_v0(),

			'listcurrency'      => WooConnectorListCurrency_v0(),

			'listtextmodal'     => $listtext

		);

		wp_localize_script( 'swp_settings_currency_js', 'wooconnector_settings_currency_js_params',  $setting  );

		wp_enqueue_script( 'swp_settings_currency_js' );

	}

}

add_action('admin_footer', 'swp_include_media_button_js_file');



function add_settings_link_swp($links, $file){

	if ( strpos( $file, 'swp-mobile-connector/swp-app-connector.php' ) !== false ) {

		$new_links = array(

			'settingswoo' => '<a href="'.get_bloginfo('url').'/wp-admin/admin.php?page=appconnector-settings" class="settings_link">' . __('Settings','woocommerce') . '</a>'		

		);

		$links = array_merge( $links, $new_links );

	}

	return $links;

}	

add_filter('plugin_action_links_'.SWP_PLUGIN_BASENAME, 'add_settings_link_swp', 10, 2);



function create_swp_table_api() {

	global $wpdb;		

	$table_name = $wpdb->prefix . "swp_data_api";

	$charset_collate = $wpdb->get_charset_collate();

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){	

		$sql = "CREATE TABLE $table_name (

			api_id INT NOT NULL AUTO_INCREMENT,

			api_key varchar(255) NOT NULL,

			rest_api varchar(255) NOT NULL,					

			PRIMARY KEY (api_id),

			UNIQUE KEY api_id (api_id)

		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );		

		update_option('swp_api_database_version',SWP_VERSION);

	}

}	



/*

* Create database with plugins load

*/

function swp_update_db_api_check() {

	create_swp_table_api();

}

add_action( 'plugins_loaded', 'swp_update_db_api_check' );



/*

* Create database

*/	

function create_swp_notification_table() {

	global $wpdb;		

	$table_name = $wpdb->prefix . "swp_data_notification";

	$charset_collate = $wpdb->get_charset_collate();

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){	

		$sql = "CREATE TABLE $table_name (

			id INT UNSIGNED NOT NULL AUTO_INCREMENT,

			api_id INT NOT NULL,

			notification_id varchar(100) NOT NULL,			  

			recipients int NOT NULL,

			converted int NOT NULL,

			failed int NOT NULL,

			remaining int NOT NULL,	

			successful int NOT NULL,

			total int NOT NULL,

			create_date datetime NOT NULL,

			PRIMARY KEY (id)

		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );		

		update_option('swp_notification_database_version',SWP_VERSION);

	}

}	

/*

* Create database with plugins load

*/

function swp_update_notification_db_check() {

	create_swp_notification_table();	

}

add_action( 'plugins_loaded', 'swp_update_notification_db_check' );



function create_swp_table_user() {

	global $wpdb;		

	$table_name = $wpdb->prefix . "swp_data_player";

	$charset_collate = $wpdb->get_charset_collate();

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){	

		$sql = "CREATE TABLE $table_name (

			id INT UNSIGNED NOT NULL AUTO_INCREMENT,

			api_id INT NOT NULL,

			player_id varchar(100) NOT NULL,

			identifier varchar(500) NULL,	

			session_count int NOT NULL,				

			test_type varchar(10) NULL,

			device_model varchar(250) NULL,

			device_os varchar(100) NULL, 

			device_type varchar(100) NOT NULL,

			language varchar(100) NULL,

			sdk varchar(100) NULL,	

			PRIMARY KEY (id)				

		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );		

		update_option('swp_player_database_version',SWP_VERSION);

	}		

}	

/*

* Create database with plugins load

*/

function swp_update_db_user_check() {

	create_swp_table_user();	

}

add_action( 'plugins_loaded', 'swp_update_db_user_check' );



function swp_display_notification(){

	require_once( SWP_ABSPATH . 'hooks/class-wooconnector-create-table.php');	

	$notification = new WooconnectorTable();

	echo $notification->current_action();

	$notification->prepare_items();

	$notification->display();
}


function swp_display_player(){

	require_once( SWP_ABSPATH . 'hooks/class-wooconnector-create-table-player.php');	

	$player = new WooconnectorTablePlayer();

	$title = __("All Users","woocommerce");

	?>

	<div class="wrap">

		<h1>

			<?php echo esc_html( $title );?>				

		</h1>

	</div>		

	<form method="POST" class="swp-setting-form" action="?page=swp-one-signal&wootab=player" id="settings-form-player">

	<?php

		$player->views();

		echo $player->current_action();

		$player->prepare_items();

		$player->display();

	?>

	</form>

	<?php

}



function WooconnectorstartLocaltiveactive(){

	global $wpdb;

	$prefix = $wpdb->prefix;

	$db_options = $prefix.'options';

	$sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name LIKE "%swp_settings-countries%" OR option_name LIKE "%swp_settings-states%"';



	$results = $wpdb->get_results( $sql_query, OBJECT );



	if ( count( $results ) === 0 ) {		

		$ct = new WC_Countries();

		$coutries = $ct->get_countries();

		$woocommerce_allowed_countries = get_option('woocommerce_allowed_countries');

		if($woocommerce_allowed_countries == 'specific'){

			$woocommerce_specific_allowed_countries = get_option('woocommerce_specific_allowed_countries');

			if(!empty($woocommerce_specific_allowed_countries)){

				foreach($coutries as $country => $value){

					foreach($woocommerce_specific_allowed_countries as $specific){

						if($country == $specific){

							$listcountry[$country] = $value;

						}

					}

				}

			}

		}elseif($woocommerce_allowed_countries == 'all_except'){

			$woocommerce_all_except_countries = get_option('woocommerce_all_except_countries');

			if(!empty($woocommerce_all_except_countries)){

				foreach($coutries as $country => $value){

					foreach($woocommerce_all_except_countries as $allex){

						if($country != $allex){

							$listcountry[$country] = $value;

						}

					}

				}

			}

		}else{

			$listcountry = $coutries;

		}

		foreach($listcountry as $coutrie => $value){

			$listcou[] = $coutrie;

		}

		$states = $ct->get_states();

		foreach($states as $state => $value){

			if(!empty($value)){

				foreach($value as $lstates => $values){

					$lists[] = $state .'-'. $lstates;					

				}

			}

		}

		$inputcountries = array();

		if(!empty($listcou)){

			$inputcountries = implode(',',$listcou).',';

		}	

		$inputstates = array();

		if(!empty($lists)){

			$inputstates = implode(',',$lists).',';

		}		

		$firstvalues = $inputcountries ;

		$oldvalues = trim($firstvalues,',');

		$values = array();

		if(!empty($oldvalues)){

			$values = explode(',',$oldvalues);

		}

		$ct = new WC_Countries();

		$coutries = $ct->get_countries();

		$out = array();

		if(!empty($values)){

			foreach($values as $val){								

				foreach($coutries as $country => $value){							

					if($val == $country){

						$list = array(

							'value' => $country,

							'name' => $value

						);

					}

				}

				$out[] = $list;

			}			

		}		

		$firststates = $inputstates;

		update_option('swp_settings-first-states',$firststates);

		$oldstates = trim($firststates,',');	

		$allstates = array();

		if(!empty($firststates)){

			$valuestates = explode(',',$oldstates);

			foreach($states as $sta => $values){

				$listst = array();

				foreach($valuestates as $valuestate){

					$country = substr($valuestate,0,strpos($valuestate,'-'));

					$state = substr($valuestate,strpos($valuestate,'-')+1);		

					if($country == $sta && !empty($values)){					

						foreach($values as $key => $val){				

							if($state == $key){

								$listst[] = array(

									'value' => $key,

									'name' => $val

								);							

							}								

						}

						$allstates[$sta] = $listst;	

					}					

				}

			}

		}	

		update_option('swp_settings-first-states',$inputstates);	

		update_option('swp_settings-countries',$out);

		update_option('swp_settings-states',$allstates);

	} else {

		return true;

	}

}

add_action( 'plugins_loaded', 'WooconnectorstartLocaltiveactive' );



function WooconnectorremovenotiSuccess(){

	if(isset($_GET['page']) && $_GET['page'] != 'swp'){

		update_option('swp_settings-save-settings-success',0);

	}

	if(isset($_GET['wootab']) && $_GET['wootab'] != 'settings'){

		update_option('swp_settings-save-settings-success',0);

	}

	if(isset($_GET['page']) && $_GET['page'] != 'swp-one-signal'){

		update_option('swp_settings-api-success',0);

		update_option('swp_settings-notification-sent-success',0);

		update_option('swp_settings-notification-save-success',0);

	}

	if(isset($_GET['wootab']) && $_GET['wootab'] != 'api'){

		update_option('swp_settings-api-success',0);

	}

	if(isset($_GET['wootab']) && $_GET['wootab'] != 'new'){			

		update_option('swp_settings-notification-sent-success',0);

		update_option('swp_settings-notification-save-success',0);

	}

}

add_action( 'plugins_loaded', 'WooconnectorremovenotiSuccess' );



function swp_check_user_login_by_token($auth){

	if(is_plugin_active('mobiconnector/mobiconnector.php')){		

		if(!empty($auth)){			

			$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

			list($token) = sscanf($auth, 'Bearer %s');

			try {

				$token = BAMobile_JWT::decode($token, $secret_key, array('HS256'));

				if ($token->iss != get_bloginfo('url')) {

					return false;

				}

				if (!isset($token->data->user->id)) {

					return false;

				}

				return true;

			}catch(Exception $e){

				return false;

			}			

		}else{

			return false;

		}		

	}else{

		return false;

	}

}



function swp_add_content_gzip(){

	if(ob_get_level() === 0){

		ob_start("ob_gzhandler");

	}else{

		ob_end_clean();

	}

}

add_action('init','swp_add_content_gzip');



function swp_remove_all_theme_styles() {

	if(is_swp_checkout() && isset($_SESSION['current_payment_method'])){

		global $wp_styles;

    	$wp_styles->queue = array('swp-checkout-style');

	}

}

add_action('wp_print_styles', 'swp_remove_all_theme_styles', 100);



function swp_convert_languagesCode_to_isoCode_v0($languageCode){

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



function WooconnectorChangePosition_v0($old,$currency){

	$currency = strtoupper($currency);

	$oucurrentposition = '';

	if($old == 'left'){

		$oucurrentposition = __('Left','swp').' ('.get_woocommerce_currency_symbol( $currency ).'99.99)';

	}elseif($old == 'right'){

		$oucurrentposition = __('Right','swp').' (99.99'.get_woocommerce_currency_symbol( $currency ).')';

	}elseif($old == 'left_space'){

		$oucurrentposition = __('Left with space','swp').' ('.get_woocommerce_currency_symbol( $currency ).' 99.99)';

	}elseif($old == 'right_space'){

		$oucurrentposition = __('Right with space','swp').' (99.99 '.get_woocommerce_currency_symbol( $currency ).')';

	}

	return $oucurrentposition;

}



function WooConnectorListSymbolCurrency_v0(){

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



function WooConnectorSetBaseCurrency_v0(){

	$currency = WooConnectorGetBaseCurrency_v0();

	$checkcurrency = get_option('swp_get_base_currency');

	if(empty($checkcurrency) || $checkcurrency == ''){

		update_option('swp_get_base_currency',$currency);

	}

}

add_action('plugins_loaded','WooConnectorSetBaseCurrency_v0');



function WooConnectorGetBaseCurrency_v0(){

	$currency = get_option('woocommerce_currency');

	return $currency;

}



function WooConnectorConvertRateOnGoogle_v0($from,$to){

	$amount = urlencode(1);

    $from = urlencode($from);

	$to = urlencode($to);

	$query_str = sprintf("%s_%s", $from, $to);

	$url = "http://free.currencyconverterapi.com/api/v3/convert?q={$query_str}&compact=y";



	if (function_exists('curl_init')) {

		$res = swp_file_get_contents_curl_v0($url);

	} else {

		$res = file_get_contents($url);

	}



	$currency_data = json_decode($res, true);



	if (!empty($currency_data[$query_str]['val'])) {

		$request = $currency_data[$query_str]['val'];

	} else {

		$request = sprintf(__("no data for %s", 'swp'), $to);

	}



	//***



	if (!$request) {

		$request = sprintf(__("no data for %s", 'swp'), $to);

	}

	return $request;

}



function swp_file_get_contents_curl_v0($url) {

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



function WooConnectorListCurrency_v0(){

	$list = array('AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BOV','BRL','BSD','BTN','BWP','BYN','BZD','CAD','CDF','CHE','CHF','CHW','CLF','CLP','CNY','COP','COU','CRC','CUC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','IQD','IRR','ISK','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MXV','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SRD','SSP','STD','SVC','SYP','SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','USN','UYI','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU','XBA','XBB','XBC','XBD','XCD','XDR','XOF','XPD','XPF','XPT','XSU','XTS','XUA','YER','ZAR','ZMW','ZWL');

	return $list;

}



function WooConnectorGetCurrency($key){

	$currencys = get_option('swp_currency_settings');

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



function WooConnectorShowNoticeAdmin(){

	if(isset($_SESSION['swp_session_notice_type']) && $_SESSION['swp_session_notice_type'] != '' && isset($_SESSION['swp_session_notice_message']) && $_SESSION['swp_session_notice_message'] != ''){

        $type = $_SESSION['swp_session_notice_type'];

        $class = 'notice notice-'.$type.' notice-swp';

        $message = $_SESSION['swp_session_notice_message'];

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ));

    }

}



function WooConnectorHiddenNoticeAdmin(){

    if(is_admin() && (!isset($_GET['page']) || isset($_GET['page']) && $_GET['page'] != 'swp-one-signal' || !isset($_GET['wootab']) || isset($_GET['wootab']) && $_GET['wootab'] != 'currency')){

        $_SESSION['swp_session_notice_type'] = '';

        $_SESSION['swp_session_notice_message'] = '';

    }

    $checkreload = get_option('swp_reload_remove_notice');

    $pageRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 

    if($pageRefreshed == 1 && !empty($checkreload) && $checkreload == 2){

        $_SESSION['swp_session_notice_type'] = '';

        $_SESSION['swp_session_notice_message'] = '';

        update_option('swp_reload_check_message','');

        update_option('swp_reload_remove_notice',0);

    }

}

add_action('plugins_loaded','WooConnectorHiddenNoticeAdmin');



function WooConnectorChangePositionPrice($price, $instance){

	if( strpos($_SERVER['REQUEST_URI'],'wp-json') != false ){

		$checkchange = get_option('swp_settings-change-price');

		if($checkchange == 1){

			if(strpos($price,'del') != false && strpos($price,'ins') != false){

				$priceleft = substr($price,0,strpos($price,'ins')-1);

				$priceleft = trim($priceleft,' ');

				$priceright = substr($price,strpos($price,'ins')-1);

				$price = $priceright.' '.$priceleft;

			}

		}

	}

	return $price;

}

add_filter( 'woocommerce_get_price_html', 'WooConnectorChangePositionPrice', 10, 2 ); 



function WooConnectorHiddenCheckOutPage($args){

	$pages = get_option( 'swp_checkout_page_id' );

	array_push($args,$pages);

	return $args;

}

add_filter( 'wp_list_pages_excludes','WooConnectorHiddenCheckOutPage',10 );



function WooConnectorGetCartItemData($product_id,$addons){

	if(isset($params['woo_currency'])){

		$currentkey = $params['woo_currency'];					

	}else{

		$currentkey = strtolower(get_woocommerce_currency());

	}

	$currencys = WooConnectorGetCurrency($currentkey);

	$ratecurrency = $currencys['rate'];

	$number_of_decimals = $currencys['number_of_decimals'];

	$listoption = array();

	if(!empty($addons) && !empty($product_id) && is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php')){

		$product = wc_get_product($product_id);

		$idaddons = $addons->id;

		$positionaddons = $addons->positions;

		$meta_data = $product->get_meta_data();

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

							for($i = 0; $i < count($options); $i++){

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

			}

		}	

	}

	return $listoption;

}



function swp_get_brand_template($name, $params = array(), $echo_html = true){

    $filename = SWP_ABSPATH . 'hooks/brands/' . $name . '.php';



    if (! file_exists($filename)) {

        return;

    }

    

    foreach ($params as $param => $value) {

        $$param = $value;

    }



    ob_start();

    include $filename;

    $html = ob_get_contents();

    ob_end_clean();



    if (! $echo_html) {

        return $html;

    }



    echo $html;

}



function swp_brand_image($params = array(), $echo = false){

    if(class_exists('WooConnector_Brand_Images')){

        $image_header = WooConnector_Brand_Images::get_brand_image($params);



        if (!$echo) {

            return $image_header;

        }



        echo $image_header;

    }

}



function swp_brand_image_image_src($params = array(), $echo = false){

    if(class_exists('WooConnector_Brand_Images')){

        $image_header = WooConnector_Brand_Images::get_brand_image($params, true);

        

        if (!$echo) {

            return $image_header;

        }

    

        echo $image_header;

    }

}



function swp_loaded_size_slider(){

	$checkfirst = get_option('swp_settings-slider-first-settings');

	if(empty($checkfirst)){

		update_option('swp_settings-width-slider',752);

		update_option('swp_settings-height-slider',564);

		update_option('swp_settings-slider-first-settings',0);

	}

}

add_action('plugins_loaded','swp_loaded_size_slider');



/**

 * Check add coupon or get all in Mobile App

 */

if(! function_exists( 'swp_is_add_coupon_or_get_all' )){

	function swp_is_add_coupon_or_get_all(){

		$url = $_SERVER['REQUEST_URI'];

		$sUrl = substr($url,strpos($url,'wp-json'));

		$oUrl = $sUrl;

		if(strpos($sUrl,'?') != false){

			$oUrl = substr($sUrl,0,strpos($sUrl,'?'));

		}

		$tUrl = trim($oUrl,'/');

		$aUrl = explode('/',$tUrl);

		if(isset($aUrl[0]) &&  $aUrl[0] != 'wp-json' || isset($aUrl[1]) &&  $aUrl[1] != 'swp-one-signal' || isset($aUrl[2]) &&  $aUrl[2] != 'calculator'){

			return false;

		}

		$lUrl = array_pop($aUrl); 

		if($lUrl == 'getall' || $lUrl == 'addcoupons'){

			return true;

		}else{

			return false;

		}

	}

}



/**

 * Check add get product in Mobile App

 */

if(! function_exists( 'swp_is_rest_get_product' )){

	function swp_is_rest_get_product(){

		$url = $_SERVER['REQUEST_URI'];

		$sUrl = substr($url,strpos($url,'wp-json'));

		$oUrl = $sUrl;

		if(strpos($sUrl,'?') != false){

			$oUrl = substr($sUrl,0,strpos($sUrl,'?'));

		}

		$tUrl = trim($oUrl,'/');

		$aUrl = explode('/',$tUrl);

		if(isset($aUrl[0]) &&  $aUrl[0] != 'wp-json' || isset($aUrl[1]) &&  $aUrl[1] != 'swp-one-signal' || isset($aUrl[2]) &&  $aUrl[2] != 'product'){

			return false;

		}

		$lUrl = array_pop($aUrl); 

		if($lUrl == 'getproduct' || $lUrl == 'getproductbycategory' || $lUrl == 'getproductbyattribute'){

			return true;

		}else{

			return false;

		}

	}

}



/**

 * Add meta box only use coupon in Mobile App

 */

if ( ! function_exists( 'swp_add_meta_boxes_to_coupon' ) ){

	function swp_add_meta_boxes_to_coupon(){

		global $woocommerce, $post;

		add_meta_box( 'swp_fields', __('Only use in Mobile App','swp'), 'swp_add_other_fields_for_coupon', 'shop_coupon', 'side', 'high' );		

	}

}

add_action( 'add_meta_boxes', 'swp_add_meta_boxes_to_coupon' );



/**

 * Content meta box only use coupon in Mobile App

 */

if ( ! function_exists( 'swp_add_other_fields_for_coupon' ) ){

	function swp_add_other_fields_for_coupon(){

		global $woocommerce, $post;

		$coupon_id = $post->ID;

		$check = get_post_meta($coupon_id,'swp_only_use_in_mobile_app');

		$checked = '';

		if(!empty($check) && $check[0] == 1){

			$checked = 'checked="checked"';

		}else{

			$checked = '';

		}

		$html = '<input type="checkbox" '.$checked.' class="woo-checkbox" id="swp-check-coupon-only-mobile" name="swp-check-coupon-only-mobile" value="1"/><label class="app-label" for="swp-check-coupon-only-mobile">'.__('Only use in Mobile App','swp').'</label>';

		echo $html;

	}

}

/**

 * Controller save coupon

 */

if ( ! function_exists( 'swp_controller_save_coupon' ) ){

	function swp_controller_save_coupon($post_id){

		global $wpdb;

		$checkbox = @$_POST['swp-check-coupon-only-mobile'];

		if($checkbox == 1){

			update_post_meta( $post_id, 'swp_only_use_in_mobile_app', '1');

		}else{

			update_post_meta( $post_id, 'swp_only_use_in_mobile_app', '');

		}

	}

}

add_action('save_post','swp_controller_save_coupon');



/**

 * Check coupon code in add coupon

 */

if ( ! function_exists( 'swp_check_coupon_with_add_discount' ) ){

	function swp_check_coupon_with_add_discount($true, $instance){

		$coupon_id = $instance->get_id();

		$checkcoupon = get_post_meta($coupon_id,'swp_only_use_in_mobile_app');

		if(is_array($checkcoupon)){

			if(!empty($checkcoupon) && $checkcoupon[0] == 1){

				if(!is_swp_checkout() || !swp_is_add_coupon_or_get_all()){

					return false;

				}else{

					return true;

				}

			}else{

				return true;

			}

		}else{

			if($checkcoupon == 1){

				if(!is_swp_checkout() || !swp_is_add_coupon_or_get_all()){

					return false;

				}else{

					return true;

				}

			}else{

				return true;

			}

		}

	}

}

add_filter('woocommerce_coupon_is_valid','swp_check_coupon_with_add_discount',10,2);





/**

 * Clear all html tag

 */

function swp_get_plaintext( $string, $keep_image = false, $keep_link = false ){

	// Get image tags

	if( $keep_image ){

		if( preg_match_all( "/\<img[^\>]*src=\"([^\"]*)\"[^\>]*\>/is", $string, $match ) ){

			foreach( $match[0] as $key => $_m )	{

				$textimg = '';

				if( strpos( $match[1][$key], 'data:image/png;base64' ) === false ){

					$textimg = " " . $match[1][$key];

				}

				if( preg_match_all( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $_m, $m_alt ) ){

					$textimg .= " " . $m_alt[1][0];

				}

				$string = str_replace( $_m, $textimg, $string );

			}

		}

	}



	// Get link tags

	if( $keep_link ){

		if( preg_match_all( "/\<a[^\>]*href=\"([^\"]+)\"[^\>]*\>(.*)\<\/a\>/isU", $string, $match ) ){

			foreach( $match[0] as $key => $_m )	{

				$string = str_replace( $_m, $match[1][$key] . " " . $match[2][$key], $string );

			}

		}

	}

	if(!empty($string) && $string !== ''){

		if(is_array($string)){

			$string = implode(',',$string);

		}

		$string = str_replace( ' ', ' ', strip_tags( $string ) );

	}

	return preg_replace( '/[ ]+/', ' ', $string );

}





function swp_pre_get_posts( &$query ) {

	if(isset($query->query_vars['post_type'])){

			switch($query->query_vars['post_type']){

				case 'nav_menu_item': return;

				default: break;

			}

	}

	$query->query_vars['suppress_filters'] = false;

}

add_action( 'pre_get_posts', 'swp_pre_get_posts', 99 );



/**

 * Loaded plugin set base currency

 */

function swp_loaded_active_currency(){

	$list[strtolower(get_woocommerce_currency())] = array(

		'currency' => get_woocommerce_currency(),

		'rate' => 1,

		'symbol' => get_woocommerce_currency_symbol(),

		'position' => get_option( 'woocommerce_currency_pos' ),

		'thousand_separator' => wc_get_price_thousand_separator(),

		'decimal_separator' => wc_get_price_decimal_separator(),

		'number_of_decimals' => wc_get_price_decimals()

	);

	$currencys = get_option('swp_currency_settings');

	if(!empty($currencys)){

		$currencies = unserialize($currencys);

		if(empty($currencies)){

			update_option('swp_currency_settings',serialize($list));

		}

	}else{

		update_option('swp_currency_settings',serialize($list));

	}

}



/**

 * Add more cron schedules.

 *

 * @param  array $schedules List of WP scheduled cron jobs.

 * @return array

 */

function swp_cron_schedules( $schedules ) {

	$schedules['swp_half_day'] = array(

		'interval' => 43200,

		'display'  => __( 'Half Day', 'swp' ),

	);

	return $schedules;

}

add_filter( 'cron_schedules', 'swp_cron_schedules');



/**

 * Function Run with Plugin Active

 */

function swp_active_plugin_action(){

	if ( 'yes' === get_transient( 'swp_installing' ) ) {

		return;

	}

	set_transient( 'swp_installing', 'yes', MINUTE_IN_SECONDS * 10 );

	swp_loaded_active_currency();

	swp_create_cron_job();

	delete_transient( 'swp_installing' );

}

add_action( 'init', 'swp_active_plugin_action' ); 



/**

 * Create Cron Job WoocConnector

 */

function swp_create_cron_job(){

	wp_clear_scheduled_hook( 'swp_update_rate_currency' );

	wp_schedule_event( time(), 'swp_half_day', 'swp_update_rate_currency' );	

}



/**

 * Update rate Currency

 */

function swp_cron_job_update_rate_currency(){

	$currencies = get_option('swp_currency_settings');

	$currencies = unserialize($currencies);

	$basecurrency = WooConnectorGetBaseCurrency_v0();

	if(!empty($currencies)){

		foreach($currencies as $currency => $value){

			if($currency != strtolower($basecurrency)){

				$rate = WooConnectorConvertRateOnGoogle_v0($basecurrency,$value['currency']);

				$currencies[$currency]['rate'] = $rate;

			}

		}

		$currencies = serialize($currencies);

		update_option('swp_currency_settings',$currencies);

	}

}

add_action('swp_update_rate_currency','swp_cron_job_update_rate_currency');



/**

 * Check is swp

 */

if(! function_exists( 'swp_is_rest_api' )){

	function swp_is_rest_api(){

		$url = $_SERVER['REQUEST_URI'];

		$sUrl = substr($url,strpos($url,'wp-json'));

		$oUrl = $sUrl;

		if(strpos($sUrl,'?') != false){

			$oUrl = substr($sUrl,0,strpos($sUrl,'?'));

		}

		$tUrl = trim($oUrl,'/');

		$aUrl = explode('/',$tUrl);

		if(!isset($aUrl[0]) || isset($aUrl[0]) &&  $aUrl[0] != 'wp-json' || !isset($aUrl[1]) || isset($aUrl[1]) &&  !in_array($aUrl[1],array('swp','mobiconnector','cellstore','modernshop'))){

			return false;

		}

		return true;		

	}

}



/**

 * Wooconnector Drop table

 */

function swp_wpmu_drop_tables($tables){	

	global $wpdb;

	$tables[] = $wpdb->prefix . 'swp_data';

	$tables[] = $wpdb->prefix . 'swp_data_api';

	$tables[] = $wpdb->prefix . 'swp_data_notification';

	$tables[] = $wpdb->prefix . 'swp_data_player';

	return $tables;

}

add_filter( 'wpmu_drop_tables','swp_wpmu_drop_tables' );      



function swp_fix_403_checkout($action,$result){

	if(isset($_REQUEST['check_swp']) && $_REQUEST['check_swp'] == 1){		

		if($result === 'false'){

			$result = true;

			return $result;

		}

	}

}

add_action('check_ajax_referer','swp_fix_403_checkout',10,2);

?>