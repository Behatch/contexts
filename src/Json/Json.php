<?php

namespace Behatch\Json;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class Json
{
    protected $content;

    public function __construct($content)
    {
        $this->content = $this->decode((string) $content);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function read($expression, PropertyAccessor $accessor)
    {
        if (is_array($this->content)) {
            $expression =  preg_replace('/^root/', '', $expression);
        } else {
            $expression =  preg_replace('/^root./', '', $expression);
        }

        // If root asked, we return the entire content
        if (strlen(trim($expression)) <= 0) {
            return $this->content;
        }

        return $accessor->getValue($this->content, $expression);
    }

    public function encode($pretty = true)
    {
        if (true === $pretty && defined('JSON_PRETTY_PRINT')) {
            // Cannot test this part JSON_PRETTY_PRINT is only 5.4
            return json_encode($this->content, JSON_PRETTY_PRINT);
        }

        return json_encode($this->content);
    }

    public function __toString()
    {
        return $this->encode(false);
    }

    private function decode($content)
    {
        $result = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("The string '$content' is not valid json");
        }

        return $result;
    }
}
