<?php

/**
Plugin Name: WP Cron Viewer and Manager
Description: WP plugin to send mail or track access on your site.
Version: 0.0.3
Author: klick on it
Author URI: http://klick-on-it.com
License: GPLv2 or later
Text Domain: klick-cvm
 */

/*
This plugin developed by klick-on-it.com
*/

/*
Copyright 2017 klick on it (http://klick-on-it.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 3 - GPLv3)
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('Klick_Cvm')) :
define('KLICK_CVM_VERSION', '0.0.1');
define('KLICK_CVM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KLICK_CVM_PLUGIN_MAIN_PATH', plugin_dir_path(__FILE__));
define('KLICK_CVM_PLUGIN_SETTING_PAGE', admin_url() . 'admin.php?page=klick_cvm');

class Klick_Cvm {

	protected static $_instance = null;

	protected static $_options_instance = null;

	protected static $_notifier_instance = null;

	protected static $_logger_instance = null;

	protected static $_dashboard_instance = null;
	
	/**
	 * Constructor for main plugin class
	 */
	public function __construct() {
		
		register_activation_hook(__FILE__, array($this, 'klick_cvm_activation_actions'));

		register_deactivation_hook(__FILE__, array($this, 'klick_cvm_deactivation_actions'));

		add_action('wp_ajax_klick_cvm_ajax', array($this, 'klick_cvm_ajax_handler'));
		
		add_action('admin_menu', array($this, 'init_dashboard'));
		
		add_action('plugins_loaded', array($this, 'setup_translation'));
		
		add_action('plugins_loaded', array($this, 'setup_loggers'));

	}

	/**
	 * Instantiate Klick_Cvm if needed
	 *
	 * @return object Klick_Cvm
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Instantiate Klick_Cvm_Options if needed
	 *
	 * @return object Klick_Cvm_Options
	 */
	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('Klick_Cvm_Options')) include_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-options.php');
			self::$_options_instance = new Klick_Cvm_Options();
		}
		return self::$_options_instance;
	}
	
	/**
	 * Instantiate Klick_Cvm_Dashboard if needed
	 *
	 * @return object Klick_Cvm_Dashboard
	 */
	public static function get_dashboard() {
		if (empty(self::$_dashboard_instance)) {
			if (!class_exists('Klick_Cvm_Dashboard')) include_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-dashboard.php');
			self::$_dashboard_instance = new Klick_Cvm_Dashboard();
		}
		return self::$_dashboard_instance;
	}
	
	/**
	 * Instantiate Klick_Cvm_Logger if needed
	 *
	 * @return object Klick_Cvm_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			if (!class_exists('Klick_Cvm_Logger')) include_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-logger.php');
			self::$_logger_instance = new Klick_Cvm_Logger();
		}
		return self::$_logger_instance;
	}
	
	/**
	 * Instantiate Klick_Cvm_Notifier if needed
	 *
	 * @return object Klick_Cvm_Notifier
	 */
	public static function get_notifier() {
		if (empty(self::$_notifier_instance)) {
			include_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-notifier.php');
			self::$_notifier_instance = new Klick_Cvm_Notifier();
		}
		return self::$_notifier_instance;
	}
	
	/**
	 * Establish Capibility
	 *
	 * @return string
	 */
	public function capability_required() {
		return apply_filters('klick_cvm_capability_required', 'manage_options');
	}
	
	/**
	 * Init dashboard with menu and layout
	 *
	 * @return void
	 */
	public function init_dashboard() {
		$dashboard = $this->get_dashboard();
		$dashboard->init_menu();
		load_plugin_textdomain('klick-cvm', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Perform post plugin loaded setup
	 *
	 * @return void
	 */
	public function setup_translation() {
		load_plugin_textdomain('klick-cvm', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Creates an array of loggers, Activate and Adds
	 *
	 * @return void
	 */
	public function setup_loggers() {
		
		$logger = $this->get_logger();

		$loggers = $logger->klick_cvm_get_loggers();
		
		$logger->activate_logs($loggers);
		
		$logger->add_loggers($loggers);
	}
	
	/**
	 * Ajax Handler
	 */
	public function klick_cvm_ajax_handler() {

		$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'klick_cvm_ajax_nonce') || empty($_POST['subaction'])) die('Security check');

		$subaction = sanitize_key($_POST['subaction']);
		$data = isset($_POST['data']) ? sanitize_text_field($_POST['data']) : null;
		$results = array();
		
		// Get sub-action class
		if (!class_exists('Klick_Cvm_Commands')) include_once(KLICK_CVM_PLUGIN_MAIN_PATH . 'includes/class-klick-cvm-commands.php');

		$commands = new Klick_Cvm_Commands();

		if (!method_exists($commands, $subaction)) {
			error_log("Klick-Cvm-Commands: ajax_handler: no such sub-action (" . esc_html($subaction) . ")");
			die('No such sub-action/command');
		} else {
			$results = call_user_func(array($commands, $subaction), $data);

			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
					);
			}
		}
		
		echo json_encode($results);
		die;
	}


	
	/**
	 * Plugin activation actions.
	 *
	 * @return void
	 */
	public function klick_cvm_activation_actions(){
		$this->get_options()->set_default_options();
	}

	/**
	 * Plugin deactivation actions.
	 *
	 * @return void
	 */
	public function klick_cvm_deactivation_actions(){
		$this->get_options()->delete_all_options();
	}
}

register_uninstall_hook(__FILE__,'klick_cvm_uninstall_option');

/**
 * Delete data when uninstall
 *
 * @return void
 */
function klick_cvm_uninstall_option(){
	Klick_Cvm()->get_options()->delete_all_options();
}

/**
 * Instantiates the main plugin class
 *
 * @return instance
 */
function Klick_Cvm(){
     return Klick_Cvm::instance();
}

endif;

$GLOBALS['Klick_Cvm'] = Klick_Cvm();
