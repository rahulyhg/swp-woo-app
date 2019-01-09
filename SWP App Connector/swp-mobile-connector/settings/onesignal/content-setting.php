<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.

}

$languages = array('en' => 'English' ,'vn' => 'Vietnamese','zh-Hant' => 'China (Traditional)','nl' => 'Dutch','ka' => 'Georgian','hi' => 'Hindi','it' => 'Italian','ja' => 'Japanese','ko' => 'Korean','lv' => 'Latvian','lt' => 'Lithuanian','fa' => 'Persian', 'sr' => 'Serbian','th' => 'Thai','ar' => 'Arabic', 'hr' => 'Croatian', 'et' => 'Estonian', 'bg' => 'Bulgarian', 'he' => 'Hebrew', 'ms' => 'Malay','pt' => 'Portuguese', 'sk' => 'Slovak', 'tr' => 'Turkish', 'ca' => 'Catalan', 'cs' => 'Czech', 'fi' => 'Finnish', 'de' => 'German', 'hu' => 'Hungarian', 'nb' => 'Norwegian', 'ro' => 'Romanian', 'es' => 'Spanish', 'uk' => 'Ukrainian', 'zh-Hans' => 'Chinese (Simplified)', 'da' => 'Danish', 'fr' => 'French', 'el' => 'Greek', 'id' => 'Indonesian', 'pl' => 'Polish', 'ru' => 'Russian','sv' => 'Swedish');

$urls = array('url-product'=> 'Product Url', 'url-category' => 'Category Url', 'url-about-us' => 'About Us Url', 'url-bookmark' => 'Bookmark Url','url-term-and-conditions' => 'Term And Conditions','url-privacy-policy'=>'Privacy Policy');

$checktitle = get_option('swp_settings-title');

if($checktitle){

	$titles = $checktitle;

	foreach($titles as $title => $value){

		if($title == 'en'){

			$titlepreview = $value;

		}

	}

}

else{

	$titles = $languages;

}

$checkcontent = get_option('swp_settings-content');

if($checkcontent){

	$contents = $checkcontent;	

	foreach($contents as $content => $value){

		if($content == 'en'){

			$contentpreview = $value;

		}

	}

}

else{

	$contents = $languages;

}

$checksubtitle = get_option('swp_settings-subtitle');

if($checksubtitle){

	$subtitles = $checksubtitle;	

}

else{

	$subtitles = $languages;

}

$icon = get_option('swp_settings-id-icon');

$smicon = get_option('swp_settings-sm-icon');

$bigimages = get_option('swp_settings-bigimages');

$responsetitlecolor = get_option('swp_settings-response-title-color');

$responsecontentcolor = get_option('swp_settings-response-content-color');

$linkdefaultimage = "";//get_bloginfo('url').'/wp-content/plugins/swp/assets/images/default.jpg';

if(get_option('swp_settings-url-selected')){

	$selectedurl = get_option('swp_settings-url-selected');

}else{

	$selectedurl = "";

}

?>

<?php require_once('subtab.php'); ?>

<div id="swp-preview-notification" <?php if(get_option('swp_settings-bgimages')){echo 'style="background-image: url('.get_option('swp_settings-bgimages').')"'; }else{ echo '';}?>>

	<div id="title-small-icon-notification" class="<?php if(get_option('swp_settings-bgimages')){echo ' bgpreviews'; }else{ echo '';} ?>" <?php if($smicon){echo 'style="display:block"';}else{echo 'style="display:none"';} ?>>

		<img src="<?php if($smicon){echo $smicon;}else{echo '';} ?>" id="swp-small-icon"/>

		<span>You app title</span>

		<button id="click-open-big-images" <?php if($bigimages){ echo 'style="display:block"'; }else{ echo 'style="display:none"';} ?>></button>

	</div>

	<div id="swp-content-notification-preview">

		<div id="swp-preview-icon-notification" class="<?php if($smicon){echo 'small';}else{echo '';} if(get_option('swp_settings-bgimages')){echo ' bgpreviews'; }else{ echo '';} if($smicon && empty($bigimages)){echo ' switchImages';} else{echo '';} if($icon){ echo ' exicon'; } else { echo '';}?>" >

			<img src="<?php if($icon){ echo $icon; } else { echo '';}?>" id="swp-preview-icon"/>

		</div>

		<div id="swp-preview-text-notification" class="<?php if($smicon){echo 'small';}else{echo '';} if($icon){echo ' exicon';}else{ echo "";} if($smicon && empty($bigimages)){echo ' switchImages';} else{echo '';} ?>">

			<div id="swp-privew-title-text" class="<?php if($smicon){echo 'small';}else{echo '';} ?>" <?php if($responsetitlecolor){echo 'style="color:#'.$responsetitlecolor.'"';}?>>

				<?php if(get_option('swp_settings-title')){echo $titlepreview;} else{echo 'Preview';} ?>

			</div>

			<div id="swp-privew-content-text" class="<?php if($smicon){echo 'small';}else{echo '';} ?>" <?php if($responsecontentcolor){echo 'style="color:#'.$responsecontentcolor.'"';}?>>

				<?php if(get_option('swp_settings-content')){echo $contentpreview;} else{echo '';} ?>

			</div>

		</div>

	</div>	

	<div id="swp-big-images-previews" class="<?php if($bigimages){echo 'switchImages'; }else{ echo '';}  if(get_option('swp_settings-bgimages')){echo ' bgpreviews'; }else{ echo '';}?>">

		<img src="<?php if($bigimages){ echo $bigimages; }else{ echo '';} ?>" id="big-images-preview" />

	</div>

</div>



<div class="wrap swp-settings">

	<?php

		$api = get_option('swp_settings-api');

		$rest = get_option('swp_settings-restkey');

	?>

	<h1><?php echo __('New push notification','swp')?></h1>

	<?php

		//bamobile_mobiconnector_print_notices();

	?>

	<?php

		if(!empty($api) && !empty($rest)){

	?>

	<form method="POST" class="swp-setting-form" action="?page=swp-one-signal&wootab=new" id="settings-form">

		<input type="hidden" name="wootask" value="saveonesignal-content"/>

		<input type="hidden" id="swp-baseurl" name="ajaxbaseurl" value="<?php echo ABSPATH; ?>"/>

		<div id="wooconnector-settings-body">

			<div id="wooconnector-body" >

				<div id="wooconnector-body-content">	

					<table id="table-swp">						

						<tr>							

							<td class="app-label"><label  for="swp-web-content-notification"><?php echo __('Title Notification','swp')?></label> * </td>

							<td class="app-content">

								<?php foreach($titles as $title => $value){ ?>

								<input type="text" style="display:none" class="swp-web-input-notification swp-web-title-notification" id="swp-web-title-notification-<?php echo $title;?>"  name="swp-web-title-notification[<?php echo $title;?>]" value="<?php if(get_option('swp_settings-title')){echo $value;} ?>"  />									

								<?php } ?>

								<select class="select-languages-notification" name="swp-web-language-notification">

									<?php foreach($languages as $language => $value){ ?>

										<option value="<?php echo $language; ?>"><?php echo $value; ?></option>										

									<?php } ?>

								</select>

							</td>												

						</tr>	

						<tr>							

							<td class="app-label"><label  for="swp-web-content-notification"><?php echo __('Content Notification','swp')?></label> * </td>

							<td class="app-content">

								<?php foreach($contents as $content => $value){ ?>

									<textarea style="display:none" class="swp-web-input-notification" id="swp-web-content-notification-<?php echo $content;?>"  name="swp-web-content-notification[<?php echo $content;?>]"><?php if(get_option('swp_settings-content')){echo $value;} ?></textarea>									

								<?php } ?>

								<select class="select-languages-notification" name="swp-web-language-notification">

									<?php foreach($languages as $language => $value){ ?>

										<option value="<?php echo $language; ?>"><?php echo $value; ?></option>										

									<?php } ?>

								</select>

							</td>												

						</tr>

						<tr style="display:none">							

							<td class="app-label"><label  for="swp-web-subtitle-notification"><?php echo __('Subtitle Notification','swp')?></label></td>

							<td class="app-content">

								<?php foreach($subtitles as $subtitle => $value){ ?>

									<input type="text" style="display:none" class="swp-web-input-notification" id="swp-web-subtitle-notification-<?php echo $subtitle;?>"  name="swp-web-subtitle-notification[<?php echo $subtitle;?>]" value="<?php if(get_option('swp_settings-subtitle')){echo $value;} ?>"  />												

								<?php } ?>								

								<select class="select-languages-notification" name="swp-web-language-notification">

									<?php foreach($languages as $language => $value){ ?>

										<option value="<?php echo $language; ?>"><?php echo $value; ?></option>										

									<?php } ?>

								</select>

								<span class="swp-warning">*<?php echo __('Subtitle only for ios 10+','swp')?></span>

							</td>												

						</tr>

						<tr>							

							<td class="app-label"><label  for="swp-web-url-notification"><?php echo __('Internal Link Clickable','swp')?> </label></td>

							<td class="app-content url-change"> 

								<select class="select-url-notification" name="swp-web-url-select-notification">

									<?php 

										foreach($urls as $url => $value){

									?>

									<option <?php if($selectedurl == $url){echo 'selected'; }else{ echo ""; } ?> value="<?php echo $url; ?>"><?php echo $value; ?></option>

									<?php

										}

									?>

								</select>

								<?php 

									foreach($urls as $url => $value){

										if($url == 'url-product' || $url == 'url-category'){	

								?>

								<input type="text" class="swp-web-input-notification swp-web-url-notification" placeholder="Input <?php echo str_replace("Url","",$value); ?>name" name="swp-web-url-notification-<?php echo $url; ?>" id="swp-web-url-notification-<?php echo $url; ?>" value="<?php if(get_option('swp_settings-url') && $selectedurl == $url){echo get_option('swp_settings-url');}?>">

								<?php

										}

									}

								?>

								<div id="list-url-notification"></div>								

							</td> 

						</tr>

						<tr>							

							<td class="app-label"><label  for="swp-web-icon-notification"><?php echo __('Small Icon Notification','swp')?></label></td>

							<td class="app-content"> 

								<input type="hidden" name="swp-web-smicon-notification" id="swp-web-smicon-notification" value="<?php if($smicon){echo $smicon;}?>">								

								<input type="button" class="swp-button-input-file" id="swp-button-input-smfile" value="Choose File">

								<input <?php if($smicon){ echo 'style="display:block"';}else{ echo 'style="display:none"'; }?> type="button" class="swp-button-input-file" id="swp-button-delete-smfile" value="Delete File">

							</td> 							

						</tr>										

						

						<tr class="swp-images-td">							

							<td class="app-label"></td>

							<td class="app-content"> 

								<img class="swp-images-after-choose" id="swp-smicon-web-browser" src="<?php if($smicon){echo $smicon;}else{ echo $linkdefaultimage;}?>"/>

								<span class="swp-warning">* <?php echo __('You should upload a 48x48 image for small icon','swp')?></span>

							</td> 

						</tr>	

						<tr>							

							<td class="app-label"><label  for="swp-web-icon-notification"><?php echo __('Icon Notification','swp')?></label></td>

							<td class="app-content"> 

								<input type="hidden" name="swp-web-icon-notification" id="swp-web-icon-notification" value="<?php if($icon){echo $icon;}?>">								

								<input type="button" class="swp-button-input-file" id="swp-button-input-file" value="Choose File">

								<input <?php if($icon){ echo 'style="display:block"';}else{ echo 'style="display:none"'; }?> type="button" class="swp-button-input-file" id="swp-button-delete-file" value="Delete File">

							</td> 							

						</tr>										

						

						<tr class="swp-images-td">							

							<td class="app-label"><label ></label></td>

							<td class="app-content"> 

								<img class="swp-images-after-choose" id="swp-icon-web-browser" src="<?php if($icon){echo $icon;}else{ echo $linkdefaultimage;}?>"/>

								<span class="swp-warning">* <?php echo __('You should upload a 256x256 image for icon','swp')?></span>

							</td> 

						</tr>

						<tr >							

							<td class="app-label"><label  for="swp-web-bigimages-notification"><?php echo __('Bigimages Notification','swp')?> </label></td>

							<td class="app-content"> 

								<input type="hidden" name="swp-web-bigimages-notification" id="swp-web-bigimages-notification" value="<?php if($bigimages){echo $bigimages;}?>">								

								<input type="button" class="swp-button-input-file" id="swp-button-input-bigimages" value="Choose File">

								<input <?php if($bigimages){ echo 'style="display:block"';}else{ echo 'style="display:none"'; }?> type="button" class="swp-button-input-file" id="swp-button-delete-bigimages" value="Delete File">

							</td> 							

						</tr>										

						

						<tr class="swp-images-td">							

							<td class="app-label"><label ></label></td>

							<td class="app-content"> 

								<img class="swp-images-after-choose" id="swp-bigimages-web-browser" src="<?php if($bigimages){echo $bigimages;}else{ echo $linkdefaultimage;}?>"/>

								<span class="swp-warning">* <?php echo __('Use a 2:1 aspect ratio, You should upload a 1024x512 image for big images','swp')?></span>

							</td> 

						</tr>

						<tr id="sound-notification">							

							<td class="app-label"><label  for="swp-web-sound-notification"><?php echo __('Sound Notification','swp')?></label></td>

							<td class="app-content"> 

								<input type="text" name="swp-web-sound-notification" id="swp-web-sound-notification" value="<?php if(get_option('swp_settings-sound')){echo get_option('swp_settings-sound');}?>">							

							</td> 							

						</tr>	

						<tr id="icon-notification">							

							<td class="app-label"><label  for="swp-web-bgimages-notification"><?php echo __('Android Background Images','swp')?></label></td>

							<td class="app-content"> 

								<input type="hidden" name="swp-web-bgimages-notification" id="swp-web-bgimages-notification" value="<?php if(get_option('swp_settings-bgimages')){echo get_option('swp_settings-bgimages');}?>">								

								<input type="button" class="swp-button-input-file" id="swp-button-input-bgimages" value="Choose File">

								<input type="button" <?php if(get_option('swp_settings-bgimages')){ echo 'style="display:block"';}else{ echo 'style="display:none"'; }?> class="swp-button-input-file" id="swp-button-delete-bgimages" value="Delete File">								

							</td> 							

						</tr>	

						<tr class="swp-images-td">							

							<td class="app-label"></td>

							<td class="app-content"> 

								<img class="swp-images-after-choose" id="swp-bgimages-web-browser" src="<?php if(get_option('swp_settings-bgimages')){echo get_option('swp_settings-bgimages');}else{ echo $linkdefaultimage;}?>"/>

								<span class="swp-warning">* <?php echo __('You should upload a 17 : 2 image for background','swp')?> </span>

							</td> 

						</tr>					

						<tr>							

							<td class="app-label"><label  for="swp-web-title-color-notification"><?php echo __('Android Title Color','swp')?> </label></td>

							<td class="app-content"> 

								<input type="text" class="jscolor" id="swp-web-title-color-notification" value="<?php if($responsetitlecolor){echo $responsetitlecolor;}else{echo 'ab2567';}?>">

								<input type="hidden" id="hidden-web-title-color-response-notification" name="swp-web-title-color-response-notification" value="<?php if($responsetitlecolor){echo $responsetitlecolor;}?>" />

								<input type="hidden" id="hidden-web-title-color-notification" name="swp-web-title-color-notification" value="<?php if(get_option('swp_settings-title-color')){echo get_option('swp_settings-title-color');}?>"/>

							</td> 

						</tr>

						<tr>							

							<td class="app-label"><label  for="swp-web-content-color-notification"><?php echo __('Android Content Color','swp')?> </label></td>

							<td class="app-content"> 

								<input type="text" class="jscolor" id="swp-web-content-color-notification" value="<?php if($responsecontentcolor){echo $responsecontentcolor;}else{echo 'ab2567';}?>">

								<input type="hidden" id="hidden-web-content-color-response-notification" name="swp-web-content-color-response-notification" value="<?php if($responsecontentcolor){echo $responsecontentcolor;}?>" />

								<input type="hidden" id="hidden-web-content-color-notification" name="swp-web-content-color-notification" value="<?php if(get_option('swp_settings-content-color')){echo get_option('swp_settings-content-color');}?>"/>

							</td> 

						</tr>

						<tr class="swp-images-td">							

							<td class="app-label"></td>

							<td class="app-content"> 

								<span class="swp-warning">* <?php echo __('The title color or content color only changes when you use background images','swp')?> </span>

							</td> 

						</tr>	

						<tr>							

							<td class="app-label"><label  for="swp-web-led-color-notification"><?php echo __('Android Led Color','swp')?></label></td>

							<td class="app-content"> 

								<input type="text" class="jscolor" id="swp-web-led-color-notification" value="<?php if(get_option('swp_settings-response-led-color')){echo get_option('swp_settings-response-led-color');}else{echo 'ab2567';}?>">

								<input type="hidden" id="hidden-web-led-color-response-notification" name="swp-web-led-color-response-notification" value="<?php if(get_option('swp_settings-response-led-color')){echo get_option('swp_settings-response-led-color');}?>" />

								<input type="hidden"  id="hidden-web-led-color-notification" name="swp-web-led-color-notification" value="<?php if(get_option('swp_settings-led-color')){echo get_option('swp_settings-led-color');}?>"/>

							</td> 

						</tr>

						

						<tr>							

							<td class="app-label"><label  for="swp-web-accent-color-notification"><?php echo __('Android Accent Color','swp')?></label></td>

							<td class="app-content"> 

								<input type="text" class="jscolor" id="swp-web-accent-color-notification" value="<?php if(get_option('swp_settings-response-accent-color')){echo get_option('swp_settings-response-accent-color');}else{echo 'ab2567';}?>">

								<input type="hidden" id="hidden-web-accent-color-response-notification" name="swp-web-accent-color-response-notification" value="<?php if(get_option('swp_settings-response-accent-color')){echo get_option('swp_settings-response-accent-color');}?>" />

								<input type="hidden" id="hidden-web-accent-color-notification" name="swp-web-accent-color-notification" value="<?php if(get_option('swp_settings-accent-color')){echo get_option('swp_settings-accent-color');}?>"/>

							</td> 

						</tr>

						<tr>							

							<td class="app-label"><label  for="swp-web-accent-color-notification"><?php echo __('Send this message to?','swp')?></label></td>

							<td class="app-content"> 

								<div class="segment-border-div">

									<input type="radio" class="radiosegment" name="checksegment" id="sendeveryone" checked="checked" value="sendeveryone"/> <label for="sendeveryone">Send to Everyone</label><br>

								</div>

								<div class="segment-border-div">

									<input type="radio" class="radiosegment" name="checksegment" id="sendtoparticular" value="sendtoparticular"/> <label for="sendtoparticular"><?php echo __('Send to Particular Segment(s)','swp')?></label><br>

									<div class="dropdown-radiosegment" id="dropdown-sendtoparticular">

										<b><?php echo __('Send to segments','swp')?></b><br>

										<div class="swp-dropdown">

											<input type="text" class="list-segment" name="include_segment" autocomplete="off" />

											<div class="mutliSelect">

												<ul>

													<li class="swp-li-dropdown">

														<input type="checkbox" value="All" id="allsegment" /><label class="swp-label-dropdown" for="allsegment"><?php echo __('All','swp')?></label></li>

													<li class="swp-li-dropdown">

														<input type="checkbox" value="Engaged Users" id="engagedusers" /><label class="swp-label-dropdown" for="engagedusers"><?php echo __('Engaged Users','swp')?></label></li>

													<li class="swp-li-dropdown">

														<input type="checkbox" value="Active Users" id="activeusers" /><label class="swp-label-dropdown" for="activeusers"><?php echo __('Active Users','swp')?></label></li>

													<li class="swp-li-dropdown">

														<input type="checkbox" value="Inactive Users" id="inactiveuser" /><label class="swp-label-dropdown" for="inactiveuser"><?php echo __('Inactive Users','swp')?></label></li>																						

												</ul>

											</div>

										 

										</div>

										<b><?php echo __('Exclude users in segments','swp')?></b><br>

										<div class="swp-dropdown">

											<input type="text" class="list-segment" name="exclude_segment" autocomplete="off" />

											<div class="mutliSelect">

												<ul>

													<li>

														<input type="checkbox" value="All" id="exallsegment" /><label class="swp-label-dropdown" for="exallsegment"><?php echo __('All','swp')?></label></li>

													<li>

														<input type="checkbox" value="Engaged Users" id="exengagedusers" /><label class="swp-label-dropdown" for="exengagedusers"><?php echo __('Engaged Users','swp')?></label></li>

													<li>

														<input type="checkbox" value="Active Users" id="exactiveusers" /><label class="swp-label-dropdown" for="exactiveusers"><?php echo __('Active Users','swp')?></label></li>

													<li>

														<input type="checkbox" value="Inactive Users" id="exinactiveuser" /><label class="swp-label-dropdown" for="exinactiveuser"><?php echo __('Inactive Users','swp')?></label></li>																						

												</ul>

											</div>

										 

										</div>

									</div>

								</div>

								<div class="segment-border-div">

									<input type="radio" class="radiosegment" name="checksegment" id="sendtotest" value="sendtotest"/> <label for="sendtotest"><?php echo __('Send to Test Device(s)','swp')?></label><br>

									<div class="dropdown-radiosegment" id="dropdown-sendtotest">

										<?php 

											global $wpdb;

											$table_name = $wpdb->prefix . "swp_data_player";

											$players = $wpdb->get_results(

												"

												SELECT * 

												FROM $table_name

												WHERE test_type = 1 OR test_type = 2 

												"

											);

											if(empty($players)){

										?>

											<span class="swp-warning"><?php echo __('You have not added any test users yet. You can do this on the List Player page.','swp')?></span>

										<?php

											}else{

											foreach($players as $player){

										?>

											<div class="list-test-player"><input type="checkbox" name="list_test_player[]" class="input-list-test-player" id="test_player_<?php echo $player->id; ?>" value="<?php echo $player->player_id ?>"/><label for="test_player_<?php echo $player->id; ?>"><?php echo $player->device_model.' '.$player->device_os ?></label></div>

										<?php

											}}

										?>

									</div>

								</div>

							</td> 

						</tr>

					</table>

				</div>

				<div id="app-button">

					<input  type="submit" id="save-notification" name="publish2" class="button button-primary button-large" value="<?php echo __('Save','swp');?>">					

					<input  type="submit" id="save-and-send-notification" name="saveandsend" class="button button-primary button-large" value="<?php echo __('Save And Send','swp');?>">					

					<div class="clear"></div>

				</div>				

			</div>

		</div>

	</form>

	<?php

		}

	?>

</div>