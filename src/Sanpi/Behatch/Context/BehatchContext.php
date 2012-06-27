<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

class BehatchContext extends BehatContext
{
    private $parameters = array();

    public function __construct(array $parameters)
    {
        $this->useContext('mink', new MinkContext($parameters));
        $this->useContext('browser', new BrowserContext($parameters));
        $this->useContext('filesystem', new FileSystemContext($parameters));
        $this->useContext('json', new JSONContext($parameters));
        $this->useContext('rest', new RESTContext($parameters));
        $this->useContext('table', new TableContext($parameters));
        $this->useContext('debug', new DebugContext($parameters));
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
}
