Feature: Add-on list command

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: List LifterLMS Add-ons
		When I run the command `wp llms addon list --name=lifterlms-groups --field=version`
		Then STDERR should be empty
		And STDOUT should be:
		"""
		N/A
		"""

		When I run the command `wp llms addon list --name=lifterlms-groups --field=update_version`
		Then STDERR should be empty
		And STDOUT should be a version string > 0.0.0
		And save STDOUT as {UPDATE_VERSION}

		When I run the command `wp llms addon list --name=lifterlms-groups --fields=all`
		Then STDERR should be empty
		And STDOUT should be a table containing rows:
			| name             | status      | update    | version | update_version   | license  | title            | channel | type   | file                                  |
			| lifterlms-groups | uninstalled | available | N/A     | {UPDATE_VERSION} | inactive | LifterLMS Groups | stable  | plugin | lifterlms-groups/lifterlms-groups.php |

		When I run the command `wp llms addon list --type=theme --fields=name,title`
		Then STDERR should be empty
		And STDOUT should be a table containing rows:
			| name                | title     |
			| lifterlms-launchpad | LaunchPad |

		When I try the command `wp llms addon list --field=fake`
		Then STDERR should be:
		"""
		Error: Invalid field: fake.
		"""
