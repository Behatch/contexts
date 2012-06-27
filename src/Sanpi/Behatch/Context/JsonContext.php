<?php

namespace Sanpi\Behatch\Context;

class JsonContext extends BaseContext
{
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
     * @Then /^the JSON node "(?P<element>[^"]*)" should be equal to "(?P<text>[^"]*)"$/
     */
    public function theJsonNodeShouldBeEqualTo($element, $text)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $element);

        if ($actual != $text) {
            throw new \Exception(sprintf("The node value is `%s`", $actual));
        }
    }

    /**
     * @Then /^the JSON node "(?P<element>[^"]*)" should have (?P<nth>\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($element, $nth)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $element);

        assertSame((integer)$nth, sizeof($actual));
    }

    /**
     * @Then /^the JSON node "(?P<element>[^"]*)" should contain "(?P<text>[^"]*)"$/
     */
    public function theJsonNodeShouldContain($element, $text)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $element);

        assertContains($text, (string)$actual);
    }

    /**
     * @Then /^the JSON node "(?P<element>[^"]*)" should not contain "(?P<text>[^"]*)"$/
     */
    public function theJsonNodeShouldNotContain($element, $text)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $actual = $this->evaluateJson($json, $element);

        assertNotContains($text, (string)$actual);
    }

    /**
     * @Given /^the JSON node "(?P<element>[^"]*)" should exists$/
     */
    public function theJsonNodeShouldExists($element)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        try {
            $this->evaluateJson($json, $element);
        }
        catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exists.", $element));
        }
    }

    /**
     * @Given /^the JSON node "(?P<element>[^"]*)" should not exists$/
     */
    public function theJsonNodeShouldNotExists($element)
    {
        $json = $this->getJson();

        if (false == $json) {
            throw new \Exception("The response is not in JSON");
        }

        $e = null;
        try {
            $actual = $this->evaluateJson($json, $element);
        }
        catch (\Exception $e) {
        }

        if ($e === null) {
            throw new \Exception(sprintf("The node '%s' exists and contains '%s'.", $element , $actual));
        }
    }

    private function getJson()
    {
        $content = $this->getSession()->getPage()->getContent();

        return json_decode($content);
    }

    private function evaluateJson($json, $expression)
    {
        if ($this->getParameter('behatch.json.evaluation_mode') == 'javascript') {
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
}
