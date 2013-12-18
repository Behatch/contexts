<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\PyStringNode;

class XmlContext extends BaseContext
{
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
            $dom->loadXML($content);
            $this->throwError();
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
            throw new \DomException($error->message . ' at line ' . $error->line);
        }
    }
}
