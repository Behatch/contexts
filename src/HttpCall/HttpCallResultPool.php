<?php

namespace Sanpi\Behatch\HttpCall;

class HttpCallResultPool
{
    private $result;

    public function store(HttpCallResult $result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
