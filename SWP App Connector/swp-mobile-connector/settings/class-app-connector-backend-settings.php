<?php

if( !defined( 'ABSPATH' ) ){
    /****** EXIT if access directly ******/
    exit;
}

/******************************************************
* @param settings function
* callback values
* @return
******************************************************/
if( !class_exists( 'SWPappsettings' ) ){
    class SWPappsettings
    {
        /**** Holds the values to be used in the fields callbacks *****/
        private $options;

        /******* Start up *****/
        public function __construct(){
            $this->init_hooks();
        }

        /***** Hook into actions and filters. ******/
        public function init_hooks(){
            //require_once('class-app-connector-backend-about-us.php');
            require_once('general-settings/general-settings.php');
            require_once( 'popup/class-app-connector-popup.php' );
            require_once( 'deals/class-app-connector-deals.php' );
            require_once( 'slider/class-core.php' );
            require_once( 'slider/class-app-connector-slider.php' );
            
            add_action( 'admin_menu',array($this, 'create_menu_to_admin' ) );
            //require_once( 'class-app-connector-backend-footer.php' );
                        
        }
        
        public function create_menu_to_admin(){
		
            $parent_slug = 'appconnector-settings';
            add_submenu_page(
                $parent_slug,
                __('One Signal'),
                __('One Signal'),
                'manage_options',
                'swp-one-signal',
                array($this,'action_onesignal_menu')
            );
        }


        public function action_onesignal_menu(){
            $task = isset($_REQUEST['wootask']) ? $_REQUEST['wootask'] : '';
            $tab = isset($_REQUEST['wootab']) ? $_REQUEST['wootab'] : 'General settings';
            if($tab == 'api'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/onesignal-form.php');
            }elseif($tab == 'new'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/content-setting.php');
            }elseif($tab == 'list'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/list-notification.php');
            }elseif($tab == 'player'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/list-user.php');
            }elseif($tab == 'viewnotification'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/details-notification.php');
            }else{
                require_once(WOOCONNECTOR_ABSPATH.'settings/onesignal/onesignal-form.php');
            }	
            if(!empty($task) || $task != ''){	
                switch($task){
                    case 'saveonesignal':
                    $this->Woo_SaveOnesignal();
                    break;
                    case 'saveonesignal-content':
                        $this->Woo_SaveContent();
                    break;
                    case 'changeTesttype':
                        $nonce = esc_attr( $_REQUEST['_wpnonce'] );
                        if ( ! wp_verify_nonce( $nonce, 'swp_change_testtype' ) ) {
                        die( 'Go get a life script kiddies' );
                        }
                        else {
                            $player = $_REQUEST['player'];
                            $device = $_REQUEST['device'];
                            $section = 	$_REQUEST['section'];
                            $this->update_type_player($player,$device,$section);
                        }
                    break;
                }
            }			
        }

        public function action_create_menu(){
            $task = isset($_REQUEST['wootask']) ? $_REQUEST['wootask'] : '';
            $tab = isset($_REQUEST['wootab']) ? $_REQUEST['wootab'] : 'settings';
            if($tab == 'settings'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/views/settings-form.php');
            }elseif($tab == 'currency'){
                require_once(WOOCONNECTOR_ABSPATH.'settings/currency/settingscurrency.php');
            }else{
                require_once(WOOCONNECTOR_ABSPATH.'settings/views/settings-form.php');
            }
            if(!empty($task) || $task != ''){	
                switch($task){
                    case 'savesetting':
                        $this->Woo_saveSetting();
                    break;
                    case 'savesettingcurrency':
                        $this->Woo_SaveSetiingsCurrency();
                    break;
                    case 'changeTesttype':
                        $nonce = esc_attr( $_REQUEST['_wpnonce'] );
                        if ( ! wp_verify_nonce( $nonce, 'swp_change_testtype' ) ) {
                          die( 'Go get a life script kiddies' );
                        }
                        else {
                            $player = $_REQUEST['player'];
                            $device = $_REQUEST['device'];
                            $section = 	$_REQUEST['section'];
                            $this->update_type_player($player,$device,$section);
                        }
                    break;
                }
            }
        }	

        private function Woo_saveSetting(){		
            update_option('swp_settings-mail',esc_sql(@$_POST['swp_settings-mail']));	
            update_option('swp_settings-custom-attribute',esc_sql(@$_POST['swp_settings-custom-attribute']));
            $defaultsearch = array(
                'name' => '1'
            );
            if(!empty($_POST['swp_settings-search'])){
                $listactivesearch = array_merge($_POST['swp_settings-search'],$defaultsearch);
            }else{
                $listactivesearch = $defaultsearch;
            }
            $listactivesearch = serialize($listactivesearch);
            update_option('swp_settings-search',$listactivesearch);
            update_option('swp_settings-change-price',esc_sql(@$_POST['swp_settings-change-price']));	

            $firstvalues = @$_POST['swp_settings_countries'];
            $oldvalues = trim($firstvalues,',');
            $values = explode(',',$oldvalues);
            $ct = new WC_Countries();
            $coutries = $ct->get_countries();
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
            $out = serialize($out);	
            update_option('swp_settings-countries',$out);

            $firststates = @$_POST['swp_settings_states'];
            update_option('swp_settings-first-states',$firststates);
            $oldstates = trim($firststates,',');		
            $valuestates = explode(',',$oldstates);
            $states = $ct->get_states();
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
            $allstates = serialize($allstates);	
            update_option('swp_settings-states', $allstates);
//            bamobile_mobiconnector_add_notice(__('Successfully Update','mobiconnector')); 
            wp_redirect( '?page=swp&wootab=settings' );

        }

        private function Woo_SaveSetiingsCurrency(){
            $currencys = @$_POST["swp_currency_settings"];
            $listout = array();
            $symbols = WooConnectorListSymbolCurrency();
            $listcheckcurrencys = WooConnectorListCurrency();
            $listcurrencys = array();
            foreach($currencys as $currency => $value){
                $value['symbol'] = get_woocommerce_currency_symbol($value['currency']);
                $listout[$currency] = $value;
                if (!preg_match ('/[a-zA-Z]/', $currency)) {
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('Currency Code is invalid.','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                if(in_array($currency,$listcurrencys)){
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('The currencies is duplicated.','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                if(!in_array(strtoupper($currency),$listcheckcurrencys)){
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('Currency Code is invalid.','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                if(!is_numeric($value['rate'])){
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('Rate is invalid.','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                if($value['rate'] == ''){
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('Rate is required.','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                if(!preg_match ('/[0-9]/', $value['number_of_decimals'])){
                    $_SESSION['swp_session_notice_type'] = 'error';
                    $_SESSION['swp_session_notice_message'] = __('Number of decimals is invalid. Please specify a valid number [0-9].','swp');
                    update_option('swp_reload_remove_notice',0);
                    update_option('swp_reload_check_message','yes');
                    wp_redirect( '?page=swp&wootab=currency' );
                    return false;
                }
                array_push($listcurrencys,$currency);
            }
            update_option('swp_currency_settings',serialize($listout));
            $_SESSION['swp_session_notice_type'] = 'success';
            $_SESSION['swp_session_notice_message'] = __('Successfully Update','swp');
            update_option('swp_reload_remove_notice',0);
            update_option('swp_reload_check_message','yes');
            wp_redirect( '?page=swp&wootab=currency' );
        }

        private function Woo_SaveOnesignal(){
            $apiid = esc_sql(@$_POST["swp-app-id-onesignal"]);
            $restapikey = esc_sql(@$_POST["swp-rest-api-key-onesignal"]);
            if(strlen($apiid) != 36){
//                bamobile_mobiconnector_add_notice(__('Your APP ID must be 36 characters. Please retype','swp'),'error'); 
                wp_redirect( '?page=swp-one-signal&wootab=api' );
                return true;
            }
            if(strlen($restapikey ) != 48){
//                bamobile_mobiconnector_add_notice(__('Your REST API KEY must be 48 characters. Please retype','swp'),'error'); 
                wp_redirect( '?page=swp-one-signal&wootab=api' );
                return true;
            }
            $apiid = trim($apiid);   
            $restapikey = trim($restapikey);
            update_option('swp_settings-api',$apiid);
            update_option('swp_settings-restkey',$restapikey);
            $mobiapi = get_option('mobiconnector_settings-onesignal-api');
            $mobirest = get_option('mobiconnector_settings-onesignal-restkey');
            if(empty($mobiapi) && empty($mobirest)){
                update_option('mobiconnector_settings-onesignal-api',$apiid);
                update_option('mobiconnector_settings-onesignal-restkey',$restapikey);
            }
            global $wpdb;
            $table_name = $wpdb->prefix . "swp_data_api";
            $table_mobi_name = $wpdb->prefix . "mobiconnector_data_api";
            $datas = $wpdb->get_results(
                "
                SELECT * 
                FROM $table_name
                WHERE api_key = '$apiid'
                "
            );
            $checkdata = $wpdb->get_results(
                "
                SELECT * 
                FROM $table_mobi_name
                WHERE api_key = '$apiid'
                "
            );
            if(empty($datas)){
                $table_name = $wpdb->prefix . "swp_data_api";			
                $wpdb->insert(
                    "$table_name",array(
                        "api_key" => $apiid,
                        "rest_api" => $restapikey,				
                    ),
                    array( 
                        '%s', 
                        '%s'
                    ) 
                );
            }	
            if(empty($checkdata)){              	
                $wpdb->insert(
                    "$table_mobi_name",array(
                        "api_key" => $apiid,
                        "rest_api" => $restapikey,				
                    ),
                    array( 
                        '%s', 
                        '%s'
                    ) 
                );
            }	
//            bamobile_mobiconnector_add_notice(__('Successfully Update','mobiconnector')); 
            wp_redirect( '?page=swp-one-signal&wootab=api' );
        }

        private function Woo_SaveContent(){		

            $title = esc_sql(@$_POST['swp-web-title-notification']);
            update_option('swp_settings-title',$title);

            $content = esc_sql(@$_POST['swp-web-content-notification']);
            update_option('swp_settings-content',$content);

            $images = @$_POST['swp-web-icon-notification'];	
            if(isset($images) && $images != ''){	
                $this->update_thumnail_swp($images,'swp_notification_icon');
                update_option('swp_settings-id-icon',$images);
            }else{
                update_option('swp_settings-id-icon',$images);
                update_option('swp_notification_icon','');
            }	

            $small = @$_POST['swp-web-smicon-notification'];
            if(isset($small) && $small != ''){
                $this->update_thumnail_swp($small,'swp_notification_icon_small');
                update_option('swp_settings-sm-icon',$small);
            }else{
                update_option('swp_notification_icon_small','');
                update_option('swp_settings-sm-icon',$small);
            }		

            $selectedurl = @$_POST['swp-web-url-select-notification'];
            update_option('swp_settings-url-selected',$selectedurl);
            if($selectedurl == 'url-product'){
                $url = @$_POST['swp-web-url-notification-url-product'];			
                if(isset($url) && $url != ''){
                    if(strpos($url,'link://') !== false){
                        update_option('swp_settings-push-url',$url);				
                    }else{
                        update_option('swp_settings-url',$url);
                        $product_id = url_to_postid($url);
                        if(!empty($product_id)) {
                            $newurl =  str_replace($url, 'link://product/'.$product_id, $url);
                            update_option('swp_settings-push-url',$newurl);
                        }else{
//                            bamobile_mobiconnector_add_notice(__('Your URL is not Product URL. Please retype','swp'),'error'); 
                            wp_redirect( '?page=swp-one-signal&wootab=api' );
                            return true;
                        }
                    }
                }
            }elseif($selectedurl == 'url-category'){
                $url = @$_POST['swp-web-url-notification-url-category'];
                if(isset($url) && $url != ''){
                    update_option('swp_settings-url',$url);
                    if(strpos($url,'link://') !== false){
                        update_option('swp_settings-push-url',$url);
                    }elseif(strpos($url,'product-category') !== false){
                        $url_split = explode('#', $url);
                        $url = $url_split[0];

                                // Get rid of URL ?query=string
                        $url_split = explode('?', $url);
                        $url = $url_split[0];

                        $scheme = parse_url( home_url(), PHP_URL_SCHEME );
                        $url = set_url_scheme( $url, $scheme );

                        if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
                        $url = str_replace('://', '://www.', $url);

                        if ( false === strpos(home_url(), '://www.') )
                        $url = str_replace('://www.', '://', $url);

                        $url = trim($url, "/");
                        $slugs = explode('/', $url);				
                        $category = $this->get_product_category_by_slug('/'.end($slugs));
                        if(!empty($category)){
                            $newurl =  'link://product-category/'.$category->term_id;
                            update_option('swp_settings-push-url',$newurl);	
                        }
                        else{
//                            bamobile_mobiconnector_add_notice(__('Your URL is not Product Category URL. Please retype','swp'),'error');
                            wp_redirect( '?page=swp-one-signal&wootab=api' );
                            return true;
                        }
                    }else{
//                        bamobile_mobiconnector_add_notice(__('Your URL is not Product Category URL. Please retype','swp'),'error');
                        wp_redirect( '?page=swp-one-signal&wootab=api' );
                        return true;
                    }
                }
            }elseif($selectedurl == 'url-about-us'){			
                $newurl = 'link://about-us';
                update_option('swp_settings-push-url',$newurl);	
            }elseif($selectedurl == 'url-bookmark'){			
                $newurl = 'link://bookmark';
                update_option('swp_settings-push-url',$newurl);	
            }elseif($selectedurl == 'url-term-and-conditions'){			
                $newurl = 'link://term-and-conditions';
                update_option('swp_settings-push-url',$newurl);	
            }elseif($selectedurl == 'url-privacy-policy'){			
                $newurl = 'link://privacy-policy';
                update_option('swp_settings-push-url',$newurl);	
            }elseif($selectedurl == 'url-contact-us'){			
                $newurl = 'link://contact-us';
                update_option('swp_settings-push-url',$newurl);
            }
            $subtitle = @$_POST['swp-web-subtitle-notification'];
            update_option('swp_settings-subtitle',$subtitle);

            $sound = @$_POST['swp-web-sound-notification'];
            update_option('swp_settings-sound',$sound);

            $bigimage = @$_POST['swp-web-bigimages-notification'];
            if(isset($bigimage) && $bigimage != ''){
                $this->update_thumnail_swp($bigimage,'swp_notification_bigimages');		
                update_option('swp_settings-bigimages',$bigimage);
            }else{
                update_option('swp_notification_bigimages','');
                update_option('swp_settings-bigimages',$bigimage);
            }

            $responsecolortitle = @$_POST['swp-web-title-color-response-notification'];
            update_option('swp_settings-response-title-color',$responsecolortitle);

            $colortitle = @$_POST['swp-web-title-color-notification'];
            update_option('swp_settings-title-color',$colortitle);

            $responsecolorcontent = @$_POST['swp-web-content-color-response-notification'];
            update_option('swp_settings-response-content-color',$responsecolorcontent);

            $colorcontent = @$_POST['swp-web-content-color-notification'];
            update_option('swp_settings-content-color',$colorcontent);

            $bgimage = @$_POST['swp-web-bgimages-notification'];
            if(isset($bgimage) && $bgimage != ''){
                $this->update_thumnail_swp($bgimage,'swp_notification_background');
                update_option('swp_settings-bgimages',$bgimage);
            }else{
                update_option('swp_notification_background','');
                update_option('swp_settings-bgimages',$bgimage);
            }

            $responsecolorled = @$_POST['swp-web-led-color-response-notification'];
            update_option('swp_settings-response-led-color',$responsecolorled);

            $colorled = @$_POST['swp-web-led-color-notification'];
            update_option('swp_settings-led-color',$colorled);

            $responsecoloraccent = @$_POST['swp-web-accent-color-response-notification'];
            update_option('swp_settings-response-accent-color',$responsecoloraccent);

            $coloraccent = @$_POST['swp-web-accent-color-notification'];
            update_option('swp_settings-accent-color',$coloraccent);

            if(isset($_POST['saveandsend'])){
                $api = get_option('swp_settings-api');
                $rest = get_option('swp_settings-restkey');
                if(empty($api)){
//                    bamobile_mobiconnector_add_notice(__('Please input your api key!','swp'),'error');
                    wp_redirect( '?page=swp-one-signal&wootab=api' );
                    return true;			
                }
                elseif(empty($rest)){
//                    bamobile_mobiconnector_add_notice(__('Please input your rest api key!','swp'),'error');
                    wp_redirect( '?page=swp-one-signal&wootab=api' );
                    return true;			
                }
                global $wpdb;
                $table_name = $wpdb->prefix . "swp_data_api";
                $datas = $wpdb->get_results(
                    "
                    SELECT * 
                    FROM $table_name
                    WHERE api_key = '$api'
                    "
                );
                foreach($datas as $data){
                    $idswpapi = $data->api_id;
                }
                if(isset($_POST['checksegment']) && $_POST['checksegment'] == 'sendeveryone' ){
                    $notification = sendWooconnectorMessage();				
                }elseif(isset($_POST['checksegment']) && $_POST['checksegment'] == 'sendtoparticular'){
                    if(empty($_POST['include_segment'])){
//                        bamobile_mobiconnector_add_notice(__('Send to segments empty!','swp'),'error');
                        wp_redirect( '?page=swp-one-signal&wootab=api' );
                        return true;				
                    }
                    $segment = explode(',',trim($_POST['include_segment'],','));
                    $exsegment = explode(',',trim($_POST['exclude_segment'],','));
                    $notification = sendWooconnectorMessageBySegment($segment,$exsegment);
                }elseif(isset($_POST['checksegment']) && $_POST['checksegment'] == 'sendtotest'){
                    if(empty($_POST['list_test_player'])){
//                        bamobile_mobiconnector_add_notice(__('List test player empty!','swp'),'error');
                        wp_redirect( '?page=swp-one-signal&wootab=api' );
                        return true;				
                    }
                    $players = $_POST['list_test_player'];
                    $notification = sendWooconnectorMessageByPlayer($players);
                }				
                $noti = json_decode($notification);
                if(!empty($noti->errors)){
                    $errornoti = $noti->errors;
                    $invalids = $errornoti->invalid_player_ids;				
                    if(!empty($invalids)){
                        foreach($invalids as $invalid){
                            $iderrors[] = $invalid;
                        }
                        $iderror = implode(',',$iderrors);
                        $iderror = trim($iderror,',');
//                        bamobile_mobiconnector_add_notice(__('Invalid player ids','swp'),'error');
                        wp_redirect( '?page=swp-one-signal&wootab=api' );
                        return true;
                    }else{
//                        bamobile_mobiconnector_add_notice(__('All included players are not subscribed','swp'),'error');
                        wp_redirect( '?page=swp-one-signal&wootab=api' );
                        return true;					
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
//                bamobile_mobiconnector_add_notice(__('Successfully Send Notice','mobiconnector')); 
                wp_redirect( '?page=swp-one-signal&wootab=new' );	
                return true;
            }
//            bamobile_mobiconnector_add_notice(__('Successfully Save Notice','mobiconnector')); 
            wp_redirect( '?page=swp-one-signal&wootab=new' );
            return true;		
        }	

        public function get_product_category_by_slug( $slug  ) {
            $category = get_term_by( 'slug', $slug, 'product_cat' );
            if ( $category )
                _make_cat_compat( $category );

            return $category;
        }

        public function update_type_player($player,$device,$section){	
            if($section == 'addtotest'){
                preg_match("/iPhone|iPad|iPod|webOS/", $device, $matches);
                $os = current($matches);
                if($os){
                    $testtype = 2;
                }else{
                    $testtype = 1;
                }
            }elseif($section == 'deletetotest'){
                $testtype = 0;
            }
            $api = get_option('swp_settings-api');
            global $wpdb;
            $table_name = $wpdb->prefix . "swp_data_api";
            $datas = $wpdb->get_results(
                "
                SELECT * 
                FROM $table_name
                WHERE api_key = '$api'
                "
            );
            $idswpapi = 0;
            if(!empty($datas)){
                foreach($datas as $data){
                    $idswpapi = $data->api_id;
                }	
            }
            $table_update = $wpdb->prefix . "swp_data_player";
            $wpdb->update( 
                $table_update, 
                array( 				
                    'test_type' => 	$testtype
                ), 
                array( 
                    'api_id' => $idswpapi,
                    'player_id' => $player
                ), 
                array( 				
                    '%s'	
                ), 
                array( 
                    '%d', 
                    '%s'
                ) 
            );
            wp_redirect( '?page=swp-one-signal&wootab=player' );
        }

        public function update_thumnail_swp($url,$type) {					
            $wp_upload_dir = wp_upload_dir();	
            if(!empty($url) || $url != ''){
                $fileurl = str_replace($wp_upload_dir['baseurl'],'',$url);
                $absolute_pathto_file = $wp_upload_dir['basedir'].'/'.$fileurl;
                $path_parts = pathinfo($fileurl);
                $ext = strtolower($path_parts['extension']);
                $basename = strtolower($path_parts['basename']);
                $dirname = strtolower($path_parts['dirname']);
                $filename = strtolower($path_parts['filename']);				
                foreach($this->thumnails as $key => $value){
                    if($key == $type){
                        if($key == 'swp_notification_bigimages'){
                            list($width, $height) = getimagesize($absolute_pathto_file);
                            if($width < 512 || $height < 256){
                                $path = $dirname.'/'.$filename.'_'.$key.'_512_256.'.$ext;
                                $dest = $wp_upload_dir['basedir'].'/'.$path;
                                if(!file_exists($dest)){
                                    SWPresizesliderimage:: resize_image($absolute_pathto_file, $dest, 512, 256);
                                }					
                                update_option($key, $wp_upload_dir['baseurl'].$path);
                            }elseif($width > 2048 || $height > 1024){
                                $path = $dirname.'/'.$filename.'_'.$key.'_2048_1024.'.$ext;
                                $dest = $wp_upload_dir['basedir'].'/'.$path;
                                if(!file_exists($dest)){
                                    SWPresizesliderimage:: resize_image($absolute_pathto_file, $dest, 2048, 1024);		
                                }			
                                update_option($key, $wp_upload_dir['baseurl'].$path);
                            }else{
                                update_option($key, $url);
                            }
                        }
                        else{						
                            $path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
                            $dest = $wp_upload_dir['basedir'].'/'.$path;
                            if(!file_exists($dest)){
                                SWPresizesliderimage:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height']);		
                            }			
                            update_option($key, $wp_upload_dir['baseurl'].$path);						
                        }			

                    }
                }
            }else{
                foreach($this->thumnails as $key){
                    if($key == $type){
                        update_option($key,'');
                    }
                }
            }

            return true;
        }

        public function swp_admin_notice__success() {
            $class = 'notice notice-success notice-swp';
            $success = get_option('swp_save_currency_success_message');
            if($success != ''){
                $message = $success;
            }
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
        }

        public function swp_admin_notice__error() {
            $class = 'notice notice-error notice-swp';
            $error = get_option('swp_save_currency_error_message');
            if($error != ''){
                $message = $error;
            }
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
        }

        public $thumnails = array(
            'swp_notification_icon_small' => array(
                'width' => 48,
                'height' => 48
            ),
            'swp_notification_icon' => array(
                'width' => 256,
                'height' => 256
            ),
            'swp_notification_bigimages' => array(
                'width' => 1024,
                'height' => 512
            ),
            'swp_notification_background' => array(
                'width' => 2176,
                'height' => 256
            )
        );


    }
    if( is_admin() )
    $SWPappsettings = new SWPappsettings();
}