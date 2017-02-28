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
        if (strpos($contextClass, 'behatch:context:') === false) {
            list(, $className) = explode(':', $contextClass);

            $className = ucfirst($className);

            @trigger_error(
                sprintf(
                    'Deprecated context alias use behatch:context:%s instead',
                    strtolower($className)
                ),
                E_USER_DEPRECATED
            );

            return "\\Sanpi\\Behatch\\Context\\{$className}Context";
        } else {
            $className = preg_replace_callback('/(^\w|:\w)/', function ($matches) {
                return str_replace(':', '\\', strtoupper($matches[0]));
            }, $contextClass);

            return $className . 'Context';
        }
    }
}
