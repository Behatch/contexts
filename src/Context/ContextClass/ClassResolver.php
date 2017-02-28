<?php

namespace Behatch\Context\ContextClass;

use Behat\Behat\Context\ContextClass\ClassResolver as BaseClassResolver;

class ClassResolver implements BaseClassResolver
{
    public function supportsClass($contextClass)
    {
        return (strpos($contextClass, 'behatch:') === 0);
    }

    public function resolveClass($contextClass)
    {
        list(, $className) = explode(':', $contextClass);

        $className = ucfirst($className);
        return "\\Sanpi\\Behatch\\Context\\{$className}Context";
    }
}
