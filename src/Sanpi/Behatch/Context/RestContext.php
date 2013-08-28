<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;

class RestContext extends BaseContext
{
    /**
     * Sends a HTTP request
     *
     * @Given /^I send a (?P<method>[A-Z]+) request on "(?P<url>[^"]*)"$/
     */
    public function iSendARequestOn($method, $url)
    {
        $client = $this->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $client->request($method, $this->locatePath($url));
        $client->followRedirects(true);
    }

    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given /^I send a (?P<method>[A-Z]+) request on "(?P<url>[^"]*)" with parameters:$/
     */
    public function iSendARequestOnWithParameters($method, $url, TableNode $datas)
    {
        $client = $this->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $parameters = array();
        foreach ($datas->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }
            $parameters[$row['key']] = $row['value'];
        }

        $client->request($method, $this->locatePath($url), $parameters);
        $client->followRedirects(true);
    }

    /**
     * Sends a HTTP request with a body
     *
     * @When /^I send a (?P<method>[A-Z]+) request on "(?P<url>[^"]*)" with body:$/
     */
    public function iSendARequestOnWithBody($method, $url, PyStringNode $body)
    {
        $client = $this->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $client->request($method, $this->locatePath($url),
            array(), array(), array(), $body->getRaw());
        $client->followRedirects(true);
    }

    /**
     * Checks, whether the response content is equal to given text
     *
     * @Then /^the response should be equal to:$/
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $this->getSession()->getPage()->getContent();
        $message = sprintf('The string "%s" is not equal to the response of the current page', $expected);
        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Checks, whether the header name is equal to given text
     *
     * @Then /^the header "(?P<name>[^"]*)" should be equal to "(?P<value>[^"]*)"$/
     */
    public function theHeaderShouldBeEqualTo($name, $value)
    {
        $actual = $this->getHttpHeader($name);
        $this->assertEquals($value, $actual,
            sprintf('The header "%s" is equal to "%s"', $name, $actual)
        );
    }

    /**
     * Checks, whether the header name contains the given text
     *
     * @Then /^the header "(?P<name>[^"]*)" should be contains "(?P<value>[^"]*)"$/
     */
    public function theHeaderShouldBeContains($name, $value)
    {
        $this->assertContains($value, $this->getHttpHeader($name),
            sprintf('The header "%s" is doesn\'t contain to "%s"', $name, $value)
        );
    }

    /**
     * Checks, whether the header name doesn't contain the given text
     *
     * @Then /^the header "(?P<name>[^"]*)" should not contain "(?P<value>[^"]*)"$/
     */
    public function theHeaderShouldNotContain($name, $value)
    {
        $this->assertNotContains($value, $this->getHttpHeader($name),
            sprintf('The header "%s" contains "%s"', $name, $value)
        );
    }

    /**
     * Checks, whether the header not exist
     *
     * @Then /^the header "(?P<name>[^"]*)" should not exist$/
     */
    public function theHeaderShouldNotExist($name)
    {
        try {
            $this->getHttpHeader($name);
            $message = sprintf('The header "%s" exist', $name);
            throw new ExpectationException($message, $this->getSession());
        }
        catch (\OutOfBoundsException $e) {
        }
    }

   /**
     * Checks, that the response header expire is in the future
     *
     * @Then /^the response should expire in the future$/
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->getHttpHeader('Date'));
        $expires = new \DateTime($this->getHttpHeader('Expires'));

        $this->assertSame(1, $expires->diff($date)->invert,
            sprintf(sprintf('The response doesn\'t expire in the future (%s)', $expires->format(DATE_ATOM)))
        );
    }

    /**
     * Add an header element in a request
     *
     * @Then /^I add "(?P<name>[^"]*)" header equal to "(?P<value>[^"]*)"$/
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->getSession()->getDriver()->getClient()->setServerParameter($name, $value);
    }

    /**
     * @Then /^the response should be encoded in "(?P<encoding>[^"]*)"$/
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        $content = $this->getSession()->getPage()->getContent();
        if (!mb_check_encoding($content, $encoding)) {
            throw new \Exception("The response is not encoded in $encoding");
        }

        return array(
            new Step\Then('the header "Content-Type" should be contains "charset=' . $encoding . '"'),
        );
    }

    /**
     * @Then /^print last response headers$/
     */
    public function printLastResponseHeaders()
    {
        $text = '';
        $headers = $this->getHttpHeaders();

        foreach ($headers as $name => $value) {
            $text .= $name . ': '. $this->getHttpHeader($name) . "\n";
        }
        $this->printDebug($text);
    }

    private function getHttpHeader($name)
    {
        $name = strtolower($name);
        $header = $this->getHttpHeaders();

        if (isset($header[$name])) {
            if (is_array($header[$name])) {
                $value = $header[$name][0];
            }
            else {
                $value = $header[$name];
            }
        }
        else {
            throw new \OutOfBoundsException(
                sprintf('The header "%s" doesn\'t exist', $name)
            );
        }
        return $value;
    }

    private function getHttpHeaders()
    {
        return array_change_key_case(
            $this->getSession()->getResponseHeaders(),
            CASE_LOWER
        );
    }
}
