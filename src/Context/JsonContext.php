<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

class JsonContext extends BaseContext
{
    private $evaluationMode;

    public function __construct($evaluationMode = 'javascript')
    {
        $this->evaluationMode = $evaluationMode;
    }

    /**
     * Checks, that the response is correct JSON
     *
     * @Then the response should be in JSON
     */
    public function theResponseShouldBeInJson()
    {
        $this->getJson();
    }

    /**
     * Checks, that the response is not correct JSON
     *
     * @Then the response should not be in JSON
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
     * @Then the JSON node :node should be equal to :text
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        if ($actual != $text) {
            throw new \Exception(
                sprintf("The node value is `%s`", json_encode($actual))
            );
        }
    }

    /**
     * Checks, that given JSON node has N element(s)
     *
     * @Then the JSON node :node should have :count element(s)
     */
    public function theJsonNodeShouldHaveElements($node, $count)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        $this->assertSame($count, sizeof($actual));
    }

    /**
     * Checks, that given JSON node contains given value
     *
     * @Then the JSON node :node should contain :text
     */
    public function theJsonNodeShouldContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        $this->assertContains($text, (string)$actual);
    }

    /**
     * Checks, that given JSON node does not contain given value
     *
     * @Then the JSON node :node should not contain :text
     */
    public function theJsonNodeShouldNotContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        $this->assertNotContains($text, (string)$actual);
    }

    /**
     * Checks, that given JSON node exist
     *
     * @Given the JSON node :node should exist
     */
    public function theJsonNodeShouldExist($node)
    {
        $json = $this->getJson();

        try {
            $this->evaluateJson($json, $node);
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exist.", $node));
        }
    }

    /**
     * Checks, that given JSON node does not exist
     *
     * @Given the JSON node :node should not exist
     */
    public function theJsonNodeShouldNotExist($node)
    {
        $json = $this->getJson();

        $e = null;
        try {
            $actual = $this->evaluateJson($json, $node);
        }
        catch (\Exception $e) {
        }

        if ($e === null) {
            throw new \Exception(sprintf("The node '%s' exists and contains '%s'.", $node , json_encode($actual)));
        }
    }

    /**
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $this->validate($schema);
    }

    /**
     * @Then the JSON should be valid according to the schema :filename
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        if (is_file($filename)) {
            $schema = file_get_contents($filename);
            $this->validate($schema, array('uri' => 'file://' . getcwd() . '/' . $filename ));
        }
        else {
            throw new \RuntimeException(
                'The JSON schema doesn\'t exist'
            );
        }
    }

    /**
     * @Then the JSON should be equal to:
     */
    public function theJsonShouldBeEqualTo(PyStringNode $content)
    {
        $actual = $this->getJson();

        try {
            $expected = $this->decode($content);
        }
        catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        $this->assertSame(
            json_encode($expected),
            json_encode($actual),
            "The json is equal to:\n" . $this->encode($actual)
        );
    }

    /**
     * @Then print last JSON response
     */
    public function printLastJsonResponse()
    {
        echo $this->encode($this->getJson());
    }

    private function evaluateJson($json, $expression)
    {
        if ($this->evaluationMode === 'javascript') {
            $expression = str_replace('.', '->', $expression);
        }

        try {
            if(is_array($json)){
                $expression =  preg_replace('/^root/', '', $expression);
                eval(sprintf('$result = $json%s;', $expression));
            } else {
                $expression =  preg_replace('/^root->/', '', $expression);
                eval(sprintf('$result = $json->%s;', $expression));
            }
        }
        catch (\Exception $e) {
            throw new \Exception("Failed to evaluate expression '$expression'.");
        }

        return $result;
    }

    private function validate($schema, $context = null)
    {
        try {
            $jsonSchema = $this->decode($schema);
        }
        catch (\Exception $e) {
            throw new \Exception('The schema is not a valid JSON');
        }

        $uri = null;
        if (null !== $context) {
            if (is_string($context)) {
                $uri = $context;
            } elseif (is_array($context) && array_key_exists('uri', $context)) {
                $uri = $context['uri'];
            }
        }

        $retriever = new \JsonSchema\Uri\UriRetriever;
        $refResolver = new \JsonSchema\RefResolver($retriever);
        $refResolver->resolve($jsonSchema, $uri);

        $validator = new \JsonSchema\Validator();
        $validator->check($this->getJson(), $jsonSchema);

        if (!$validator->isValid()) {
            $msg = "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $msg .= sprintf("  - [%s] %s\n", $error['property'], $error['message']);
            }
            throw new \Exception($msg);
        }
    }

    private function getJson()
    {
        $content = $this->getSession()->getPage()->getContent();

        return $this->decode($content);
    }

    private function decode($content)
    {
        $result = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("The response is not in JSON");
        }

        return $result;
    }

    private function encode($content)
    {
        $json = null;

        if (defined('JSON_PRETTY_PRINT')) {
            $json = json_encode($content, JSON_PRETTY_PRINT);
        }
        else {
            $json = json_encode($content);
        }
        return $json;
    }
}
