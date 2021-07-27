Feature: License command

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: Activate, deactivate, and list invalid Keys
		When I try the command `wp llms license list`
		Then STDERR should be:
		"""
		Warning: No license keys found on this site.
		"""

		When I try the command `wp llms license activate fake`
		Then STDERR should be:
		"""
		Error: "fake" is not a valid license key. Please ensure your license key is correct and try again.
		"""

		When I try the command `wp llms license deactivate fake`
		Then STDERR should be:
		"""
		Error: License key "fake" was not active on this site.
		"""

	@api-integration
	Scenario: Activate, deactivate, and list real keys
		When I have the env var `LLMS_CLI_TEST_KEY_INFINITY`
		And save STDOUT as {TEST_KEY}

		When I run the command `wp llms license activate {TEST_KEY}`
		Then STDERR should be empty
		And STDOUT should be:
		"""
		Success: License key "{TEST_KEY}" has been activated on this site.
		"""

		When I run the command `wp llms license list`
		Then STDERR should be empty
		And STDOUT should be:
		"""
		{TEST_KEY}
		"""

		When I try the command `wp llms license deactivate {TEST_KEY}`
		Then STDERR should be empty
		And STDOUT should be:
		"""
		Success: License key "{TEST_KEY}" has been deactivated from this site.
		"""

