<?php

namespace Behatch\Tests\Units\Json;

use JsonSchema\RefResolver;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Symfony\Component\PropertyAccess\PropertyAccess;

class JsonInspector extends \atoum
{
    public function test_evaluate()
    {
        $json = new \Behatch\Json\Json('{ "foo": { "bar": "foobar" } }');
        $inspector = $this->newTestedInstance('php');
        $result = $inspector->evaluate($json, 'foo.bar');

        $this->string($result)
            ->isEqualTo('foobar');
    }

    public function test_evaluate_invalid()
    {
        $json = new \Behatch\Json\Json('{}');
        $inspector = $this->newTestedInstance('php');

        $this->exception(function () use($json, $inspector) {
            $inspector->evaluate($json, 'foo.bar');
        })
        ->hasMessage("Failed to evaluate expression 'foo.bar'");
    }

    public function test_evaluate_javascript_mode()
    {
        $json = new \Behatch\Json\Json('{ "foo": { "bar": "foobar" } }');
        $inspector = $this->newTestedInstance('javascript');
        $result = $inspector->evaluate($json, 'foo->bar');

        $this->string($result)
            ->isEqualTo('foobar');
    }

    public function test_evaluate_php_mode()
    {
        $json = new \Behatch\Json\Json('{ "foo": { "bar": "foobar" } }');
        $inspector = $this->newTestedInstance('php');
        $result = $inspector->evaluate($json, 'foo.bar');

        $this->string($result)
            ->isEqualTo('foobar');
    }

    public function test_validate()
    {
        $json = new \Behatch\Json\Json('{ "foo": { "bar": "foobar" } }');
        $inspector = $this->newTestedInstance('php');
        $schema = new \mock\Behatch\Json\JsonSchema('{}');

        $result = $inspector->validate($json, $schema);

        $this->boolean($result)
            ->isEqualTo(true);
    }
}
