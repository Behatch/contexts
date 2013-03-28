Feature: Browser Feature

    # If this scenari fails
    # It's probably because your web environment is not properly setup
    # You will find the necessery help in README.md
    @javascript
    Scenario: Testing simple web access
        Given I am on "/index.html"
        Then I should see "Congratulations, you've correctly set up your apache environment."

    # Testing basic authentication
    Scenario: Basic authentication
        Given I am on "/browser/auth.php"
        Then the response status code should be 401
        And I should see "NONE SHALL PASS"

        When I set basic authentication with "something" and "wrong"
        And I go to "/browser/auth.php"
        Then the response status code should be 401
        And I should see "NONE SHALL PASS"

        When I set basic authentication with "gabriel" and "30091984"
        And I go to "/browser/auth.php"
        Then the response status code should be 200
        And I should see "Successfuly logged in"

        When I go to "/browser/auth.php?logout"
        Then I should see "Logged out"

        When I go to "/browser/auth.php"
        Then the response status code should be 401
        And I should see "NONE SHALL PASS"

    Scenario: Elements testing
        Given I am on "/browser/elements.html"
        Then I should see 2 "div" in the 1st "body"
        And I should see less than 3 "div" in the 1st "body"
        And I should see more than 1 "div" in the 1st "body"

    @javascript
    Scenario: Wait before seeing
        Given I am on "/browser/timeout.html"
        Then I wait 3 seconds until I see "timeout"

    @javascript
    Scenario: Check element visibility
        Given I am on "/browser/index.html"
        Then the "#visible-element" element should be visible
        And the "#hidden-element" element should not be visible
