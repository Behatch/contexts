<?php

namespace Behatch\HttpCall;

interface FilterableHttpCallResult
{
    public function filter(HttpCallResult $httpCallResult);
}
