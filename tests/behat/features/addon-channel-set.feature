Feature: Add-on channel-set command

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: Update an add-ons channel subscription

		# Subscribe to the beta channel
		When I run the command `wp llms addon channel-set groups beta`
		And STDERR should be empty
		And STDOUT should be:
			"""
			Success: Subscribed to the beta channel.
			"""

		# Subscribe to the beta channel
		When I run the command `wp llms addon channel-set groups stable`
		And STDERR should be empty
		And STDOUT should be:
			"""
			Success: Subscribed to the stable channel.
			"""

		# Invalid add-on
		When I try the command `wp llms addon channel-set fake stable`
		And STDERR should be:
			"""
			Error: Invalid slug: fake.
			"""
		And STDOUT should be empty
		And the return code should be 1
