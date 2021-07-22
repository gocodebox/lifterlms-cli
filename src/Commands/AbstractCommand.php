<?php
/**
 * LLMS_CLI_Abstract_Command file.
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands;

defined( 'ABSPATH' ) || exit;

/**
 * Base CLI command for use by LifterLMS CLI commands
 *
 * @since [version]
 */
abstract class AbstractCommand extends \WP_CLI_Command {

	/**
	 * Retrieve an LLMS_Add_On object for a given add-on by it's slug.
	 *
	 * @since [version]
	 *
	 * @param string $slug An add-on slug. Must be prefixed.
	 * @return LLMS_Add_On|boolean Returns an add-on object if the add-on can be located or `false` if not found.
	 */
	protected function get_addon( $slug ) {
		$addon = llms_get_add_on( $this->prefix_slug( $slug ), 'slug' );
		return empty( $addon->get( 'id' ) ) ? false : $addon;
	}

	/**
	 * Prefix an add-on slug with `lifterlms-` if it's not already present.
	 *
	 * @since [version]
	 *
	 * @param string $slug Add-on slug.
	 * @return string
	 */
	protected function prefix_slug( $slug ) {
		if ( 0 !== strpos( $slug, 'lifterlms-' ) ) {
			$slug = "lifterlms-{$slug}";
		}
		return $slug;
	}

}
