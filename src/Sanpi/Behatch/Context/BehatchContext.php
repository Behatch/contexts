<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

class BehatchContext extends BehatContext
{
    public function __construct($parameters)
    {
        $contexts = array('browser', 'debug', 'json', 'rest', 'system',
            'table', 'xml');

        foreach ($contexts as $context) {
            $className = __NAMESPACE__ . '\\' . ucfirst($context) . 'Context';
            $this->useContext($context, new $className($parameters));
        }
    }

    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
