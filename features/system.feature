Feature: System feature

    Scenario: Testing execution
        Given I execute "ls"

    Scenario: Testing execution from the project root
        Given I execute "bin/behat --help"

    Scenario: File creation
        When I create the file "fixtures/test" contening:
        """
        A new file
        """
