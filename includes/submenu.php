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
        include_once (DCMS_ENQUEU_PATH. '/views/main-screen.php');
    }
}