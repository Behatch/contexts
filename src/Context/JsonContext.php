<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

use Behat\Gherkin\Node\TableNode;
use Sanpi\Behatch\Json\Json;
use Sanpi\Behatch\Json\JsonSchema;
use Sanpi\Behatch\Json\JsonInspector;
use Sanpi\Behatch\HttpCall\HttpCallResultPool;

class JsonContext extends BaseContext
{
    private $inspector;

    private $httpCallResultPool;

    public function __construct(HttpCallResultPool $httpCallResultPool, $evaluationMode = 'javascript')
    {
        $this->inspector = new JsonInspector($evaluationMode);
        $this->httpCallResultPool = $httpCallResultPool;
    }

    /**
     * Checks, that the response is correct JSON
     * Example: Then the response should be in JSON
     * Example: And the response should be in JSON
     *
     * @Then the response should be in JSON
     */
    public function theResponseShouldBeInJson()
    {
        $this->getJson();
    }

    /**
     * Checks, that the response is not correct JSON
     * Example: Then the response should not be in JSON
     * Example: And the response should not be in JSON
     *
     * @Then the response should not be in JSON
     */
    public function theResponseShouldNotBeInJson()
    {
        $this->not(
            [$this, 'theResponseShouldBeInJson'],
            'The response is in JSON'
        );
    }

    /**
     * Checks, that given JSON node is equal to given value
     * Example: Then the JSON node "isBatman"should be equal to "true"
     * Example: And the JSON node "isBatman" should be equal to "true"
     *
     * @Then the JSON node :node should be equal to :text
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->inspector->evaluate($json, $node);

        if ($actual != $text) {
            throw new \Exception(
                sprintf("The node value is '%s'", json_encode($actual))
            );
        }
    }

    /**
     * Checks, that given JSON nodes are equal to givens values
     *
     * @Then the JSON nodes should be equal to:
     */
    public function theJsonNodesShoudBeEqualTo(TableNode $nodes)
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldBeEqualTo($node, $text);
        }
    }

    /**
     * Checks, that given JSON node has N element(s)
     * Example: Then the JSON node "bio" should have 5 elements
     * Example: And the JSON node "bio" should have 5 elements
     *
     * @Then the JSON node :node should have :count element(s)
     */
    public function theJsonNodeShouldHaveElements($node, $count)
    {
        $json = $this->getJson();

        $actual = $this->inspector->evaluate($json, $node);

        $this->assertSame($count, sizeof((array) $actual));
    }

    /**
     * Checks, that given JSON node contains given value
     * Example: Then the JSON node "trueIdentity" should contain "Bruce Wayne"
     * Example: And the JSON node "trueIdentity" should contain "Bruce Wayne"
     *
     * @Then the JSON node :node should contain :text
     */
    public function theJsonNodeShouldContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->inspector->evaluate($json, $node);

        $this->assertContains($text, (string) $actual);
    }

    /**
     * Checks, that given JSON nodes contains values
     *
     * @Then the JSON nodes should contain:
     */
    public function theJsonNodesShoudContain(TableNode $nodes)
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldContain($node, $text);
        }
    }

    /**
     * Checks, that given JSON node does not contain given value
     * Example: Then the JSON node "isRobin" should not contain "true"
     * Example: And the JSON node "isRobin" should not contain "true"
     *
     * @Then the JSON node :node should not contain :text
     */
    public function theJsonNodeShouldNotContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->inspector->evaluate($json, $node);

        $this->assertNotContains($text, (string) $actual);
    }

    /**
     * Checks, that given JSON nodes does not contain given value
     *
     * @Then the JSON nodes should not contain:
     */
    public function theJsonNodesShoudNotContain(TableNode $nodes)
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldNotContain($node, $text);
        }
    }

    /**
     * Checks, that given JSON node exist
     * Example: Then the JSON node "id" should exist
     * Example: And the JSON node "id" should exist
     *
     * @Given the JSON node :name should exist
     */
    public function theJsonNodeShouldExist($name)
    {
        $json = $this->getJson();

        try {
            $node = $this->inspector->evaluate($json, $name);
        }
        catch (\Exception $e) {
            throw new \Exception("The node '$name' does not exist.");
        }
        return $node;
    }

    /**
     * Checks, that given JSON node does not exist
     * Example: Then the JSON node "Robin" should not exist
     * Example: And the JSON node "Robin" should not exist
     *
     * @Given the JSON node :name should not exist
     */
    public function theJsonNodeShouldNotExist($name)
    {
        $this->not(function () use($name) {
            return $this->theJsonNodeShouldExist($name);
        }, "The node '$name' exists.");
    }

    /**
     * Checks provided JSON against a JSON schema
     * Example: Then the JSON should be valid according to this schema:
     *          """
     *          {
     *              "$schema": "http://json-schema.org/draft-04/schema#",
     *              "title": "Heroes",
     *              "description": "A list of heroes from the known universe",
     *              "type": "object",
     *              "properties": {
     *                   "id": {
     *                        "description": "The unique identifier for a hero",
     *                        "type": "integer"
     *                   },
     *                   "name": {
     *                        "description": "Name of the hero",
     *                        "type": "string"
     *                   },
     *                   "alterEgo": {
     *                        "description": "Alter ego for hero",
     *                        "type": "string"
     *                   }
     *              },
     *              "required": ["id", "name", "alterEgo"]
     *          }
     *          """
     * Example: And the JSON should be valid according to this schema:
     *          """
     *          {
     *              "$schema": "http://json-schema.org/draft-04/schema#",
     *              "title": "Heroes",
     *              "description": "A list of heroes from the known universe",
     *              "type": "object",
     *              "properties": {
     *                   "id": {
     *                        "description": "The unique identifier for a hero",
     *                        "type": "integer"
     *                   },
     *                   "name": {
     *                        "description": "Name of the hero",
     *                        "type": "string"
     *                   },
     *                   "alterEgo": {
     *                        "description": "Alter ego for hero",
     *                        "type": "string"
     *                   }
     *              },
     *              "required": ["id", "name", "alterEgo"]
     *          }
     *          """
     *
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $this->inspector->validate(
            $this->getJson(),
            new JsonSchema($schema)
        );
    }

    /**
     * Checks provided JSON against a provided JSON schema
     * Example: Then the JSON should be valid according to the schema "http://batman.com/secret-schema"
     * Example: And the JSON should be valid according to the schema "http://batman.com/secret-schema"
     *
     * @Then the JSON should be valid according to the schema :filename
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        if (false === is_file($filename)) {
            throw new \RuntimeException(
                'The JSON schema doesn\'t exist'
            );
        }

        $this->inspector->validate(
            $this->getJson(),
            new JsonSchema(
                file_get_contents($filename),
                'file://' . getcwd() . '/' . $filename
            )
        );
    }

    /**
     * Checks that the JSON response is equal to value
     * Example: Then the JSON should be equal to:
     *           """
     *           {
     *               "id": 1,
     *               "name": "Batman",
     *               "alterEgo": "Bruce Wayne"
     *           }
     *           """
     * Example: And the JSON should be equal to:
     *           """
     *           {
     *               "id": 1,
     *               "name": "Batman",
     *               "alterEgo": "Bruce Wayne"
     *           }
     *           """
     *
     * @Then the JSON should be equal to:
     */
    public function theJsonShouldBeEqualTo(PyStringNode $content)
    {
        $actual = $this->getJson();

        try {
            $expected = new Json($content);
        }
        catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            "The json is equal to:\n". $actual->encode()
        );
    }

    /**
     * Prints last JSON response
     * Example: Then print last JSON response
     * Example: And print last JSON response
     *
     * @Then print last JSON response
     */
    public function printLastJsonResponse()
    {
        echo $this->getJson()
            ->encode();
    }

    private function getJson()
    {
        return new Json($this->httpCallResultPool->getResult()->getValue());
    }
}
