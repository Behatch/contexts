<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Mink\Mink;
use Sanpi\Behatch\HttpCall\Request\BrowserKit;
use Sanpi\Behatch\HttpCall\Request\Goutte;

class Request
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var Goutte|BrowserKit
     */
    private $client;

    /**
     * @param Mink $mink
     */
    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getClient(), $name], $arguments);
    }

    /**
     * @return BrowserKit|Goutte
     */
    private function getClient()
    {
        if (is_null($this->client)) {
            if ($this->mink->getDefaultSessionName() === 'symfony2') {
                $this->client = new Goutte($this->mink);
            } else {
                $this->client = new BrowserKit($this->mink);
            }
        }

        return $this->client;
    }
}
