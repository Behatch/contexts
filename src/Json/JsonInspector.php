<?php

namespace Behatch\Json;

use JsonSchema\RefResolver;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Symfony\Component\PropertyAccess\PropertyAccess;

class JsonInspector
{
    private $evaluationMode;

    private $accessor;

    public function __construct($evaluationMode)
    {
        $this->evaluationMode = $evaluationMode;
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    public function evaluate(Json $json, $expression)
    {
        if ($this->evaluationMode === 'javascript') {
            $expression = str_replace('->', '.', $expression);
        }

        try {
            return $json->read($expression, $this->accessor);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Failed to evaluate expression "%s"', $expression));
        }
    }

    public function validate(Json $json, JsonSchema $schema)
    {
        return $schema
            ->resolve(new RefResolver(new UriRetriever))
            ->validate($json, new Validator)
        ;
    }
}
