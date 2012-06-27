<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

class BehatchContext extends BehatContext
{
    private $parameters;

    public function __construct()
    {
        $this->useContext('browser', new BrowserContext());
        $this->useContext('filesystem', new FileSystemContext());
        $this->useContext('json', new JSONContext());
        $this->useContext('rest', new RESTContext());
        $this->useContext('table', new TableContext());
        $this->useContext('debug', new DebugContext());
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
