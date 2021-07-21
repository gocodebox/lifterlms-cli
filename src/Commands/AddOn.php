<?php
/**
 * LLMS_CLI_Command_Add_On file.
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands;

use WP_CLI\Formatter;

defined( 'ABSPATH' ) || exit;

/**
 * Manage LifterLMS add-on plugins and themes.
 *
 * @since [version]
 */
class AddOn extends AbstractCommand {

	/**
	 * Accepts an add-on array and converts it to the format used by the output method
	 *
	 * @since [version]
	 *
	 * @param array $item LifterLMS Add-on item array from `llms_get_add_ons()`.
	 * @return array Associative array containing all possible fields as used by the output method.
	 */
	protected function format_item( $item ) {

		$addon = llms_get_add_on( $item );

		$formatted = array(
			'name'           => $item['slug'],
			'status'         => $addon->get_status(),
			'license'        => str_replace( 'license_', '', $addon->get_license_status() ),
			'update'         => $addon->has_available_update() ? 'available' : 'none',
			'version'        => $addon->is_installed() ? $addon->get_installed_version() : 'N/A',
			'update_version' => $item['version'],
			'title'          => $item['title'],
			'type'           => $item['type'],
			'file'           => $item['update_file'],
		);

		return $formatted;
	}

	/**
	 * Retrieves an optionally filtered list of add-ons for use in the `list` command.
	 *
	 * @since [version]
	 *
 	 * @param array $args       Indexed array of positional command arguments.
 	 * @param array $assoc_args Associative array of command options.
 	 * @return array[] Array of add-on items.
	 */
	protected function get_filtered_items( $assoc_args, $filter_field = null ) {

		$addons = llms_get_add_ons();

		$list = array_filter( $addons['items'], function( $item ) {
			return
				// Skip anything without a slug.
				! empty( $item['slug'] ) &&
				// Skip the LifterLMS core.
				'lifterlms' !== $item['slug'] &&
				// Skip third party add-ons.
				! in_array( 'third-party', array_keys( $item['categories'] ), true );
		} );

		// Format remaining items.
		$list = array_map( array( $this, 'format_item' ), $list );

		// Filter by field value.
		if ( $filter_field ) {
			$field_val = $assoc_args[ $filter_field ];
			$list = array_filter( $list, function( $item ) use ( $filter_field, $field_val ) {
				return $item[ $filter_field ] === $field_val;
			} );
		}

		// Alpha sort the list by slug.
		usort( $list, function( $a, $b ) {
			return strcmp( $a['name'], $b['name'] );
		} );

		return $list;

	}

	/**
	 * Gets a list of LifterLMS add-on plugins and themes.
	 *
	 * Displays a list of LifterLMS add-ons with their activation status,
	 * license status, current version, update availability, etc...
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : Filter results based on the value of a field.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each add-on.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to only the specified fields. Use "all" to display all available fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - count
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each add-on:
	 *
	 * * name
	 * * status
	 * * update
	 * * version
	 *
	 * These fields are optionally available:
	 *
	 * * update_version
	 * * license
	 * * title
	 * * type
	 * * file
	 *
	 * ## EXAMPLES
	 *
	 *	   # List all add-ons.
	 *	   wp llms addon list
	 *
	 *     # List all add-ons in JSON format.
	 *     wp llms addon list --format=json
	 *
	 *     # List all add-ons by name only.
	 *     wp llms addon list --field=name
	 *
	 *     # List all add-ons with all available fields.
	 *     wp llms addon list --fields=all
	 *
	 *     # List all add-ons with a custom fields list.
	 *     wp llms addon list --fields=title,status,version
	 *
	 * 	   # List currently activated add-ons.
	 *     wp llms addon list --status=active
	 *
	 *     # List all theme add-ons.
	 *     wp llms addon list --type=theme
	 *
	 *     # List all add-ons with available updates.
	 *     wp llms addon list --update=available
	 *
	 *     # List all add-ons licensed on the site.
	 *     wp llms addon list --license=active
	 *
 	 * @since [version]
 	 *
 	 * @param array $args       Indexed array of positional command arguments.
 	 * @param array $assoc_args Associative array of command options.
 	 * @return null
	 */
	public function list( $args, $assoc_args ) {

		$fields     = array( 'name', 'status', 'update', 'version' );
		$all_fields = array_merge( $fields, array( 'update_version', 'license', 'title', 'type', 'file' ) );

		// Determine if there's a user filter submitted through`--<field>=<value>`.
		$filter_field = array_values( array_intersect( $all_fields, array_keys( $assoc_args ) ) );

		$list = $this->get_filtered_items( $assoc_args, ! empty( $filter_field ) ? $filter_field[0] : null );

		if ( ! empty( $assoc_args['fields'] ) && 'all' === $assoc_args['fields'] ) {
			$assoc_args['fields'] = $all_fields;
		}

		$formatter = new Formatter( $assoc_args, $fields );
		return $formatter->display_items( $list );

	}

}
