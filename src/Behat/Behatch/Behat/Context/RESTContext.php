<?php

namespace Behat\Behatch\Behat\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\TranslatedContextInterface;
use PHPUnit_Framework_ExpectationFailedException as AssertException;

/**
 * This context is intended for Browser interractions
 */
class RESTContext extends BehatContext implements TranslatedContextInterface
{
    /**
     * Shortcut for retrieving Mink context
     *
     * @return \Behat\Mink\Behat\Context\MinkContext
     */
    public function getMinkContext()
    {
        return $this->getMainContext()->getSubContext('mink');
    }

    /**
     * Sends a HTTP request
     *
     * @Given /^I send a (GET|POST|PUT|DELETE|OPTION) request on "([^"]*)"$/
     */
    public function iSendARequestOn($method, $url)
    {
        $client = $this->getMinkContext()->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $client->request($method, $this->getMinkContext()->locatePath($url));
        $client->followRedirects(true);
    }


    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given /^I send a (GET|POST|PUT|DELETE|OPTION) request on "([^"]*)" with parameters:$/
     */
    public function iSendARequestOnWithParameters($method, $url, TableNode $datas)
    {
        $client = $this->getMinkContext()->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $parameters = array();
        foreach ($datas->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }
            $parameters[$row['key']] = $row['value'];
        }

        $client->request($method, $this->getMinkContext()->locatePath($url), $parameters);
        $client->followRedirects(true);
    }

    /**
     * Sends a HTTP request with a body
     *
     * @When /^I send a (GET|POST|PUT|DELETE|OPTION) request on "([^"]*)" with body:$/
     */
    public function iSendARequestOnWithBody($method, $url, PyStringNode $body)
    {
        $client = $this->getMinkContext()->getSession()->getDriver()->getClient();

        // intercept redirection
        $client->followRedirects(false);

        $client->request($method, $this->getMinkContext()->locatePath($url),
            array(), array(), array(), $body->getRaw());
        $client->followRedirects(true);
    }

    /**
     * Checks, whether the response content is equal to given text
     *
     * @Given /^the response should be equal to:$/
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $this->getMinkContext()->getSession()->getPage()->getContent();

        try {
            assertEquals($expected, $actual);
        } catch (AssertException $e) {
            $message = sprintf('The string "%s" is not equal to the response of the current page', $expected);
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getMinkContext()->getSession(), $e);
        }
    }

    /**
     * Checks, whether the header name is equal to given text
     *
     * @Given /^the header "([^"]*)" should be equal to "([^"]*)"$/
     */
    public function theHeaderShouldBeEqualTo($name, $expected)
    {
        $header = $this->getMinkContext()->getSession()->getResponseHeaders();

        try {
            if (!isset($header[$name])) {
                throw new \Exception(sprintf('The "%s" header do not exist'));
            }
            assertEquals($expected, $header[$name]);
        } catch (AssertException $e) {
            $message = sprintf('The header "%s" is not equal to "%s"', $name, $expected);
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getMinkContext()->getSession(), $e);
        }
    }
    /**
     * Checks, whether the header name contains the given text
     *
     * @Given /^the header "([^"]*)" should be contains "([^"]*)"$/
     */
    public function theHeaderShouldBeContains($name, $expected)
    {
        $header = $this->getMinkContext()->getSession()->getResponseHeaders();

        assertArrayHasKey($name, $header,
            sprintf('The header "%s" doesn\'t exist', $name)
        );
        assertContains($expected, $header[$name]);
    }

    /**
     * Add an header element in a request
     *
     * @Given /^I add "([^"]*)" header equal to "([^"]*)"$/
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->getMinkContext()->getSession()->getDriver()->getClient()->setServerParameter($name, $value);
    }

    /**
     * Returns list of definition translation resources paths.
     *
     * @return array
     */
    public function getTranslationResources()
    {
        return glob(__DIR__.'/../../../../../i18n/*.xliff');

    }
}
