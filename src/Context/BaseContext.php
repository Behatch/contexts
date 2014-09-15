<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\TranslatableContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;

abstract class BaseContext extends RawMinkContext implements TranslatableContext
{
    private $parameters;

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

    protected function assertContains($expected, $actual, $message = null)
    {
        $regex   = '/'.preg_quote($expected, '/').'/ui';

        if (!preg_match($regex, $actual)) {
            if (is_null($message)) {
                $message = sprintf('The string "%s" was not found.', $expected);
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertNotContains($expected, $actual, $message = null)
    {
        $regex   = '/'.preg_quote($expected, '/').'/ui';

        if (preg_match($regex, $actual)) {
            if (is_null($message)) {
                $message = sprintf('The string "%s" was found.', $expected);
            }
            throw new ExpectationException($message, $this->getSession());
        }
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
                $message = sprintf(
                    'The element "%s" is not equal to "%s"',
                    $actual,
                    $expected
                );
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertSame($expected, $actual, $message = null)
    {
        if ($expected !== $actual) {
            if (is_null($message)) {
                $message = sprintf(
                    'The element "%s" is not equal to "%s"',
                    $actual,
                    $expected
                );
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertArrayHasKey($key, $array, $message = null)
    {
        if (!isset($array[$key])) {
            if (is_null($message)) {
                $message = sprintf('The array has no key "%s"', $key);
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertArrayNotHasKey($key, $array, $message = null)
    {
        if (isset($array[$key])) {
            if (is_null($message)) {
                $message = sprintf('The array has key "%s"', $key);
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertTrue($value, $message = null)
    {
        if (!$value) {
            if (is_null($message)) {
                $message = sprintf('The value is false');
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function assertFalse($value, $message = null)
    {
        if ($value) {
            if (is_null($message)) {
                $message = sprintf('The value is true');
            }
            throw new ExpectationException($message, $this->getSession());
        }
    }

    protected function getMinkContext()
    {
        $context = new \Behat\MinkExtension\Context\MinkContext();
        $context->setMink($this->getMink());
        $context->setMinkParameters($this->getMinkParameters());

        return $context;
    }
}
