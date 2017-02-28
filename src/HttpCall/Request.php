<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Mink\Mink;

class Request
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * Request constructor.
     * @param Mink $mink
     */
    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getClient(), $name], $arguments);
    }

    /**
     * @return Request\BrowserKit
     */
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
