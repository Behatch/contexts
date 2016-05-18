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

        Then the JSON node "foo" should exist
        And the JSON node "root.foo" should exist
        And the JSON node "foo" should contain "bar"
        And the JSON node "foo" should not contain "something else"

        And the JSON node "numbers[0]" should contain "one"
        And the JSON node "numbers[1]" should contain "two"
        And the JSON node "numbers[2]" should contain "three"
        And the JSON node "numbers[3].complexeshizzle" should be equal to "true"
        And the JSON node "numbers[3].so[0]" should be equal to "very"
        And the JSON node "numbers[3].so[1].complicated" should be equal to "indeed"

        And the JSON nodes should be equal to:
            | foo        | bar   |
            | numbers[0] | one   |
            | numbers[1] | two   |
            | numbers[2] | three |

        And the JSON nodes should contain:
            | foo        | bar   |
            | numbers[0] | one   |
            | numbers[1] | two   |
            | numbers[2] | three |

        And the JSON nodes should not contain:
            | foo | something else |

        And the JSON node "bar" should not exist

    Scenario: Json validation with schema
        Given I am on "/json/imajson.json"
        Then the JSON should be valid according to the schema "tests/fixtures/www/json/schema.json"

    Scenario: Json validation with schema containing ref (invalid case)
        Given I am on "/json/withref-invalid.json"
        Then the JSON should be invalid according to the schema "tests/fixtures/www/json/schemaref.json"

    Scenario: Json validation with schema containing ref
        Given I am on "/json/withref.json"
        Then the JSON should be valid according to the schema "tests/fixtures/www/json/schemaref.json"

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
        And print last JSON response

    Scenario: Check json root node
        Given I am on "/json/rootarray.json"
        Then the response should be in JSON
        And the JSON node "root[0].name" should exist
        And the JSON node "root" should have 2 elements

    Scenario: Check with type comparison
        Given I am on "/json/arraywithtypes.json"
        Then the response should be in JSON
        And the JSON node "root[0]" should be null
        And the JSON node "root[1]" should be true
        And the JSON node "root[2]" should be false
        And the JSON node "root[3]" should be equal to the string "dunglas.fr"
        And the JSON node "root[4]" should be equal to the number 1312
        And the JSON node "root[4]" should be equal to the number 1312.0
        And the JSON node "root[5]" should be equal to the number 1936.2

    Scenario: Check not null values
        Given I am on "/json/notnullvalues.json"
        Then the response should be in JSON
        And the JSON node '' should have 5 elements
        And the JSON node "one" should not be null
        And the JSON node "one" should be false
        And the JSON node "two" should not be null
        And the JSON node "two" should be true
        And the JSON node "three" should not be null
        And the JSON node "three" should be equal to the string ""
        And the JSON node "four" should not be null
        And the JSON node "four" should be equal to the string "foo"
        And the JSON node "five" should not be null
        And the JSON node "five" should be equal to the number 5
