<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_ExpectationFailedException as AssertException;

class BrowserContext extends BaseContext
{
    private $timeout = 10;
    private $dateFormat = 'dmYHi';

    /**
     * @AfterScenario
     */
    public function closeBrowser()
    {
        $this->getSession()->stop();
    }

    /**
     * @When /^I set basic authentication with "(?P<user>[^"]*)" and "(?P<password>[^"]*)"$/
     */
    public function iSetBasicAuthenticationWithAnd($user, $password)
    {
        $this->getSession()->setBasicAuth($user, $password);
    }

    /**
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
     * @When /^(?:|I )click on the (?P<nth>\d+)(?:st|nd|rd|th) "(?P<element>[^"]*)" element$/
     */
    public function iClickOnTheNthElement($nth, $element)
    {
        $nodes = $this->getSession()->getPage()->findAll('css', $element);

        if (isset($nodes[$nth - 1])) {
            $nodes[$nth - 1]->click();
        }
        else {
            throw new \Exception(sprintf("The element %s number %s was not found anywhere in the page", $element, $nth));
        }
    }

    /**
     * @When /^(?:|I )follow the (?P<nth>\d+)(?:st|nd|rd|th) "(?P<link>[^"]*)" link$/
     */
    public function iFollowTheNthLink($nth, $link)
    {
        $page = $this->getSession()->getPage();

        $links = $page->findAll('named', array(
            'link', $this->getSession()->getSelectorsHandler()->xpathLiteral($link)
        ));

        if (!isset($links[$nth - 1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $nth, $link));
        }

        $links[$nth - 1]->click();
    }

    /**
     * @When /^(?:|I )fill in "(?P<field>[^"]*)" with the current date$/
     */
    public function iFillInWithTheCurrentDate($field)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat)));
    }

    /**
     * @When /^(?:|I )fill in "(?P<field>[^"]*)" with the current date and modifier "(?P<modifier>[^"]*)"$/
     */
    public function iFillInWithTheCurentDateAndModifier($field, $modifier)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat, strtotime($modifier))));
    }

    /**
     * @When /^(?:|I )hover "(?P<element>[^"]*)"$/
     */
    public function iHoverIShouldSeeIn($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new \Exception(sprintf('The hovered element "%s" was not found anywhere in the page', $element));
        }
        $node->mouseOver();
    }

    /**
     * @When /^(?:|I )save the value of "(?P<field>[^"]*)" in the "(?P<parameter>[^"]*)" parameter$/
     */
    public function iSaveTheValueOfInTheParameter($field, $parameter)
    {
        $field = str_replace('\\"', '"', $field);
        $node  = $this->getSession()->getPage()->findField($field);
        if ($node === null) {
            throw new \Exception(sprintf('The field "%s" was not found anywhere in the page', $field));
        }

        $this->getMainContext()->setParameter($parameter, $node->getValue());
    }

    /**
     * @Then /^(?:|I )wait "(?P<seconds>\d+)" seconds until I see "(?P<text>[^"]*)"$/
     */
    public function iWaitsSecondsUntilISee($seconds, $text)
    {
        $this->iWaitSecondsUntilISeeInTheElement($seconds, $text, $this()->getSession()->getPage());
    }

    /**
     * @Then /^(?:|I )wait until I see "(?P<text>[^"]*)"$/
     */
    public function iWaitUntilISee($text)
    {
        $this->iWaitsSecondsUntilISee($this->timeout, $text);
    }

    /**
     * @Then /^(?:|I )wait (?P<seconds>\d+) seconds until I see "(?P<text>[^"]*)" in the "(?P<element>[^"]*)" element$/
     */
    public function iWaitSecondsUntilISeeInTheElement($seconds, $text, $element)
    {
        $expected = str_replace('\\"', '"', $text);

        $time = 0;

        if (is_string($element)) {
            $node = $this()->getSession()->getPage()->find('css', $element);
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
                    throw new ResponseTextException($message, $this()->getSession(), $e);
                }
            }

            if ($e == null) {
                break;
            }

            sleep(1);
        }
    }

    /**
     * @Then /^(?:|I )wait until I see "(?P<text>[^"]*)" in the "(?P<element>[^"]*)" element$/
     */
    public function iWaitUntilISeeInTheElement($text, $element)
    {
        $this->iWaitSecondsUntilISeeInTheElement($this->timeout, $text, $element);
    }

    /**
     * @Then /^(?:|I )Should see (?P<nth>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     */
    public function iShouldSeeNElementInTheNthParent($nth, $element, $index, $parent)
    {
        $page = $this()->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $index, $parent));
        }

        $elements = $parents[$index - 1]->findAll('css', $element);
        if (count($elements) !== (int)$nth) {
            throw new \Exception(sprintf("%d occurences of the %s element in %s found", count($elements), $element, $parent));
        }
    }

    /**
     * @Then /^(?:|I )should see (?P<nth>\d+) "(?P<element>[^"]*)" elements?$/
     */
    public function iShouldSeeNElements($nth, $element)
    {
        $nodes = $this()->getSession()->getPage()->findAll('css', $element);
        $actual = sizeof($nodes);
        if ($actual !== (int)$nth) {
            throw new \Exception(sprintf('%s occurences of the "%s" element found', $actual, $element));
        }
    }

    /**
     * @Then /^the element "(?P<element>[^"]*)" should be disabled$/
     */
    public function theElementShouldBeDisabled($element)
    {
        $node = $this()->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }

        if (!$node->hasAttribute('disabled')) {
            throw new \Exception(sprintf('The element "%s" is not disabled', $element));
        }
    }

    /**
     * @Then /^the element "(?P<element>[^"]*)" should be enabled$/
     */
    public function theElementShouldBeEnabled($element)
    {
        $node = $this()->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new \Exception(sprintf('There is no "%s" element', $element));
        }

        if ($node->hasAttribute('disabled')) {
            throw new \Exception(sprintf('The element "%s" is not enabled', $element));
        }
    }

    /**
     * @Then /^(?:|I )shoud see the "(?P<parameter>[^"]*)" parameter$/
     */
    public function iShouldSeeTheParameter($parameter)
    {
        return new Step\Then(sprintf('I should see "%s"', $this->getMainContext()->getParameter($parameter)));
    }

    /**
     * @Then /^the "(?P<select>[^"]*)" select box should contain "(?P<option>[^"]*)"$/
     */
    public function theSelectBoxShouldContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $optionText = $this()->getSession()->getPage()->findField($select)->getText();

        try {
            assertContains($option, $optionText);
        }
        catch (AssertException $e) {
            throw new \Exception(sprintf('The "%s" select box does not contain the "%s" option', $select, $option));
        }
    }

    /**
     * @Then /^the "(?P<select>[^"]*)" select box should not contain "(?P<option>[^"]*)"$/
     */
    public function theSelectBoxShouldNotContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $optionText = $this()->getSession()->getPage()->findField($select)->getText();

        try {
            assertNotContains($option, $optionText);
        }
        catch (AssertException $e) {
            throw new \Exception(sprintf('The "%s" select box does contain the "%s" option', $select, $option));
        }
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should be visible$/
     */
    public function theElementShouldBeVisible($element)
    {
        $displayedNode = $this()->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }

        assertTrue($displayedNode->isVisible(), sprintf('The element "%s" is not visible', $element));
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should not be visible$/
     */
    public function theElementShouldNotBeVisible($element)
    {
        $displayedNode = $this()->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }

        assertFalse($displayedNode->isVisible(), sprintf('The element "%s" is not visible', $element));
    }
}
