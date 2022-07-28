<?php

define( 'WP_USE_THEMES', false );
require __DIR__ . '/../../../wp-blog-header.php';

include_once ( plugin_dir_path( __FILE__ ) . '/includes/process.php');


use dcms\enqueu\includes\Process;


$process = new Process();
$options = get_option('dcms_enqueu_options');

if ( $options['dcms_enable_queue'] ){
    $process->process_sent();
    error_log(print_r('Procesado', true));
}