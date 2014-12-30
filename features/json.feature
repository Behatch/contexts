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
        And the JSON node "object[0]" should exist
        And the JSON node "object[2]" should not exist

        And the JSON node "bar" should not exist

    Scenario: Json validation with schema
        Given I am on "/json/imajson.json"
        Then the JSON should be valid according to the schema "fixtures/www/json/schema.json"

    Scenario: Json validation with schema containing ref
        Given I am on "/json/withref.json"
        Then the JSON should be valid according to the schema "fixtures/www/json/schemaref.json"

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


    Scenario: Json validation deep
        Given I am on "/json/booking.json"
        Then the JSON should be invalid according to this schema:
            """
            {
                "type":"object",
                "$schema": "http://json-schema.org/draft-03/schema",
                "id": "http://jsonschema.net",
                "required":false,
                "properties":{
                    "Booking": {
                        "type":"object",
                        "id": "http://jsonschema.net/Booking",
                        "required":false
                    },
                    "Metadata": {
                        "type":"object",
                        "id": "http://jsonschema.net/Metadata",
                        "required":false,
                        "properties":{
                            "First": {
                                "type":"object",
                                "id": "http://jsonschema.net/Metadata/First",
                                "required":false,
                                "properties":{
                                    "default_value": {
                                        "type":"boolean",
                                        "id": "http://jsonschema.net/Metadata/First/default_value",
                                        "required":false
                                    },
                                    "enabled": {
                                        "type":"boolean",
                                        "id": "http://jsonschema.net/Metadata/First/enabled",
                                        "required":true
                                    }
                                }
                            }
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

    Scenario: Check json value with double quote
        Given I am on "/json/withdoublequote.json"
        Then the response should be in JSON
        And the JSON node "foo" should be equal to "A "bar" in a bar"
