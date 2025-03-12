<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Database;

// Email class for overriding filters and avoid sending email
class Email{
    private $enable_enqueu;

    public function __construct(){
		global $dcms_mail_real;

        $options = get_option(DCMS_ENQUEU_OPTIONS);
        $this->enable_enqueu = $options['dcms_enable_queue']??0;

		$dcms_mail_real = ! $this->enable_enqueu ? true : false;

        add_filter( 'wp_mail', [$this, 'dcms_wp_mail'], 1, 1 );
		add_filter( 'pre_wp_mail', [$this, 'dcms_pre_wp_mail'], 9999, 2 );
        add_action( 'phpmailer_init', [$this, 'dcms_phpmailer_init'], 1 , 1);
    }

    // Save email data
    public function dcms_wp_mail( $atts ){
        global $dcms_mail_real;
	    global $dcms_mail_real_specific_subject;

	    $email_subject = $atts['subject'];

		if ( ! $dcms_mail_real_specific_subject ){
			//Only for mails with certain subject
			$options_customarea = get_option( 'customarea_options' ) ?? [];
			$subject_customarea = $options_customarea['dcms_subject_email_validation'] ?? '';

			if ( $subject_customarea !== '' &&  $email_subject !== $subject_customarea ) {
				$dcms_mail_real = true;
			} else {
				$dcms_mail_real = false;
			}
		}

		if( ! $dcms_mail_real ){

            $email_data = base64_encode(json_encode($atts));

            $db = new Database();
            $result = $db->insert_email_data($email_subject, $email_data);
            
            if ( ! $result ) error_log('Error insertar: '. $atts['to'] . ' - '.$email_subject);
        }

        return $atts;
    }

    // For cancel email send
    public function dcms_pre_wp_mail(){
        global $dcms_mail_real;
		if( ! $dcms_mail_real ){
			return true;
		}
		return null;
    }

    // Callback PHPMailer hook
    public function dcms_phpmailer_init( &$phpmailer ){
		global $dcms_mail_real;

		if( ! $dcms_mail_real ){
			remove_all_actions( 'phpmailer_init' );
			$phpmailer = new class {
                                function send(){ return true; }
		                };
        }
    }
}