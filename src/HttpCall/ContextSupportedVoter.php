<?php

namespace Behatch\HttpCall;

interface ContextSupportedVoter
{
    public function vote(HttpCallResult $httpCallResult);
}
