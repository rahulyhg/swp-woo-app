<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.	
}
	
?>
<?php require_once('subtab.php'); ?>

<div class="wrap wooconnector-settings">	
	<h1><?php echo __('Sent Notification','wooconnector')?></h1>
	<form method="POST" class="wooconnector-setting-form" action="?page=swp-one-signal&wootab=list" id="settings-form">
		<?php WooconnectordisplayNotification(); ?>
	</form>
</div>