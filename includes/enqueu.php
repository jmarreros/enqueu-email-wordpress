<?php

namespace dcms\enqueu\includes;

// Enqueu files style.css and javascript.js
class Enqueu{

    public function __construct(){
        add_action('admin_enqueue_scripts', [$this, 'register_scripts_backend']);
    }

    // Backend scripts
    public function register_scripts_backend(){
        wp_register_style('admin-enqueu-style',
                            DCMS_ENQUEU_URL.'/assets/style.css',
                            [],
                            DCMS_ENQUEU_VERSION );

    }

}