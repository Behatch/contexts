<?php

namespace Behatch\Context;

use Behat\Mink\Exception\ExpectationException;
use Behatch\HttpCall\Request;
use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;

class RestContext extends BaseContext
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Sends a HTTP request
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, PyStringNode $body = null, $files = [])
    {
        return $this->request->send(
            $method,
            $this->locatePath($url),
            [],
            $files,
            $body !== null ? $body->getRaw() : null
        );
    }

    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, TableNode $data)
    {
        $files = [];
        $parameters = [];

        foreach ($data->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }

            if (is_string($row['value']) && substr($row['value'], 0, 1) == '@') {
                $files[$row['key']] = rtrim($this->getMinkParameter('files_path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.substr($row['value'],1);
            }
            else {
                $parameters[] = sprintf('%s=%s', $row['key'], $row['value']);
            }
        }

        parse_str(implode('&', $parameters), $parameters);

        return $this->request->send(
            $method,
            $this->locatePath($url),
            $parameters,
            $files
        );
    }

    /**
     * Sends a HTTP request with a body
     *
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body)
    {
        return $this->iSendARequestTo($method, $url, $body);
    }

    /**
     * Checks, whether the response content is equal to given text
     *
     * @Then the response should be equal to
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $this->request->getContent();
        $message = "The string '$expected' is not equal to the response of the current page";
        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Checks, whether the response content is null or empty string
     *
     * @Then the response should be empty
     */
    public function theResponseShouldBeEmpty()
    {
        $actual = $this->request->getContent();
        $message = 'The response of the current page is not empty';
        $this->assertTrue(null === $actual || "" === $actual, $message);
    }

    /**
     * Checks, whether the header name is equal to given text
     *
     * @Then the header :name should be equal to :value
     */
    public function theHeaderShouldBeEqualTo($name, $value)
    {
        $actual = $this->request->getHttpHeader($name);
        $this->assertEquals(strtolower($value), strtolower($actual),
            "The header '$name' is equal to '$actual'"
        );
    }

    /**
    * Checks, whether the header name is not equal to given text
    *
    * @Then the header :name should not be equal to :value
    */
    public function theHeaderShouldNotBeEqualTo($name, $value) {
        $actual = $this->getSession()->getResponseHeader($name);
        if (strtolower($value) == strtolower($actual)) {
            throw new ExpectationException(
                "The header '$name' is equal to '$actual'",
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * Checks, whether the header name contains the given text
     *
     * @Then the header :name should contain :value
     */
    public function theHeaderShouldBeContains($name, $value)
    {
        $this->assertContains($value, $this->request->getHttpHeader($name),
            "The header '$name' doesn't contain '$value'"
        );
    }

    /**
     * Checks, whether the header name doesn't contain the given text
     *
     * @Then the header :name should not contain :value
     */
    public function theHeaderShouldNotContain($name, $value)
    {
        $this->assertNotContains($value, $this->request->getHttpHeader($name),
            "The header '$name' contains '$value'"
        );
    }

    /**
     * Checks, whether the header not exist
     *
     * @Then the header :name should not exist
     */
    public function theHeaderShouldNotExist($name)
    {
        $this->not(function () use($name) {
            $this->theHeaderShouldExist($name);
        }, "The header '$name' exists");
    }

    protected function theHeaderShouldExist($name)
    {
        return $this->request->getHttpHeader($name);
    }

   /**
     * Checks, that the response header expire is in the future
     *
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->request->getHttpRawHeader('Date')[0]);
        $expires = new \DateTime($this->request->getHttpRawHeader('Expires')[0]);

        $this->assertSame(1, $expires->diff($date)->invert,
            sprintf('The response doesn\'t expire in the future (%s)', $expires->format(DATE_ATOM))
        );
    }

    /**
     * Add an header element in a request
     *
     * @Then I add :name header equal to :value
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->request->setHttpHeader($name, $value);
    }

    /**
     * @Then the response should be encoded in :encoding
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        $content = $this->request->getContent();
        if (!mb_check_encoding($content, $encoding)) {
            throw new \Exception("The response is not encoded in $encoding");
        }

        $this->theHeaderShouldBeContains('Content-Type', "charset=$encoding");
    }

    /**
     * @Then print last response headers
     */
    public function printLastResponseHeaders()
    {
        $text = '';
        $headers = $this->request->getHttpHeaders();

        foreach ($headers as $name => $value) {
            $text .= $name . ': '. $this->request->getHttpHeader($name) . "\n";
        }
        echo $text;
    }


    /**
     * @Then print the corresponding curl command
     */
    public function printTheCorrespondingCurlCommand()
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUri();

        $headers = '';
        foreach ($this->request->getServer() as $name => $value) {
            if (substr($name, 0, 5) !== 'HTTP_' && $name !== 'HTTPS') {
                $headers .= " -H '$name: $value'";
            }
        }

        $data = '';
        $params = $this->request->getParameters();
        if (!empty($params)) {
            $query = http_build_query($params);
            $data = " --data '$query'" ;
        }

        echo "curl -X $method$data$headers '$url'";
    }
}
