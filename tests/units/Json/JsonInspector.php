<?php

namespace Sanpi\Behatch\Tests\Units\Json;

use \atoum;
use JsonSchema\RefResolver;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Sanpi\Behatch\Json\JsonInspector as TestedClass;
use Symfony\Component\PropertyAccess\PropertyAccess;

class JsonInspector extends atoum
{
    public function test_should_read_json()
    {
        $this
            ->given(
                $json = new \mock\Sanpi\Behatch\Json\Json('{}'),
                $json->getMockController()->read = 'foobar'
            )
            ->and(
                $inspector = new TestedClass('mode')
            )
            ->when(
                $result = $inspector->evaluate($json, 'foo.bar')
            )
                ->variable($result)
                    ->isEqualTo('foobar')

                ->mock($json)
                    ->call('read')
                    ->withArguments('foo.bar')
                    ->once()
        ;
    }

    public function test_should_fail_if_json_reading_fail()
    {
        $this
            ->given(
                $json = new \mock\Sanpi\Behatch\Json\Json('{}'),
                $json->getMockController()->read->throw = new \Exception()
            )
            ->and(
                $inspector = new TestedClass('mode')
            )
                ->exception(function () use ($json, $inspector) {
                    $inspector->evaluate($json, 'foo.bar');
                })
                    ->hasMessage("Failed to evaluate expression 'foo.bar'")
        ;
    }

    public function test_should_convert_expression_if_javascript_mode()
    {
        $this
            ->given(
                $json = new \mock\Sanpi\Behatch\Json\Json('{}'),
                $json->getMockController()->read = 'foobar'
            )
            ->and(
                $inspector = new TestedClass('javascript')
            )
            ->when(
                $result = $inspector->evaluate($json, 'foo->bar')
            )
                ->variable($result)
                    ->isEqualTo('foobar')

                ->mock($json)
                    ->call('read')
                    ->withArguments('foo.bar')
                    ->once()
        ;
    }

    public function test_should_no_convert_expression_if_no_javascript_mode()
    {
        $this
            ->given(
                $json = new \mock\Sanpi\Behatch\Json\Json('{}'),
                $json->getMockController()->read = 'foobar'
            )
            ->and(
                $inspector = new TestedClass('foo')
            )
            ->when(
                $result = $inspector->evaluate($json, 'foo->bar')
            )
                ->variable($result)
                    ->isEqualTo('foobar')

                ->mock($json)
                    ->call('read')
                    ->withArguments('foo->bar')
                    ->once()
        ;
    }

    public function test_should_valid_json_through_its_schema()
    {
        $this
            ->given(
                $json = new \mock\Sanpi\Behatch\Json\Json('{}'),
                $schema = new \mock\Sanpi\Behatch\Json\JsonSchema('{}'),
                $schema->getMockController()->resolve = $schema,
                $schema->getMockController()->validate = 'foobar',
                $inspector = new TestedClass('foo')
            )
            ->when(
                $result = $inspector->validate($json, $schema)
            )
                ->variable($result)
                    ->isEqualTo('foobar')

                ->mock($schema)
                    ->call('resolve')
                    ->withArguments(new RefResolver(new UriRetriever))
                    ->once()

                    ->call('validate')
                    ->withArguments($json, new Validator)
                    ->once()
        ;
    }
}
