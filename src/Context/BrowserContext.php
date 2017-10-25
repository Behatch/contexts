<?php

namespace Behatch\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementNotFoundException;
use WebDriver\Exception\StaleElementReference;
use Behat\Behat\Tester\Exception\PendingException;

class BrowserContext extends BaseContext
{
    private $timeout;
    private $dateFormat = 'dmYHi';
    private $timerStartedAt;

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
     * @BeforeScenario
     *
     * @When (I )start timing now
     */
    public function startTimer()
    {
        $this->timerStartedAt = time();
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
        $node = $this->findElement('css', $element, $index);
        $node->click();
    }

    /**
     * Click on the nth specified link
     *
     * @When (I )follow the :index :link link
     */
    public function iFollowTheNthLink($index, $link)
    {
        $node = $this->findElement('named', ['link', $link], $index);
        $node->click();
    }

    /**
     * Presses the nth specified button
     *
     * @When (I )press the :index :button button
     */
    public function pressTheNthButton($index, $button)
    {
        $node = $this->findElement('named', ['button', $button], $index);
        $node->click();
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
        $this->iWaitSecondsUntilISeeInTheElement($count, $text, 'html');
    }

    /**
     * Checks, that the page should not contain specified text before given timeout
     *
     * @Then (I )should not see :text within :count second(s)
     */
    public function iDontSeeInSeconds($count, $text)
    {
        $caught = false;
        try {
            $this->iWaitSecondsUntilISee($count, $text);
        }
        catch (ExpectationException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, "Text '$text' has been found");
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
        $startTime = time();
        $this->iWaitSecondsForElement($count, $element);

        $expected = str_replace('\\"', '"', $text);
        $message = "The text '$expected' was not found after a $count seconds timeout";

        $found = false;
        do {
            try {
                usleep(1000);
                $node = $this->getSession()->getPage()->find('css', $element);
                $this->assertContains($expected, $node->getText(), $message);
                return;
            }
            catch (ExpectationException $e) {
                /* Intentionally leave blank */
            }
            catch (StaleElementReference $e) {
                // assume page reloaded whilst we were still waiting
            }
        } while (!$found && (time() - $startTime < $count));

        // final assertion...
        $node = $this->getSession()->getPage()->find('css', $element);
        $this->assertContains($expected, $node->getText(), $message);
    }

    /**
     * @Then (I )wait :count second(s)
     */
    public function iWaitSeconds($count)
    {
        usleep($count * 1000000);
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
        $found = false;
        $startTime = time();
        $e = null;

        do {
            try {
                usleep(1000);
                $node = $this->getSession()->getPage()->findAll('css', $element);
                $this->assertCount(1, $node);
                $found = true;
            }
            catch (ExpectationException $e) {
                /* Intentionally leave blank */
            }
        }
        while (!$found && (time() - $startTime < $count));

        if ($found === false) {
            $message = "The element '$element' was not found after a $count seconds timeout";
            throw new ResponseTextException($message, $this->getSession()->getDriver(), $e);
        }
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

    /**
     * Checks, that element with given CSS is enabled
     *
     * @Then the element :element should be enabled
     */
    public function theElementShouldBeEnabled($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
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
                $this->getSession()->getDriver(), 'select box', 'id|name|label|value', $select
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

    /**
     * test time from when the scenario started
     *
     * @Then (the )total elapsed time should be :comparison than :expected seconds
     * @Then (the )total elapsed time should be :comparison to :expected seconds
     */
    public function elapsedTime($comparison, $expected)
    {
        $elapsed = time() - $this->timerStartedAt;

        switch ($comparison) {
            case 'less':
                $this->assertTrue($elapsed < $expected, "Elapsed time '$elapsed' is not less than '$expected' seconds.");
                break;

            case 'more':
                $this->assertTrue($elapsed > $expected, "Elapsed time '$elapsed' is not more than '$expected' seconds.");
                break;

            case 'equal':
                $this->assertTrue($elapsed === $expected, "Elapsed time '$elapsed' is not '$expected' seconds.");
                break;

            default:
                throw new PendingException("Unknown comparison '$comparison'. Use 'less', 'more' or 'equal'");
        }
    }
}
