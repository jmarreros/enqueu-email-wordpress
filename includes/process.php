<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\helpers\State;


class Process {
	private $email_from;
	private $email_from_name;

	public function __construct() {
		add_action( 'admin_post_process_force_sent', [ $this, 'process_force_sent' ] );
	}

	// Force send mails queue
	public function process_force_sent() {
		$this->process_sent();
		wp_redirect( admin_url(     DCMS_ENQUEU_SUBMENU . '&page=enqueu-email' ) );
	}


	// Filter from and name if exixts
	private function filter_from_sender_mail() {
		if ( $this->email_from ) {
			add_filter( 'wp_mail_from', function () {
				return $this->email_from;
			} );
		}

		if ( $this->email_from_name ) {
			add_filter( 'wp_mail_from_name', function () {
				return $this->email_from_name;
			} );
		}
	}

	public function process_sent() {
		$restrict_mails = [ '@emailtemp.com', '@emailexists.com' ];

		$options          = get_option( DCMS_ENQUEU_OPTIONS );
		$quantity_batch   = $options['dcms_quantity_batch'];
		$this->email_from = $options['dcms_enqueue_from'];
		$this->email_from_name = $options['dcms_enqueue_from_name'];

		$db = new Database();

		$this->filter_from_sender_mail();

		$items = $db->get_pending_emails( $quantity_batch );

		if ( $items ) {
			foreach ( $items as $item ) {
				$atts = json_decode( base64_decode( $item->data ), true );

				$to          = $atts['to'];
				$subject     = $atts['subject'];
				$message     = $atts['message'];
				$headers     = $atts['headers'];
				$attachments = $atts['attachments'];

				// Exclude restricted emails
				$status = State::pending;
				foreach ( $restrict_mails as $restrict_mail ) {
					if ( strpos( $to, $restrict_mail ) !== false ) {
						$status = State::error;
						$db->update_email_status( $item->id, $status );
						break;
					}
				}
				if ( $status === State::error ) {
					continue;
				}

				// Send email
				global $dcms_mail_real;
				$dcms_mail_real = true; // real email sending

				$sended = wp_mail( $to, $subject, $message, $headers, $attachments );

				$status = $sended ? State::sent : State::error; // 1 ok , -1 error
				$db->update_email_status( $item->id, $status );

				usleep( DCMS_ENQUEU_TIME_BETWEEN_MAILS );
			}
		}
	}
}