<?php
/*
 * @package WP Live Debug
 * @version 1.0.0
 *
 * Plugin Name:       WP Live Debug
 * Plugin URI:        https://xkon.gr/wp-live-debug/
 * Description:       Enables debugging and adds a screen into the Admin to view the debug.log.
 * Version:           1.0.0
 * Author:            Xenos (xkon) Konstantinos
 * Author URI:        https://xkon.gr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-live-debug
 * Domain Path:       /languages
 *
*/

function wp_live_debug_admin_menu() {
	add_menu_page(
		'WP Live Debug',
		'WP Live Debug',
		'manage_options',
		'wp-live-debug',
		'wp_live_debug_page',
		'dashicons-media-code',
		'80'
	);
}// end bloginfotest_admin_menu
add_action( 'admin_menu', 'wp_live_debug_admin_menu' );

function wp_live_debug_page() {

	// Start the debug process
	ini_set( 'error_reporting', E_ALL );
	ini_set( 'log_errors', 1 );
	ini_set( 'display_errors', 1 );
	ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );

	// Start the page output
	?>
	<h1>WP Live Debug</h1>
	<textarea id="wp-live-debug-area" style="width:calc( 100% - 40px );height:calc( 100vh - 300px );"></textarea>
	<p class="wp-live-debug-buttons">
		<input type="submit" id="wp-live-debug-start-stop" class="button button-primary" value="Pause Refresh" />
		<input type="submit" id="wp-live-debug-clear-log" class="button button-primary" value="Clear Log" />
		<input type="hidden" id="wp-live-debug-scroll" value="yes" />
	</p>
	<script>
		(function($){

			var doScroll = $('#wp-live-debug-scroll');
			var debugArea = $('#wp-live-debug-area');
			var debugStartStop = $('#wp-live-debug-start-stop');

			$.get('<?php echo get_template_directory_uri () . '/../../debug.log'; ?>', function(data) {
				// Process debug.log line by line
				debugArea.html(data.replace('n',''));
				scrollDebugAreaToBottom();
			});
			debugStartStop.on('click', function(){
				if ( doScroll.val() === 'yes' ) {
					doScroll.val('no');
				} else {
					doScroll.val('yes');
				}
			});

			setInterval(function(){
				if ( doScroll.val() === 'yes' ) {
					$.get('<?php echo get_template_directory_uri () . '/../../debug.log'; ?>', function(data) {
						// Process debug.log line by line
						debugArea.html(data.replace('n',''));
						scrollDebugAreaToBottom();
					});
				}
			}, 3000);

			function scrollDebugAreaToBottom() {
				debugArea.scrollTop(debugArea[0].scrollHeight);
			}

		})(jQuery)
	</script>
	<?php
}// end wp_live_debug_page
