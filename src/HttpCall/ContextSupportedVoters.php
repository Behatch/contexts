<?php

namespace Behatch\HttpCall;

class ContextSupportedVoters implements ContextSupportedVoter
{
    private $voters;

    public function __construct(array $voters = array())
    {
        foreach ($voters as $voter) {
            $this->register($voter);
        }
    }

    public function register(ContextSupportedVoter $voter)
    {
        $this->voters[] = $voter;
    }

    public function vote(HttpCallResult $httpCallResult)
    {
        foreach ($this->voters as $voter) {
            if ($voter->vote($httpCallResult)) {
                if ($voter instanceof FilterableHttpCallResult) {
                    $httpCallResult->update($voter->filter($httpCallResult));
                }

                return true;
            }
        }

        return false;
    }
}
