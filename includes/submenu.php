<?php

namespace dcms\enqueu\includes;

/**
 * Class for creating a dashboard submenu
 */
class Submenu{
    // Constructor
    public function __construct(){
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    // Register submenu
    public function register_submenu(){
        add_submenu_page(
            DCMS_ENQUEU_SUBMENU,
            __('Enqueu Emails','dcms-enqueu-email'),
            __('Enqueu Emails','dcms-enqueu-email'),
            'manage_options',
            'enqueu-email',
            [$this, 'submenu_page_callback']
        );
    }

    // Callback, show view
    public function submenu_page_callback(){
        wp_enqueue_style('admin-enqueu-style');
        // wp_enqueue_script('admin-reservation-script');
        // wp_localize_script('admin-reservation-script','dcms_res_config',[
        //         'ajaxurl'=>admin_url('admin-ajax.php'),
        //         'nonce' => wp_create_nonce('ajax-nonce-config')
        //     ]);

        include_once (DCMS_ENQUEU_PATH. '/views/main-screen.php');
    }
}