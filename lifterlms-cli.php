<?php
/**
 * LifterLMS CLI Plugin
 *
 * @package LifterLMS_CLI/Main
 *
 * @since [version]
 * @version [version]
 *
 * Plugin Name: LifterLMS CLI
 * Plugin URI: https://lifterlms.com/
 * Description: WP CLI feature plugin for the LifterLMS Core.
 * Version: 0.0.1
 * Author: LifterLMS
 * Author URI: https://lifterlms.com/
 * Text Domain: lifterlms
 * Domain Path: /i18n
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires LifterLMS: 5.0
 */

defined( 'ABSPATH' ) || exit;

// Don't load the CLI.
if ( defined( 'LLMS_CLI_DISABLE' ) && LLMS_CLI_DISABLE ) {
	return;
}

// Only load in CLI context.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

// Define Constants.
if ( ! defined( 'LLMS_CLI_PLUGIN_FILE' ) ) {
	define( 'LLMS_CLI_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'LLMS_CLI_PLUGIN_DIR' ) ) {
	define( 'LLMS_CLI_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
}

// Load Plugin.
if ( ! class_exists( 'LifterLMS_CLI' ) ) {

	require_once LLMS_CLI_PLUGIN_DIR . 'class-lifterlms-cli.php';

	/**
	 * Main Plugin Instance
	 *
	 * @since [version]
	 *
	 * @return LLMS_CLI
	 */
	function llms_cli() {
		return LifterLMS_CLI::instance();
	}

}

return llms_cli();
