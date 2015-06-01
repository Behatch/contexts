<?php

namespace Sanpi\Behatch\Context;

/**
 * Set this to your local timezone
 */
date_default_timezone_set('America/New_York');

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class BrowserContext extends BaseContext implements Context, SnippetAcceptingContext
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

    //Window sizes for Mac and Windows

    /**
     * Sets browser window to custom size
     * Example: Given I set browser window size to "1900" x "1200"
     * Example: When I set browser window size to "800" x "400"
     * Example: And I set browser window size to "2880" x "1800"
     *
     * @Given (I )set my browser window size to :width x :height
     * @param string $width, $height The message.
     */
    public function iSetMyBrowserWindowSizeToX($width, $height) {
        $this->getSession()->getDriver()->resizeWindow((int)$width, (int)$height, 'current');
    }
    /**
     * Sets browser window to 1440 by 900
     * Example: Given I set my browser window size to MacBook Standard
     * Example: When I set my browser window size to MacBook Standard
     * Example: And I set my browser window size to MacBook Standard
     *
     * @Given (I )set my browser window size to MacBook Air
     */
    public function iSetMyBrowserWindowSizeToMacbookStandard()
    {
        $this->getSession()->getDriver()->resizeWindow((int)'1440', (int)'900', 'current');
    }

    /**
     * Sets browser window to 2880 by 1800
     * Example: Given I set my browser window size to 15 inch MacBook Retina
     * Example: When I set my browser window size to 15 inch MacBook Retina
     * Example: And I set my browser window size to 15 inch MacBook Retina
     *
     * @Given I set my browser window size to 15 inch MacBook Retina
     */
    public function iSetMyBrowserWindowSizeTo15InchMacbookRetina()
    {
        $this->getSession()->getDriver()->resizeWindow((int)'2880', (int)'1800', 'current');
    }

    /**
     * Sets browser window to 2560 by 1600
     * Example: Given I set my browser window size to 13 inch MacBook Retina
     * Example: When I set my browser window size to 13 inch MacBook Retina
     * Example: And I set my browser window size to 13 inch MacBook Retina
     *
     * @Given (I )set my browser window size to 13 inch MacBook Retina
     */
    public function iSetMyBrowserWindowSizeTo13InchMacbookRetina()
    {
        $this->getSession()->getDriver()->resizeWindow((int)'2560', (int)'1600', 'current');
    }

    /**
     * Sets browser window to 1280 by 1280
     * Example: Given I set my browser window size to Windows Standard
     * Example: When I set my browser window size to Windows Standard
     * Example: And I set my browser window size to Windows Standard
     *
     * @Given (I )set my browser window size to Windows Standard
     */
    public function iSetMyBrowserWindowSizeToWindowsStandard()
    {
        $this->getSession()->getDriver()->resizeWindow((int)'1280', (int)'1280', 'current');
    }

    //HTTP Authentication

    /**
     * Set login / password for next HTTP authentication
     * Example: Given I set authentication with "bwayne" and "iLoveBats"
     * Example: When I set authentication with "bwayne" and "iLoveBats"
     * Example: And I set authentication with "bwayne" and "iLoveBats"
     *
     * @When (I )set basic authentication with :user and :password
     * @param $user
     * @param $password
     */
    public function iSetBasicAuthenticationWithAnd($user, $password)
    {
        $this->getSession()->setBasicAuth($user, $password);
    }

    //Go to URL with parameters

    /**
     * Open url with various parameters
     * Change line:128 $url variable to url of choice
     * Example: Given I am on url composed by:
     *          | parameters |
     *          | /heroes |
     *          | /batman |
     * Example: When I am on url composed by:
     *          | parameters |
     *          | /heroes |
     *          | /batman |
     * Example: And I am on url composed by:
     *          | parameters |
     *          | /heroes |
     *          | /batman |
     *
     * @Given (I )am on url composed by:
     * @param TableNode $tableNode
     */
    public function iAmOnUrlComposedBy(TableNode $tableNode)
    {
        $url = 'http://adcade.com/';
        foreach ($tableNode->getHash() as $hash) {
            $url .= $hash['parameters'];
        }

        return $this->getMinkContext()
            ->visit($url);
    }

    //Clicks CSS element

    /**
     * Clicks on the nth CSS element
     * Example: When I click on 1st "ul li a" element
     * Example: And I click on 6th "ul li a" element
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
     * Confirms the popup with "OK" press
     * Example: When I confirm the popup
     * Example: And I confirm the popup
     *
     * @When /^I confirm the popup$/
     */
    public function confirmPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }
    /**
     * Cancels the popup with "OK" press
     * Example: When I cancel the popup
     * Example: And I cancel the popup
     *
     * @When /^(?:|I )cancel the popup$/
     */
    public function cancelPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }

    /**
     * Asserts string in popup
     * Example: And I should see "Bruce Wayne is not Batman" in popup
     * Example: Then I should see "Bruce Wayne is not Batman" in popup
     *
     * @When /^I should see "([^"]*)" in popup$/
     * @param string $message The message.
     * @throws Exception
     */
    public function assertPopupMessage($message)
    {
        $alertText = $this->getSession()->getDriver()->getWebDriverSession()->getAlert_text();
        if ($alertText !== $message){
            throw new Exception("Modal dialog present: $alertText, when expected was $message");
        }
    }

    /**
     * Fills out popup field with text
     * Example: When I fill "Then why does he hang out with Dick Grayson?" in popup
     * Example: And I fill "Then why does he hang out with Dick Grayson?" in popup
     *
     * @When /^(?:|I )fill "([^"]*)" in popup$/
     * @param string $message The message.
     */
    public function setPopupText($message)
    {
        $this->getSession()->getDriver()->getWebDriverSession()->postAlert_text($message);
    }

    /**
     * Scrolls to the bottom of the given page
     * Example: Given I scroll to the bottom
     * Example: When I scroll to the bottom
     * Example: And I scroll to the bottom
     *
     * @Given /^I scroll to the bottom$/
     */
    public function iScrollToBottom() {
        $javascript = 'window.scrollTo(0, Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, document.documentElement.clientHeight));';
        $this->getSession()->executeScript($javascript);
    }

    /**
     * Scrolls to the top of the given page
     * Example: Given I scroll to the top
     * Example: When I scroll to the top
     * Example: And I scroll to the top
     *
     * @Given /^I scroll to the top$/
     */
    public function iScrollToTop() {
        $this->getSession()->executeScript('window.scrollTo(0,0);');
    }

    /**
     * Scroll to a certain element by label.
     * Requires an "id" attribute to uniquely identify the element in the document.
     *
     * Example: Given I scroll to the "Submit" button
     * Example: Given I scroll to the "My Date" field
     *
     * @Given /^I scroll to the "([^"]*)" (field|link|button)$/
     */
    public function iScrollToField($locator, $type) {
        $page = $this->getSession()->getPage();
        $el = $page->find('named', array($type, $locator));
        # assertNotNull($el, sprintf('%s element not found', $locator));
        $id = $el->getAttribute('id');
        if(empty($id)) {
            throw new \InvalidArgumentException('Element requires an "id" attribute');
        }
        $js = sprintf("document.getElementById('%s').scrollIntoView(true);", $id);
        $this->getSession()->executeScript($js);
    }

    /**
     * Restarts Selenium session
     * Example: Given I am on a new session
     * Example: And I am on a new session
     *
     * @Given /^I am on a new session$/
     */
    public function iAmOnANewSession()
    {
        $this->getSession()->restart();
    }

    /**
     * Clicks on element via XPath
     * Example: When I click on the element with xpath '//*[@id="find-out-who-batman-is"]'
     * Example: And I click on the element with xpath '//*[@id="find-out-who-batman-is"]'
     *
     * @When /^I click on the element with xpath \'([^\']*)\'$/
     * @Given /^I click on the element with xpath "([^"]*)"$/
     * @param string $xpath is an XPath for an object
     */
    public function iClickOnTheElementWithXPath($xpath)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
        ); // runs the actual query and returns the element

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
        }

        // ok, let's click on it
        $element->click();
    }

    //Follows XPath element
    //TODO: Fix this function

    /**
     * THIS FUNCTION IS CURRENTLY DEPRECATED AND NEEDS TLC
     * Click on the nth specified link
     * Example: When I click on 1st "ul li a" link
     * Example: And I click on 6th "ul li a" link
     *
     * @When (I )follow the :index :link link
     */
    public function iFollowTheNthLink($index, $link)
    {
        $page = $this->getSession()->getPage();

        $links = $page->findAll('named', [
            'link', $this->getSession()->getSelectorsHandler()->xpathLiteral($link)
        ]);

        if (!isset($links[$index - 1])) {
            throw new \Exception("The $index element '$link' was not found anywhere in the page");
        }

        $links[$index - 1]->click();
    }

    /**
     * Fills in form field with current date
     * Example: When I fill in "unix-date" with the current date
     * Example: And I fill in "unix-date" with the current date
     *
     * @When (I )fill in :field with the current date
     */
    public function iFillInWithTheCurrentDate($field)
    {
        return $this->iFillInWithTheCurrentDateAndModifier($field, 'now');
    }

    /**
     * Fills in form field with current date and string to time (strtotime) modifier
     * Example: When I fill in "unix-date" with the current date and modifier "+1 day"
     * Example: And I fill in "unix-date" with the current date
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
     * Example: When I hover "large-button"
     * Example: And I hover "large-button"
     *
     * @When (I )hover :element
     */
    public function iHoverIShouldSeeIn($element)
    {
        $node = $this->getSession()->getPage()->find('css', $element);
        if ($node === null) {
            throw new \Exception("The hoverable element '$element' was not found anywhere in the page");
        }
        $node->mouseOver();
    }

    /**
     * Save value of the field in parameters array
     * Example: When I save the value of "name" in the "name" parameter
     * Example: And I save the value of "name" in the "name" parameter
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
     * Example: Given I wait 3 seconds until I see "Hello, Bruce Wayne"
     * Example: When I wait 3 seconds until I see "Hello, Bruce Wayne"
     * Example: And I wait 3 seconds until I see "Hello, Bruce Wayne"
     *
     * @Then (I )wait :count second(s) until I see :text
     */
    public function iWaitSecondsUntilISee($count, $text)
    {
        $this->iWaitSecondsUntilISeeInTheElement($count, $text, 'html');
    }

    /**
     * Checks, that the page should contains specified text after timeout
     * Example: Given I wait until I see "Hello, Bruce Wayne"
     * Example: When I wait until I see "Hello, Bruce Wayne"
     * Example: And I wait until I see "Hello, Bruce Wayne"
     *
     * @Then (I )wait until I see :text
     */
    public function iWaitUntilISee($text)
    {
        $this->iWaitSecondsUntilISee($this->timeout, $text);
    }

    /**
     * Checks, that the element contains specified text after timeout
     * Example: Given I wait 3 seconds until I see "Hello, Bruce Wayne" in the "nav" element
     * Example: When I wait 3 seconds until I see "Hello, Bruce Wayne" in the "nav" element
     * Example: And I wait 3 seconds until I see "Hello, Bruce Wayne" in the "nav" element
     *
     * @Then (I )wait :count second(s) until I see :text in the :element element
     */
    public function iWaitSecondsUntilISeeInTheElement($count, $text, $element)
    {
        $this->iWaitSecondsForElement($count, $element);

        $expected = str_replace('\\"', '"', $text);
        $node = $this->getSession()->getPage()->find('css', $element);
        $message = "The text '$expected' was not found after a $count seconds timeout";

        $this->assertContains($expected, $node->getText(), $message);
    }


    /**
     * Checks, that the element contains specified text after timeout
     * Example: Given I wait until I see "Hello, Bruce Wayne" in the "nav" element
     * Example: When I wait until I see "Hello, Bruce Wayne" in the "nav" element
     * Example: And I wait until I see "Hello, Bruce Wayne" in the "nav" element
     *
     * @Then (I )wait until I see :text in the :element element
     */
    public function iWaitUntilISeeInTheElement($text, $element)
    {
        $this->iWaitSecondsUntilISeeInTheElement($this->timeout, $text, $element);
    }

    /**
     * Wait for a element
     * Example: Given I wait 1 second for "sign-up" element
     * Example: When I wait 2 seconds for "sign-up" element
     * Example: And I wait 3 seconds for "sign-up" element
     *
     * @Then (I )wait :count second(s) for :element element
     */
    public function iWaitSecondsForElement($count, $element)
    {
        $found = false;
        $startTime = time();

        do {
            try {
                $node = $this->getSession()->getPage()->findAll('css', $element);
                $this->assertCount(1, $node);
                $found = true;
            }
            catch (ExpectationException $e) {
                /* Intentionnaly leave blank */
            }
        }
        while (time() - $startTime < $count);

        if ($found === false) {
            $message = "The element '$element' was not found after a $count seconds timeout";
            throw new ResponseTextException($message, $this->getSession(), $e);
        }
    }

    /**
     * Checks, that the page should contains specified element after timeout
     * Example: Given I wait for "sign-up" element
     * Example: When I wait for "sign-up" element
     * Example: And I wait for "sign-up" element
     *
     * @Then (I )wait for :element element
     */
    public function iWaitForElement($element)
    {
        $this->iWaitSecondsForElement($this->timeout, $element);
    }

    /**
     * Waits seconds
     * Example: Given I wait 1 second
     * Example: When I wait 2 second
     * Example: And I wait 3 seconds
     *
     * @Then (I )wait :count second(s)
     *
     */
    public function iWaitSeconds($count)
    {
        sleep($count);
    }

    /**
     * Waits seconds
     * Example: Given I wait for 10 seconds
     * Example: When I wait for 9 seconds
     * Example: And I wait for 8 seconds
     *
     * @Given /^I wait for (\d+) seconds$/
     *
     */
    public function iWaitForSeconds($seconds)
    {
        $this->getSession()->wait($seconds*1000);
    }

    /**
     * Asserts against number of elements in a response
     * Example: Then I should see 80 "div" in the 1st "body"
     * Example: And I should see 10 "li" in the 1st "body"
     *
     * @Then /^(?:|I )should see (?P<count>\d+) "(?P<element>[^"]*)" in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<parent>[^"]*)"$/
     *
     */
    public function iShouldSeeNElementInTheNthParent($count, $element, $index, $parent)
    {
        $actual = $this->countElements($element, $index, $parent);
        if ($actual !== $count) {
            throw new \Exception("$actual occurrences of the '$element' element in '$parent' found");
        }
    }

    /**
     * Asserts against number of elements in a response
     * Example: Then I should see less than 199 "div" in the 1st "body"
     * Example: And I should see less than 200 "li" in the 1st "body"
     *
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
     * Asserts against number of elements in a response
     * Example: Then I should see more than 10 "div" in the 1st "body"
     * Example: And I should see more than 1 "li" in the 1st "body"
     *
     * @Then (I )should see more than :count :element in the :index :parent
     *
     * @param $count
     * @param $element
     * @param $index
     * @param $parent
     * @throws \Exception
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
     * Example: Then the element ".btn .btn-default" should be enabled
     * Example: And the element ".btn .btn-default" should be enabled
     *
     * @Then the element :element should be enabled
     * @param $element
     * @throws \Exception
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
     * Example: Then the element ".btn .btn-default" should be disabled
     * Example: And the element ".btn .btn-default" should be disabled
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
     * Example: Then the "heroes" select box should contain "Batman"
     * Example: And the "heroes" select box should contain "Batman"
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
     * Example: Then the "heroes" select box should not contain "Superman"
     * Example: And the "heroes" select box should not contain "Superman"
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
     * Example: Then the ".btn" element should be visible
     * Example: And the ".btn" element should be visible
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
     * Example: Then the ".btn" element should not be visible
     * Example: And the ".btn" element should not be visible
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
     * Select a frame by its name or ID
     * Example: When I switch to iframe "justAnotherIframe"
     * Example: And I switch to frame "justAnotherIframe"
     *
     * @When (I )switch to iframe :name
     * @When (I )switch to frame :name
     */
    public function switchToIFrame($name)
    {
        $this->getSession()->switchToIFrame($name);
    }

    /**
     * Go back to main document frame
     * Example: When I switch to main frame
     * Example: And I switch to main frame
     *
     * @When (I )switch to main frame
     */
    public function switchToMainFrame()
    {
        $this->getSession()->switchToIFrame();
    }
}
