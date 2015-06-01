<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;
use Sanpi\Behatch\HttpCall\Request;
use Behat\Gherkin\Node\PyStringNode;
use Buzz\Browser;

class RestContext extends BaseContext
{
    public function __construct(Request $request)
    {
        $this->request = $request;

    }
    /**
     * Sends a HTTP request
     * Example: Given I send a GET request to "/heroes/list"
     * Example: When I send a GET request to "/heroes/list"
     * Example: And I send a GET request to "/heroes/list"
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, PyStringNode $body = null)
    {
        return $this->request->send(
            $method,
            $this->locatePath($url),
            [],
            [],
            $body !== null ? $body->getRaw() : null
        );
    }

    /**
     * Sends a HTTP request with a some parameters
     * Example: Given I send a GET request to "/heroes/list" with parameters:
     *          | userId | 27 |
     *          | username | bruceWayne |
     *          | password | iLoveBats123 |
     * Example: When I send a GET request to "/heroes/list" with parameters:
     *          | userId | 27 |
     *          | username | bruceWayne |
     *          | password | iLoveBats123 |
     * Example: And I send a GET request to "/heroes/list" with parameters:
     *          | userId | 27 |
     *          | username | bruceWayne |
     *          | password | iLoveBats123 |
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

        return $this->request->send(
            $method,
            $this->locatePath($url),
            $parameters,
            $files
        );
    }

    /**
     * Sends a HTTP request with a body
     * Example: Given I send a GET request to "/heroes/list" with body:
     *          """
     *          {
     *             {
     *               "body": "I am not batman I take serious offense to any claims suggesting such outlandish remarks.",
     *                "id" : 1
     *             }
     *          }
     *          """
     * Example: When I send a POST request to "/heroes/list" with form data:
     *          """
     *          {
     *             {
     *               "postId": 1,
     *               "id": 1,
     *               "name": "I know who Batman is",
     *               "email": "Eliseo@gardner.biz",
     *             }
     *          }
     *          """
     * Example: And I send a GET request to "/heroes/list" with body:
     *          """
     *          {
     *             {
     *               "body": "I am not batman I take serious offense to any claims suggesting such outlandish remarks.",
     *                "id" : 1
     *             }
     *          }
     *          """
     *
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body)
    {
        $this->iSendARequestTo($method, $url, $body);
    }


    /**
     * Checks, whether the response content is equal to given text
     * Example: Then the response should be equal to
     *          """
     *          {
     *             {
     *               "body": "Bruce Wayne, billionaire playboy.",
     *                "id" : 1
     *             }
     *          }
     *          """
     * Example: And the response should be equal to
     *          """
     *          {
     *             {
     *               "body": "Bruce Wayne, billionaire playboy.",
     *                "id" : 1
     *             }
     *          }
     *          """
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
     * Example: Then the response should be empty
     * Example: And the response should be empty
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
     * Example: Then the header "User-Agent" should be equal to "Batbook, BatBrowser"
     * Example: And the header "User-Agent" should be equal to "Batbook, BatBrowser"
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
     * Checks, whether the header name contains the given text
     * Example: Then the header "Authentication" should contain "1024 Bit Super-Authenticated"
     * Example: And the header "Authentication" should contain "1024 Bit Super-Authenticated"
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
     * Example: Then the header "" should contain "1024 Bit Super-Authenticated"
     * Example: And the header "Authentication" should contain "1024 Bit Super-Authenticated"
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
     * Example: Then the header "Content-Type" should not exist
     * Example: And the header "Content-Type" should not exist
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
     * Example: Then the response should expire in the future
     * Example: And the response should expire in the future
     *
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->request->getHttpHeader('Date'));
        $expires = new \DateTime($this->request->getHttpHeader('Expires'));

        $this->assertSame(1, $expires->diff($date)->invert,
            sprintf('The response doesn\'t expire in the future (%s)', $expires->format(DATE_ATOM))
        );
    }

    /**
     * Add an header element in a request
     * Example: Then I add "Content-Type" header equal to "application/json"
     * Example: And I add "Content-Type" header equal to "application/json"
     *
     * @Then I add :name header equal to :value
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->request->setHttpHeader($name, $value);
    }

    /**
     * Asserts against responses encoding type, see http://www.iana.org/assignments/character-sets/character-sets.xhtml
     * Example: Then the response should be encoded in "UTF-8"
     * Example: And the response should be encoded in "UTF-8"
     *
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
     * Prints last response
     * Example: Then print last response headers
     *
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
     * Prints the curl equivalent to the request
     * Example: Then print the corresponding curl command
     *
     * @Then print the corresponding curl command
     */
    public function printTheCorrespondingCurlCommand()
    {
        $request = $this->request->getRequest();

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
}
