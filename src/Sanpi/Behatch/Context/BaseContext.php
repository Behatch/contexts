<?php

namespace Sanpi\Behatch\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\TranslatedContextInterface;

abstract class BaseContext extends RawMinkContext implements TranslatedContextInterface
{
    public function getTranslationResources()
    {
        return glob(__DIR__ . '/../../../../i18n/*.xliff');
    }

    protected function getParameter($extension, $name)
    {
        return $this->getMainContext()->getSubContext('behatch')
            ->getParameter($extension, $name);
    }
}
