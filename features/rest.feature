Feature: Testing RESTContext

    Scenario: Testing headers
        When I send a GET request on "rest/index.php"
        Then print last response headers
        And the header "Content-Type" should be contains "text"
        And the header "Content-Type" should be equal to "text/html"
        And the header "Content-Type" should not contain "text/json"
        And the header "xxx" should not exist
        And the response should expire in the future

    Scenario: Testing request methods.
        Given I send a GET request on "/rest/index.php"
        Then I should see "You have sent a GET request. "
        And I should see "No parameter received"

        When I send a GET request on "/rest/index.php?first=foo&second=bar"
        Then I should see "You have sent a GET request. "
        And I should see "2 parameter(s)"
        And I should see "first : foo"
        And I should see "second : bar"

        When I send a POST request on "/rest/index.php" with parameters:
            | key | value |
            | foo | bar   |
        Then I should see "You have sent a POST request. "
        And I should see "1 parameter(s)"
        And I should see "foo : bar"

        When I send a PUT request on "/rest/index.php"
        Then I should see "You have sent a PUT request. "

        When I send a DELETE request on "/rest/index.php"
        Then I should see "You have sent a DELETE request. "

        When I send a POST request on "/rest/index.php" with body:
            """
            This is a body.
            """
        Then I should see "Body : This is a body."

    Scenario: Add header
        Given I add "xxx" header equal to "yyy"
        When I send a GET request on "/rest/index.php"
        Then I should see "HTTP_XXX : yyy"
