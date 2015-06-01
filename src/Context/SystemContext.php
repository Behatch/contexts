<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

class SystemContext implements Context
{
    private $root;
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
     * Changes directory to :directory requested
     * Example: Given I am in "/Users/bWayne/secretfiles/batman"
     * Example: And I am in "/Users/bWayne/secretfiles/batman"
     *
     * @Given I am in :dir directory
     */
    public function iAmInDirectory($dir)
    {
        //if (!file_exists($dir)) {
        //    mkdir($dir);
        //}
        chdir($dir);
    }

    /**
     * Runs a command line argument
     * Example: When I run ".openCaveEntrance"
     * Example: And I run ".openCaveEntrance"
     *
     * @When /^I run :command
     */
    public function iRun($command)
    {
        exec($command, $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * Uploads a file using the specified input field
     * Example: Given I put the file "batman_profile.jpg" into "heroImage"
     * Example: When I put the file "batman_profile.jpg" into "heroImage"
     * Example: And I put the file "batman_profile.jpg" into "heroImage"
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
     * Example: Given I execute "pwd"
     * Example: When I execute "pwd"
     * Example: And I execute "pwd"
     *
     * @Given (I )execute :command
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
     * Example: Given I execute "php ./openBatCave.php" from project root
     * Example: When I execute "php ./openBatCave.php" from project root
     * Example: And I execute "php ./openBatCave.php" from project root
     *
     * @Given (I )execute :command from project root
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $cmd = $this->root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }

    /**
     * Creates a file via touch command
     * Example: Given I create the file "batmansPasswords.txt" containing"
     *           """
     *           Facebook: RIPMarthaThomas1927
     *           Twitter: RIPTimothyDrake1986
     *           Netflix: RIPDamianWayne2009
     *           Google: DamnIveSeenSomeStuff
     *           """
     * Example: When I create the file "batmansPasswords.txt" containing"
     *           """
     *           Facebook: RIPMarthaThomas1927
     *           Twitter: RIPTimothyDrake1986
     *           Netflix: RIPDamianWayne2009
     *           Google: DamnIveSeenSomeStuff
     *           """
     * Example: And I create the file "batmansPasswords.txt" containing"
     *           """
     *           Facebook: RIPMarthaThomas1927
     *           Twitter: RIPTimothyDrake1986
     *           Netflix: RIPDamianWayne2009
     *           Google: DamnIveSeenSomeStuff
     *           """
     *
     * @Given (I )create the file :filename containing:
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
     * Prints the content of passed file
     * Example: Given I print the content of "./batmansGreatestSecrets.txt" file
     * Example: When I print the content of "./batmansGreatestSecrets.txt" file
     * Example: Then I print the content of "./batmansGreatestSecrets.txt" file
     *
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
     * Asserts against previously run command line argument
     * Example: Then I should see:
     *          """
     *          Opening cave, master Bruce.
     *          """
     * Example: And I should see:
     *          """
     *          Opening cave, master Bruce.
     *          """
     *
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        if ($string->getRaw() !== $this->output) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
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
