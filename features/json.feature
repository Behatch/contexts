Feature: Testing JSONContext

    Scenario: Am I a JSON ?
        Given I am on "/json/imajson.json"
        Then the response should be in JSON
        When I am on "/json/emptyarray.json"
        Then the response should be in JSON
        When I am on "/json/emptyobject.json"
        Then the response should be in JSON
        When I am on "/json/imnotajson.json"
        Then the response should not be in JSON

    Scenario: Count JSON elements
        Given I am on "/json/imajson.json"
        Then the JSON node "numbers" should have 4 elements

    Scenario: Checking JSON evaluation
        Given I am on "/json/imajson.json"

        Then the JSON node "foo" should exists
        And the JSON node "foo" should contain "bar"
        And the JSON node "foo" should not contain "something else"

        And the JSON node "numbers[0]" should contain "one"
        And the JSON node "numbers[1]" should contain "two"
        And the JSON node "numbers[2]" should contain "three"
        And the JSON node "numbers[3].complexeshizzle" should be equal to "true"
        And the JSON node "numbers[3].so[0]" should be equal to "very"
        And the JSON node "numbers[3].so[1].complicated" should be equal to "indeed"

        And the JSON node "bar" should not exists

    Scenario: Json validation with schema
        Given I am on "/json/imajson.json"
        Then the JSON should be valid according to the schema "fixtures/www/json/schema.json"

    Scenario: Json validation
        Given I am on "/json/imajson.json"
        Then the JSON should be valid according to this schema:
            """
            {
                "type": "object",
                "$schema": "http://json-schema.org/draft-03/schema",
                "required":true,
                "properties": {
                    "foo": {
                        "type": "string",
                        "required":true
                    },
                    "numbers": {
                        "type": "array",
                        "required":true,
                        "one": {
                            "type": "string",
                            "required":true
                        },
                        "two": {
                            "type": "string",
                            "required":true
                        },
                        "three": {
                            "type": "string",
                            "required":true
                        }
                    }
                }
            }
            """

    Scenario: Json contents validation
        Given I am on "/json/imajson.json"
        Then the JSON should be equal to:
            """
            {
                "foo": "bar",
                "numbers": [
                    "one",
                    "two",
                    "three",
                    {
                        "complexeshizzle": true,
                        "so": [
                            "very",
                            {
                                "complicated": "indeed"
                            }
                        ]
                    }
                ]
            }
            """
