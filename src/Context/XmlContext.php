<?php

namespace Sanpi\Behatch\Context;

use Sanpi\Behatch\Xml\Dom;
use Behat\Gherkin\Node\PyStringNode;

class XmlContext extends BaseContext
{
    /**
     * Checks that the response is correct XML
     * Example: Then the response should be in XML
     * Example: And the response should be in XML
     *
     * @Then the response should be in XML
     */
    public function theResponseShouldBeInXml()
    {
        $this->getDom();
    }

    /**
     * Checks that the response is not correct XML
     * Example: Then the response should not be in XML
     * Example: And the response should not be in XML
     * @Then the response should not be in XML
     */
    public function theResponseShouldNotBeInXml()
    {
        $this->not(
            [$this, 'theResponseShouldBeInXml'],
            'The response is in XML'
        );
    }

    /**
     * Checks that the specified XML element exists
     * Example: Then the XML element "Vehicles" should exist
     * Example: And the XML element "Vehicles" should exist
     * @param string $element
     * @throws \Exception
     * @return \DomNodeList
     * @Then the XML element :element should exist(s)
     */
    public function theXmlElementShouldExist($element)
    {
        $elements = $this->getDom()
            ->xpath($element);

        if ($elements->length == 0) {
            throw new \Exception("The element '$element' does not exist.");
        }

        return $elements;
    }

    /**
     * Checks that the specified XML element does not exist
     * Example: Then the XML element "Suits" should not exist
     * Example: And the XML element "Suits" should exist
     * @Then the XML element :element should not exist(s)
     */
    public function theXmlElementShouldNotExist($element)
    {
        $this->not(function () use($element) {
            $this->theXmlElementShouldExist($element);
        }, "The element '$element' exists.");
    }

    /**
     * Checks that the specified XML element is equal to the given value
     * Example: Then the XML element "Vehicles" should be equal to "Batmobile, Batwing, Batboat, Battank"
     * Example: And the XML element "Vehicles" should be equal to "Batmobile, Batwing, Batboat, Battank"
     *
     * @Then the XML element :element should be equal to :text
     */
    public function theXmlElementShouldBeEqualTo($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $actual = $elements->item(0)->nodeValue;

        if ($text != $actual) {
            throw new \Exception("The element value is '$actual'");
        }
    }

    /**
     * Checks that the specified XML element is not equal to the given value
     * Example: Then the XML element "Suits" should not be equal to "Santiago's BatSuit"
     * Example: And the XML element "Suits" should not be equal to "Santiago's BatSuit"
     *
     * @Then the XML element :element should not be equal to :text
     */
    public function theXmlElementShouldNotBeEqualTo($element, $text)
    {
        $this->not(function () use($element, $text) {
            $this->theXmlElementShouldBeEqualTo($element, $text);
        }, "The element '$element' value is not '$text'");
    }

    /**
     * Checks that the XML attribute on the specified element exists
     * Example: Then the XML attribute "VehicleID" on element "Vehicles" should exist
     * Example: And the XML attribute "VehicleID" on element "Vehicles" should exist
     *
     * @Then the XML attribute :attribute on element :element should exist(s)
     */
    public function theXmlAttributeShouldExist($attribute, $element)
    {
        $elements = $this->theXmlElementShouldExist("{$element}[@{$attribute}]");

        $actual = $elements->item(0)->getAttribute($attribute);

        if (empty($actual)) {
            throw new \Exception("The attribute value is '$actual'");
        }

        return $actual;
    }

    /**
     * Checks that the XML attribute on the specified element does not exist
     * Example: Then the XML attribute "VehicleID" on element "Vehicles" should not exist
     * Example: And the XML attribute "VehicleID" on element "Vehicles" should not exist
     *
     * @Then the XML attribute :attribute on element :element should not exist(s)
     */
    public function theXmlAttributeShouldNotExist($attribute, $element)
    {
        $this->theXmlElementShouldNotExist("{$element}[@{$attribute}]");
    }

    /**
     * Checks that the XML attribute on the specified element is equal to the given value
     * Example: Then the XML attribute "VehicleName" on element "Vehicles" should be equal to "Batmobile"
     * Example: And the XML attribute "VehicleName" on element "Vehicles" should be equal to "Batmobile"
     *
     * @Then the XML attribute :attribute on element :element should be equal to :text
     */
    public function theXmlAttributeShouldBeEqualTo($attribute, $element, $text)
    {
        $actual = $this->theXmlAttributeShouldExist($attribute, $element);

        if ($text != $actual) {
            throw new \Exception("The attribute value is '$actual'");
        }
    }

    /**
     * Checks that the XML attribute on the specified element is not equal to the given value
     * Example: Then the XML attribute "SuitOwner" on element "Suits" should not be equal to "David Zavimbe"
     * Example: And the XML attribute "SuitOwner" on element "Suits" should not be equal to "David Zavimbe"
     *
     * @Then the XML attribute :attribute on element :element should not be equal to :text
     */
    public function theXmlAttributeShouldNotBeEqualTo($attribute, $element, $text)
    {
        $actual = $this->theXmlAttributeShouldExist($attribute, $element);

        if ($text === $actual) {
            throw new \Exception("The attribute value is '$actual'");
        }
    }

    /**
     * Checks that the given XML element has N child element(s)
     * Example: Then the XML element "Vehicles" should have "6" elements
     * Example: And the XML element "Vehicles" should have "6" elements
     *
     * @Then the XML element :element should have :count element(s)
     */
    public function theXmlElementShouldHaveNChildElements($element, $count)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $length = 0;
        foreach ($elements->item(0)->childNodes as $node) {
            if ($node->hasAttributes() || (trim($node->nodeValue) != '')) {
                ++$length;
            }
        }

        $this->assertEquals($count, $length);
    }

    /**
     * Checks that the given XML element contains the given value
     * Example: Then the XML element "VehicleName" should contain "Batmobile"
     * Example: And the XML element "VehicleName" should contain "Batmobile"
     *
     * @Then the XML element :element should contain :text
     */
    public function theXmlElementShouldContain($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $this->assertContains($text, $elements->item(0)->nodeValue);
    }

    /**
     * Checks that the given XML element does not contain the given value
     * Example: Then the XML element "VehicleName" should not contain "Batsub"
     * Example: And the XML element "VehicleName" should not contain "Batsub"
     *
     * @Then the XML element :element should not contain :text
     */
    public function theXmlElementShouldNotContain($element, $text)
    {
        $elements = $this->theXmlElementShouldExist($element);

        $this->assertNotContains($text, $elements->item(0)->nodeValue);
    }

    /**
     * Checks that the XML uses the specified namespace
     * Example: Then the XML should use the namespace "Batman"
     * Example: And the XML should use the namespace "Batman"
     *
     * @Then the XML should use the namespace :namespace
     */
    public function theXmlShouldUseTheNamespace($namespace)
    {
        $namespaces = $this->getDom()
            ->getNamespaces();

        if (!in_array($namespace, $namespaces)) {
            throw new \Exception("The namespace '$namespace' is not used");
        }
    }

    /**
     * Checks that the XML does not use the specified namespace
     * Example: Then the XML should not use the namespace "Robin"
     * Example: And the XML should not use the namespace "Robin"
     *
     * @Then the XML should not use the namespace :namespace
     */
    public function theXmlShouldNotUseTheNamespace($namespace)
    {
        $namespaces = $this->getDom()
            ->getNamespaces();

        if (in_array($namespace, $namespaces)) {
            throw new \Exception("The namespace '$namespace' is used");
        }
    }

    /**
     * Optimistically (ignoring errors) attempt to pretty-print the last XML response
     * Example: Then print last XML response
     *
     * @Then print last XML response
     */
    public function printLastXmlResponse()
    {
        echo (string)$this->getDom();
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
     * Checks validity of XML against a DTD
     * Example: Then the XML feed should be valid according to its DTD
     * Example: And the XML feed should be valid according to its DTD
     *
     * @Then the XML feed should be valid according to its DTD
     */
    public function theXmlFeedShouldBeValidAccordingToItsDtd()
    {
        try {
            $this->getDom();
        }
        catch(\DOMException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Checks validity of XML against a provided XSD
     * Example: Then the XML feed should be valid according to the XSD "batman.xsd"
     * Example: And the XML feed should be valid according to the XSD "batman.xsd"
     *
     * @Then the XML feed should be valid according to the XSD :filename
     */
    public function theXmlFeedShouldBeValidAccordingToTheXsd($filename)
    {
        if (is_file($filename)) {
            $xsd = file_get_contents($filename);
            $this->getDom()
                ->validateXsd($xsd);
        }
        else {
            throw new \RuntimeException("The xsd doesn't exist");
        }
    }

    /**
     * Checks validity of XML against a provided XSD
     * Example: Then the XML feed should be valid according to this XSD:
     *           """
     *           <?xml version="1.0"?>
     *           <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
     *
     *           <xs:element name="note">
     *           <xs:complexType>
     *           <xs:sequence>
     *           <xs:element name="to" type="xs:string"/>
     *           <xs:element name="from" type="xs:string"/>
     *           <xs:element name="heading" type="xs:string"/>
     *           <xs:element name="body" type="xs:string"/>
     *           </xs:sequence>
     *           </xs:complexType>
     *           </xs:element>
     *
     *           </xs:schema>
     *           """
     * Example: And the XML feed should be valid according to this XSD:
     *           """
     *           <?xml version="1.0"?>
     *           <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
     *
     *           <xs:element name="note">
     *           <xs:complexType>
     *           <xs:sequence>
     *           <xs:element name="to" type="xs:string"/>
     *           <xs:element name="from" type="xs:string"/>
     *           <xs:element name="heading" type="xs:string"/>
     *           <xs:element name="body" type="xs:string"/>
     *           </xs:sequence>
     *           </xs:complexType>
     *           </xs:element>
     *
     *           </xs:schema>
     *           """
     *
     * @Then the XML feed should be valid according to this XSD:
     */
    public function theXmlFeedShouldBeValidAccordingToThisXsd(PyStringNode $xsd)
    {
        $this->getDom()
            ->validateXsd($xsd->getRaw());
    }

    /**
     * Checks validity of XML against a provided RELAX NG Schema
     * Example: Then the XML feed should be valid according to the relax NG schema "batman.xsd"
     * Example: And the XML feed should be valid according to the relax NG schema "batman.xsd"
     *
     * @Then the XML feed should be valid according to the relax NG schema :filename
     */
    public function theXmlFeedShouldBeValidAccordingToTheRelaxNgSchema($filename)
    {
        if (is_file($filename)) {
            $ng = file_get_contents($filename);
            $this->getDom()
                ->validateNg($ng);
        }
        else {
            throw new \RuntimeException("The relax NG doesn't exist");
        }
    }

    /**
     * Checks validity of XML against a provided RELAX NG Schema
     * Example: Then the XML feed should be valid according to this relax NG schema:
     *           """
     *           <element name="book" xmlns="http://relaxng.org/ns/structure/1.0">
     *           <oneOrMore>
     *           <element name="page">
     *           <text/>
     *           </element>
     *           </oneOrMore>
     *           </element>
     *           """
     * Example: And the XML feed should be valid according to this relax NG schema:
     *           """
     *           <element name="book" xmlns="http://relaxng.org/ns/structure/1.0">
     *           <oneOrMore>
     *           <element name="page">
     *           <text/>
     *           </element>
     *           </oneOrMore>
     *           </element>
     *           """
     *
     * @Then the XML feed should be valid according to this relax NG schema:
     */
    public function theXmlFeedShouldBeValidAccordingToThisRelaxNgSchema(PyStringNode $ng)
    {
        $this->getDom()
            ->validateNg($ng->getRaw());
    }

    /**
     * Checks the validity of the atom XML feed
     * Example: Then the atom feed should be valid
     *
     * @Then the atom feed should be valid
     */
    public function theAtomFeedShouldBeValid()
    {
        $this->theXmlFeedShouldBeValidAccordingToTheXsd(
            __DIR__ . '/../Resources/schemas/atom.xsd'
        );
    }

    /**
     * Checks the validity of the RSS2 feed
     * Example: Then the RSS2 feed should be valid
     *
     * @Then the RSS2 feed should be valid
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

        return new Dom($content);
    }
}
