<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;

class BehatchContext extends BehatContext
{
    private $parameters;

    public function getParameter($extension, $name)
    {
        return $this->parameters[$extension][$name];
    }

    public function hasParameter($extension, $name)
    {
        return isset($this->parameters[$extension][$name]);
    }

    public function setParameter($extension, $name, $value)
    {
        $this->parameters[$extension][$name] = $value;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters['contexts'];

        foreach ($this->parameters as $name => $values) {
            $className = __NAMESPACE__ . '\\' . ucfirst($name) . 'Context';
            $this->useContext($name, new $className());
        }
    }
}
