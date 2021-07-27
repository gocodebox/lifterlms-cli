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

WP_CLI::add_command( 'llms', 'LifterLMS\CLI\Commands\Root' );

WP_CLI::add_command( 'llms addon', 'LifterLMS\CLI\Commands\AddOn\Main' );
WP_CLI::add_command( 'llms license', 'LifterLMS\CLI\Commands\License' );
