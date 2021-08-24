<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Database;

class Process{

    public function __construct(){
		global $dcms_mail_real;
		$dcms_mail_real = false;

        add_filter( 'wp_mail', [$this, 'dcms_wp_mail'], 1, 1 );
		add_filter( 'pre_wp_mail', [$this, 'dcms_pre_wp_mail'], 9999, 2 );
        add_action( 'phpmailer_init', [$this, 'dcms_phpmailer_init'], 1 , 1);

        add_action('admin_post_process_force_sent', [$this, 'process_force_sent']);
    }

    // Save email data
    public function dcms_wp_mail( $atts ){
        global $dcms_mail_real;

		if($dcms_mail_real == false){
            $email_subject = $atts['subject'];
            $email_data = base64_encode(json_encode($atts));

            $db = new Database();
            $db->insert_email_data($email_subject, $email_data);
        }

        return $atts;
    }


    // For cancel email send
    public function dcms_pre_wp_mail(){
        global $dcms_mail_real;
		if($dcms_mail_real == false){
			return true;
		}
		return null;
    }

    // Callback PHPMailer hook
    public function dcms_phpmailer_init( &$phpmailer ){
		global $dcms_mail_real;

		if( $dcms_mail_real == false ){
			remove_all_actions( 'phpmailer_init' );
			$phpmailer = new class {
                                function send(){ return true; }
		                    };
        }
    }

    // Force send mails queue
    public function process_force_sent(){

        $db = new Database();

        $result = $db->get_pending_emails(150);

        wp_redirect( admin_url( DCMS_ENQUEU_SUBMENU . '?page=enqueu-email' ) );
    }

}