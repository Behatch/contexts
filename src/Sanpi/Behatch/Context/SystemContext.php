<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;

class SystemContext extends BaseContext
{
    /**
     * @When /^(?:|I )put the file "(?P<path>[^"]*)" into "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function putFileIntoField($path, $field)
    {
        $root = $this->getParameter('behatch.system.root');
        $path = $root . DIRECTORY_SEPARATOR . $path;

        return array(
            new Step\When(sprintf('I attach the file "%s" to "%s"', $path, $field))
        );
    }

    /**
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
     * @Given /^(?:|I )execute "(?P<command>[^"]*)" from project root$/
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $root = $this->getParameter('behatch.system.root');
        $cmd = $root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }
}
