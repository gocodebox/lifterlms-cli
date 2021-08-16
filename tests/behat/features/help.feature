Feature: Run help commands

	Background:
		Given a WP install with the LifterLMS plugin

	Scenario: Run help commands
		When I run the command `wp help`
		Then STDERR should be empty
		And STDOUT should contain:
			"""
			llms                  Manage LifterLMS.
			"""

		When I run the command `wp llms --help`
		Then STDERR should be empty
		And STDOUT should contain:
			"""
			NAME

			  wp llms

			DESCRIPTION

			  Manage LifterLMS.

			SYNOPSIS

			  wp llms <command>

			SUBCOMMANDS

			  access-plan               Manage access plans.
			  addon                     Manage LifterLMS add-on plugins and themes.
			  api-key                   Manage api keys.
			  course                    Manage courses.
			  instructor                Manage instructors.
			  license                   Manage LifterLMS License Keys.
			  lesson                    Manage lessons.
			  membership                Manage memberships.
			  section                   Manage sections.
			  student                   Manage students.
			  students-enrollments      Manage student enrollments.
			  students-progress         Manage student progress.
			  version                   Display the version of LifterLMS or the specified LifterLMS add-on.
			"""
