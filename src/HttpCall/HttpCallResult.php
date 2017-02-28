<?php

namespace Behatch\HttpCall;

class HttpCallResult
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function update($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
