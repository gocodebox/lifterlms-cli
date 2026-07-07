<?php
/**
 * Course sub-resource commands file.
 *
 * @package LifterLMS/CLI
 *
 * @since [version]
 * @version [version]
 */

namespace LifterLMS\CLI\Commands\Course;

use LifterLMS\CLI\Commands\AbstractCommand;

/**
 * Additional course commands for sub-resource endpoints.
 *
 * These commands supplement the auto-generated CRUD commands
 * by adding access to sub-resource REST API routes that the
 * Restful bridge does not discover automatically.
 *
 * @since [version]
 */
class Main extends AbstractCommand {

	use Content, Enrollments;

}
