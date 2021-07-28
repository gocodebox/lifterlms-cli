<?php
/**
 * Load LifterLMS CLI classes
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI;

use WP_CLI;
use LifterLMS\CLI\Commands\Restful\Runner;

/**
 * Root Command
 *
 * @since [version]
 */
WP_CLI::add_command( 'llms', 'LifterLMS\CLI\Commands\Root' );

/**
 * Add-on Command
 *
 * @since [version]
 */
WP_CLI::add_command( 'llms addon', 'LifterLMS\CLI\Commands\AddOn\Main' );

/**
 * License Command
 *
 * @since [version]
 */
WP_CLI::add_command( 'llms license', 'LifterLMS\CLI\Commands\License' );

/**
 * Restful Commands
 *
 * @since [version]
 */
Runner::after_wp_load();
