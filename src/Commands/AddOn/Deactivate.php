<?php
/**
 * AddOn Deactivate class file
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands\AddOn;

// defined( 'ABSPATH' ) || exit;

/**
 * AddOn Activation and deactivation commands
 *
 * @since [version]
 */
trait Deactivate {

	/**
	 * Deactivate one or more plugin add-ons.
	 *
	 * ## OPTIONS
	 *
	 * [<slug>...]
	 * : The slug of one or more add-on to deactivate.
	 *
	 * [--uninstall]
	 * : Uninstall the add-ons after deactivation.
	 *
	 * [--all]
	 * : If set, all of the plugin add-ons installed on the site will be activated.
	 *
	 * @since [version]
	 *
 	 * @param array $args       Indexed array of positional command arguments.
 	 * @param array $assoc_args Associative array of command options.
 	 * @return null
	 */
	public function deactivate( $args, $assoc_args ) {

		if ( ! empty( $assoc_args['all'] ) ) {
			$args = $this->get_available_addons( 'active', false, 'plugin' );
			if ( empty( $args ) ) {
				return \WP_CLI::warning( 'No add-ons to activate.' );
			}
		}

		$results = $this->loop( $args, $assoc_args, 'deactivate_one' );
		if ( ! $this->chaining ) {
			\WP_CLI\Utils\report_batch_operation_results( 'add-on', 'activate', count( $args ), $results['successes'], $results['errors'] );
		}

	}

	/**
	 * Loop callback function for deactivate()
	 *
	 * Ensures add-on can be deactivated and actually deactivates (and maybe uninstalls) the add-on.
	 *
	 * @since [version]
	 *
	 * @param string      $slug       Add-on slug.
	 * @param LLMS_Add_On $addon      Add-on object.
 	 * @param array       $assoc_args Associative array of command options.
	 * @return null|true Returns `null` if an error is encountered and `true` on success.
	 */
	private function deactivate_one( $slug, $addon, $assoc_args ) {

		if ( ! $addon->is_installed() ) {
			return \WP_CLI::warning( sprintf( 'Add-on "%1$s" is not installed.', $slug ) );
		}

		if ( ! $addon->is_active() ) {
			return \WP_CLI::warning( sprintf( 'Add-on "%s" is already deactivated.', $slug ) );
		}

		$res = $addon->deactivate();
		if ( is_wp_error( $res ) ) {
			return \WP_CLI::warning( $res );
		}

		if ( ! empty( $assoc_args['uninstall'] ) ) {
			$this->chain_command( 'uninstall', array( $slug ) );
		}

		\WP_CLI::log( $res );

		return true;

	}

}
