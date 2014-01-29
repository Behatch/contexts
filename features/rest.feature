Feature: Testing RESTContext

    Scenario: Testing headers
        When I send a GET request to "rest/index.php"
        And the header "Content-Type" should contain "text"
        And the header "Content-Type" should be equal to "text/html"
        And the header "Content-Type" should not contain "text/json"
        And the header "xxx" should not exist
        And the response should expire in the future

    Scenario: Testing request methods.
        Given I send a GET request to "/rest/index.php"
        Then I should see "You have sent a GET request. "
        And I should see "No parameter received"

        When I send a GET request to "/rest/index.php?first=foo&second=bar"
        Then I should see "You have sent a GET request. "
        And I should see "2 parameter(s)"
        And I should see "first : foo"
        And I should see "second : bar"

        When I send a POST request to "/rest/index.php" with parameters:
            | key | value |
            | foo | bar   |
        Then I should see "You have sent a POST request. "
        And I should see "1 parameter(s)"
        And I should see "foo : bar"

        When I send a POST request to "/rest/index.php" with parameters:
            | file | key      | value     |
            |      | foo      | bar       |
            | x    | foofile  | lorem.txt |
        Then I should see "You have sent a POST request. "
        And I should see "1 parameter(s)"
        And I should see "1 file(s)"
        And I should see "foo : bar"
        And I should see "foofile - name : lorem.txt"
        And I should see "foofile - error : 0"
        And I should see "foofile - size : 39"

        When I send a PUT request to "/rest/index.php"
        Then I should see "You have sent a PUT request. "

        When I send a DELETE request to "/rest/index.php"
        Then I should see "You have sent a DELETE request. "

        When I send a POST request to "/rest/index.php" with body:
            """
            This is a body.
            """
        Then I should see "Body : This is a body."

    Scenario: Add header
        Given I add "xxx" header equal to "yyy"
        When I send a GET request to "/rest/index.php"
        Then I should see "HTTP_XXX : yyy"

    Scenario: Case-insensitive header name
        Like describe in the rfc2614 §4.2
        https://tools.ietf.org/html/rfc2616#section-4.2

        When I send a GET request to "rest/index.php"
        Then the header "content-type" should contain "text"

    Scenario: Debug
        Given I add "xxx" header equal to "yyy"
        When I send a POST request to "/rest/index.php" with parameters:
            | key | value |
            | foo | bar   |
        Then print last response headers
        And print the corresponding curl command
