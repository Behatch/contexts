<?php

namespace Sanpi\Behatch\Tests\Units\Json;

use \atoum;
use Sanpi\Behatch\Json\JsonSchema as TestedClass;

class JsonSchema extends atoum
{
    public function test_should_not_resolve_without_uri()
    {
        $this
            ->given(
                $schema = new TestedClass('{}'),
                $this->mockGenerator->orphanize('__construct'),
                $resolver = new \mock\JsonSchema\RefResolver
            )
            ->when(
                $schema->resolve($resolver)
            )
                ->mock($resolver)
                    ->call('resolve')
                    ->never()
        ;
    }

    public function test_should_resolve_with_uri()
    {
        $this
            ->given(
                $schema = new TestedClass('{}', 'file://test'),
                $this->mockGenerator->orphanize('__construct'),
                $resolver = new \mock\JsonSchema\RefResolver,
                $resolver->getMockController()->resolve = true
            )
            ->when(
                $result = $schema->resolve($resolver)
            )
                ->mock($resolver)
                    ->call('resolve')
                    ->withArguments('{}', 'file://test')
                    ->once()

                ->object($result)
                    ->isIdenticalTo($schema)
        ;
    }

    public function test_should_validate_correct_json()
    {
        $this
            ->given(
                $schema = new TestedClass('{}'),
                $json = new \Sanpi\Behatch\Json\Json('{}'),
                $validator = new \mock\JsonSchema\Validator,
                $validator->getMockController()->check = true
            )
            ->when(
                $result = $schema->validate($json, $validator)
            )
                ->mock($validator)
                    ->call('check')
                    ->withArguments($json, $schema)
                    ->once()

                ->boolean($result)
                    ->isTrue()
        ;
    }

    public function test_should_throw_exception_for_incorrect_json()
    {
        $this
            ->given(
                $schema = new TestedClass('{}'),
                $json = new \Sanpi\Behatch\Json\Json('{}'),
                $validator = new \mock\JsonSchema\Validator,
                $validator->getMockController()->check = false,
                $validator->getMockController()->getErrors = array(
                    array('property' => 'foo', 'message' => 'invalid'),
                    array('property' => 'bar', 'message' => 'not found')
                )
            )
            ->exception(function () use ($schema, $json, $validator) {
                $schema->validate($json, $validator);
            })
                ->hasMessage(<<<"ERROR"
JSON does not validate. Violations:
  - [foo] invalid
  - [bar] not found

ERROR
                )
        ;
    }
}
