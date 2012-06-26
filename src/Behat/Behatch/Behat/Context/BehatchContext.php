<?php

namespace Behat\Behatch\Behat\Context;

use Behat\Mink\Behat\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class BehatchContext extends BehatContext
{
    /**
     * Array for storing custom parameters during steps
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Context initialization
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
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

    /**
     * @param string $name
     * @return string
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
