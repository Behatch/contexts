Feature: System feature

    Scenario: Testing execution
        Given I execute "ls"

    Scenario: Testing execution time
        Given I execute "sleep 1"
        Then Command should last less than 2 seconds

        Given I execute "sleep 2"
        Then Command should last more than 1 seconds

    Scenario: Testing execution output
        Given I execute "echo 'Hello world'"
        Then I should see on output "Hello world"
        Then I should not see on output "Hello John"

    Scenario: Testing execution from the project root
        Given I execute "bin/behat --help"

    Scenario: File creation
        When I create the file "tests/fixtures/test" containing:
        """
        A new file
        """
        Then print the content of "tests/fixtures/test" file
