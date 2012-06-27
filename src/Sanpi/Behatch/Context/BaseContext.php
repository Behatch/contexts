<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\TranslatedContextInterface;

abstract class BaseContext extends BehatContext implements TranslatedContextInterface
{
    public function getTranslationResources()
    {
        return glob(__DIR__.'/../../../../../i18n/*.xliff');
    }

    protected function getMinkContext()
    {
        return $this->getMainContext()->getSubContext('mink');
    }

    protected function getParameter($name)
    {
        return $this->getMainContext()->getSubContext('behatch')
            ->getParameter($name);
    }
}
