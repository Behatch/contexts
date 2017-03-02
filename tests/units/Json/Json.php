<?php

namespace Behatch\Tests\Units\Json;

use Symfony\Component\PropertyAccess\PropertyAccess;

class Json extends \atoum
{
    public function test_construct()
    {
        $json = $this->newTestedInstance('{"foo": "bar"}');
        $this->object($json)
            ->isInstanceOf('Behatch\Json\Json');
    }

    public function test_construct_invalid_json()
    {
        $this->exception(function () {
            $json = $this->newTestedInstance('{{json');
        })
        ->hasMessage("The string '{{json' is not valid json");
    }

    public function test_to_string()
    {
        $content = '{"foo":"bar"}';
        $json = $this->newTestedInstance($content);

        $this->castToString($json)
            ->isEqualTo($content);
    }

    public function test_read()
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $json = $this->newTestedInstance('{"foo":"bar"}');
        $result = $json->read('foo', $accessor);

        $this->string($result)
            ->isEqualTo('bar');
    }

    public function test_read_invalid_expression()
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $json = $this->newTestedInstance('{"foo":"bar"}');

        $this->exception(function () use ($json, $accessor) {
            $json->read('jeanmarc', $accessor);
        })
        ->isInstanceOf('Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException');
    }
}
