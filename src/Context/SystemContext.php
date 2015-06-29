<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

class SystemContext implements Context
{
    private $root;
    private $output;
    private $lastExecutionTime;
    private $createdFiles = [];

    public function __construct($root = '.')
    {
        $this->root = $root;
    }

    public static function getTranslationResources()
    {
        return glob(__DIR__ . '/../../i18n/*.xliff');
    }

    /**
     * Uploads a file using the specified input field
     *
     * @When (I )put the file :file into :field
     */
    public function putFileIntoField($file, $field)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $file;

        return [
            new Step\When("I attach the file '$path' to '$field'")
        ];
    }

    /**
     * Execute a command
     *
     * @Given (I )execute :command
     */
    public function iExecute($cmd)
    {
        $start = microtime(true);

        exec($cmd, $this->output, $return);

        $this->lastExecutionTime = microtime(true) - $start;

        if ($return !== 0) {
            throw new \Exception(sprintf("Command %s returned with status code %s\n%s", $cmd, $return, implode("\n", $this->output)));
        }
    }

    /**
     * Execute a command from project root
     *
     * @Given (I )execute :command from project root
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $cmd = $this->root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }

    /**
     * Command should last less than
     *
     * @Then command should last less than :seconds seconds
     */
    public function commandShouldLastLessThan($seconds)
    {
        if ($this->lastExecutionTime > $seconds) {
            throw new \Exception(sprintf("Last command last %s which is more than %s seconds", $lastExecutionTime, $seconds));
        }
    }

    /**
     * Command should last more than
     *
     * @Then command should last more than :seconds seconds
     */
    public function commandShouldMoreLessThan($seconds)
    {
        if ($this->lastExecutionTime < $seconds) {
            throw new \Exception(sprintf("Last command last %s which is less than %s seconds", $lastExecutionTime, $seconds));
        }
    }

    /**
     * Checks, that output contains specified text.
     *
     * @Then (I )should see on output ":text"
     */
    public function iShouldSeeOnOutput($text)
    {
        $regex = '/'.preg_quote($text, '/').'/ui';

        $check = false;
        foreach ($this->output as $line) {
            if (preg_match($regex, $line) === 1) {
                $check = true;
                break;
            }
        }

        if ($check === false) {
            throw new \Exception(sprintf("The text '%s' was not found anywhere on output of command.\n%s", $text, implode("\n", $this->output)));
        }
    }

    /**
     * Checks, that output not contains specified text.
     *
     * @Then (I )should not see on output ":text"
     */
    public function iShouldNotSeeOnOutput($text)
    {
        $regex = '/'.preg_quote($text, '/').'/ui';

        foreach ($this->output as $line) {
            if (preg_match($regex, $line) === 1) {
                throw new \Exception(sprintf("The text '%s' was found somewhere on output of command.\n%s", $text, implode("\n", $this->output)));
            }
        }
    }

    /**
     * @Given (I )create the file :filename containing:
     * @Given (I )create the file :filename contening:
     */
    public function iCreateTheFileContaining($filename, PyStringNode $string)
    {
        if (!is_file($filename)) {
            file_put_contents($filename, $string);
            $this->createdFiles[] = $filename;
        }
        else {
            throw new \RuntimeException("'$filename' already exists.");
        }
    }

    /**
     * @Then print the content of :filename file
     */
    public function printTheContentOfFile($filename)
    {
        if (is_file($filename)) {
            echo file_get_contents($filename);
        }
        else {
            throw new \RuntimeException("'$filename' doesn't exists.");
        }
    }

    /**
     * @AfterScenario
     */
    public function after()
    {
        foreach ($this->createdFiles as $filename) {
            unlink($filename);
        }
    }
}
