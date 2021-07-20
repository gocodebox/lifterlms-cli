<?php
/**
 * LLMS_CLI_Command_Root file.
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */
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

		$slug = empty( $args[0] ) ? 'core' : $args[0];
		if ( in_array( $slug, array( 'core', 'lifterlms' ), true ) ) {
			WP_CLI::log( llms()->version );
		}
	}

}
