<?php
/**
 * License command file
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands;

use WP_CLI\Formatter;

// defined( 'ABSPATH' ) || exit;

/**
 * Manage LifterLMS License Keys
 *
 * @since [version]
 */
class License extends AbstractCommand {

	/**
	 * Activate a license key.
	 *
	 * [<key>]
	 * : The license key to be activated.
	 *
 	 * @since [version]
 	 *
 	 * @param array $args Indexed array of positional command arguments.
 	 * @return null
	 */
	public function activate( $args ) {

		$res = \LLMS_Helper_Keys::activate_keys( $args[0] );
		if ( ! empty( $res['data']['errors'] ) ) {
			return \WP_CLI::error( $res['data']['errors'][0] );
		} elseif ( ! empty( $res['data']['activations'] ) ) {
			\LLMS_Helper_Keys::add_license_key( $res['data']['activations'][0] );
			return \WP_CLI::success( sprintf( 'License key "%s" has been activated on this site.', $args[0] ) );
		}

		return \WP_CLI::error( 'An unknown error was encountered.' );

	}

	/**
	 * Deactivate a license key.
	 *
	 * [<key>]
	 * : The license key to be deactivated.
	 *
 	 * @since [version]
 	 *
 	 * @param array $args Indexed array of positional command arguments.
 	 * @return null
	 */
	public function deactivate( $args ) {

		$res = \LLMS_Helper_Keys::deactivate_keys( array( $args[0] ) );
		if ( ! empty( $res['data']['errors'] ) ) {
			return \WP_CLI::error( $res['data']['errors'][0] );
		} elseif ( ! empty( $res['data']['deactivations'] ) ) {
			\LLMS_Helper_Keys::remove_license_key( $args[0] );
			return \WP_CLI::success( sprintf( 'License key "%s" has been deactivated from this site.', $args[0] ) );
		} elseif ( ! empty( $res['data']['status'] ) && 200 == $res['data']['status'] ) {
			return \WP_CLI::error(  sprintf( 'License key "%s" was not active on this site.', $args[0] ) );
		}

		return \WP_CLI::error( 'An unknown error was encountered.' );

	}

	/**
	 * List activated license keys.
	 *
	 * [<key>]
	 * : The license key to be deactivated.
	 *
 	 * @since [version]
 	 *
 	 * @return null
	 */
	public function list() {

		$list = array_keys( llms_helper_options()->get_license_keys() );

		if ( 0 === count( $list ) ) {
			return \WP_CLI::warning( 'No license keys found on this site.' );
		}

		foreach ( $list as $key ) {
			\WP_CLI::log( $key );
		}

	}

}