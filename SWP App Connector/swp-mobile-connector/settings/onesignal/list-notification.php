<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.	
}
	
?>
<?php require_once('subtab.php'); ?>

<div class="wrap swp-settings">	
	<h1><?php echo __('Sent Notification','wooconnector')?></h1>
	<form method="POST" class="swp-setting-form" action="?page=swp-one-signal&wootab=list" id="settings-form">
		<?php swp_display_notification(); ?>
	</form>
</div>