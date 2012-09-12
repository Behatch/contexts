<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;

class SystemContext extends BaseContext
{
    /**
     * Uploads a file using the specified input field
     *
     * @When /^(?:|I )put the file "(?P<file>[^"]*)" into "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function putFileIntoField($file, $field)
    {
        $root = $this->getParameter('system', 'root');
        $path = $root . DIRECTORY_SEPARATOR . $file;

        return array(
            new Step\When(sprintf('I attach the file "%s" to "%s"', $path, $field))
        );
    }

    /**
     * Execute a command
     *
     * @Given /^(?:|I )execute "(?P<command>[^"]*)"$/
     */
    public function iExecute($cmd)
    {
        exec($cmd, $output, $return);

        if ($return !== 0) {
            throw new \Exception(sprintf("Command %s returned with status code %s\n%s", $cmd, $return, implode("\n", $output)));
        }
    }

    /**
     * Execute a command from project root
     *
     * @Given /^(?:|I )execute "(?P<command>[^"]*)" from project root$/
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $root = $this->getParameter('system', 'root');
        $cmd = $root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }
}
