Feature: System feature

    Scenario: Testing execution
        Given I execute "ls"

    Scenario: Testing execution from the project root
        Given I execute "bin/behat --help"
