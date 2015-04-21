<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Mink\Mink;

class Request
{
    /**
     * headers are no more stored on client, because client does not flush them when reset/restart session.
     * They are on Behat\Mink\Driver\BrowserKitDriver and there is no way to get them.
     *
     * @var array
     */
    private $requestHeaders = [];
    private $mink;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function getRequest()
    {
        return $this->mink->getSession()->getDriver()->getClient()->getRequest();
    }

    public function getContent()
    {
        return $this->mink->getSession()->getPage()->getContent();
    }

    public function send($method, $url, $parameters = [], $files = [], $content = null)
    {
        $client = $this->mink->getSession()->getDriver()->getClient();

        $client->followRedirects(false);
        $client->request($method, $url, $parameters, $files, $this->requestHeaders, $content);
        $client->followRedirects(true);

        $this->resetHttpHeaders();

        return $this->mink->getSession()->getPage();
    }

    public function setHttpHeader($name, $value)
    {
        $this->requestHeaders[$name] = $value;

        $client = $this->mink->getSession()->getDriver()->getClient();
        $client->setHeader($name, $value);
    }

    public function getHttpHeaders()
    {
        return array_change_key_case(
            $this->mink->getSession()->getResponseHeaders(),
            CASE_LOWER
        );
    }

    public function getHttpHeader($name)
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

    private function resetHttpHeaders()
    {
        $this->requestHeaders = [];
    }
}
