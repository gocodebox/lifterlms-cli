<?php
/**
 * LLMS_CLI_Command_Root file.
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands;

// defined( 'ABSPATH' ) || exit;

/**
 * Manage LifterLMS.
 *
 * @since [version]
 */
class Root extends AbstractCommand {

	/**
	 * Display the version of LifterLMS or the specified LifterLMS add-on.
	 *
	 * [<slug>]
	 * : The slug of the LifterLMS plugin or theme. Default: lifterlms.
	 *
	 * ## OPTIONS
	 *
	 * [--db]
	 * : Display the database version.
	 *
	 * ## EXAMPLES
	 *
	 *     # Show the LifterLMS core plugin version
	 *     wp llms version
	 *
	 *     # Show the LifterLMS core plugin version
	 *     wp llms version core
	 *
	 *     # Show an add-on version without the "lifterlms-" prefix.
	 *     wp llms version groups
	 *
	 *     # Show an add-on version with the "lifterlms-" prefix.
	 *     wp llms version lifterlms-assignments
	 *
	 * @since [version]
	 *
	 * @param array $args       Indexed array of positional command arguments.
	 * @param array $assoc_args Associative array of command options.
	 * @return null
	 */
	public function version( $args, $assoc_args ) {

		// @todo Implement --db option.
		if ( ! empty( $assoc_args['db'] ) ) {
			return \WP_CLI::error( 'Not implemented.' );
		}

		$slug = empty( $args[0] ) ? 'core' : $args[0];
		if ( in_array( $slug, array( 'core', 'lifterlms' ), true ) ) {
			return \WP_CLI::log( llms()->version );
		}

		$addon = $this->get_addon( $slug );
		if ( empty( $addon ) ) {
			return \WP_CLI::error( 'Invalid slug.' );
		}

		if ( $addon->is_installed() ) {
			return \WP_CLI::log( $addon->get_installed_version() );
		}

		return \WP_CLI::error(
			sprintf(
				"The requested add-on is not installed. Run 'wp llms addon install %s.' to install it.",
				$args[0],
			)
		);

	}

}
