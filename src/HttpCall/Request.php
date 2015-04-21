<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Mink\Mink;

class Request
{
    private $mink;

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
        if ($this->mink->getDefaultSessionName() === 'symfony2') {
            return new Request\Goutte($this->mink);
        }
        else {
            return new Request\BrowserKit($this->mink);
        }
    }
}
