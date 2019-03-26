<?php

namespace Behatch\Tests\Units\Json;

class JsonSchema extends \atoum
{
    public function test_resolve_without_uri()
    {
        $schema = $this->newTestedInstance('{}');
        $resolver = new \JsonSchema\SchemaStorage(new \JsonSchema\Uri\UriRetriever, new \JsonSchema\Uri\UriResolver);
        $schema->resolve($resolver);
    }

    public function test_resolve_with_uri()
    {
        $file = 'file://' . __DIR__ . '/../../fixtures/files/schema.json';
        $schema = (object)['id' => $file];
        $resolver = new \JsonSchema\SchemaStorage(new \JsonSchema\Uri\UriRetriever, new \JsonSchema\Uri\UriResolver);
        $result = $resolver->resolveRef($file);

        $this->object($result)
            ->isEqualTo($schema);
    }

    public function test_validate()
    {
        $schema = $this->newTestedInstance('{}');
        $json = new \Behatch\Json\Json('{}');
        $validator = new \JsonSchema\Validator();
        $result = $schema->validate($json, $validator);

        $this->boolean($result)
            ->isTrue();
    }

    public function test_validate_invalid()
    {
        $schema = $this->newTestedInstance('{ "type": "object", "properties": {}, "additionalProperties": false }');
        $json = new \Behatch\Json\Json('{ "foo": { "bar": "foobar" } }');
        $validator = new \JsonSchema\Validator();
        $this->exception(function () use($schema, $json, $validator) {
            $schema->validate($json, $validator);
        })
        ->hasMessage(<<<EOD
JSON does not validate. Violations:
  - [] The property foo is not defined and the definition does not allow additional properties
JSON is:
{
    "foo": {
        "bar": "foobar"
    }
}

EOD
        );
    }
}
