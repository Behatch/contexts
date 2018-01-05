<?php

namespace Behatch\HttpCall\Request;

class Goutte extends BrowserKit
{
    /**
     * headers are no more stored on client, because client does not flush them when reset/restart session.
     * They are on Behat\Mink\Driver\BrowserKitDriver and there is no way to get them.
     *
     * @var array
     */
    private $requestHeaders = [];

    public function send($method, $url, $parameters = [], $files = [], $content = null, $headers = [])
    {
        $page = parent::send($method, $url, $parameters, $files, $content, array_merge($headers, $this->requestHeaders));
        $this->resetHttpHeaders();

        return $page;
    }

    public function setHttpHeader($name, $value)
    {
        $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];
        $name = str_replace('-', '_', strtoupper($name));

        // CONTENT_* are not prefixed with HTTP_ in PHP when building $_SERVER
        if (!isset($contentHeaders[$name])) {
            $name = 'HTTP_' . $name;
        }
        
        $this->requestHeaders[$name] = $value;
    }

    protected function resetHttpHeaders()
    {
        $this->requestHeaders = [];
    }
}
