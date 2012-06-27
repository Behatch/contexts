<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\TranslatedContextInterface;

abstract class BaseContext extends BehatContext implements TranslatedContextInterface
{
    /**
     * Shortcut for retrieving Mink context
     *
     * @return \Behat\MinkExtension\Context\MinkContext
     */
    public function getMinkContext()
    {
        return $this->getMainContext()->getSubContext('mink');
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
