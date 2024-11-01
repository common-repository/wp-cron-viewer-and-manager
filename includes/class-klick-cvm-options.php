<?php
if (!defined('KLICK_CVM_VERSION')) die('No direct access allowed');
/**
 * Access via Klick_Cvm()->get_options().
 */
class Klick_Cvm_Options {
	
	/**
	 * Get option from table
	 *
	 * @return string
	 */
	public function get_option($option, $setting = false) {
		return get_option('klick-cvm-' . $option, $setting);
	}
	
	/**
	 * Update option in table
	 *
	 * @param  String 	$option
	 * @param  String 	$asetting
	 *
	 * @returns boolean
	 */
	public function update_option($option, $setting = false) {
		return update_option('klick-cvm-' . $option, $setting);
	}
	
	/**
	 * Delete option from options table
	 *
	 * @param  string
	 * @return void
	 */
	public function delete_option($option) {
		delete_option('klick-cvm-' . $option);
	}
	
	/**
	 * Get option names
	 *
	 * @return array of options names
	 */
	public function get_option_keys() {

		return apply_filters(
			'klick_cvm_option_keys',
			array('logging','notice-display-time')
		);
	}
	
	/**
	 * Delete all options
	 *
	 * @return void
	 */
	public function delete_all_options() {
		$option_keys = $this->get_option_keys();
		foreach ($option_keys as $key) {
			$this->delete_option($key);
		}
	}

	/**
	 * Setup options if not exists already.
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->update_option('notice-display-time', false);
	}

	/**
	 * Save settings
	 *
	 * @param  Array 	$settings an array of data  
	 *
	 * @return Array 	
	 */
	public function save_settings($settings) {
		
		($settings['klick_cvm_toggle'] == "ON" ? $this->update_option('send-email', true) : $this->update_option('send-email', false) );
		
		$return_array['messages'] = $this->show_admin_warning(__("Setting Saved.", "klick-cvm"),'updated fade');
		$return_array['status'] = 1;
		
		if (!empty($settings['klick_cvm_email'])) {
			if (is_email($settings['klick_cvm_email'])) {
				$this->update_option('email', sanitize_email($settings['klick_cvm_email']));
			} else {
				$return_array['messages'] = $this->show_admin_warning(__("Invalid email address.", "klick-cvm"),'updated fade');
				$return_array['status'] = 0;
			}
		}
		
		$return_array['data'] = array('email' =>  sanitize_email ($settings['klick_cvm_email']));
		
		return $return_array;
	}
	
	/**
	 * Update option with time + interval
	 *
	 * @param  String 	$notice_id
	 * 
	 * @return void
	 */
	public function dismiss_page_notice_until($notice_id) {

		$notices = Klick_Cvm()->get_notifier()->get_notices();

		foreach ($notices as $notice) {

		    if ($notice->notice_id == $notice_id) {

		    	if (0 == $notice->dismiss_interval) return;

		    	 $display_notice_time = $this->get_option('notice-display-time');

		    	 $display_notice_time[$notice_id] = time() + $notice->dismiss_interval;
		    	 
		    	 $this->update_option('notice-display-time',$display_notice_time);
		    }
		}
	}

	/**
	 * Update option 0 to dismiss forever
	 *
	 * @param  String 	$notice_id
	 * 
	 * @return void
	 */
	public function dismiss_page_notice_until_forever($notice_id) {

		$notices = Klick_Cvm()->get_notifier()->get_notices();

		foreach ($notices as $notice) {

		    if ($notice->notice_id == $notice_id) {

		    	if (0 == $notice->dismiss_interval) return;

		    	 $display_notice_time = $this->get_option('notice-display-time');

		    	 $display_notice_time[$notice_id] = 0;
		    	 
		    	 $this->update_option('notice-display-time',$display_notice_time);
		    }
		}
	}

	
	/**
	 * Create ajax notice
	 *
	 * @param  String 	$message as a notice
	 * @param  String 	$class an string if many then separated by space defalt is 'updated'
	 *
	 * @return String 	returns message
	 */
	public function show_admin_warning($message, $class = "updated") {
		return  '<div class="klick-ajax-notice ' . $class . '">' . "<p>$message</p></div>";
	}
	
	/**
	 * Returns the admin page url
	 *
	 * @return string
	 */
	public function admin_page_url() {
		return admin_url('admin.php?page=klick-cvm');
	}
}
