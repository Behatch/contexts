<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;

class FileSystemContext extends BaseContext
{
    private $root;

    public function __construct(array $parameters)
    {
        $this->root = isset($parameters["filesystem"]['root']) ? $parameters["filesystem"]['root'] : null;
    }

    /**
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
     * @Given /^(?:|I )execute "([^"]*)"$/
     */
    public function iExecute($cmd)
    {
        exec($cmd, $output, $return);

        if ($return == 1) {
            throw new \Exception(sprintf("Command %s returned with status code %s\n%s", $cmd, $return, implode("\n", $output)));
        }
    }

    /**
     * @Given /^(?:|I )execute "([^"]*)" from project root$/
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $cmd = $this->root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }
}
