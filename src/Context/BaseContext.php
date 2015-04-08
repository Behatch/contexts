<?php

namespace Sanpi\Behatch\Context;

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
     * @transform /^(0|[1-9]\d*)(?:st|nd|rd|th)?$/
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

    protected function assertContains($expected, $actual, $message = null)
    {
        $regex   = '/'.preg_quote($expected, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            if (is_null($message)) {
                $message = "The string '$expected' was not found.";
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertNotContains($expected, $actual, $message = null)
    {
        if (is_null($message)) {
            $message = "The string '$expected' was found.";
        }

        $this->not(function () use($expected, $actual) {
                $this->assertContains($expected, $actual);
        }, $message);
    }

    protected function assertCount($expected, array $elements, $message = null)
    {
        if (intval($expected) !== count($elements)) {
            if (is_null($message)) {
                $message = sprintf(
                    '%d elements found, but should be %d.',
                    count($elements),
                    $expected
                );
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertEquals($expected, $actual, $message = null)
    {
        if ($expected != $actual) {
            if (is_null($message)) {
                $message = "The element '$actual' is not equal to '$expected'";
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertSame($expected, $actual, $message = null)
    {
        if ($expected !== $actual) {
            if (is_null($message)) {
                $message = "The element '$actual' is not equal to '$expected'";
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertArrayHasKey($key, $array, $message = null)
    {
        if (!isset($array[$key])) {
            if (is_null($message)) {
                $message = "The array has no key '$key'";
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertArrayNotHasKey($key, $array, $message = null)
    {
        if (is_null($message)) {
            $message = "The array has key '$key'";
        }

        $this->not(function () use($key, $array) {
            $this->assertArrayHasKey($key, $array);
        }, $message);
    }

    protected function assertTrue($value, $message = null)
    {
        if (!$value) {
            if (is_null($message)) {
                $message = 'The value is false';
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertFalse($value, $message = null)
    {
        if (is_null($message)) {
            $message = 'The value is true';
        }

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
}
