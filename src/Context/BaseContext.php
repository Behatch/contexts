<?php

namespace Behatch\Context;

use Behat\Behat\Context\TranslatableContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;

abstract class BaseContext extends RawMinkContext implements TranslatableContext
{
    public static function getTranslationResources()
    {
        return glob(__DIR__ . '/../../i18n/*.xliff');
    }

    /**
     * en
     * @transform /^(0|[1-9]\d*)(?:st|nd|rd|th)?$/
     *
     * fr
     * @transform /^(0|[1-9]\d*)(?:ier|er|e|ème)?$/
     *
     * pt
     * @transform /^(0|[1-9]\d*)º?$/
     */
    public function castToInt($count)
    {
        return intval($count);
    }

    protected function not(Callable $callbable, $errorMessage)
    {
        try {
            $callbable();
        }
        catch (\Exception $e) {
            return;
        }

        throw new ExpectationException($errorMessage, $this->getSession());
    }

    protected function assert($test, $message)
    {
        if ($test === false) {
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertContains($expected, $actual, $message = null)
    {
        $regex   = '/' . preg_quote($expected, '/') . '/ui';

        $this->assert(
            preg_match($regex, $actual) > 0,
            $message ?: "The string '$expected' was not found."
        );
    }

    protected function assertNotContains($expected, $actual, $message = null)
    {
        $message = $message ?: "The string '$expected' was found.";

        $this->not(function () use($expected, $actual) {
                $this->assertContains($expected, $actual);
        }, $message);
    }

    protected function assertCount($expected, array $elements, $message = null)
    {
        $this->assert(
            intval($expected) === count($elements),
            $message ?: sprintf('%d elements found, but should be %d.', count($elements), $expected)
        );
    }

    protected function assertEquals($expected, $actual, $message = null)
    {
        $this->assert(
            $expected == $actual,
            $message ?: "The element '$actual' is not equal to '$expected'"
        );
    }

    protected function assertSame($expected, $actual, $message = null)
    {
        $this->assert(
            $expected === $actual,
            $message ?: "The element '$actual' is not equal to '$expected'"
        );
    }

    protected function assertArrayHasKey($key, $array, $message = null)
    {
        $this->assert(
            isset($array[$key]),
            $message ?: "The array has no key '$key'"
        );
    }

    protected function assertArrayNotHasKey($key, $array, $message = null)
    {
        $message = $message ?: "The array has key '$key'";

        $this->not(function () use($key, $array) {
            $this->assertArrayHasKey($key, $array);
        }, $message);
    }

    protected function assertTrue($value, $message = 'The value is false')
    {
        $this->assert($value, $message);
    }

    protected function assertFalse($value, $message = 'The value is true')
    {
        $this->not(function () use($value) {
            $this->assertTrue($value);
        }, $message);
    }

    protected function getMinkContext()
    {
        $context = new \Behat\MinkExtension\Context\MinkContext();
        $context->setMink($this->getMink());
        $context->setMinkParameters($this->getMinkParameters());

        return $context;
    }

    protected function countElements($element, $index, $parent)
    {
        $page = $this->getSession()->getPage();

        $parents = $page->findAll('css', $parent);
        if (!isset($parents[$index - 1])) {
            throw new \Exception("The $index element '$parent' was not found anywhere in the page");
        }

        $elements = $parents[$index - 1]->findAll('css', $element);

        return count($elements);
    }

    protected function findElement($selector, $locator, $index)
    {
        $page = $this->getSession()->getPage();

        $nodes = $page->findAll($selector, $locator);

        if (!isset($nodes[$index - 1])) {
            throw new \Exception("The $index $selector '$locator' was not found anywhere in the page");
        }

        return $nodes[$index - 1];
    }
}
