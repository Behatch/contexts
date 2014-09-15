<?php

namespace Sanpi\Behatch\HttpCall;

use Behat\Behat\Context\Environment\Handler\ContextEnvironmentHandler;
use Behat\Behat\Context\Argument\ArgumentResolver;

use Sanpi\Behatch\HttpCall\HttpCallResultPool;

class HttpCallResultPoolResolver implements ArgumentResolver
{
    private $httpCallResultPool;

    public function __construct(HttpCallResultPool $httpCallResultPool)
    {
        $this->httpCallResultPool = $httpCallResultPool;
    }

    public function resolveArguments(\ReflectionClass $classReflection, array $arguments)
    {
        $constructor = $classReflection->getConstructor();
        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                if (null !== $parameter->getClass() && $parameter->getClass()->name === 'Sanpi\\Behatch\\HttpCall\\HttpCallResultPool') {
                    $arguments[$parameter->name] = $this->httpCallResultPool;
                }
            }
        }
        return $arguments;
    }
}
