<?php

defined( 'ABSPATH' ) || die();

/**
 * Manage LifterLMS.
 *
 * @since [version]
 */
class LLMS_CLI_Command_Root extends WP_CLI_Command {

	/**
	 * Display the version of LifterLMS or the specified LifterLMS add-on.
	 * 
	 * [<slug>]
	 * : The slug of the plugin. Default: lifterlms.
	 *
	 * ## OPTIONS
	 *
	 * [--db]
	 * : Display the database version.
	 *
	 * ## EXAMPLES
	 *
	 *     wp llms version
	 *     wp llms version core
	 *     wp llms version groups
	 *     wp llms version lifterlms-assignments
	 *
 	 * @since [version]
	 */
	public function version( $args, $assoc_args ) {

		if ( ! function_exists( 'llms' ) ) {
			WP_CLI::error( 'Gravity Forms is not installed. Use the wp gf install command.' );
		}

		$slug = empty( $args[0] ) ? 'core' : $args[0];
		if ( in_array( $slug, array( 'core', 'lifterlms' ), true ) ) {
			WP_CLI::log( llms()->version );
		// } else {
		// 	$addon = $this->get_addon( $slug );
		// 	WP_CLI::log( $addon->get_version() );
		}
	}

}