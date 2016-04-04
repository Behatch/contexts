<?php

namespace Behatch\HttpCall;

use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Mink\Mink;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpCallListener implements EventSubscriberInterface
{
    private $contextSupportedVoter;

    private $httpCallResultPool;

    private $mink;

    public function __construct(ContextSupportedVoter $contextSupportedVoter, HttpCallResultPool $httpCallResultPool, Mink $mink)
    {
        $this->contextSupportedVoter = $contextSupportedVoter;
        $this->httpCallResultPool = $httpCallResultPool;
        $this->mink = $mink;
    }

    public static function getSubscribedEvents()
    {
        return [
           StepTested::AFTER => 'afterStep'
        ];
    }

    public function afterStep(AfterStepTested $event)
    {
        $testResult = $event->getTestResult();

        if (!$testResult instanceof ExecutedStepResult) {
            return;
        }

        $httpCallResult = new HttpCallResult(
            $testResult->getCallResult()->getReturn()
        );

        if ($this->contextSupportedVoter->vote($httpCallResult)) {
            $this->httpCallResultPool->store($httpCallResult);

            return true;
        }

        // For now to avoid modification on MinkContext
        // We add fallback on Mink
        try {
            $this->httpCallResultPool->store(
                new HttpCallResult($this->mink->getSession()->getPage()->getContent())
            );
        } catch (\LogicException $e) {
            // Mink has no response
        } catch (\Behat\Mink\Exception\DriverException $e) {
            // No Mink
        }
    }
}
