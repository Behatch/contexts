Feature: Browser Feature

    # If this scenario fails
    # It's probably because your web environment is not properly setup
    # You will find the necessary help in README.md
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
        Then I should see "You clicked First"

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
        Then I wait 3 seconds until I see "timeout"
        And I wait 1 second
        And I wait for "#iframe" element
        And I wait 5 seconds for "#iframe" element

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
        Then I save the value of "today" in the "today" parameter

    Scenario: Regression for new step definitions added by sajjadhossain on 6/5/15
        Given I am on "/"
        And I am on a new session
        And I set my browser window size to 15 inch MacBook Retina
        And I set my browser window size to "500" x "500"
        When I go to "https://github.com/Behatch/contexts"
        And I scroll to the bottom
        And I scroll to the top
        When I go to "http://www.javascripter.net/faq/alert.htm"
        And I press "Try it now"
        And I confirm the popup
        Then I should see "Alert: Modal Message Box"


