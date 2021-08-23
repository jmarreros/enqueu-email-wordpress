<form action="options.php" method="post">
    <?php
        settings_errors( 'dcms_messages' );
        settings_fields('dcms_enqueu_options_bd');
        do_settings_sections('dcms_enqueue_sfields');
        submit_button();
    ?>
</form>