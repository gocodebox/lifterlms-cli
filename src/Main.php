<?php
/**
 * LifterLMS CLI Main Class file
 *
 * @package LifterLMS_CLI/Classes
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI;

use WP_CLI\Dispatcher\CommandAddition;

defined( 'ABSPATH' ) || exit;

/**
 * LifterLMS Assignments Main Class
 *
 * @since [version]
 */
final class Main {

	/**
	 * Current version of the plugin
	 *
	 * @var string
	 */
	public $version = '0.0.1';

	/**
	 * Singleton instance of the class
	 *
	 * @var LifterLMS_CLI
	 */
	private static $instance = null;

	/**
	 * Singleton Instance of the LifterLMS_CLI class
	 *
	 * @since [version]
	 * 
	 * @return LifterLMS_CLI
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor
	 *
	 * @since [version]
	 * 
	 * @return void
	 */
	private function __construct() {

		if ( ! defined( 'LLMS_CLI_VERSION' ) ) {
			define( 'LLMS_CLI_VERSION', $this->version );
		}

		add_action( 'init', array( $this, 'load_textdomain' ), 0 );

		// Get started (after REST).
		add_action( 'plugins_loaded', array( $this, 'init' ) );

	}

	public function commands() {
		require_once LLMS_CLI_PLUGIN_DIR . 'src/commands.php';
	}

	private function hooks() {

		\WP_CLI::add_hook( 'after_wp_load', array( $this, 'commands' ) );
		\WP_CLI::add_hook( 'after_wp_load', 'LifterLMS\CLI\Commands\Restful\Runner::after_wp_load' );

		// If the Helper doesn't exist abort command addition.
		if ( ! class_exists( 'LifterLMS_Helper' ) ) {
			$helper_commands = array(
				'license',
				'addon install',
				'addon uninstall',
				'addon activate',
				'addon deactivate',
				'addon update',
			);
			foreach ( $helper_commands as $command ) {
				\WP_CLI::add_hook( "before_add_command:llms {$command}", function( CommandAddition $command_addition ) {
					$command_addition->abort( 'The LifterLMS Helper is required to use this command.' );
				} );
			}

		}

	}
	/**
	 * Include all required files and classes
	 *
	 * @since [version
	 *
	 * @return void
	 */
	public function init() {

		// Only load if we have the minimum LifterLMS version installed & activated.
		if ( function_exists( 'llms' ) && version_compare( '5.0.0', llms()->version, '<=' ) ) {

			$this->hooks();

		}

	}

	/**
	 * Load l10n files.
	 *
	 * This method is only used when the plugin is loaded as a standalone plugin (for development purposes),
	 * otherwise (when loaded as a library from within the LifterLMS core plugin) the localization
	 * strings are included into the LifterLMS Core plugin's po/mo files and are localized by the LifterLMS
	 * core plugin.
	 *
	 * Files can be found in the following order (The first loaded file takes priority):
	 *   1. WP_LANG_DIR/lifterlms/lifterlms-cli-LOCALE.mo
	 *   2. WP_LANG_DIR/plugins/lifterlms-cli-LOCALE.mo
	 *   3. WP_CONTENT_DIR/plugins/lifterlms-cli/i18n/lifterlms-cli-LOCALE.mo
	 *
	 * Note: The function `load_plugin_textdomain()` is not used because the same textdomain as the LifterLMS core
	 * is used for this plugin but the file is named `lifterlms-cli` in order to allow using a separate language
	 * file for each codebase.
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function load_textdomain() {

		// load locale.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'lifterlms' );

		// Load from the LifterLMS "safe" directory if it exists.
		load_textdomain( 'lifterlms', WP_LANG_DIR . '/lifterlms/lifterlms-cli-' . $locale . '.mo' );

		// Load from the default plugins language file directory.
		load_textdomain( 'lifterlms', WP_LANG_DIR . '/plugins/lifterlms-cli-' . $locale . '.mo' );

		// Load from the plugin's language file directory.
		load_textdomain( 'lifterlms', LLMS_REST_API_PLUGIN_DIR . '/i18n/lifterlms-cli-' . $locale . '.mo' );

	}

}
