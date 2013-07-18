<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

class JsonContext extends BaseContext
{
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
            throw new \Exception("The response is in JSON");
        }
        catch (\Exception $e) {
        }
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should be equal to "(?P<text>[^"]*)"$/
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        if ($actual != $text) {
            throw new \Exception(sprintf("The node value is `%s`", $actual));
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

        $actual = $this->evaluateJson($json, $node);

        $this->assertSame((integer)$nth, sizeof($actual));
    }

    /**
     * Checks, that given JSON node contains given value
     *
     * @Then /^the JSON node "(?P<node>[^"]*)" should contain "(?P<text>[^"]*)"$/
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
     * @Then /^the JSON node "(?P<node>[^"]*)" should not contain "(?P<text>[^"]*)"$/
     */
    public function theJsonNodeShouldNotContain($node, $text)
    {
        $json = $this->getJson();

        $actual = $this->evaluateJson($json, $node);

        $this->assertNotContains($text, (string)$actual);
    }

    /**
     * Checks, that given JSON node exists
     *
     * @Given /^the JSON node "(?P<node>[^"]*)" should exists$/
     */
    public function theJsonNodeShouldExists($node)
    {
        $json = $this->getJson();

        try {
            $this->evaluateJson($json, $node);
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exists.", $node));
        }
    }

    /**
     * Checks, that given JSON node does not exist
     *
     * @Given /^the JSON node "(?P<node>[^"]*)" should not exists$/
     */
    public function theJsonNodeShouldNotExists($node)
    {
        $json = $this->getJson();

        $e = null;
        try {
            $actual = $this->evaluateJson($json, $node);
        }
        catch (\Exception $e) {
        }

        if ($e === null) {
            throw new \Exception(sprintf("The node '%s' exists and contains '%s'.", $node , $actual));
        }
    }

    /**
     * @Then /^the JSON should be valid according to this schema:$/
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $this->validate($schema);
    }

    /**
     * @Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        if (is_file($filename)) {
            $schema = file_get_contents($filename);
            $this->validate($schema);
        }
        else {
            throw new \RuntimeException(
                'The JSON schema doesn\'t exist'
            );
        }
    }

    /**
     * @Then /^the JSON should be equal to:$/
     */
    public function theJsonShouldBeEqualTo(PyStringNode $content)
    {
        $actual = $this->getJson();
        $expected = $this->decode($content);

        $this->assertSame(
            json_encode($expected),
            json_encode($actual),
            'The json are different'
        );
    }

    private function evaluateJson($json, $expression)
    {
        if ($this->getParameter('json', 'evaluation_mode') == 'javascript') {
            $expression = str_replace('.', '->', $expression);
        }

        try {
            $result = null;
            if (preg_match('/^(?:root)(.*)/', $expression, $r)) {
                eval(sprintf('$result = $json%s;', $r[1]));
            }
            else {
                eval(sprintf('$result = $json->%s;', $expression));
            }
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("Failed to evaluate expression '%s'.", $expression));
        }

        return $result;
    }

    private function validate($schema)
    {
        $validator = new \JsonSchema\Validator();
        $validator->check($this->getJson(), $this->decode($schema));
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
}
