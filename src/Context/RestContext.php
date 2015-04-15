<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;

class RestContext extends BaseContext
{
    /**
     * headers are no more stored on client, because client does not flush them when reset/restart session.
     * They are on Behat\Mink\Driver\BrowserKitDriver and there is no way to get them.
     *
     * @var array
     */
    private $requestHeaders = [];

    /**
     * Sends a HTTP request
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, PyStringNode $body = null)
    {
        return $this->request(
            $method,
            $url,
            [],
            [],
            $this->requestHeaders,
            $body !== null ? $body->getRaw() : null
        );
    }

    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, TableNode $datas)
    {
        $files = [];
        $parameters = [];

        foreach ($datas->getHash() as $row) {
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

        return $this->request(
            $method,
            $url,
            $parameters,
            $files,
            $this->requestHeaders
        );
    }

    /**
     * Sends a HTTP request with a body
     *
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body)
    {
        $this->iSendARequestTo($method, $url, $body);
    }

    /**
     * Checks, whether the response content is equal to given text
     *
     * @Then the response should be equal to
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $this->getSession()->getPage()->getContent();
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
        $actual = $this->getSession()->getPage()->getContent();
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
        $actual = $this->getHttpHeader($name);
        $this->assertEquals(strtolower($value), strtolower($actual),
            "The header '$name' is equal to '$actual'"
        );
    }

    /**
     * Checks, whether the header name contains the given text
     *
     * @Then the header :name should contain :value
     */
    public function theHeaderShouldBeContains($name, $value)
    {
        $this->assertContains($value, $this->getHttpHeader($name),
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
        $this->assertNotContains($value, $this->getHttpHeader($name),
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
        return $this->getHttpHeader($name);
    }

   /**
     * Checks, that the response header expire is in the future
     *
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->getHttpHeader('Date'));
        $expires = new \DateTime($this->getHttpHeader('Expires'));

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
        $this->requestHeaders[$name] = $value;

        $client = $this->getSession()->getDriver()->getClient();
        if (method_exists($client, 'setHeader')) {
            $client->setHeader($name, $value);
        }
    }

    /**
     * @Then the response should be encoded in :encoding
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        $content = $this->getSession()->getPage()->getContent();
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
        $headers = $this->getHttpHeaders();

        foreach ($headers as $name => $value) {
            $text .= $name . ': '. $this->getHttpHeader($name) . "\n";
        }
        echo $text;
    }


    /**
     * @Then print the corresponding curl command
     */
    public function printTheCorrespondingCurlCommand()
    {
        $request = $this->getSession()->getDriver()->getClient()->getRequest();

        $method = $request->getMethod();
        $url = $request->getUri();

        $headers = '';
        foreach ($request->getServer() as $name => $value) {
            if (substr($name, 0, 5) !== 'HTTP_' && $name !== 'HTTPS') {
                $headers .= " -H '$name: $value'";
            }
        }

        $data = '';
        $params = $request->getParameters();
        if (!empty($params)) {
            $query = http_build_query($params);
            $data = " --data '$query'" ;
        }

        echo "curl -X $method$data$headers '$url'";
    }

    private function getHttpHeader($name)
    {
        $name = strtolower($name);
        $header = $this->getHttpHeaders();

        if (isset($header[$name])) {
            if (is_array($header[$name])) {
                $value = implode(', ', $header[$name]);
            }
            else {
                $value = $header[$name];
            }
        }
        else {
            throw new \OutOfBoundsException(
                "The header '$name' doesn't exist"
            );
        }
        return $value;
    }

    private function request($method, $url, $parameters = [], $files = [], $headers = [], $content = null)
    {
        $client = $this->getSession()->getDriver()->getClient();

        $client->followRedirects(false);
        $client->request($method, $this->locatePath($url), $parameters, $files, $headers, $content);
        $client->followRedirects(true);

        $this->resetHttpHeaders();

        return $this->getSession()->getPage();
    }

    private function getHttpHeaders()
    {
        return array_change_key_case(
            $this->getSession()->getResponseHeaders(),
            CASE_LOWER
        );
    }

    private function resetHttpHeaders()
    {
        $this->requestHeaders = [];
    }
}
