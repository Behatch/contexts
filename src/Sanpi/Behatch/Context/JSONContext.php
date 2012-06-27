<?php

namespace Sanpi\Behatch\Context;

class JSONContext extends BaseContext
{
    private $evaluationMode = 'php';

    public function __construct(array $parameters)
    {
        if (isset($parameters['json']['evaluation_mode'])) {
            $evaluationMode = $parameters['json']['evaluation_mode'];
            switch ($evaluationMode) {
            case 'php':
            case 'javascript':
                $this->evaluationMode = $evaluationMode;
                break;
            default:
                throw new \Exception(sprintf("Unknown JSON evaluation mode '%s'", $evaluationMode));
            }
        }
    }

    private function getJson()
    {
        $content = $this->getMinkContext()->getSession()->getPage()->getContent();

        return json_decode($content);
    }

    private function evaluateJson($json, $expression)
    {
        if ($this->evaluationMode == 'javascript') {
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

    /**
     * @Then /^the response should be in JSON$/
     */
    public function theResponseShouldBeInJson()
    {
        if (false == $this->getJson()) {
            throw new \Exception("The response is not in JSON");
        }
    }

    /**
     * @Then /^the response should not be in JSON$/
     */
    public function theResponseShouldNotBeInJson()
    {
        if (false != $this->getJson()) {
            throw new \Exception("The response is in JSON");
        }
    }

    /**
     * @Then /^the JSON node "([^"]*)" should be equal to "([^"]*)"$/
     */
    public function theJsonNodeShouldBeEqualTo($jsonExpression, $expected)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $jsonExpression);

        if ($actual != $expected) {
            throw new \Exception(sprintf("The node value is `%s`", $actual));
        }
    }

    /**
     * @Then /^the JSON node "([^"]*)" should have (\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($jsonExpression, $expected)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $jsonExpression);

        assertSame((integer)$expected, sizeof($actual));
    }

    /**
     * @Then /^the JSON node "([^"]*)" should contain "([^"]*)"$/
     */
    public function theJsonNodeShouldContain($jsonExpression, $expected)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $jsonExpression);

        assertContains($expected, (string)$actual);
    }

    /**
     * @Then /^the JSON node "([^"]*)" should not contain "([^"]*)"$/
     */
    public function theJsonNodeShouldNotContain($jsonExpression, $expected)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $jsonExpression);

        assertNotContains($expected, (string)$actual);
    }

    /**
     * @Given /^the JSON node "([^"]*)" should exists$/
     */
    public function theJsonNodeShouldExists($jsonExpression)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        try {
            $this->evaluateJson($json, $jsonExpression);
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exists.", $jsonExpression));
        }
    }

    /**
     * @Given /^the JSON node "([^"]*)" should not exists$/
     */
    public function theJsonNodeShouldNotExists($jsonExpression)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $e = null;
        try {
            $actual = $this->evaluateJson($json, $jsonExpression);
        }
        catch (\Exception $e) {
        }

        if ($e === null) {
            throw new \Exception(sprintf("The node '%s' exists and contains '%s'.", $jsonExpression, $actual));
        }
    }
}
