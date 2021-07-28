<?php
/**
 * AddOn ChannelSet class file
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands\AddOn;

// defined( 'ABSPATH' ) || exit;

/**
 * AddOn channel-set command
 *
 * @since [version]
 */
trait ChannelSet {

	/**
	 * Set the update channel subscription for an add-on.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The slug of the add-on.
	 *
	 * [<channel>]
	 * : The update channel to subscribe to.
	 * ---
	 * default: 'stable'
	 * options:
	 *   - stable
	 *   - beta
	 * ---
	 *
	 * @subcommand channel-set
	 *
	 * @since [version]
	 *
	 * @param array $args Indexed array of positional command arguments.
	 * @return null
	 */
	public function channel_set( $args ) {

		$addon = $this->get_addon( $args[0], true, 'warning' );
		$addon->subscribe_to_channel( $args[1] );
		return \WP_CLI::success( sprintf( 'Subscribed to %s channel.', $args[1] ) );

	}

}
