<?php

namespace Sanpi\Behatch\Tests\Units\Json;

use Symfony\Component\PropertyAccess\PropertyAccess;

class Json extends \atoum
{
    public function test_should_not_decode_invalid_json()
    {
        $this
            ->exception(function () {
                $json = $this->newTestedInstance('{{json');
            })
                ->hasMessage("The string '{{json' is not valid json")
        ;
    }

    public function test_should_decode_valid_json()
    {
        try {
            $this
                ->given(
                    $hasException = false
                )
                ->when(
                    $json = $this->newTestedInstance('{"foo": "bar"}')
                )
            ;
        } catch (\Exception $e) {
            $hasException = true;
        }

        $this->boolean($hasException)->isFalse();
    }

    public function test_should_encode_valid_json()
    {
        $this
            ->given(
                $content = '{"foo":"bar"}'
            )
            ->when(
                $json = $this->newTestedInstance($content)
            )
            ->castToString($json)
                ->isEqualTo($content)
        ;
    }

    public function test_should_not_read_invalid_expression()
    {
        $this
            ->given(
                $accessor = PropertyAccess::createPropertyAccessor(),
                $json = $this->newTestedInstance('{"foo":"bar"}')
            )
            ->exception(function () use ($json, $accessor) {
                $json->read('jeanmarc', $accessor);
            })
                ->isInstanceOf('Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException')
        ;
    }

    public function test_should_read_valid_expression()
    {
        $this
            ->given(
                $accessor = PropertyAccess::createPropertyAccessor(),
                $json = $this->newTestedInstance('{"foo":"bar"}')
            )
            ->when(
                $result = $json->read('foo', $accessor)
            )
                ->string($result)
                    ->isEqualTo('bar')
        ;
    }
}
