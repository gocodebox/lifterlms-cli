<?php
/**
 * File Summary
 *
 * Forked from wp-cli/restful (by Daniel Bachhuber, released under the MIT license https://opensource.org/licenses/MIT).
 * https://github.com/wp-cli/restful
 *
 * @package LifterLMS_CLI/Classes
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @link https://github.com/wp-cli/restful/blob/master/inc/Runner.php
 * @link https://github.com/wp-cli/restful/commit/6ea62c149944d8fcb31a7ade7b4f65fb72c8a5a3
 */

namespace LifterLMS\CLI\Commands\Restful;

/**
 * LifterLMS REST API to LifterLMS CLI Bridge.
 *
 * Hooks into the REST API, figures out which endpoints come from LifterLMS,
 * and registers them as CLI commands.
 *
 * @since 0.0.1
 */
class Runner {

	public static function after_wp_load() {

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			return;
		}

		if ( ! class_exists( 'WP_REST_Server' ) ) {
			return;
		}

		global $wp_rest_server;
		$wp_rest_server = new \WP_REST_Server();

		do_action( 'rest_api_init', $wp_rest_server );

		$request = new \WP_REST_Request( 'GET', '/' );
		$request->set_param( 'context', 'help' );

		$response      = $wp_rest_server->dispatch( $request );
		$response_data = $response->get_data();
		if ( empty( $response_data ) ) {
			return;
		}

		foreach ( $response_data['routes'] as $route => $route_data ) {

			// Skip non LifterLMS routes.
			if ( 0 !== strpos( $route, '/llms/' ) ) {
				continue;
			}

			if ( empty( $route_data['schema']['title'] ) ) {
				\WP_CLI::debug( "No schema title found for {$route}, skipping LifterLMS CLI REST command registration.", 'lifterlms' );
				continue;
			}

			$name         = $route_data['schema']['title'];
			$rest_command = new Command( $name, $route, $route_data['schema'] );
			self::register_route_commands( $rest_command, $route, $route_data );

		}

	}

	private static function get_command_title( $title ) {
		return 'llms ' . str_replace( array( 'llms_', '_' ), array( '', '-' ), $title );
	}

	private static function get_command_root_desc( $title ) {
		$title = str_replace( array( 'llms_', '_', '-', 'students' ), array( '', ' ', ' ', 'student' ), $title );
		if ( 's' !== substr( $title, -1 ) ) {
			$title .= 's';
		}
		return sprintf( 'Manage %s.', $title );
	}

	/**
	 * Register WP-CLI commands for all endpoints on a route
	 *
	 * @param string
	 * @param array  $endpoints
	 */
	private static function register_route_commands( $rest_command, $route, $route_data, $command_args = array() ) {

		$parent = self::get_command_title( $route_data['schema']['title'] );

		$supported_commands = array();
		foreach ( $route_data['endpoints'] as $endpoint ) {

			$parsed_args   = preg_match_all( '#\([^\)]+\)#', $route, $matches );
			$resource_id   = ! empty( $matches[0] ) ? array_pop( $matches[0] ) : null;
			$trimmed_route = rtrim( $route );
			$is_singular   = $resource_id === substr( $trimmed_route, - strlen( $resource_id ) );

			$command = '';
			// List a collection
			if ( array( 'GET' ) == $endpoint['methods']
				&& ! $is_singular ) {
				$supported_commands['list'] = ! empty( $endpoint['args'] ) ? $endpoint['args'] : array();
			}

			// Create a specific resource
			if ( array( 'POST' ) == $endpoint['methods']
				&& ! $is_singular ) {
				$supported_commands['create'] = ! empty( $endpoint['args'] ) ? $endpoint['args'] : array();
			}

			// Get a specific resource
			if ( array( 'GET' ) == $endpoint['methods']
				&& $is_singular ) {
				$supported_commands['get'] = ! empty( $endpoint['args'] ) ? $endpoint['args'] : array();
			}

			// Update a specific resource
			if ( in_array( 'POST', $endpoint['methods'] )
				&& $is_singular ) {
				$supported_commands['update'] = ! empty( $endpoint['args'] ) ? $endpoint['args'] : array();
			}

			// Delete a specific resource
			if ( array( 'DELETE' ) == $endpoint['methods']
				&& $is_singular ) {
				$supported_commands['delete'] = ! empty( $endpoint['args'] ) ? $endpoint['args'] : array();
			}
		}

		foreach ( $supported_commands as $command => $endpoint_args ) {

			$synopsis = array();
			if ( in_array( $command, array( 'delete', 'get', 'update' ) ) ) {
				$synopsis[] = array(
					'name'        => 'id',
					'type'        => 'positional',
					'description' => 'The id for the resource.',
					'optional'    => false,
				);
			}

			foreach ( $endpoint_args as $name => $args ) {
				$arg_reg = array(
					'name'        => $name,
					'type'        => 'assoc',
					'description' => ! empty( $args['description'] ) ? $args['description'] : '',
					'optional'    => empty( $args['required'] ) ? true : false,
				);
				foreach ( array( 'enum', 'default' ) as $key ) {
					if ( isset( $args[ $key ] ) ) {
						$new_key             = 'enum' === $key ? 'options' : $key;
						$arg_reg[ $new_key ] = $args[ $key ];
					}
				}
				$synopsis[] = $arg_reg;
			}

			if ( in_array( $command, array( 'list', 'get' ) ) ) {
				$synopsis[] = array(
					'name'        => 'fields',
					'type'        => 'assoc',
					'description' => 'Limit response to specific fields. Defaults to all fields.',
					'optional'    => true,
				);
				$synopsis[] = array(
					'name'        => 'field',
					'type'        => 'assoc',
					'description' => 'Get the value of an individual field.',
					'optional'    => true,
				);
				$synopsis[] = array(
					'name'        => 'format',
					'type'        => 'assoc',
					'description' => 'Render response in a particular format.',
					'optional'    => true,
					'default'     => 'table',
					'options'     => array(
						'table',
						'json',
						'csv',
						'ids',
						'yaml',
						'count',
						'headers',
						'body',
						'envelope',
					),
				);
			}

			if ( in_array( $command, array( 'create', 'update', 'delete' ) ) ) {
				$synopsis[] = array(
					'name'        => 'porcelain',
					'type'        => 'flag',
					'description' => 'Output just the id when the operation is successful.',
					'optional'    => true,
				);
			}

			$methods = array(
				'list'   => 'list_items',
				'create' => 'create_item',
				'delete' => 'delete_item',
				'get'    => 'get_item',
				'update' => 'update_item',
			);

			$before_invoke = null;
			if ( empty( $command_args['when'] ) && \WP_CLI::get_config( 'debug' ) ) {
				$before_invoke = function() {
					if ( ! defined( 'SAVEQUERIES' ) ) {
						define( 'SAVEQUERIES', true );
					}
				};
			}

			\WP_CLI::add_command(
				"{$parent}",
				$rest_command,
				array(
					'shortdesc' => self::get_command_root_desc( $route_data['schema']['title'] ),
				)
			);

			\WP_CLI::add_command(
				"{$parent} {$command}",
				array( $rest_command, $methods[ $command ] ),
				array(
					'synopsis'      => $synopsis,
					'when'          => ! empty( $command_args['when'] ) ? $command_args['when'] : '',
					'before_invoke' => $before_invoke,
				)
			);

			if ( 'list' === $command ) {
				\WP_CLI::add_command(
					"{$parent} diff",
					array( $rest_command, 'diff_items' ),
					array(
						'when' => ! empty( $command_args['when'] ) ? $command_args['when'] : '',
					)
				);
			}

			if ( 'create' === $command ) {
				// Reuse synopsis from 'create' command
				$generate_synopsis   = array();
				$generate_synopsis[] = array(
					'name'        => 'count',
					'type'        => 'assoc',
					'description' => 'Number of items to generate.',
					'optional'    => true,
					'default'     => 10,
				);
				$generate_synopsis[] = array(
					'name'        => 'format',
					'type'        => 'assoc',
					'description' => 'Render generation in specific format.',
					'optional'    => true,
					'default'     => 'progress',
					'options'     => array(
						'progress',
						'ids',
					),
				);
				// Reuse synopsis from 'create' command
				$generate_synopsis = array_merge( $generate_synopsis, $synopsis );
				\WP_CLI::add_command(
					"{$parent} generate",
					array( $rest_command, 'generate_items' ),
					array(
						'synopsis' => $generate_synopsis,
						'when'     => ! empty( $command_args['when'] ) ? $command_args['when'] : '',
					)
				);
			}

			if ( 'update' === $command && array_key_exists( 'get', $supported_commands ) ) {
				$synopsis   = array();
				$synopsis[] = array(
					'name'        => 'id',
					'type'        => 'positional',
					'description' => 'The id for the resource.',
					'optional'    => false,
				);
				\WP_CLI::add_command(
					"{$parent} edit",
					array( $rest_command, 'edit_item' ),
					array(
						'synopsis' => $synopsis,
						'when'     => ! empty( $command_args['when'] ) ? $command_args['when'] : '',
					)
				);
			}
		}
	}

}
