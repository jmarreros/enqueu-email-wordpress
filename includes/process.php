<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Database;

class Process{

    private $enable_enqueu;
    private $quantity_batch;
    private $email_from;
    private $email_from_name;

    public function __construct(){
		global $dcms_mail_real;

        $options = get_option(DCMS_ENQUEU_OPTIONS);
        $this->enable_enqueu = $options['dcms_enable_queue'];
        $this->quantity_batch = $options['dcms_quantity_batch'];
        $this->email_from = $options['dcms_enqueue_from'];
        $this->email_from_name = $options['dcms_enqueue_from_name'];

		$dcms_mail_real = ! $this->enable_enqueu ? true : false;

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
        $this->process_sent();
        wp_redirect( admin_url( DCMS_ENQUEU_SUBMENU . '?page=enqueu-email' ) );
    }


    // Filter from and name if exixts
    private function filter_from_sender_mail(){
        if ( $this->email_from ){
            add_filter( 'wp_mail_from', function(){
                return $this->email_from;
            });
        }

        if ( $this->email_from_name ){
            add_filter( 'wp_mail_from_name', function(){
                return $this->email_from_name;
            });
        }
    }


    public function process_sent(){
        $db = new Database();

        $this->filter_from_sender_mail();
        $items = $db->get_pending_emails($this->quantity_batch);

        if ( $items ){
            foreach( $items as $item){
                $atts = json_decode(base64_decode($item->data), true);

                $to = $atts['to'];
				$subject = $atts['subject'];
				$message = $atts['message'];
				$headers = $atts['headers'];
				$attachments = $atts['attachments'];

                global $dcms_mail_real;
                $dcms_mail_real = true;

                $sended= wp_mail($to, $subject, $message, $headers, $attachments);

                $status = $sended ? 1 : -1; // 1 ok , -1 error
                $db->update_email_status($item->id, $status);

                usleep(DCMS_ENQUEU_TIME_BETWEEN_MAILS);
            }
        }
    }
}