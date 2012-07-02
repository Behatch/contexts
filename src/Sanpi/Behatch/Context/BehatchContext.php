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

    public function getParameter($extension, $name)
    {
        return $this->parameters["behatch.$extension.$name"];
    }

    public function hasParameter($extension, $name)
    {
        return isset($this->parameters["behatch.$extension.$name"]);
    }

    public function setParameter($extenison, $name, $value)
    {
        $this->parameters["behatch.$extension.$name"] = $value;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
