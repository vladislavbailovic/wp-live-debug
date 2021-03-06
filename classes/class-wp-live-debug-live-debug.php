<?php

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/**
 * WP_Live_Debug_Live_Debug Class.
 */
if ( ! class_exists( 'WP_Live_Debug_Live_Debug' ) ) {
	class WP_Live_Debug_Live_Debug {

		/**
		 * WP_Live_Debug_Live_Debug constructor.
		 *
		 * @uses WP_Live_Debug_Live_Debug::init()
		 *
		 * @return void
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Plugin initialization.
		 *
		 * @uses add_action()
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'wp_ajax_wp-live-debug-read-log', array( 'WP_Live_Debug_Live_Debug', 'read_debug_log' ) );
			add_action( 'wp_ajax_wp-live-debug-clear-debug-log', array( 'WP_Live_Debug_Live_Debug', 'clear_debug_log' ) );
			add_action( 'wp_ajax_wp-live-debug-create-backup', array( 'WP_Live_Debug_Live_Debug', 'create_wp_config_backup' ) );
			add_action( 'wp_ajax_wp-live-debug-restore-backup', array( 'WP_Live_Debug_Live_Debug', 'restore_wp_config_backup' ) );
			add_action( 'wp_ajax_wp-live-debug-enable', array( 'WP_Live_Debug_Live_Debug', 'enable_wp_debug' ) );
			add_action( 'wp_ajax_wp-live-debug-disable', array( 'WP_Live_Debug_Live_Debug', 'disable_wp_debug' ) );
			add_action( 'wp_ajax_wp-live-debug-enable-script-debug', array( 'WP_Live_Debug_Live_Debug', 'enable_script_debug' ) );
			add_action( 'wp_ajax_wp-live-debug-disable-script-debug', array( 'WP_Live_Debug_Live_Debug', 'disable_script_debug' ) );
			add_action( 'wp_ajax_wp-live-debug-enable-savequeries', array( 'WP_Live_Debug_Live_Debug', 'enable_savequeries' ) );
			add_action( 'wp_ajax_wp-live-debug-disable-savequeries', array( 'WP_Live_Debug_Live_Debug', 'disable_savequeries' ) );
		}

		/**
		 * Create Admin Page
		 *
		 * @uses ini_set()
		 *
		 * @return void
		 */
		public static function create_page() {
			?>
				<div class="sui-box">
					<div class="sui-box-body">
						<textarea id="wp-live-debug-area" class="sui-form-control"></textarea>
					</div>
				</div>
				<div class="sui-box">
					<div class="sui-box-header">
						<h3 class="sui-box-title">Options</h3>
					</div>
					<div class="sui-box-body">
						<div class="sui-row">
							<div class="sui-col-md-4 sui-col-lg-2">
								<?php if ( ! WP_Live_Debug_Live_Debug::check_wp_config_backup() ) { ?>
									<input id="wp-live-debug-backup" type="button" class="sui-button sui-button-green" value="<?php esc_html_e( 'Backup wp-config', 'wp-live-debug' ); ?>">
								<?php } else { ?>
									<input id="wp-live-debug-restore" type="button" class="sui-button sui-button-primary" value="<?php esc_html_e( 'Restore wp-config', 'wp-live-debug' ); ?>">
								<?php } ?>
							</div>
							<div class="sui-col-md-4 sui-col-lg-2">
									<input id="wp-live-debug-clear" type="button" class="sui-button sui-button" value="<?php esc_html_e( 'Clear debug.log', 'wp-live-debug' ); ?>">
							</div>
							<div class="sui-col-md-4 sui-col-lg-2">
								<span class="sui-tooltip sui-tooltip-top sui-tooltip-constrained" data-tooltip="The WP_DEBUG constant that can be used to trigger the 'debug' mode throughout WordPress. This will enable WP_DEBUG, WP_DEBUG_LOG and disable WP_DEBUG_DISPLAY and display_errors.">
									<label class="sui-toggle">
										<input type="checkbox" id="toggle-wp-debug" <?php echo ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'checked' : ''; ?> >
										<span class="sui-toggle-slider"></span>
									</label>
									<label for="toggle-wp-debug">WP Debug</label>
								</span>
							</div>
							<div class="sui-col-md-4 sui-col-lg-2">
								<span class="sui-tooltip sui-tooltip-top sui-tooltip-constrained" data-tooltip="The SCRIPT_DEBUG constant will force WordPress to use the 'dev' versions of some core CSS and JavaScript files rather than the minified versions that are normally loaded.">
									<label class="sui-toggle">
										<input type="checkbox" id="toggle-script-debug" <?php echo ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'checked' : ''; ?> >
										<span class="sui-toggle-slider"></span>
									</label>
									<label for="toggle-script-debug">Script Debug</label>
								</span>
							</div>
							<div class="sui-col-md-4 sui-col-lg-2">
								<span class=" sui-tooltip sui-tooltip-top sui-tooltip-constrained" data-tooltip="The SAVEQUERIES constant causes each query to be saved in the databse along with how long that query took to execute and what function called it. The array is stored in the global $wpdb->queries.">
									<label class="sui-toggle">
										<input type="checkbox" id="toggle-savequeries" <?php echo ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) ? 'checked' : ''; ?> >
										<span class="sui-toggle-slider"></span>
									</label>
									<label for="toggle-savequeries">Save Queries</label>
								</span>
							</div>
							<div class="sui-col-md-4 sui-col-lg-2">
								<label class="sui-toggle">
									<input type="checkbox" id="toggle-auto-refresh">
									<span class="sui-toggle-slider"></span>
								</label>
								<label for="toggle-auto-refresh">Auto Refresh</label>
							</div>
						</div>
					</div>
					<div class="sui-box-footer">
						<p class="sui-description">
							More information at <a target="_blank" rel="noopener" href="https://codex.wordpress.org/Debugging_in_WordPress">Debugging in WordPress</a>.
						</p>
					</div>
				</div>
			<?php
		}

		/**
		 * Check if wp-config backup exists
		 *
		 * @uses file_exists()
		 *
		 * @return bool true/false depending if the backup exists
		 */
		public static function check_wp_config_backup() {
			$wpconfig_backup = ABSPATH . 'wp-config_wpld_backup.php';

			if ( file_exists( $wpconfig_backup ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Creates a backup of wp-config.php
		 *
		 * @uses file_exists()
		 * @uses copy()
		 *
		 * @return string $output Success/Fail
		 */
		public static function create_wp_config_backup() {
			$wpconfig        = ABSPATH . 'wp-config.php';
			$wpconfig_backup = ABSPATH . 'wp-config_wpld_backup.php';

			if ( ! copy( $wpconfig, $wpconfig_backup ) ) {
				$response = array(
					'message' => esc_html__( 'wp-config.php backup failed.', 'wp-live-debug' ),
				);
				wp_send_json_error( $response );
			}

			$response = array(
				'message' => esc_html__( 'wp-config.php backup was created.', 'wp-live-debug' ),
			);

			wp_send_json_success( $response );
		}

		/**
		 * Restores a backup of wp-config.php
		 *
		 * @uses copy()
		 * @uses unlink()
		 *
		 * @return string $output Success/Fail
		 */
		public static function restore_wp_config_backup() {
			$wpconfig        = ABSPATH . 'wp-config.php';
			$wpconfig_backup = ABSPATH . 'wp-config_wpld_backup.php';

			if ( ! copy( $wpconfig_backup, $wpconfig ) ) {
				$response = array(
					'message' => esc_html__( 'wp-config.php restore failed.', 'wp-live-debug' ),
				);
				wp_send_json_error( $response );
			}

			$response = array(
				'message' => esc_html__( 'wp-config.php backup was restored.', 'wp-live-debug' ),
			);

			unlink( $wpconfig_backup );

			wp_send_json_success( $response );
		}

		/**
		 * Enables WP_DEBUG
		 *
		 * @uses copy()
		 * @uses file()
		 * @uses file_put_contents()
		 * @uses fopen()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function enable_wp_debug() {
			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'WP_DEBUG'" ) || false !== strpos( $line, '"WP_DEBUG"' ) ) {
					$line  = "define( 'WP_DEBUG', true ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'WP_DEBUG', true ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

			$response = array(
				'message' => esc_html__( 'WP_DEBUG was enabled.', 'wp-live-debug' ),
			);

			WP_Live_Debug_Live_Debug::enable_wp_debug_log();
			WP_Live_Debug_Live_Debug::disable_wp_debug_display();
			WP_Live_Debug_Live_Debug::disable_wp_debug_ini_set_display();

			wp_send_json_success( $response );

		}

		/**
		 * Disables WP_DEBUG
		 *
		 * @uses fopen()
		 * @uses copy()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_wp_debug() {
			$wpconfig = ABSPATH . 'wp-config.php';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'WP_DEBUG'" ) || false !== strpos( $line, '"WP_DEBUG"' ) ) {
					$line = "define( 'WP_DEBUG', false ); // Added by WP Live Debug" . PHP_EOL;
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			$response = array(
				'message' => esc_html__( 'WP_DEBUG was disabled.', 'wp-live-debug' ),
			);

			WP_Live_Debug_Live_Debug::disable_wp_debug_log();
			WP_Live_Debug_Live_Debug::disable_wp_debug_display();
			WP_Live_Debug_Live_Debug::disable_wp_debug_ini_set_display();

			wp_send_json_success( $response );

		}

		/**
		 * Enables WP_DEBUG_LOG
		 *
		 * @uses copy()
		 * @uses file()
		 * @uses file_put_contents()
		 * @uses fopen()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function enable_wp_debug_log() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'WP_DEBUG_LOG'" ) || false !== strpos( $line, '"WP_DEBUG_LOG"' ) ) {
					$line  = "define( 'WP_DEBUG_LOG', true ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'WP_DEBUG_LOG', true ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

		}

		/**
		 * Disables WP_DEBUG_LOG
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses copy()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_wp_debug_log() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'WP_DEBUG_LOG'" ) || false !== strpos( $line, '"WP_DEBUG_LOG"' ) ) {
					$line  = "define( 'WP_DEBUG_LOG', false ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'WP_DEBUG_LOG', false ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

		}

		/**
		 * Disables WP_DEBUG_DISPLAY
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses copy()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_wp_debug_display() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'WP_DEBUG_DISPLAY'" ) || false !== strpos( $line, '"WP_DEBUG_DISPLAY"' ) ) {
					$line  = "define( 'WP_DEBUG_DISPLAY', false ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'WP_DEBUG_DISPLAY', false ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

		}

		/**
		 * Disables ini_set display_errors
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses copy()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_wp_debug_ini_set_display() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'display_errors'" ) || false !== strpos( $line, '"display_errors"' ) ) {
					$line  = "@ini_set( 'display_errors', 0 ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "@ini_set( 'display_errors', 0 ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

		}

		/**
		 * Enables SCRIPT_DEBUG
		 *
		 * @uses copy()
		 * @uses file()
		 * @uses file_put_contents()
		 * @uses fopen()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function enable_script_debug() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'SCRIPT_DEBUG'" ) || false !== strpos( $line, '"SCRIPT_DEBUG"' ) ) {
					$line  = "define( 'SCRIPT_DEBUG', true ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'SCRIPT_DEBUG', true ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

			$response = array(
				'message' => esc_html__( 'SCRIPT_DEBUG was enabled.', 'wp-live-debug' ),
			);

			wp_send_json_success( $response );

		}

		/**
		 * Disables SCRIPT_DEBUG
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses copy()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_script_debug() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'SCRIPT_DEBUG'" ) || false !== strpos( $line, '"SCRIPT_DEBUG"' ) ) {
					$line  = "define( 'SCRIPT_DEBUG', false ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'SCRIPT_DEBUG', false ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

			$response = array(
				'message' => esc_html__( 'SCRIPT_DEBUG was disabled.', 'wp-live-debug' ),
			);

			wp_send_json_success( $response );

		}

		/**
		 * Enables SAVEQUERIES
		 *
		 * @uses copy()
		 * @uses file()
		 * @uses file_put_contents()
		 * @uses fopen()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function enable_savequeries() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'SAVEQUERIES'" ) || false !== strpos( $line, '"SAVEQUERIES"' ) ) {
					$line  = "define( 'SAVEQUERIES', true ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'SAVEQUERIES', true ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

			$response = array(
				'message' => esc_html__( 'SAVEQUERIES was enabled.', 'wp-live-debug' ),
			);

			wp_send_json_success( $response );

		}

		/**
		 * Disables SAVEQUERIES
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses copy()
		 * @uses strpos()
		 * @uses fputs()
		 * @uses fclose()
		 * @uses wp_send_json_error()
		 * @uses wp_send_json_succes()
		 *
		 * @return void
		 */
		public static function disable_savequeries() {

			$wpconfig = ABSPATH . 'wp-config.php';

			$found = 'no';

			$editing_wpconfig = file( $wpconfig );

			file_put_contents( $wpconfig, '' );

			$write_wpconfig = fopen( $wpconfig, 'w' );

			foreach ( $editing_wpconfig as $line ) {
				if ( false !== strpos( $line, "'SAVEQUERIES'" ) || false !== strpos( $line, '"SAVEQUERIES"' ) ) {
					$line  = "define( 'SAVEQUERIES', false ); // Added by WP Live Debug" . PHP_EOL;
					$found = 'yes';
				}
				fputs( $write_wpconfig, $line );
			}

			fclose( $write_wpconfig );

			if ( 'no' === $found ) {

				$editing_wpconfig = file( $wpconfig );

				file_put_contents( $wpconfig, '' );

				$write_wpconfig = fopen( $wpconfig, 'w' );

				foreach ( $editing_wpconfig as $line ) {
					if ( false !== strpos( $line, 'stop editing!' ) ) {
						$line  = "define( 'SAVEQUERIES', false ); // Added by WP Live Debug" . PHP_EOL;
						$line .= "/* That's all, stop editing! Happy blogging. */" . PHP_EOL;
					}
					fputs( $write_wpconfig, $line );
				}

				fclose( $write_wpconfig );
			}

			$response = array(
				'message' => esc_html__( 'SAVEQUERIES was disabled.', 'wp-live-debug' ),
			);

			wp_send_json_success( $response );

		}

		/**
		 * Read debug.log contents and return them.
		 *
		 * @uses file_exists()
		 * @uses fopen()
		 * @uses die()
		 * @uses fwrite()
		 * @uses fclose()
		 * @uses WP_CONTENT_DIR
		 * @uses wp_die()
		 *
		 * @return string $debug_contents The content of debug.log
		 */
		public static function read_debug_log() {
			if ( ! file_exists( WP_CONTENT_DIR . '/debug.log' ) ) {
				$fo = fopen( WP_CONTENT_DIR . '/debug.log', 'w' ) or die( 'Cannot create debug.log!' );
				fwrite( $fo, '' );
				fclose( $fo );
			}

			if ( 2000000 > filesize( WP_CONTENT_DIR . '/debug.log' ) ) {
				$debug_contents = file_get_contents( WP_CONTENT_DIR . '/debug.log' );
				if ( empty( $debug_contents ) ) {
					$debug_contents = esc_html__( 'Awesome! debug.log seems to be empty.', 'wp-live-deubg' );
				}
			} else {
				$debug_contents = esc_html__( 'debug.log is over 2 MB. Please open it via FTP -or- click the "CLEAR DEBUG.LOG" button for a fresh start!', 'wp-live-debug' );
			}

			echo $debug_contents;

			wp_die();
		}

		/**
		 * Clear debug.log content.
		 *
		 * @uses file_put_contents()
		 * @uses wp_die()
		 *
		 * @return void
		 */
		public static function clear_debug_log() {
			file_put_contents( WP_CONTENT_DIR . '/debug.log', '' );

			wp_die();
		}
	}

	new WP_Live_Debug_Live_Debug();
}
