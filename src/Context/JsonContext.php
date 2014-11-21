<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

use Behat\Mink\Exception\ExpectationException;
use Sanpi\Behatch\Json\Json;
use Sanpi\Behatch\Json\JsonSchema;
use Sanpi\Behatch\Json\JsonInspector;

class JsonContext extends BaseContext
{
    private $inspector;

    /**
     * Checks, that the response is correct JSON
     *
     * @Then /^the response should be in JSON$/
     */
    public function theResponseShouldBeInJson()
    {
        $this->getJson();
    }

    /**
     * Checks, that the response is not correct JSON
     *
     * @Then /^the response should not be in JSON$/
     */
    public function theResponseShouldNotBeInJson()
    {
        try {
            $this->getJson();
        }
        catch (\Exception $e) {
        }

        if (!isset($e)) {
            throw new \Exception("The response is in JSON");
        }
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should be equal to "(?P<text>.*)"$/
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->getInspector()->evaluate($json, $node);

        if ($actual != $text) {
            throw new \Exception(
                sprintf("The node value is `%s`", json_encode($actual))
            );
        }
    }

    /**
     * Checks, that given JSON node has N element(s)
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should have (?P<nth>\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($node, $nth)
    {
        $json = $this->getJson();

        $actual = $this->getInspector()->evaluate($json, $node);

        $this->assertSame((integer)$nth, sizeof($actual));
    }

    /**
     * Checks, that given JSON node contains given value
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should contain "(?P<text>.*)"$/
     */
    public function theJsonNodeShouldContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->getInspector()->evaluate($json, $node);

        $this->assertContains($text, (string) $actual);
    }

    /**
     * Checks, that given JSON node does not contain given value
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should not contain "(?P<text>.*)"$/
     */
    public function theJsonNodeShouldNotContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->getInspector()->evaluate($json, $node);

        $this->assertNotContains($text, (string) $actual);
    }

    /**
     * Checks, that given JSON node exist
     *
     * @Given /^the JSON node "(?P<node>[^"]*)" should exist$/
     */
    public function theJsonNodeShouldExist($node)
    {
        $json = $this->getJson();

        try {
            $this->getInspector()->evaluate($json, $node);
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exist.", $node));
        }
    }

    /**
     * Checks, that given JSON node does not exist
     *
     * @Given /^the JSON node "(?P<node>[^"]*)" should not exist$/
     */
    public function theJsonNodeShouldNotExist($node)
    {
        $json = $this->getJson();

        $e = null;
        $actual = null;
        try {
            $actual = $this->getInspector()->evaluate($json, $node);
        } catch (\Exception $e) {
        }

        if (null === $e && null !== $actual) {
            throw new \Exception(sprintf("The node '%s' exists and contains '%s'.", $node , json_encode($actual)));
        }
    }

    /**
     * @Then /^the JSON should be valid according to this schema:$/
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $this->getInspector()->validate(
            $this->getJson(),
            new JsonSchema($schema)
        );
    }

    /**
     * @Then /^the JSON should be invalid according to this schema:$/
     */
    public function theJsonShouldBeInvalidAccordingToThisSchema(PyStringNode $schema)
    {
        try {
            $isValid = $this->getInspector()->validate(
                $this->getJson(),
                new JsonSchema($schema)
            );

        } catch (\Exception $e) {
            $isValid = false;
        }

        if (true === $isValid) {
            throw new ExpectationException('Expected to receive invalid json, got valid one', $this->getSession());
        }
    }

    /**
     * @Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        if (false === is_file($filename)) {
            throw new \RuntimeException(
                'The JSON schema doesn\'t exist'
            );
        }

        $this->getInspector()->validate(
            $this->getJson(),
            new JsonSchema(
                file_get_contents($filename),
                'file://' . getcwd() . '/' . $filename
            )
        );
    }

    /**
     * @Then /^the JSON should be equal to:$/
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
     * @Then /^print last JSON response$/
     */
    public function printLastJsonResponse()
    {
        echo (string) $this->getJson();
    }

    private function getJson()
    {
        $content = $this->getSession()->getPage()->getContent();

        return new Json($content);
    }

    private function getInspector()
    {
        if (null === $this->inspector) {
            $this->inspector = new JsonInspector($this->getParameter('json', 'evaluation_mode'));
        }

        return $this->inspector;
    }
}
