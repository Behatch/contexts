<?php

namespace Behatch\Xml;

class Dom
{
    private $dom;

    public function __construct($content)
    {
        $this->dom = new \DomDocument();
        $this->dom->strictErrorChecking = false;
        $this->dom->validateOnParse = false;
        $this->dom->preserveWhiteSpace = true;
        $this->dom->loadXML($content, LIBXML_PARSEHUGE);
        $this->throwError();
    }

    public function __toString()
    {
        $this->dom->formatOutput = true;
        return $this->dom->saveXML();
    }

    public function validate()
    {
        $this->dom->validate();
        $this->throwError();
    }

    public function validateXsd($xsd)
    {
        $this->dom->schemaValidateSource($xsd);
        $this->throwError();
    }

    public function validateNg($ng)
    {
        try {
            $this->dom->relaxNGValidateSource($ng);
            $this->throwError();
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function xpath($element)
    {
        $xpath = new \DOMXpath($this->dom);
        $this->registerNamespace($xpath);

        $element = $this->fixNamespace($element);
        $elements = $xpath->query($element);

        return ($elements === false) ? new \DOMNodeList() : $elements;
    }

    private function registerNamespace(\DOMXpath $xpath)
    {
        $namespaces = $this->getNamespaces();

        foreach ($namespaces as $prefix => $namespace) {
            if (empty($prefix) && $this->hasDefaultNamespace()) {
                $prefix = 'rootns';
            }
            $xpath->registerNamespace($prefix, $namespace);
        }
    }

    /**
     * "fix" queries to the default namespace if any namespaces are defined
     */
    private function fixNamespace($element)
    {
        $namespaces = $this->getNamespaces();

        if (!empty($namespaces) && $this->hasDefaultNamespace()) {
            for ($i = 0; $i < 2; ++$i) {
                $element = preg_replace('/\/(\w+)(\[[^]]+\])?\//', '/rootns:$1$2/', $element);
            }
            $element = preg_replace('/\/(\w+)(\[[^]]+\])?$/', '/rootns:$1$2', $element);
        }
        return $element;
    }

    private function hasDefaultNamespace()
    {
        $defaultNamespaceUri = $this->dom->lookupNamespaceURI(null);
        $defaultNamespacePrefix = $defaultNamespaceUri ? $this->dom->lookupPrefix($defaultNamespaceUri) : null;

        return empty($defaultNamespacePrefix) && !empty($defaultNamespaceUri);
    }

    public function getNamespaces()
    {
        $xml = simplexml_import_dom($this->dom);
        return $xml->getNamespaces(true);
    }

    private function throwError()
    {
        $error = libxml_get_last_error();
        if (!empty($error)) {
            // https://bugs.php.net/bug.php?id=46465
            if ($error->message != 'Validation failed: no DTD found !') {
                throw new \DomException($error->message . ' at line ' . $error->line);
            }
        }
    }
}
