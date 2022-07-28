<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! current_user_can( 'manage_options' ) ) return; // only administrator

// Tabs definitions
$plugin_tabs = Array();
$plugin_tabs['cron-log'] = __('Log de Correos', 'dcms-enqueu-email');
$plugin_tabs['cron-config'] = __('Configuración', 'dcms-enqueu-email');
$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'cron-log';


echo "<div class='wrap'>"; //start wrap
echo "<h1>" . __('Cola de correos', 'dcms-enqueu-email') . "</h1>";

// Intefaz tabs
echo '<h2 class="nav-tab-wrapper">';
foreach ( $plugin_tabs as $tab_key => $tab_caption ) {
    $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
    echo "<a data-tab='".$current_tab."' class='nav-tab " . $active . "' href='".admin_url( DCMS_ENQUEU_SUBMENU . "&page=enqueu-email&tab=" . $tab_key )."'>" . $tab_caption . '</a>';
}
echo '</h2>';

switch ($current_tab){
    case 'cron-log':
        include_once('partials/screen-log.php');
        break;
    case 'cron-config':
        include_once('partials/screen-settings.php');
        break;
}

echo "</div>"; //end wrap