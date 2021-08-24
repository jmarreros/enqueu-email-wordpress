<?php
/*
Plugin Name: Enqueu Email
Plugin URI: https://decodecms.com
Description: Plugin enqueu email, enqueu emails and send it via cron or by a button to force, save emails in the database
Version: 1.0
Author: Jhon Marreros Guzmán
Author URI: https://decodecms.com
Text Domain: dcms-enqueu-email
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\enqueu;

use dcms\enqueu\includes\Database;
use dcms\enqueu\includes\Plugin;
use dcms\enqueu\includes\Submenu;
use dcms\enqueu\includes\Settings;
use dcms\enqueu\includes\Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $dcms_mail_real;

/**
 * Plugin class to handle settings constants and loading files
**/
final class Loader{

	// Define all the constants we need
	public function define_constants(){
		define ('DCMS_ENQUEU_VERSION', '1.0');
		define ('DCMS_ENQUEU_PATH', plugin_dir_path( __FILE__ ));
		define ('DCMS_ENQUEU_URL', plugin_dir_url( __FILE__ ));
		define ('DCMS_ENQUEU_BASE_NAME', plugin_basename( __FILE__ ));
		define ('DCMS_ENQUEU_SUBMENU', 'tools.php');
		define ('DCMS_ENQUEU_OPTIONS', 'dcms_enqueu_options');
	}

	// Load all the files we need
	public function load_includes(){
		include_once ( DCMS_ENQUEU_PATH . '/helpers/helper.php');
		include_once ( DCMS_ENQUEU_PATH . '/includes/database.php');
		include_once ( DCMS_ENQUEU_PATH . '/includes/plugin.php');
		include_once ( DCMS_ENQUEU_PATH . '/includes/submenu.php');
		include_once ( DCMS_ENQUEU_PATH . '/includes/settings.php');
		include_once ( DCMS_ENQUEU_PATH . '/includes/process.php');
	}

	// Load tex domain
	public function load_domain(){
		add_action('plugins_loaded', function(){
			$path_languages = dirname(DCMS_ENQUEU_BASE_NAME).'/languages/';
			load_plugin_textdomain('dcms-enqueu-email', false, $path_languages );
		});
	}

	// Add link to plugin list
	public function add_link_plugin(){
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ){
			return array_merge( array(
				'<a href="' . esc_url( admin_url( DCMS_SUBMENU . '?page=enqueu-email' ) ) . '">' . __( 'Settings', 'dcms-enqueu-email' ) . '</a>'
			), $links );
		} );
	}

	// Initialize all
	public function init(){
		$this->define_constants();
		$this->load_includes();
		$this->load_domain();
		$this->add_link_plugin();
		new Plugin();
		new SubMenu();
		new Settings();
		new Process();
	}

}

$dcms_enqueu_process = new Loader();
$dcms_enqueu_process->init();


