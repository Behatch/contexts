Feature: Testing RESTContext

    @symfony
    Scenario: Testing post method
        When I send a POST request to "/symfony/rest" with parameters:
            | key      | value      |
            | foo      | bar        |
            | foofile1 | @lorem.txt |
            | foofile2 | @lorem.txt |
        Then I should see "You have sent a POST request. "
        And I should see "1 parameter(s)"
        And I should see "2 file(s)"
        And I should see "foo : bar"
        And I should see "foofile1 - name : lorem.txt"
        And I should see "foofile1 - error : 0"
        And I should see "foofile1 - size : 39"
        And I should see "foofile2 - name : lorem.txt"
        And I should see "foofile2 - error : 0"
        And I should see "foofile2 - size : 39"
