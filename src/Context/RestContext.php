<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;

class RestContext extends BaseContext
{
    private $response;
    private $options = [];
    private $headers = [];

    /**
     * Sends a HTTP request
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, PyStringNode $body = null)
    {

        $this->request = new \GuzzleHttp\Psr7\Request(
            $method,
            $this->locatePath($url),
            $this->headers,
            $body !== null ? $body->getRaw() : []
        );

        $http = new \GuzzleHttp\Client();
        $this->response = $http->send($this->request);
        $this->headers = [];
        $this->options = [];

        return $this->response;
    }

    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, TableNode $datas)
    {
        $parameters = [];

        foreach ($datas->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }

            if (is_string($row['value']) && substr($row['value'], 0, 1) == '@') {
                $filename = substr($row['value'], 1);
                $parameters[] = [
                    'name' => $row['key'],
                    'filename' => $filename,
                    'contents' => file_get_contents($this->getMinkParameter('files_path') . '/' . $filename),
                ];
            }
            else {
                $parameters[] = [
                    'name' => $row['key'],
                    'contents' =>  $row['value'],
                ];
            }
        }

        $this->request = new \GuzzleHttp\Psr7\Request(
            $method,
            $this->locatePath($url)
        );

        $http = new \GuzzleHttp\Client();
        $this->response = $http->send($this->request, ['multipart' => $parameters]);
        $this->headers = [];
        $this->options = $parameters;

        return $this->response;
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
        $actual   = $this->response->getBody();
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
        $actual = (string)$this->response->getBody();
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
        $actual = $this->response->getHeaderLine($name);
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
        $actual = $this->response->getHeaderLine($name);
        $this->assertContains($value, $actual,
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
        $actual = $this->response->getHeaderLine($name);
        $this->assertNotContains($value, $actual,
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
        $this->assert(
            $this->response->hasHeader($name),
            "The header '$name' not exists"
        );
    }

   /**
     * Checks, that the response header expire is in the future
     *
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->response->getHeaderLine('Date'));
        $expires = new \DateTime($this->response->getHeaderLine('Expires'));

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
        $this->headers[$name] = $value;
    }

    /**
     * @Then the response should be encoded in :encoding
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        $content = $this->response->getBody();
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
        $headers = $this->response->getHeaders();

        foreach ($headers as $name => $value) {
            $text .= $name . ': '. $this->response->getHeaderLine($name) . "\n";
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
        foreach ($this->request->getHeaders() as $name => $values) {
            if (substr($name, 0, 5) !== 'HTTP_' && $name !== 'HTTPS') {
                foreach ($values as $value) {
                    $headers .= " -H '$name: $value'";
                }
            }
        }

        $data = '';
        if (!empty($this->options)) {
            foreach ($this->options as $option) {
                $data .= " -d '{$option['name']}={$option['contents']}'";
            }
        }

        echo "curl -X $method$data$headers '$url'";
    }
}
