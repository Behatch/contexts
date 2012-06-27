<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\TranslatedContextInterface;

abstract class BaseContext extends BehatContext implements TranslatedContextInterface
{
    public function getMinkContext()
    {
        return $this->getMainContext()->getSubContext('mink');
    }

    public function getTranslationResources()
    {
        return glob(__DIR__.'/../../../../../i18n/*.xliff');
    }
}
