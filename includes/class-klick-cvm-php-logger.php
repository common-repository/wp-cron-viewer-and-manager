<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cvm_PHP_Logger')) return;

/**
 * Class Klick_Cvm_PHP_Logger
 */
class Klick_Cvm_PHP_Logger extends Klick_Cvm_Abstract_Logger {

	public $id = "php";

	public $additiona_params = array();
	
	/**
	 * Klick_Cvm_PHP_Logger constructor
	 */
	public function __construct() {
	}

	/**
	 * Returns logger description
	 *
	 * @return string|void
	 */
	public function get_description() {
		return __('Log events into the PHP error log', 'klick-cvm');
	}
	
	/**
	 * Log message with any level
	 *
	 * @param  mixed  $level
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function log($level, $message) {

		if (!$this->is_enabled()) return false;
		
		$message = 'From php[' . $level . '] : ' . $message;
		error_log($message);
	}
}
