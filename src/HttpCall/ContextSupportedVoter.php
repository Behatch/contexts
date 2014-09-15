<?php

namespace Sanpi\Behatch\HttpCall;

interface ContextSupportedVoter
{
    public function vote(HttpCallResult $httpCallResult);
}
