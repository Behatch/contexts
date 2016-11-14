<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Mink\Mink;

class Request
{
    private $mink;
    private $client;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getClient(), $name], $arguments);
    }

    private function getClient()
    {
        if (null === $this->client) {
            if ('symfony2' === $this->mink->getDefaultSessionName()) {
                $this->client = new Request\Goutte($this->mink);
            } else {
                $this->client = new Request\BrowserKit($this->mink);
            }
        }

        return $this->client;
    }
}
