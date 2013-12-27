<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

class XmlContext extends BaseContext
{
    /**
     * Checks that the response is correct XML
     *
     * @Then /^the response should be in XML$/
     */
    public function theResponseShouldBeInXml()
    {
        $this->getDom();
    }

    /**
     * Checks that the response is not correct XML
     *
     * @Then /^the response should not be in XML$/
     */
    public function theResponseShouldNotBeInXml()
    {
        try {
            $this->getDom();
        }
        catch (\Exception $e) {
        }

        if (!isset($e)) {
            throw new \Exception("The response is in XML");
        }
    }

    /**
     * Checks that the specified XML element exists
     *
     * @param string $element
     * @throws \Exception
     * @return \DomNodeList
     *
     * @Then /^the XML element "(?P<element>[^"]*)" should exists?$/
     */
    public function theXmlElementShouldExist($element)
    {
        $elements = $this->xpath($element);

        if ($elements->length == 0) {
            throw new \Exception(sprintf("The element '%s' does not exist.", $element));
        }

        return $elements;
    }

    /**
     * Checks that the specified XML element does not exist
     *
     * @Then /^the XML element "(?P<element>[^"]*)" should not exists?$/
     */
    public function theXmlElementShouldNotExist($element)
    {
        $elements = $this->xpath($element);

        if ($elements->length != 0) {
            throw new \Exception(sprintf("The element '%s' exists.", $element));
        }
    }

    /**
     * Checks that the specified XML element is equal to the given value
     *
     * @Then /^the XML element "(?P<element>(?:[^"]|\\")*)" should be equal to "(?P<text>[^"]*)"$/
     */
    public function theXmlElementShouldBeEqualTo($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $actual = $elements->item(0)->nodeValue;

        if ($text != $actual) {
            throw new \Exception(sprintf("The element value is `%s`", $actual));
        }
    }

    /**
     * Checks that the specified XML element is not equal to the given value
     *
     * @Then /^the XML element "(?P<element>(?:[^"]|\\")*)" should not be equal to "(?P<text>[^"]*)"$/
     */
    public function theXmlElementShouldNotBeEqualTo($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $actual = $elements->item(0)->nodeValue;

        if ($text == $actual) {
            throw new \Exception(sprintf("The element value is `%s`", $actual));
        }
    }

    /**
     * Checks that the XML attribute on the specified element exists
     *
     * @Then /^the XML attribute "(?P<attribute>[^"]*)" on element "(?P<element>(?:[^"]|\\")*)" should exists?$/
     */
    public function theXmlAttributeShouldExist($attribute, $element)
    {
        $elements = $this->theXmlElementShouldExist("{$element}[@{$attribute}]");

        $actual = $elements->item(0)->getAttribute($attribute);

        if (empty($actual)) {
            throw new \Exception(sprintf("The attribute value is `%s`", $actual));
        }

        return $actual;
    }

    /**
     * Checks that the XML attribute on the specified element does not exist
     *
     * @Then /^the XML attribute "(?P<attribute>[^"]*)" on element "(?P<element>(?:[^"]|\\")*)" should not exists?$/
     */
    public function theXmlAttributeShouldNotExist($attribute, $element)
    {
        try {
            $elements = $this->theXmlElementShouldExist("{$element}[@{$attribute}]");

            $actual = $elements->item(0)->getAttribute($attribute);

            if (!empty($actual)) {
                throw new \Exception(sprintf("The element '%s' exists and contains '%s'.", $element , $elements));
            }
        }
        catch (\Exception $e) {
        }
    }

    /**
     * Checks that the XML attribute on the specified element is equal to the given value
     *
     * @Then /^the XML attribute "(?P<attribute>[^"]*)" on element "(?P<element>(?:[^"]|\\")*)" should be equal to "(?P<text>[^"]*)"$/
     */
    public function theXmlAttributeShouldBeEqualTo($attribute, $element, $text)
    {
        $actual = $this->theXmlAttributeShouldExist($attribute, $element);

        if ($text != $actual) {
            throw new \Exception(sprintf("The attribute value is `%s`", $actual));
        }
    }

    /**
     * Checks that the XML attribute on the specified element is not equal to the given value
     *
     * @Then /^the XML attribute "(?P<attribute>[^"]*)" on element "(?P<element>(?:[^"]|\\")*)" should not be equal to "(?P<text>[^"]*)"$/
     */
    public function theXmlAttributeShouldNotBeEqualTo($attribute, $element, $text)
    {
        $actual = $this->theXmlAttributeShouldExist($attribute, $element);

        if ($text === $actual) {
            throw new \Exception(sprintf("The attribute value is `%s`", $actual));
        }
    }

    /**
     * Checks that the given XML element has N child element(s)
     *
     * @Then /^the XML element "(?P<element>[^"]*)" should have (?P<nth>\d+) elements?$/
     */
    public function theXmlElementShouldHaveNChildElements($element, $nth)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $length = 0;
        foreach ($elements->item(0)->childNodes as $node) {
            if ($node->hasAttributes() || (trim($node->nodeValue) != '')) {
                ++$length;
            }
        }

        $this->assertEquals((integer) $nth, $length);
    }

    /**
     * Checks that the given XML element contains the given value
     *
     * @Then /^the XML element "(?P<element>[^"]*)" should contain "(?P<text>[^"]*)"$/
     */
    public function theXmlElementShouldContain($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $this->assertContains($text, $elements->item(0)->nodeValue);
    }

    /**
     * Checks that the given XML element does not contain the given value
     *
     * @Then /^the XML element "(?P<element>[^"]*)" should not contain "(?P<text>[^"]*)"$/
     */
    public function theXmlElementShouldNotContain($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $this->assertNotContains($text, $elements->item(0)->nodeValue);
    }

    /**
     * Checks that the XML uses the specified namespace
     *
     * @Then /^[Tt]he XML should use the namespace "(?P<namespace>[^"]*)"$/
     */
    public function theXmlShouldUseTheNamespace($namespace)
    {
        $xml = $this->getSimpleXml();
        $namespaces = $xml->getNamespaces(true);

        if (!in_array($namespace, $namespaces)) {
            throw new \Exception(sprintf("The namespace '%s' is not used", $namespace));
        }
    }

    /**
     * Checks that the XML does not use the specified namespace
     *
     * @Then /^[Tt]he XML should not use the namespace "(?P<namespace>[^"]*)"$/
     */
    public function theXmlShouldNotUseTheNamespace($namespace)
    {
        $xml = $this->getSimpleXml();
        $namespaces = $xml->getNamespaces(true);

        if (in_array($namespace, $namespaces)) {
            throw new \Exception(sprintf("The namespace '%s' is used", $namespace));
        }
    }

    /**
     * @param string $element
     * @return \DomNodeList
     */
    public function xpath($element)
    {
        $dom = $this->getDom();
        $xpath = new \DOMXpath($dom);
        $xml = $this->getSimpleXml($dom);
        $namespaces = $xml->getNamespaces(true);

        foreach ($namespaces as $prefix => $namespace) {
            if (empty($prefix)) {
                $prefix = 'rootns';
            }
            $xpath->registerNamespace($prefix, $namespace);
        }

        // "fix" queries to the default namespace if any namespaces are defined
        if (!empty($namespaces)) {
            for ($i=0; $i < 2; ++$i) {
                $element = preg_replace('/\/(\w+)(\[[^]]+\])?\//', '/rootns:$1$2/', $element);
            }
            $element = preg_replace('/\/(\w+)(\[[^]]+\])?$/', '/rootns:$1$2', $element);
        }

        $elements = $xpath->query($element);

        return ($elements === false) ? new \DOMNodeList() : $elements;
    }

    /**
     * @param \DomDocument $dom
     * @return \SimpleXMLElement
     */
    private function getSimpleXml($dom = null)
    {
        return simplexml_import_dom($dom ? $dom : $this->getDom());
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        libxml_clear_errors();
        libxml_use_internal_errors(true);
    }

    /**
     * @Then /^the XML feed should be valid according to its DTD$/
     */
    public function theXmlFeedShouldBeValidAccordingToItsDtd()
    {
        $dom = $this->getDom();
        try {
            $dom->validate();
            $this->throwError();
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @Then /^the XML feed should be valid according to the XSD "(?P<filename>[^"]*)"$/
     */
    public function theXmlFeedShouldBeValidAccordingToTheXsd($filename)
    {
        if (is_file($filename)) {
            $dom = $this->getDom();
            $xsd = file_get_contents($filename);
            $this->schemaValidate($dom, $xsd);
        }
        else {
            throw new \RuntimeException(
                'The xsd doesn\'t exist'
            );
        }
    }

    /**
     * @Then /^the XML feed should be valid according to this XSD:$/
     */
    public function theXmlFeedShouldBeValidAccordingToThisXsd(PyStringNode $xsd)
    {
        $dom = $this->getDom();
        $this->schemaValidate($dom, $xsd->getRaw());
    }

    private function schemaValidate(\DomDocument $dom, $xsd)
    {
        try {
            $dom->schemaValidateSource($xsd);
            $this->throwError();
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @Then /^the XML feed should be valid according to the relax NG schema "(?P<filename>[^"]*)"$/
     */
    public function theXmlFeedShouldBeValidAccordingToTheRelaxNgSchema($filename)
    {
        if (is_file($filename)) {
            $dom = $this->getDom();
            $ng = file_get_contents($filename);
            $this->relaxNGValidate($dom, $ng);
        }
        else {
            throw new \RuntimeException(
                'The relax NG doesn\'t exist'
            );
        }
    }

    /**
     * @Then /^the XML feed should be valid according to this relax NG schema:$/
     */
    public function theXmlFeedShouldBeValidAccordingToThisRelaxNgSchema(PyStringNode $ng)
    {
        $dom = $this->getDom();
        $this->relaxNGValidate($dom, $ng->getRaw());
    }

    private function relaxNGValidate(\DomDocument $dom, $ng)
    {
        try {
            $dom->relaxNGValidateSource($ng);
            $this->throwError();
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @Then /^the atom feed should be valid$/
     */
    public function theAtomFeedShouldBeValid()
    {
        $this->theXmlFeedShouldBeValidAccordingToTheXsd(
            __DIR__ . '/../Resources/schemas/atom.xsd'
        );
    }

    /**
     * @Then /^the RSS2 feed should be valid$/
     */
    public function theRss2FeedShouldBeValid()
    {
        $this->theXmlFeedShouldBeValidAccordingToTheXsd(
            __DIR__ . '/../Resources/schemas/rss-2.0.xsd'
        );
    }

    private function getDom()
    {
        $content = $this->getSession()->getPage()->getContent();

        $dom = new \DomDocument();
        try {
            $dom->strictErrorChecking = false;
            $dom->validateOnParse = false;
            $dom->loadXML($content, LIBXML_PARSEHUGE);
            $this->throwError($dom);
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $dom;
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
