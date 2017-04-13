Feature: Browser Feature

    # If this scenario fails
    # It's probably because your web environment is not properly setup
    # You will find the necessery help in README.md
    @javascript
    Scenario: Testing simple web access
        Given I am on "/index.html"
        Then I should see "Congratulations, you've correctly set up your apache environment."

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

    @javascript
    Scenario: Elements testing
        Given I am on url composed by:
            | parameters     |
            | /browser       |
            | /elements.html |
        Then I should see 4 "div" in the 1st "body"
        And I should see less than 6 "div" in the 1st "body"
        And I should see more than 2 "div" in the 1st "body"
        And the "months_selector" select box should not contain "december"
        And the "months_selector" select box should contain "january"
        When I click on the 1st "ul li" element
        Then I should see "You clicked First LI"
        When I press the 2nd "Submit" button
        Then I should see "You clicked Second BUTTON"
        When I follow the 1st "Second" link
        Then I should see "You clicked Second A"

    @javascript
    Scenario: Frames testing
        Given I am on "/browser/frames.html"
        When I switch to iframe "index"
        Then I should see "Visible"

        When switch to main frame

        When switch to iframe "elements"
        Then the "months_selector" select box should contain "january"

    @javascript
    Scenario: Wait before seeing
        Given I am on "/browser/timeout.html"
        When I wait 3 seconds until I see "timeout"
        And I wait 1 second
        And I wait for "#iframe" element
        And I wait 5 seconds for "#iframe" element
        Then the total elapsed time should be more than 3 seconds

    @javascript
    Scenario: Waited upon text should actually be visible
        Given I am on "/browser/timeout.html"
        Then I should not see "timeout"
        When I wait 3 seconds until I see "timeout"
        Then I should see "timeout"

    Scenario: Waited upon text should actually be visible
        Given I am on "/browser/index.html"
        Then I should not see "foobar" within 1 second

    @javascript
    Scenario: Check element visibility
        Given I am on "/browser/index.html"
        Then the "#visible-element" element should be visible
        And the "#hidden-element" element should not be visible

    @javascript
    Scenario:
        Given I am on "/browser/elements.html"
        Then I fill in "today" with the current date
        And I fill in "today" with the current date and modifier "-1 day"

    Scenario:
        Given I am on "/browser/elements.html"
        Then i save the value of "today" in the "today" parameter

    Scenario: Waiting for fractions of a second
        Given I am on "/browser/index.html"
        And I wait 1.9 seconds
        And I wait 1.9 seconds
        And I wait 1.9 seconds
        Then the total elapsed time should be more than 4 seconds
