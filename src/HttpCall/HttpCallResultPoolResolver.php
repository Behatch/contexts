<?php

namespace Behatch\HttpCall;

use Behat\Behat\Context\Argument\ArgumentResolver;
use ReflectionClass;

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
                $class = PHP_VERSION_ID < 80000 ? $parameter->getClass() : ($parameter->getType() && !$parameter->getType()->isBuiltin()
                    ? new ReflectionClass($parameter->getType()->getName())
                    : null
                );
                if (null !== $class && isset($this->dependencies[$class->name])) {
                    $arguments[$parameter->name] = $this->dependencies[$class->name];
                }
            }
        }
        return $arguments;
    }
}
