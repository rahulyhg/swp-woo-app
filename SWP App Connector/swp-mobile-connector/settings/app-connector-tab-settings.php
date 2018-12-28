<?php defined('ABSPATH') or die('Denied');
$active_tab = isset( $_REQUEST[ 'tab' ] ) ? $_REQUEST[ 'tab' ] : 'settings';
global $wp_version;
$debug = 0;
?>
<h2 class="nav-tab-wrapper">
    <a href="?page=swp-app-settings&tab=swp_app_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_settings_tab' ? 'nav-tab-active' : ''; ?>">General Setting</a>
    <a href="?page=swp-app-settings&tab=swp_app_about_us_tab" class="nav-tab <?php echo $active_tab == 'swp_app_about_us_tab' ? 'nav-tab-active' : ''; ?>">About Us</a>
    <a href="?page=swp-app-settings&tab=swp_app_slider_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_slider_settings_tab' ? 'nav-tab-active' : ''; ?>">Slider</a>
    <a href="?page=swp-app-settings&tab=swp_app_popup_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_popup_settings_tab' ? 'nav-tab-active' : ''; ?>">Popup</a>
    <a href="?page=swp-app-settings&tab=swp_app_footer_settings_tab" class="nav-tab <?php echo $active_tab == 'swp_app_footer_settings_tab' ? 'nav-tab-active' : ''; ?>">Footer</a>
 </h2>