<?php
/**
 * Settings Theme Application
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$design_data = get_option('mobiconnector_settings-design');
if(!empty($design_data) && is_string($design_data)){
    $design_data = unserialize($design_data);
}elseif(!empty($design_data) && is_array($design_data)){
    $design_data = $design_data;
}else{
    $design_data = array();
}
?>
<?php require_once(MOBICONNECTOR_ABSPATH.'settings/views/mobiconnector-tab-settings.php'); ?>
<div class="wrap mobiconnector-settings">
	<h1><?php echo esc_html(__('Mobile Themes','mobiconnector')); ?></h1>
    <?php bamobile_mobiconnector_print_notices(); ?>
	<form method="POST" class="mobiconnector-setting-form" action="?page=mobiconnector-settings" id="settings-form">
        <input type="hidden" name="mtask" value="saveapplicationdesign"/>		
        <table id="mobiconnector-design">                       
            <tr class="mobiconnector-images-td">							
                <td class="mobi-label"></td> 
                <td class="mobi-content"> 
                    <img class="mobiconnector-images-after-choose" id="mobiconnector-application-logo" src=""/>
                    <span class="mobiconnector-warning">* <?php echo esc_html(__('You should upload a 1024x1024 image for logo','mobiconnector')); ?></span>
                </td> 
            </tr>	
            <tr>							
                <td class="mobi-label"><label  for="mobiconnector-application-logo"><?php echo esc_html(__('Application Logo','mobiconnector')); ?></label></td>
                <td class="mobi-content">
                    <?php bamobile_show_update_file_for_design_form('mobiconnector-application-logo',$design_data) ?>
                </td> 							
            </tr>  
            <tr>							
                <td class="mobi-label"><label  for="mobiconnector-application-logo"><?php echo esc_html(__('Items per page','mobiconnector')); ?></label></td>
                <td class="mobi-content"> 
                    <?php bamobile_show_input_for_design_form('mobiconnector-items-per-page',$design_data); ?>                    
                </td> 							
            </tr>          
            <tr class="mobiconnector-selected-form">							
                <td class="mobi-label"><label  for="mobiconnector-application-logo"><?php echo esc_html(__('Select Title Laster','mobiconnector')); ?></label></td>
                <td class="mobi-content"> 
                    <?php bamobile_show_select_for_design_form('mobiconnector-application-title-laster'); ?>
                </td> 							
            </tr>
            <tr>
                <td><input type="submit" class="button button-primary" id="mobiconnector-save-design" value="<?php esc_html_e(__('Save','mobiconnector'));  ?>"></td>
                <td></td>
            </tr>									
        </table>
    </form>
</div>