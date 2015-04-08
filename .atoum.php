<?php

$runner->addTestsFromDirectory(__DIR__ . '/tests/units');

$script->addDefaultReport();

$xunitWriter = new \atoum\writers\file(__DIR__ . '/atoum.xunit.xml');

$xunitReport = new \atoum\reports\asynchronous\xunit();
$xunitReport->addWriter($xunitWriter);

$runner->addReport($xunitReport);
