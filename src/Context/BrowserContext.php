<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementNotFoundException;

class BrowserContext extends BaseContext
{
    private $timeout;
    private $dateFormat = 'dmYHi';

    public function __construct($timeout = 1)
    {
        $this->timeout = $timeout;
    }

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
     * @When I set basic authentication with :user and :password
     */
    public function iSetBasicAuthenticationWithAnd($user, $password)
    {
        $this->getSession()->setBasicAuth($user, $password);
    }

    /**
     * Open url with various parameters
     *
     * @Given (I )am on url composed by:
     */
    public function iAmOnUrlComposedBy(TableNode $tableNode)
    {
        $url = '';
        foreach ($tableNode->getHash() as $hash) {
            $url .= $hash['parameters'];
        }

        return $this->getMinkContext()
            ->visit($url);
    }

    /**
     * Clicks on the nth CSS element
     *
     * @When (I )click on the :index :element element
     */
    public function iClickOnTheNthElement($index, $element)
    {
        $nodes = $this->getSession()->getPage()->findAll('css', $element);

        if (isset($nodes[$index - 1])) {
            $nodes[$index - 1]->click();
        }
        else {
            throw new \Exception("The element '$element' number $index was not found anywhere in the page");
        }
    }

    /**
     * Click on the nth specified link
     *
     * @When (I )follow the :index :link link
     */
    public function iFollowTheNthLink($index, $link)
    {
        $page = $this->getSession()->getPage();

        $links = $page->findAll('named', array(
            'link', $this->getSession()->getSelectorsHandler()->xpathLiteral($link)
        ));

        if (!isset($links[$index - 1])) {
            throw new \Exception("The $index element '$link' was not found anywhere in the page");
        }

        $links[$index - 1]->click();
    }

    /**
     * Fills in form field with current date
     *
     * @When (I )fill in :field with the current date
     */
    public function iFillInWithTheCurrentDate($field)
    {
        return $this->iFillInWithTheCurrentDateAndModifier($field, 'now');
    }

    /**
     * Fills in form field with current date and strtotime modifier
     *
     * @When (I )fill in :field with the current date and modifier :modifier
     */
    public function iFillInWithTheCurrentDateAndModifier($field, $modifier)
    {
        return $this->getMinkContext()
            ->fillField($field, date($this->dateFormat, strtotime($modifier)));
    }

    /**
     * Mouse over a CSS element
     *
     * @When (I )hover :element
     */
    public function iHoverIShouldSeeIn($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new \Exception("The hovered element '$element' was not found anywhere in the page");
        }
        $node->mouseOver();
    }

    /**
     * Save value of the field in parameters array
     *
     * @When (I )save the value of :field in the :parameter parameter
     */
    public function iSaveTheValueOfInTheParameter($field, $parameter)
    {
        $field = str_replace('\\"', '"', $field);
        $node  = $this->getSession()->getPage()->findField($field);
        if ($node === null) {
            throw new \Exception("The field '$field' was not found anywhere in the page");
        }

        $this->setMinkParameter($parameter, $node->getValue());
    }

    /**
     * Checks, that the page should contains specified text after given timeout
     *
     * @Then (I )wait :count second(s) until I see :text
     */
    public function iWaitSecondsUntilISee($count, $text)
    {
        $this->iWaitSecondsUntilISeeInTheElement($count, $text, $this->getSession()->getPage());
    }

    /**
     * Checks, that the page should contains specified text after timeout
     *
     * @Then (I )wait until I see :text
     */
    public function iWaitUntilISee($text)
    {
        $this->iWaitSecondsUntilISee($this->timeout, $text);
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then (I )wait :count second(s) until I see :text in the :element element
     */
    public function iWaitSecondsUntilISeeInTheElement($count, $text, $element)
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
                if ($now - $startTime >= $count) {
                    $message = "The text '$expected' was not found after a $count seconds timeout";
                    throw new ResponseTextException($message, $this->getSession(), $e);
                }
            }

            if ($e == null) {
                break;
            }

        } while ($now - $startTime < $count);
    }

    /**
     * @Then (I )wait :count second(s)
     */
    public function iWaitSeconds($count)
    {
        sleep($count);
    }

    /**
     * Checks, that the element contains specified text after timeout
     *
     * @Then (I )wait until I see :text in the :element element
     */
    public function iWaitUntilISeeInTheElement($text, $element)
    {
        $this->iWaitSecondsUntilISeeInTheElement($this->timeout, $text, $element);
    }

    /**
     * Checks, that the page should contains specified element after timeout
     *
     * @Then (I )wait for :element element
     */
    public function iWaitForElement($element)
    {
        $this->iWaitSecondsForElement($this->timeout, $element);
    }

    /**
     * Wait for a element
     *
     * @Then (I )wait :count second(s) for :element element
     */
    public function iWaitSecondsForElement($count, $element)
    {
        $startTime = time();

        do {
            $now = time();
            $e = null;

            try {
                $node = $this->getSession()->getPage()->findAll('css', $element);
                $this->assertCount(1, $node);
            }
            catch (ExpectationException $e) {
                if ($now - $startTime >= $count) {
                    $message = "The element '$element' was not found after a $count seconds timeout";
                    throw new ResponseTextException($message, $this->getSession(), $e);
                }
            }

            if ($e == null) {
                break;
            }

        } while ($now - $startTime < $count);
    }

    /**
     * @Then /^(?:|I )should see (?P<count>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     */
    public function iShouldSeeNElementInTheNthParent($count, $element, $index, $parent)
    {
        $actual = $this->countElements($element, $index, $parent);
        if ($actual !== $count) {
            throw new \Exception("$actual occurrences of the '$element' element in '$parent' found");
        }
    }

    /**
     * @Then (I )should see less than :count :element in the :index :parent
     */
    public function iShouldSeeLessThanNElementInTheNthParent($count, $element, $index, $parent)
    {
        $actual = $this->countElements($element, $index, $parent);
        if ($actual > $count) {
            throw new \Exception("$actual occurrences of the '$element' element in '$parent' found");
        }
    }

    /**
     * @Then (I )should see more than :count :element in the :index :parent
     */
    public function iShouldSeeMoreThanNElementInTheNthParent($count, $element, $index, $parent)
    {
        $actual = $this->countElements($element, $index, $parent);
        if ($actual < $count) {
            throw new \Exception("$actual occurrences of the '$element' element in '$parent' found");
        }
    }

    private function countElements($element, $index, $parent)
    {
        $page = $this->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception("The $index element '$parent' was not found anywhere in the page");
        }

        $elements = $parents[$index - 1]->findAll('css', $element);
        return count($elements);
    }

    /**
     * Checks, that element with given CSS is enabled
     *
     * @Then the element :element should be enabled
     */
    public function theElementShouldBeEnabled($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node == null) {
            throw new \Exception("There is no '$element' element");
        }

        if ($node->hasAttribute('disabled')) {
            throw new \Exception("The element '$element' is not enabled");
        }
    }

    /**
     * Checks, that element with given CSS is disabled
     *
     * @Then the element :element should be disabled
     */
    public function theElementShouldBeDisabled($element)
    {
        $this->not(function () use($element) {
            $this->theElementShouldBeEnabled($element);
        }, "The element '$element' is not disabled");
    }

    /**
     * Checks, that given select box contains the specified option
     *
     * @Then the :select select box should contain :option
     */
    public function theSelectBoxShouldContain($select, $option)
    {
        $select = str_replace('\\"', '"', $select);
        $option = str_replace('\\"', '"', $option);

        $obj = $this->getSession()->getPage()->findField($select);
        if ($obj === null) {
            throw new ElementNotFoundException(
                $this->getSession(), 'select box', 'id|name|label|value', $select
            );
        }
        $optionText = $obj->getText();

        $message = "The '$select' select box does not contain the '$option' option";
        $this->assertContains($option, $optionText, $message);
    }

    /**
     * Checks, that given select box does not contain the specified option
     *
     * @Then the :select select box should not contain :option
     */
    public function theSelectBoxShouldNotContain($select, $option)
    {
        $this->not(function () use($select, $option) {
            $this->theSelectBoxShouldContain($select, $option);
        }, "The '$select' select box does contain the '$option' option");
    }

    /**
     * Checks, that the specified CSS element is visible
     *
     * @Then the :element element should be visible
     */
    public function theElementShouldBeVisible($element)
    {
        $displayedNode = $this->getSession()->getPage()->find('css', $element);
        if ($displayedNode === null) {
            throw new \Exception("The element '$element' was not found anywhere in the page");
        }


        $message = "The element '$element' is not visible";
        $this->assertTrue($displayedNode->isVisible(), $message);
    }

    /**
     * Checks, that the specified CSS element is not visible
     *
     * @Then the :element element should not be visible
     */
    public function theElementShouldNotBeVisible($element)
    {
        $exception = new \Exception("The element '$element' is visible");

        $this->not(function () use($element) {
            $this->theElementShouldBeVisible($element);
        }, $exception);
    }

    /**
     * Select a frame by its name or ID.
     *
     * @When (I )switch to iframe :name
     * @When (I )switch to frame :name
     */
    public function switchToIFrame($name)
    {
        $this->getSession()->switchToIFrame($name);
    }

    /**
     * Go back to main document frame.
     *
     * @When (I )switch to main frame
     */
    public function switchToMainFrame()
    {
        $this->getSession()->switchToIFrame();
    }
}
