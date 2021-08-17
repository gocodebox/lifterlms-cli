Feature: Version command

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: Retrieve LifterLMS core and add-on versions
		When I run the command `wp llms version`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0

	Scenario Outline: Provide valid slugs
		When I run the command `wp llms version <slug>`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0

		Examples:
			| slug      |
			| core      |
			| lifterlms |

	Scenario Outline: Try non-existent add-ons or invalid slugs.
		When I try the command `wp llms version <slug>`
		Then STDERR should be:
			"""
			Error: Invalid slug.
			"""

		Examples:
			| slug           |
			| fake           |
			| lifterlms-fake |

	Scenario Outline: Use real add-on slugs that aren't installed
		When I try the command `wp llms version <slug>`
		Then STDERR should be:
			"""
			Error: The requested add-on is not installed. Run 'wp llms addon install <slug>.' to install it.
			"""

		Examples:
			| slug                              |
			| assignments                       |
			| lifterlms-assignments             |
			| lifterlms-integration-woocommerce |

	@api-integration
	Scenario Outline: Use installed add-on slugs
		Given the LifterLMS Add-on "lifterlms-groups" is installed
		When I run the command `wp llms version <slug>`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0

		Examples:
			| slug             |
			| groups           |
			| lifterlms-groups |
