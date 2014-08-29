<?php

namespace Sanpi\Behatch\HttpCall;

interface FilterableHttpCallResult
{
    public function filter(HttpCallResult $httpCallResult);
}
