<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cvm_Rate_Us')) return;

require_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-abstract-notice.php');

/**
 * Class Klick_Cvm_Rate_Us
 */
class Klick_Cvm_Rate_Us extends Klick_Cvm_Abstract_Notice {

	/**
	 * Klick_Cvm_Rate_Us constructor
	 */
	public function __construct() {
		$this->notice_id = 'givemerate';
		$this->title = __('Please Rate WP Cron Viewer and Manager', 'klick-cvm');
		$this->klick_cvm = "";
		$this->notice_text = __('If you could spare just a few minutes it would help us alot - thanks', 'klick-cvm');
		$this->image_url = '../images/our-more-plugins/CVM.svg';
		$this->dismiss_time = 'dismiss-page-notice-until';
		$this->dismiss_interval = 30;
		$this->display_after_time = 0;
		$this->dismiss_type = 'dismiss forever';
		$this->dismiss_text= __('I have already rated', 'klick-cvm');
		$this->position = 'top';
		$this->only_on_this_page = '';
		$this->button_link = 'https://wordpress.org/support/plugin/klick-cvm-logger/reviews/?rate=5#new-post';
		$this->button_text = __('Click Here', 'klick-cvm');
		$this->notice_template_file = 'horizontal-notice.php';
		$this->validity_function_param = '';
		$this->validity_function = '';
	}
}

