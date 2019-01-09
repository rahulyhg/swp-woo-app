<?php defined('ABSPATH') or die('Denied');
$active_tab = isset( $_REQUEST[ 'wootab' ] ) ? $_REQUEST[ 'wootab' ] : 'settings';
global $wp_version;
?>
<h2 class="nav-tab-wrapper" <?php if($wp_version < '4.8'){echo 'style="border-bottom: 1px solid #ccc"';}  ?>>
	<a id="wooconnector-general-settings" href="?page=app-connector&amp;wootab=settings" class="nav-tab nav-tab-general<?php echo (($active_tab == 'settings') ? ' nav-tab-active' : ''); ?>"><?php echo __('Settings','swp');?></a>
	<?php
		if(is_plugin_active('cellstore/cellstore.php') || is_plugin_active('olike/olike.php') || bamobile_is_extension_active('cellstore/cellstore.php') || bamobile_is_extension_active('olike/olike.php') ){
	?>
		<a id="wooconnector-settings-currency" href="?page=wooconnector&amp;wootab=currency" class="nav-tab nav-tab-template<?php echo (($active_tab == 'currency') ? ' nav-tab-active' : ''); ?>"><?php echo __('Currency','swp');?></a>	
	<?php
		}
	?>	
</h2>