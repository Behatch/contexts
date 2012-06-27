<?php

namespace Behat\Behatch\Context;

use Behat\Behat\Context\Step;

/**
 * This context is intended for file system interractions
 */
class FileSystemContext extends BaseContext
{
    /**
     * Root directory
     *
     * @var string
     */
    private $root;

    /**
     * Context initialization
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->root = isset($parameters["filesystem"]['root']) ? $parameters["filesystem"]['root'] : null;
    }

    /**
     * Uploads a file using the specified input field
     *
     * @When /^(?:|I )put the file "(?P<path>[^"]*)" into "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function putFileIntoField($path, $field)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($path)) {
            throw new \Exception(sprintf("The %s file does not exist", $path));
        }

        return array(
            new Step\When(sprintf('I attach the file "%s" to "%s"', $path, $field))
        );
    }

    /**
     * Execute a command
     *
     * @Given /^(?:|I )execute "([^"]*)"$/
     */
    public function iExecute($cmd)
    {
        //execution de la commande
        exec($cmd, $output, $return);

        if ($return == 1) {
            throw new \Exception(sprintf("Command %s returned with status code %s\n%s", $cmd, $return, implode("\n", $output)));
        }
    }

    /**
     * Execute a command from project root
     *
     * @Given /^(?:|I )execute "([^"]*)" from project root$/
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $cmd = $this->root . DIRECTORY_SEPARATOR . $cmd;
        //execution de la commande
        $this->iExecute($cmd);
    }
}
