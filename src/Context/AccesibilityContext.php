<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Exception\ElementNotFoundException;

class AccesibilityContext extends BaseContext
{
    /**
     * @Then all images should have an alt attribute
     */
    public function allImagesShouldHaveAnAltAttribute()
    {
        $images = $this->getSession()->getPage()->findAll('xpath', '//img[not(@alt)]');
        if ($images !== null) {
            throw new \Exception("There are images without an alt attribute");
        } 
    }

    /**
     * @Then the title should not be longer than :arg1
     */
    public function theTitleShouldNotBeLongerThan($arg1)
    {
        $title = $this->getSession()->getPage()->find('css', 'h1')->getText();
        if (strlen($title) > $arg1) {
            throw new \Exception("The h1 title is more than '$arg1' characters long");
        }
    }

    /**
     * @Then all tables should have a table header
     */
    public function allTablesShouldHaveATableHeader()
    {
        $tables = $this->getSession()->getPage()->findAll('xpath', '//table/*[not(th)]');
        if ($tables !== null) {
            throw new \Exception("There are tables without a table header");
        }
    }

    /**
     * @Then all tables should have at least one data row
     */
    public function allTablesShouldHaveAtLeastOneDataRow()
    {
        $tables = $this->getSession()->getPage()->findAll('xpath', '//table/*[not(td)]');
        if ($tables !== null) {
            throw new \Exception("There are tables without a data row");
        }
    }    
}
