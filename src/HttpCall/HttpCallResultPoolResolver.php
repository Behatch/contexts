<?php

namespace Behatch\HttpCall;

use Behat\Behat\Context\Argument\ArgumentResolver;

class HttpCallResultPoolResolver implements ArgumentResolver
{
    private $dependencies;

    public function __construct(/* ... */)
    {
        $this->dependencies = [];

        foreach (func_get_args() as $param) {
            $this->dependencies[get_class($param)] = $param;
        }
    }

    public function resolveArguments(\ReflectionClass $classReflection, array $arguments)
    {
        $constructor = $classReflection->getConstructor();
        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                if (
                    null !== $parameter->getClass()
                    && isset($this->dependencies[$parameter->getClass()->name])
                ) {
                    $arguments[$parameter->name] = $this->dependencies[$parameter->getClass()->name];
                }
            }
        }
        return $arguments;
    }
}
