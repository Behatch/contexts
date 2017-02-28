<?php

namespace Sanpi\Behatch\HttpCall;

class HttpCallResultPool
{
    /**
     * @var HttpCallResult|null
     */
    private $result;

    /**
     * @param HttpCallResult $result
     */
    public function store(HttpCallResult $result)
    {
        $this->result = $result;
    }

    /**
     * @return HttpCallResult|null
     */
    public function getResult()
    {
        return $this->result;
    }
}
