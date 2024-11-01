<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cvm_No_Config')) return;

require_once(KLICK_CVM_PLUGIN_MAIN_PATH . '/includes/class-klick-cvm-abstract-notice.php');

/**
 * Class Klick_Cvm_No_Config
 */
class Klick_Cvm_No_Config extends Klick_Cvm_Abstract_Notice {
	
	/**
	 * Klick_Cvm_No_Config constructor
	 */
	public function __construct() {
		$this->notice_id = 'WP-Cron-Viewer-and-Manager-configure';
		$this->title = __('WP Cron Viewer and Manager plugin is installed but not configured', 'klick-cvm');
		$this->klick_cvm = "";
		$this->notice_text = __('Configure it Now', 'klick-cvm');
		$this->image_url = '../images/our-more-plugins/CVM.svg';
		$this->dismiss_time = 'dismiss-page-notice-until';
		$this->dismiss_interval = 30;
		$this->display_after_time = 0;
		$this->dismiss_type = 'dismiss';
		$this->dismiss_text = __('Hide Me!', 'klick-cvm');
		$this->position = 'dashboard';
		$this->only_on_this_page = 'index.php';
		$this->button_link = KLICK_CVM_PLUGIN_SETTING_PAGE;
		$this->button_text = __('Click here', 'klick-cvm');
		$this->notice_template_file = 'main-dashboard-notices.php';
		$this->validity_function_param = 'wp-cron-viewer-and-manager/wp-cron-viewer-and-manager.php';
		$this->validity_function = 'is_plugin_configured';
	}
}
