<?php

namespace Behat\Behatch\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\TranslatedContextInterface;
use PHPUnit_Framework_ExpectationFailedException as AssertException;

/**
 * This context is intended for Browser interractions
 */
class BrowserContext extends BehatContext implements TranslatedContextInterface
{
    /**
     * Timeout value
     *
     * @var int
     */
    private $timeout = 10;

    /**
     * Date format
     *
     * @var string
     */
    private $dateFormat = 'dmYHi';

    /**
     * Shortcut for retrieving Mink context
     *
     * @return \Behat\Mink\Behat\Context\MinkContext
     */
    public function getMinkContext()
    {
        return $this->getMainContext()->getSubContext('mink');
    }

    /**
     * After each scenario, we close the browser
     *
     * @AfterScenario
     * @return void
     */
    public function closeBrowser()
    {
        $this->getMinkContext()->getSession()->stop();
    }

    /**
     * Set login / password for next HTTP authentication
     *
     * @When /^I set basic authentication with "([^"]*)" and "([^"]*)"$/
     */
    public function iSetBasicAuthenticationWithAnd($user, $password)
    {
        $this->getMinkContext()->getSession()->setBasicAuth($user, $password);
    }

    /**
     * Open url with various parameters
     *
     * @Given /^(?:|I )am on url composed by$/
     */
    public function iAmOnUrlComposedBy(TableNode $tableNode)
    {
        $url = '';
        foreach ($tableNode->getHash() as $hash) {
            $param = $hash['parameters'];

            //this parameter is actually a context parameter
            if ($this->getMainContext()->hasParameter($param)) {
                $url .= $this->getMainContext()->getParameter($param);
            }
            else {
                $url .= $param;
            }
        }

        return new Step\Given(sprintf('I am on "%s"', $url));
    }

    /**
     * Clicks on the nth CSS element
     *
     * @When /^(?:|I )click on the ([0-9]+)(?:st|nd|rd|th) "([^"]*)" element$/
     */
    public function iClickOnTheNthElement($index, $element)
    {
        $nodes = $this->getMinkContext()->getSession()->getPage()->findAll('css', $element);

        if (isset($nodes[$index-1])) {
            $nodes[$index-1]->click();
        }
        else {
            throw new \Exception(sprintf("The element %s number %s was not found anywhere in the page", $element, $index));
        }
    }

    /**
     * Click on the nth specified link
     *
     * @When /^(?:|I )follow the ([0-9]+)(?:st|nd|rd|th) "([^"]*)" link$/
     */
    public function iFollowTheNthLink($number, $locator)
    {
        $page = $this->getMinkContext()->getSession()->getPage();

        $links = $page->findAll('named', array(
            'link', $this->getMinkContext()->getSession()->getSelectorsHandler()->xpathLiteral($locator)
        ));

        if (!isset($links[$number-1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $number, $locator));
        }

        $links[$number-1]->click();
    }

    /**
     * Fills in form field with current date
     *
     * @When /^(?:|I )fill in "([^"]*)" with the current date$/
     */
    public function iFillInWithTheCurrentDate($field)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat)));
    }

    /**
     * Fills in form field with current date and strtotime modifier
     *
     * @When /^(?:|I )fill in "([^"]*)" with the current date and modifier "([^"]*)"$/
     */
    public function iFillInWithTheCurentDateAndModifier($field, $modifier)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat, strtotime($modifier))));
    }

    /**
     * Mouse over a CSS element
     *
     * @When /^(?:|I )hover "([^"]*)"$/
     */
    public function iHoverIShouldSeeIn($element)
    {
        $node = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new \Exception(sprintf('The hovered element "%s" was not found anywhere in the page', $element));
        }
        $node->mouseOver();
    }

    /**
     * Save value of the field in parameters array
     *
     * @When /^(?:|I )save the value of "([^"]*)" in the "([^"]*)" parameter$/
     */
    public function iSaveTheValueOfInTheParameter($field, $parameterName)
    {
        $field = str_replace('\\"', '"', $field);
        $node  = $this->getMinkContext()->getSession()->getPage()->findField($field);
        if ($node === null) {
            throw new \Exception(sprintf('The field "%s" was not found anywhere in the page', $field));
        }

        $this->getMainContext()->setParameter($parameterName, $node->getValue());
    }

    /**
     * Checks, that the page should contains specified text after given timeout
     *
     * @Then /^(?:|I )wait "([^"]*)" seconds until I see "([^"]*)"$/
     */
    public function iWaitsSecondsUntilISee($timeOut, $text)
    {
        $this->iWaitSecondsUntilISeeInTheElement($timeOut, $text, $this->getMinkContext()->getSession()->getPage());
    }

    /**
     * Checks, that the page should contains specified text after timeout
     *
     * @Then /^(?:|I )wait until I see "([^"]*)"$/
     */
    public function iWaitUntilISee($text)
    {
        $this->iWaitsSecondsUntilISee($this->timeout, $text);
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then /^(?:|I )wait (\d+) seconds until I see "([^"]*)" in the "([^"]*)" element$/
     */
    public function iWaitSecondsUntilISeeInTheElement($seconds, $text, $element)
    {
        $expected = str_replace('\\"', '"', $text);

        $time = 0;

        if (is_string($element)) {
            $node = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        }
        else {
            $node = $element;
        }

        while ($time < $seconds) {
            $actual   = $node->getText();
            $e = null;

            try {
                $time++;
                assertContains($expected, $actual);
            }
            catch (AssertException $e) {
                if ($time >= $seconds) {
                    $message = sprintf('The text "%s" was not found anywhere in the text of %s atfer a %s seconds timeout', $expected, $element, $seconds);
                    throw new ResponseTextException($message, $this->getMinkContext()->getSession(), $e);
                }
            }

            if ($e == null) {
                break;
            }

            sleep(1);
        }
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then /^(?:|I )wait until I see "([^"]*)" in the "([^"]*)" element$/
     */
    public function iWaitUntilISeeInTheElement($text, $element)
    {
        $this->iWaitSecondsUntilISeeInTheElement($this->timeout, $text, $element);
    }

    /**
     * @Then /^(?:|I )Should see (\d+) "([^"]*)" in the (\d+)(?:st|nd|rd|th) "([^"]*)"$/
     */
    public function iShouldSeeNElementInTheNthParent($occurences, $element, $index, $parent)
    {
        $page = $this->getMinkContext()->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index-1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $index, $parent));
        }

        $elements = $parents[$index-1]->findAll('css', $element);
        if (count($elements) !== (int)$occurences) {
            throw new \Exception(sprintf("%d occurences of the %s element in %s found", count($elements), $element, $parent));
        }
    }

    /**
     * Checks, that there is the given number of elements with specified CSS on page
     *
     * @Then /^(?:|I )should see ([0-9]+) "([^"]*)" elements?$/
     */
    public function iShouldSeeNElements($occurences, $element)
    {
        $nodes = $this->getMinkContext()->getSession()->getPage()->findAll('css', $element);
        $actual = sizeof($nodes);
        if ($actual !== (int)$occurences) {
            throw new \Exception(sprintf('%s occurences of the "%s" element found', $actual, $element));
        }
    }

    /**
     * Checks, that element with given CSS is disabled
     *
     * @Then /^the element "([^"]*)" should be disabled$/
     */
    public function theElementShouldBeDisabled($element)
    {
        $node = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }

        if (!$node->hasAttribute('disabled')) {
            throw new \Exception(sprintf('The element "%s" is not disabled', $element));
        }
    }

    /**
     * Checks, that element with given CSS is enabled
     *
     * @Then /^the element "([^"]*)" should be enabled$/
     */
    public function theElementShouldBeEnabled($element)
    {
        $node = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }

        if ($node->hasAttribute('disabled')) {
            throw new \Exception(sprintf('The element "%s" is not enabled', $element));
        }
    }

    /**
     * Checks, that page contains specified parameter value
     *
     * @Then /^(?:|I )shoud see the "([^"]*)" parameter$/
     */
    public function iShouldSeeTheParameter($parameter)
    {
        return new Step\Then(sprintf('I should see "%s"', $this->getMainContext()->getParameter($parameter)));
    }

    /**
     * Checks, that given select box contains the specified option
     *
     * @Then /^the "([^"]*)" select box should contain "([^"]*)"$/
     */
    public function theSelectBoxShouldContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $optionText = $this->getMinkContext()->getSession()->getPage()->findField($select)->getText();

        try {
            assertContains($option, $optionText);
        }
        catch (AssertException $e) {
            throw new \Exception(sprintf('The "%s" select box does not contain the "%s" option', $select, $option));
        }
    }

    /**
     * Checks, that given select box does not contain the specified option
     *
     * @Then /^the "([^"]*)" select box should not contain "([^"]*)"$/
     */
    public function theSelectBoxShouldNotContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $optionText = $this->getMinkContext()->getSession()->getPage()->findField($select)->getText();

        try {
            assertNotContains($option, $optionText);
        }
        catch (AssertException $e) {
            throw new \Exception(sprintf('The "%s" select box does contain the "%s" option', $select, $option));
        }
    }

    /**
     * Checks, that the specified CSS element is visible
     *
     * @Then /^the "([^"]*)" element should be visible$/
     */
    public function theElementShouldBeVisible($element)
    {
        $displayedNode = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }

        assertTrue($displayedNode->isVisible(), sprintf('The element "%s" is not visible', $element));
    }

    /**
     * Checks, that the specified CSS element is not visible
     *
     * @Then /^the "([^"]*)" element should not be visible$/
     */
    public function theElementShouldNotBeVisible($element)
    {
        $displayedNode = $this->getMinkContext()->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }

        assertFalse($displayedNode->isVisible(), sprintf('The element "%s" is not visible', $element));
    }

    /**
     * Returns list of definition translation resources paths.
     *
     * @return array
     */
    public function getTranslationResources()
    {
        return glob(__DIR__.'/../../../../../i18n/*.xliff');
    }
}
