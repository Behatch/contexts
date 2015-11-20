Feature: System feature

    Scenario: Testing execution
        Given I execute "true"
        Then command should succeed
        Given I execute "false"
        Then command should fail

    Scenario: Testing execution time
        Given I execute "sleep 1"
        Then Command should last less than 2 seconds

        Given I execute "sleep 2"
        Then Command should last more than 1 seconds

    Scenario: Testing execution output
        Given I execute "echo 'Hello world'"
        Then output should contain "Hello world"
        And output should contain "Hel.*ld"
        And output should not contain "Hello John"
        And output should not contain "Hel.*hn"

    Scenario: Testing execution output wall output
        Given I execute "echo 'Hello world\nHow are you?'"
        Then output should be:
        """
        Hello world
        How are you?
        """
        And output should not be:
        """
        Hello John
        How are you?
        """

    Scenario: Testing execution from the project root
        Given I execute "bin/behat --help"

    Scenario: File creation
        When I create the file "tests/fixtures/test" containing:
        """
        A new file
        """
        Then print the content of "tests/fixtures/test" file
