<?php

namespace Sanpi\Behatch\Context\Initializer;

use Behat\Behat\Context\Context;
use Sanpi\Behatch\Context\BaseContext;
use Behat\Behat\Context\Initializer\ContextInitializer;

class BehatchAwareInitializer implements ContextInitializer
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function initializeContext(Context $context)
    {
        if (!$context instanceof BaseContext) {
            return;
        }

        $context->setParameters($this->parameters);
    }
}
