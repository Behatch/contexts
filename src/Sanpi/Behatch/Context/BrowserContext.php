<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;

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
     * Set login / password for next HTTP authentication
     *
     * @When /^I set basic authentication with "(?P<user>[^"]*)" and "(?P<password>[^"]*)"$/
     */
    public function iSetBasicAuthenticationWithAnd($user, $password)
    {
        $this->getSession()->setBasicAuth($user, $password);
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
     * Click on the nth specified link
     *
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
     * Fills in form field with current date
     *
     * @When /^(?:|I )fill in "(?P<field>[^"]*)" with the current date$/
     */
    public function iFillInWithTheCurrentDate($field)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat)));
    }

    /**
     * Fills in form field with current date and strtotime modifier
     *
     * @When /^(?:|I )fill in "(?P<field>[^"]*)" with the current date and modifier "(?P<modifier>[^"]*)"$/
     */
    public function iFillInWithTheCurrentDateAndModifier($field, $modifier)
    {
        return new Step\When(sprintf('I fill in "%s" with "%s"', $field, date($this->dateFormat, strtotime($modifier))));
    }

    /**
     * Mouse over a CSS element
     *
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
     * Save value of the field in parameters array
     *
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
     * Checks, that the page should contains specified text after given timeout
     *
     * @Then /^(?:|I )wait (?P<seconds>\d+) seconds until I see "(?P<text>[^"]*)"$/
     */
    public function iWaitSecondsUntilISee($seconds, $text)
    {
        $this->iWaitSecondsUntilISeeInTheElement($seconds, $text, $this->getSession()->getPage());
    }

    /**
     * Checks, that the page should contains specified text after timeout
     *
     * @Then /^(?:|I )wait until I see "(?P<text>[^"]*)"$/
     */
    public function iWaitUntilISee($text)
    {
        $this->iWaitSecondsUntilISee($this->timeout, $text);
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then /^(?:|I )wait (?P<seconds>\d+) seconds until I see "(?P<text>[^"]*)" in the "(?P<element>[^"]*)" element$/
     */
    public function iWaitSecondsUntilISeeInTheElement($seconds, $text, $element)
    {
        $expected = str_replace('\\"', '"', $text);

        if (is_string($element)) {
            $node = $this->getSession()->getPage()->find('css', $element);
        }
        else {
            $node = $element;
        }

        $startTime = time();

        do {
            $now = time();
            $actual   = $node->getText();
            $e = null;

            try {
                $this->assertContains($expected, $actual);
            }
            catch (ExpectationException $e) {
                if ($now - $startTime >= $seconds) {
                    $message = sprintf('The text "%s" was not found after a %s seconds timeout', $expected, $seconds);
                    throw new ResponseTextException($message, $this->getSession(), $e);
                }
            }

            if ($e == null) {
                break;
            }

        } while ($now - $startTime < $seconds);
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then /^(?:|I )wait until I see "(?P<text>[^"]*)" in the "(?P<element>[^"]*)" element$/
     */
    public function iWaitUntilISeeInTheElement($text, $element)
    {
        $this->iWaitSecondsUntilISeeInTheElement($this->timeout, $text, $element);
    }

    /**
     * @Then /^(?:|I )should see (?P<nth>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     */
    public function iShouldSeeNElementInTheNthParent($nth, $element, $index, $parent)
    {
        $page = $this->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $index, $parent));
        }

        $elements = $parents[$index - 1]->findAll('css', $element);
        if (count($elements) !== (int)$nth) {
                    throw new \Exception(sprintf("%d occurrences of the %s element in %s found", count($elements), $element, $parent));
        }

    }

    /**
     * @Then /^(?:|I )should see less than (?P<nth>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     */
    public function iShouldSeeLessThanNElementInTheNthParent($nth, $element, $index, $parent)
    {
        $page = $this->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $index, $parent));
        }

        $elements = $parents[$index - 1]->findAll('css', $element);
        if (count($elements) > (int)$nth) {
            throw new \Exception(sprintf("%d occurrences of the %s element in %s found", count($elements), $element, $parent));
        }

    }

    /**
     * @Then /^(?:|I )should see more than (?P<nth>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     */
    public function iShouldSeeMoreThanNElementInTheNthParent($nth, $element, $index, $parent)
    {
        $page = $this->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception(sprintf("The %s element %s was not found anywhere in the page", $index, $parent));
        }

        $elements = $parents[$index - 1]->findAll('css', $element);
        if (count($elements) < (int)$nth) {
            throw new \Exception(sprintf("%d occurrences of the %s element in %s found", count($elements), $element, $parent));
        }
    }

    /**
     * Checks, that element with given CSS is disabled
     *
     * @Then /^the element "(?P<element>[^"]*)" should be disabled$/
     */
    public function theElementShouldBeDisabled($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
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
     * @Then /^the element "(?P<element>[^"]*)" should be enabled$/
     */
    public function theElementShouldBeEnabled($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
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
     * @Then /^(?:|I )shoud see the "(?P<parameter>[^"]*)" parameter$/
     */
    public function iShouldSeeTheParameter($parameter)
    {
        return new Step\Then(sprintf('I should see "%s"', $this->getMainContext()->getParameter($parameter)));
    }

    /**
     * Checks, that given select box contains the specified option
     *
     * @Then /^the "(?P<select>[^"]*)" select box should contain "(?P<option>[^"]*)"$/
     */
    public function theSelectBoxShouldContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $obj = $this->getSession()->getPage()->findField($select);
        if ( $obj == null)
        {
            throw new ElementNotFoundException(
                $this->getSession(), 'select box', 'id|name|label|value', $select
            );
        }
        $optionText = $obj->getText();



        $message = sprintf('The "%s" select box does not contain the "%s" option', $select, $option);
        $this->assertContains($option, $optionText, $message);
    }

    /**
     * Checks, that given select box does not contain the specified option
     *
     * @Then /^the "(?P<select>[^"]*)" select box should not contain "(?P<option>[^"]*)"$/
     */
    public function theSelectBoxShouldNotContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $obj = $this->getSession()->getPage()->findField($select);
        if ( $obj == null)
        {
            throw new ElementNotFoundException(
                $this->getSession(), 'select box', 'id|name|label|value', $select
            );
        }
        $optionText = $obj->getText();

        $message = sprintf('The "%s" select box does contain the "%s" option', $select, $option);
        $this->assertNotContains($option, $optionText, $message);
    }

    /**
     * Checks, that the specified CSS element is visible
     *
     * @Then /^the "(?P<element>[^"]*)" element should be visible$/
     */
    public function theElementShouldBeVisible($element)
    {
        $displayedNode = $this->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }


        $message = sprintf('The element "%s" is not visible', $element);
        $this->assertTrue($displayedNode->isVisible(), $message);
    }

    /**
     * Checks, that the specified CSS element is not visible
     *
     * @Then /^the "(?P<element>[^"]*)" element should not be visible$/
     */
    public function theElementShouldNotBeVisible($element)
    {
        $displayedNode = $this->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception(sprintf('The element "%s" was not found anywhere in the page', $element));
        }

        $message = sprintf('The element "%s" is visible', $element);
        $this->assertFalse($displayedNode->isVisible(), $message);
    }


    /**
     * Select a frame by its name or ID.
     * 
     * @Then /^switch to iframe "([^"]*)"$/
     * @Then /^switch to frame "([^"]*)"$/
     */
    public function switchToIFrame($name)
    {
        $this->getSession()->switchToIFrame($name);
    }

    /**
     * Go back to main document frame.
     *
     * @Then /^switch to main frame$/
     */
    public function switchToMainFrame()
    {
        $this->getSession()->switchToIFrame();
    }
}
