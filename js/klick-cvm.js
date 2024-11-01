/**
 * Send an action via admin-ajax.php
 * 
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 */
var klick_cvm_send_command = function (action, data, callback, json_parse) {
	json_parse = ('undefined' === typeof json_parse) ? true : json_parse;
	var ajax_data = {
		action: 'klick_cvm_ajax',
		subaction: action,
		nonce: klick_cvm_ajax_nonce,
		data: data
	};
	jQuery.post(ajaxurl, ajax_data, function (response) {
		
		if (json_parse) {
			try {
				var resp = JSON.parse(response);
			} catch (e) {
				console.log(e);
				console.log(response);
				return;
			}
		} else {
			var resp = response;
		}
		
		if ('undefined' !== typeof callback) callback(resp);
	});
}

/**
 * When DOM ready
 * 
 */
jQuery(document).ready(function ($) {
	klick_Cvm = klick_Cvm(klick_cvm_send_command);
});

/**
 * Function for sending communications
 * 
 * @callable sendcommandCallable
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 */
/**
 * Main klick_Cvm
 * 
 * @param {sendcommandCallable} klick_cvm_send_command
 */
var klick_Cvm = function (klick_cvm_send_command) {
	var $ = jQuery;

	

	/**
	 * Gathers the details from form
	 * 
	 * @returns (string) - serialized row data
	 */
	function gather_row(){
		// Gatyher data to send through ajax request as form_data
	}

	// Send 'klick_Template_save_settings' command, Response handler
	$("#klick_any_button").click(function() {
		klick_cvm_send_command('do_any_stuff', form_data, function (resp) {
		});	
	});
	/**
	 * Proceses the tab click handler
	 *
	 * @return void
	 */
	$('#klick_cvm_nav_tab_wrapper .nav-tab').click(function (e) {
		e.preventDefault();
		
		var clicked_tab_id = $(this).attr('id');
		if (!clicked_tab_id) { return; }
		if ('klick_cvm_nav_tab_' != clicked_tab_id.substring(0, 18)) { return; }
		
		var clicked_tab_id = clicked_tab_id.substring(18);

		$('#klick_cvm_nav_tab_wrapper .nav-tab:not(#klick_cvm_nav_tab_' + clicked_tab_id + ')').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		$('.klick-cvm-nav-tab-contents:not(#klick_cvm_nav_tab_contents_' + clicked_tab_id + ')').hide();
		$('#klick_cvm_nav_tab_contents_' + clicked_tab_id).show();
	});
}
