Feature: Version command

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: Run the llms version command
		When I run the WP-CLI command `wp llms version`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0

	Scenario: Run the llms version command with the --db option
		When I run the WP-CLI command `wp llms version --db`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0
