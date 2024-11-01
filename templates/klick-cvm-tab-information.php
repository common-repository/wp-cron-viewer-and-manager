<!-- First Tab content -->
<div id="klick_cvm_tab_first">
		<div class="klick-notice-message"></div>
	    <hr/>
	    <div class="klick-cvm-form-wrapper"> <!-- Form wrapper starts -->
		<?php 

		/**
		 * Fetch all cron jobs using wp function
		 *
		 * @return Array    @events object of array
		 */
		function get_cron_events() {
			$crons  = _get_cron_array();
			$events = array();
			if ( empty( $crons ) ) {
				return new WP_Error(
					'no_events',
					__( 'You currently have no scheduled cron events.', 'wp-crontrol' )
				);
			}

			foreach ( $crons as $time => $cron ) {
				foreach ( $cron as $hook => $dings ) {
					foreach ( $dings as $sig => $data ) {
						$events[ "$hook-$sig-$time" ] = (object) array(
							'hook'     => $hook,
							'time'     => $time,
							'sig'      => $sig,
							'args'     => $data['args'],
							'schedule' => $data['schedule'],
							'interval' => isset( $data['interval'] ) ? $data['interval'] : null,
						);

					}
				}
			}

			return $events;
		}

		$events = get_cron_events(); 
		$time_format = 'Y-m-d H:i:s';
		echo "<table class='wp-list-table widefat fixed striped pages cron-table'>";
		echo "<tr>";
				echo "<th><b>Cron</b></th>";
				echo "<th><b>Time</b></th>";
				echo "<th><b>Schedule</b></th>";
		echo "</tr>";
		if ( is_wp_error( $events ) ) {
			echo "Event list is empty or error";
		} else {
			foreach ($events as $id => $event ) {
				echo "<tr>";
				echo "<td>" . esc_html($event->hook) . "</td>";
				echo "<td>" . esc_html( get_date_from_gmt( date( 'Y-m-d H:i:s', $event->time ), $time_format ) ) . "</td>";
				echo "<td>" . esc_html($event->schedule) . "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		?>

	    </div> <!-- Form wrapper ends -->
</div>

<script type="text/javascript">
    var klick_cvm_ajax_nonce='<?php echo wp_create_nonce('klick_cvm_ajax_nonce'); ?>';
</script>
