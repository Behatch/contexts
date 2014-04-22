<?php

namespace Sanpi\Behatch\Context\Initializer;

use Sanpi\Behatch\Context\BehatchContext;
use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Context\Initializer\InitializerInterface;

class BehatchAwareInitializer implements InitializerInterface
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function supports(ContextInterface $context)
    {
        return ($context instanceof BehatchContext);
    }

    public function initialize(ContextInterface $context)
    {
        $context->setParameters($this->parameters);
    }
}
