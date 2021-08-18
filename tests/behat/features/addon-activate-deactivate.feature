@api-integration
Feature: Add-on activate and deactivate commands

	Background:
		Given a WP install with the LifterLMS plugin
		And the LifterLMS Add-on "lifterlms-groups" is installed
		And the LifterLMS Add-on "lifterlms-assignments" is installed

	Scenario: Activate and Deactivate LifterLMS add-ons

		# Activate an invalid add-on.
		When I try the command `wp llms addon activate fake`
		Then STDERR should be:
			"""
			Warning: Invalid slug: fake.
			Error: No add-ons activated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Deactivate an invalid add-on.
		When I try the command `wp llms addon deactivate fake`
		Then STDERR should be:
			"""
			Warning: Invalid slug: fake.
			Error: No add-ons deactivated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Activate something that isn't installed.
		When I try the command `wp llms addon activate pdfs`
		Then STDERR should be:
			"""
			Warning: Add-on "pdfs" is not installed. Run 'wp llms addon install pdfs' to install it.
			Error: No add-ons activated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Deactivate something that isn't installed.
		When I try the command `wp llms addon deactivate pdfs`
		Then STDERR should be:
			"""
			Warning: Add-on "pdfs" is not installed.
			Error: No add-ons deactivated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Activate multiple installed add-ons.
		When I run the command `wp llms addon activate assignments groups`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Assignments was successfully activated.
			LifterLMS Groups was successfully activated.
			Success: Activated 2 of 2 add-ons.
			"""

		# Can't activate an already activated add-on.
		When I try the command `wp llms addon activate assignments`
		And STDERR should be:
			"""
			Warning: Add-on "assignments" is already active.
			Error: No add-ons activated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Deactivate all.
		When I run the command `wp llms addon deactivate assignments groups`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Assignments was successfully deactivated.
			LifterLMS Groups was successfully deactivated.
			Success: Deactivated 2 of 2 add-ons.
			"""

		# Activate all using the --all flag.
		When I run the command `wp llms addon activate --all`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Assignments was successfully activated.
			LifterLMS Groups was successfully activated.
			Success: Activated 2 of 2 add-ons.
			"""

		# All installed add-ons are activated.
		When I try the command `wp llms addon activate --all`
		And STDERR should be:
			"""
			Warning: No add-ons to activate.
			"""
		And STDOUT should be empty
		And the return code should be 0

		# Deactivate all using the --all flag
		When I run the command `wp llms addon deactivate --all`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Assignments was successfully deactivated.
			LifterLMS Groups was successfully deactivated.
			Success: Deactivated 2 of 2 add-ons.
			"""

		# All installed add-ons are already dactivated.
		When I try the command `wp llms addon deactivate --all`
		And STDERR should be:
			"""
			Warning: No add-ons to deactivate.
			"""
		And STDOUT should be empty
		And the return code should be 0

		# Activate single
		When I run the command `wp llms addon activate groups`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Groups was successfully activated.
			Success: Activated 1 of 1 add-ons.
			"""

		# Deactivate single
		When I run the command `wp llms addon deactivate groups`
		Then STDERR should be empty
		And STDOUT should be:
			"""
			LifterLMS Groups was successfully deactivated.
			Success: Deactivated 1 of 1 add-ons.
			"""

		# Deactivate add-on that's already deactivated.
		When I try the command `wp llms addon deactivate groups`
		And STDERR should be:
			"""
			Warning: Add-on "groups" is already deactivated.
			Error: No add-ons deactivated.
			"""
		And STDOUT should be empty
		And the return code should be 1

		# Activate multiple with a success and an error.
		When I try the command `wp llms addon activate groups pdfs`
		And STDERR should be:
			"""
			Warning: Add-on "pdfs" is not installed. Run 'wp llms addon install pdfs' to install it.
			Error: Only activated 1 of 2 add-ons.
			"""
		And STDOUT should be:
			"""
			LifterLMS Groups was successfully activated.
			"""
		And the return code should be 1
