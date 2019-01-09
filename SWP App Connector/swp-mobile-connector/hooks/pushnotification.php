<?php
	function sendWooconnectorMessage(){		
		$checkfirst = get_option('swp_first_push_notification');
		if($checkfirst == 1){
			$inclu = array('All');
		}else{
			$inclu = array('Engaged Users','Inactive Users','Active Users');
		}
		$fields = array(
			'app_id' => get_option('swp_settings-api'),			
			'included_segments' => $inclu,			
			'data' => array("foo" => "bar"),
			'headings' => get_option('swp_settings-title'),
			'contents' => get_option('swp_settings-content'),
			'subtitle' => get_option('swp_settings-subtitle'),
			'url' => get_option('swp_settings-push-url'),		
			'big_picture' =>  get_option('swp_notification_bigimages'),
			'chrome_web_image' => get_option('swp_notification_bigimages'),			
			'adm_big_picture' => get_option('swp_notification_bigimages'),
			'chrome_big_picture' =>  get_option('swp_notification_bigimages'),	
			'large_icon' => get_option('swp_notification_icon'),
			'chrome_web_icon' => get_option('swp_notification_icon'),
			'adm_large_icon' => get_option('swp_notification_icon'),
			'firefox_icon' => get_option('swp_notification_icon'),
			'chrome_icon' => get_option('swp_notification_icon'),			
			'adm_small_icon' => get_option('swp_notification_icon_small'),
			'small_icon' =>get_option('swp_notification_icon_small'),			
			'ios_sound' => get_option('swp_settings-sound'),
			'android_sound' => get_option('swp_settings-sound'),
			'wp_sound' => get_option('swp_settings-sound'),
			'adm_sound' => get_option('swp_settings-sound'),
			'wp_wns_sound' => get_option('swp_settings-sound'),
			'android_led_color' => get_option('swp_settings-led-color'),
			'android_accent_color' => get_option('swp_settings-accent-color'), 
			'android_background_layout' => array(
				'image' => get_option('swp_notification_background'),
				'headings_color' => get_option('swp_settings-title-color'),
				'contents_color' => get_option('swp_settings-content-color')
			)
		);
		
		$fields = json_encode($fields); 		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.get_option('swp_settings-restkey')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		update_option('swp_first_push_notification','1');
		return $response;
	}
	
	function sendWooconnectorMessageBySegment($segments,$exsegment){		
		$fields = array(
			'app_id' => get_option('swp_settings-api'),			
			'included_segments' => $segments,
			'excluded_segments' => $exsegment,	
			'data' => array("foo" => "bar"),
			'headings' => get_option('swp_settings-title'),
			'contents' => get_option('swp_settings-content'),
			'subtitle' => get_option('swp_settings-subtitle'),
			'url' => get_option('swp_settings-push-url'),
			'chrome_web_icon' => get_option('swp_notification_icon'),
			'chrome_web_image' => get_option('swp_notification_bigimages'),
			'big_picture' =>  get_option('swp_notification_bigimages'),
			'adm_big_picture' => get_option('swp_notification_bigimages'),
			'chrome_big_picture' =>  get_option('swp_notification_bigimages'),
			'adm_small_icon' => get_option('swp_notification_icon_small'),
			'adm_large_icon' => get_option('swp_notification_icon'),
			'firefox_icon' => get_option('swp_notification_icon'),
			'chrome_icon' => get_option('swp_notification_icon'),
			'small_icon' =>get_option('swp_notification_icon_small'),
			'large_icon' => get_option('swp_notification_icon'),
			'ios_sound' => get_option('swp_settings-sound'),
			'android_sound' => get_option('swp_settings-sound'),
			'wp_sound' => get_option('swp_settings-sound'),
			'adm_sound' => get_option('swp_settings-sound'),
			'wp_wns_sound' => get_option('swp_settings-sound'),
			'android_led_color' => get_option('swp_settings-led-color'),
			'android_accent_color' => get_option('swp_settings-accent-color'), 
			'android_background_layout' => array(
				'image' => get_option('swp_notification_background'),
				'headings_color' => get_option('swp_settings-title-color'),
				'contents_color' => get_option('swp_settings-content-color')
			)
		);
		
		$fields = json_encode($fields); 		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.get_option('swp_settings-restkey')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		update_option('swp_first_push_notification','1');
		return $response;
	}
	
	function sendWooconnectorMessageByPlayer($players){		
		$fields = array(
			'app_id' => get_option('swp_settings-api'),
			'include_player_ids' => $players,
			'data' => array("foo" => "bar"),
			'headings' => get_option('swp_settings-title'),
			'contents' => get_option('swp_settings-content'),
			'subtitle' => get_option('swp_settings-subtitle'),
			'url' => get_option('swp_settings-push-url'),
			'chrome_web_icon' => get_option('swp_notification_icon'),
			'chrome_web_image' => get_option('swp_notification_bigimages'),
			'big_picture' =>  get_option('swp_notification_bigimages'),
			'adm_big_picture' => get_option('swp_notification_bigimages'),
			'chrome_big_picture' =>  get_option('swp_notification_bigimages'),
			'adm_small_icon' => get_option('swp_notification_icon_small'),
			'adm_large_icon' => get_option('swp_notification_icon'),
			'firefox_icon' => get_option('swp_notification_icon'),
			'chrome_icon' => get_option('swp_notification_icon'),
			'small_icon' =>get_option('swp_notification_icon_small'),
			'large_icon' => get_option('swp_notification_icon'),
			'ios_sound' => get_option('swp_settings-sound'),
			'android_sound' => get_option('swp_settings-sound'),
			'wp_sound' => get_option('swp_settings-sound'),
			'adm_sound' => get_option('swp_settings-sound'),
			'wp_wns_sound' => get_option('swp_settings-sound'),
			'android_led_color' => get_option('swp_settings-led-color'),
			'android_accent_color' => get_option('swp_settings-accent-color'), 
			'android_background_layout' => array(
				'image' => get_option('swp_notification_background'),
				'headings_color' => get_option('swp_settings-title-color'),
				'contents_color' => get_option('swp_settings-content-color')
			)
		);
		
		$fields = json_encode($fields); 	

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.get_option('swp_settings-restkey')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		update_option('swp_first_push_notification','1');
		return $response;
	}

	function sendWooconnectorMessageByOrderStatus($players,$order_id,$nowtime){				
		$fields = array(
			'app_id' => get_option('swp_settings-api'),
			'include_player_ids' => array("$players"),
			'data' => array("foo" => "bar"),
			'headings' => array(
				'en' => sprintf(__('The order %s has been updated','swp'),'#'.$order_id)
			),
			'contents' => array(
				'en' => sprintf(__('Order %1$s has been updated on %2$s. You should log in your account to review this Order','swp'),'#'.$order_id,$nowtime)
			),
			'subtitle' => array(
				'en' => sprintf(__('The order %s has been updated','swp'),'#'.$order_id)
			)
		);
		
		$fields = json_encode($fields); 	

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.get_option('swp_settings-restkey')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		update_option('swp_first_push_notification','1');
		return $response;
	}
	
	function sendWooconnectorMessageOnProduct($productid,$title,$content){
		$url = get_post_permalink($productid);
		$product_id = url_to_postid($url);
		$newurl = '';
		if(!empty($product_id)) {
			if(strpos($url,'product') != false)	{
				$newurl =  str_replace($url, 'link://product/'.$product_id, $url);					
			}				
		}
		$imagesid = get_post_thumbnail_id($productid);
		update_icon_save_post($imagesid);
		$image = get_post_meta($imagesid,'swp_notification_icon',true);
		$wp_upload_dir = wp_upload_dir();
		$image =  $wp_upload_dir['baseurl']."/".$image;		
		$languageCode = get_locale();
		$language = swp_convert_languagesCode_to_isoCode($languageCode);
		if($language != 'en'){
			$head = array("en" => $title, $language => $title);
			$cont = array("en" => $content, $language => $content);
		}else{
			$head = array("en" => $title);			
			$cont = array("en" => $content);
		}
		$fields = array(
			'app_id' => get_option('swp_settings-api'),			
			'included_segments' => array('All'),			
			'data' => array("foo" => "bar"),
			'headings' => $head,
			'contents' => $cont,
			'subtitle' => $head,
			'url' => $newurl,
			'chrome_web_icon' => $image,			
			'adm_large_icon' => $image,
			'firefox_icon' => $image,
			'chrome_icon' => $image,		
			'large_icon' => $image	
		);
		$fields = json_encode($fields); 		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.get_option('swp_settings-restkey')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		update_option('swp_first_push_notification','1');
		return $response;
	}
	
	function update_icon_save_post($imagesid){
		$thumnails = array(		
			'swp_notification_icon' => array(
				'width' => 256,
				'height' => 256
			)
		);
		$wp_upload_dir = wp_upload_dir();
		$image = get_post_meta( $imagesid, '_wp_attached_file', true);		
		if(!empty($image)){
			$absolute_pathto_file = $wp_upload_dir['basedir'].'/'.$image;
			$path_parts = pathinfo($image);
			$ext = strtolower($path_parts['extension']);
			$basename = strtolower($path_parts['basename']);
			$dirname = strtolower($path_parts['dirname']);
			$filename = strtolower($path_parts['filename']);
			foreach($thumnails as $key => $value){				
				$path = $dirname.'/'.$filename.'_'.$key.'_'.$value['width'].'_'.$value['height'].'.'.$ext;
				$dest = $wp_upload_dir['basedir'].'/'.$path;
				WooConnectorCore:: resize_image($absolute_pathto_file, $dest, $value['width'], $value['height'],true);
				update_post_meta ($imagesid, $key, $path);
			}
		}	
	}
	
	function getNotificationById($notificationId){
		$api = get_option('swp_settings-api');
		$rest = get_option('swp_settings-restkey');		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications/$notificationId?app_id=$api");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
								   'Authorization: Basic '.$rest));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		
		$response = curl_exec($ch);
		curl_close($ch);				
		$return = json_decode( $response);	
		return $return;	
	}
?>