Feature: Testing RESTContext

    Scenario: Testing headers
        When I send a GET request to "rest/index.php"
        And the header "Content-Type" should contain "text"
        And the header "Content-Type" should be equal to "text/html; charset=UTF-8"
        And the header "Content-Type" should not be equal to "x-test/no-such-type"
        And the header "Content-Type" should not contain "text/json"
        And the header "Content-Type" should match "@^text/html; [a-zA-Z=-]+@"
        And the header "Content-Type" should not match "/^no-such-type$/"
        And the header "xxx" should not exist
        And the response should expire in the future
        And the response should be encoded in "UTF-8"

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
            | key     | value      |
            | foo     | bar        |
            | foofile | @lorem.txt |
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

        When I send a PUT request to "/rest/index.php" with body:
            """
            {"this is":"some json"}
            """
        Then the response should be empty

    Scenario: request parameter with dot
        https://github.com/Behatch/contexts/issues/256
        When I send a POST request to "/rest/index.php" with parameters:
            | key     | value |
            | item.id | 1     |
        Then I should see "item.id=1"

    Scenario: Add header
        Given I add "xxx" header equal to "yyy"
        When I send a GET request to "/rest/index.php"
        Then I should see "HTTP_XXX : yyy"

    Scenario: Add header with large numeric value
        Given I add "xxx-large-numeric" header equal to "92233720368547758070"
        When I send a GET request to "/rest/index.php"
        Then I should see "HTTP_XXX_LARGE_NUMERIC : 92233720368547758070"

    Scenario: Header should not be cross-scenarios persistent
        When I send a GET request to "/rest/index.php"
        Then I should not see "HTTP_XXX : yyy"
        Then I should not see "HTTP_XXX_LARGE_NUMERIC"

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

    Scenario: Response body
        Given I send a GET request to "/"
        Then the response should be equal to:
        """
        Congratulations, you've correctly set up your apache environment.
        """

    @>php5.5
    Scenario: Set content headers in POST request
        When I add "Content-Type" header equal to "xxx"
        When I send a "POST" request to "rest/index.php" with body:
        """
        {"name": "test"}
        """
        Then the response should contain ">CONTENT_TYPE : xxx"
        Then the response should contain ">HTTP_CONTENT_TYPE : xxx"

    Scenario: Content header is clear in different scenario
        When I send a "POST" request to "rest/index.php" with body:
        """
        {"name": "test"}
        """
        Then the response should not contain ">CONTENT_TYPE : xxx"
        Then the response should not contain ">HTTP_CONTENT_TYPE : xxx"
