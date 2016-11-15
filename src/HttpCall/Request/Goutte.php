<?php

namespace Sanpi\Behatch\HttpCall\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        foreach ($files as $originalName => &$file) {
            $file = new UploadedFile($file, $originalName);
        }
        $page = parent::send($method, $url, $parameters, $files, $content, array_merge($headers, $this->requestHeaders));

        return $page;
    }

    public function getServer()
    {
        return $this->getRequest()
            ->server->all();
    }

    public function getParameters()
    {
        return $this->getRequest()
            ->query->all();
    }

    public function setHttpHeader($name, $value)
    {
        $name = strtoupper("http_$name");
        $this->requestHeaders[$name] = $value;
    }

    protected function resetHttpHeaders()
    {
        $this->requestHeaders = [];
    }
}
