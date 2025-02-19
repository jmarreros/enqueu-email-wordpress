<?php

namespace dcms\enqueu\includes;

use dcms\enqueu\includes\Database;

class Plugin {

	public function __construct() {
		register_activation_hook( DCMS_ENQUEU_BASE_NAME, [ $this, 'dcms_activation_plugin' ] );
		register_deactivation_hook( DCMS_ENQUEU_BASE_NAME, [ $this, 'dcms_deactivation_plugin' ] );
	}

	// Activate plugin - create options and database table
	public function dcms_activation_plugin() {
		$db = new Database();
		$db->create_table_enqueu();

		// // Create cron
		if ( ! wp_next_scheduled( 'dcms_enqueu_hook' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'dcms_enqueu_interval', 'dcms_enqueu_hook' );
		}

		if ( ! wp_next_scheduled( 'dcms_remove_log_hook' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'dcms_remove_log_interval', 'dcms_remove_log_hook' );
		}

		// Get site name and email administrator
		$site_name   = get_bloginfo( 'name' );
		$admin_email = get_option( 'admin_email' );

		// Save by default in DCMS_ENQUEU_OPTIONS
		$options = get_option( DCMS_ENQUEU_OPTIONS );
		if ( ! $options ) {
			$options = [
				'dcms_cron_interval'     => 10,
				'dcms_quantity_batch'    => 100,
				'dcms_remove_log'        => 30,
				'dcms_enqueue_from'      => $admin_email,
				'dcms_enqueue_from_name' => $site_name,
			];
			update_option( DCMS_ENQUEU_OPTIONS, $options );
		}
	}

	// Deactivate plugin
	public function dcms_deactivation_plugin() {
		wp_clear_scheduled_hook( 'dcms_enqueu_hook' );
		wp_clear_scheduled_hook( 'dcms_remove_log_hook' );
	}

}
