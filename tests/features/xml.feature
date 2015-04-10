Feature: Testing XmlContext

    Background:
        Given I am on "/xml/feed.xml"

    Scenario: Am I a XML ?
        Then the response should be in XML
        When I am on "/xml/feed.atom"
        Then the response should be in XML
        When I am on "/xml/feed.rss"
        Then the response should be in XML
        When I am on "/xml/book.xml"
        Then the response should be in XML
        When I am on "/xml/people.xml"
        Then the response should be in XML
        When I am on "/xml/country.xml"
        Then the response should be in XML
        When I am on "/xml/needsformatting.xml"
        Then the response should be in XML
        When I am on "/xml/imnotaxml.xml"
        Then the response should not be in XML
        When I am on "/xml/notfound.xml"
        Then the response should not be in XML

    Scenario: Validation with DTD
        Then the XML feed should be valid according to its DTD

    Scenario: Validation with XSD file
        Then the XML feed should be valid according to the XSD "tests/fixtures/www/xml/schema.xsd"

    Scenario: Validation with inline XSD
        Then the XML feed should be valid according to this XSD:
            """
            <?xml version="1.0" encoding="UTF-8"?>
            <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
                <xs:element name="page">
                    <xs:complexType>
                        <xs:sequence>
                            <xs:element name="title" type="xs:string" />
                            <xs:element name="content" type="xs:string" />
                            <xs:element name="comment" type="xs:string" />
                        </xs:sequence>
                    </xs:complexType>
                </xs:element>
            </xs:schema>
            """

    Scenario: Validation with relax NG file
        Then the XML feed should be valid according to the relax NG schema "tests/fixtures/www/xml/schema.ng"

    Scenario: Validation with inline relax NG
        Then the XML feed should be valid according to this relax NG schema:
            """
            <?xml version="1.0" encoding="UTF-8"?>
            <grammar ns="" xmlns="http://relaxng.org/ns/structure/1.0"
              datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes">
                <start>
                    <element name="page">
                        <element name="title">
                            <data type="string"/>
                        </element>
                        <element name="content">
                            <data type="string"/>
                        </element>
                        <element name="comment">
                            <data type="string"/>
                        </element>
                    </element>
                </start>
            </grammar>
            """

    Scenario: Atom feed validation
        Given I am on "/xml/feed.atom"
        Then the atom feed should be valid

    Scenario: RSS feed validation
        Given I am on "/xml/feed.rss"
        Then the RSS2 feed should be valid

    Scenario: Check XML evaluation
        Given I am on "/xml/book.xml"
         Then the XML element "//book/chapter/title" should exist
          And the XML element "//book/chapter/index" should not exist
          And the XML element "//book/chapter/title" should be equal to "My books"
          And the XML element "//book/title" should not be equal to "My wonderful lists"
          And the XML attribute "cols" on element "//book/chapter/para/informaltable/tgroup" should exist
          And the XML attribute "color" on element "//book/chapter/title" should not exist
          And the XML attribute "id" on element "//book/chapter" should be equal to "books"
          And the XML attribute "id" on element "//book" should not be equal to "choices"
          And the XML element "//book/chapter/para/informaltable/tgroup/tbody" should have 3 elements
          And the XML element "//book/title" should contain "is"
          And the XML element "//book/chapter/title" should not contain "if"

    Scenario: Check XML evaluation with namespaces and a default namespace
        Given I am on "/xml/country.xml"
         Then the XML should use the namespace "http://example.org/xsd/country"
          And the XML element "//country/airports" should exist
          And the XML element "//country/cities/city:city/city:park" should exist
          And the XML element "//country/treasure" should not exist
          And the XML attribute "opened" on element "//city:city[@id=1]/city:park" should be equal to "1873"
          And the XML attribute "attraction" on element "//city:city[@id=2]/city:park" should not be equal to "Fireworks"
          And the XML attribute "version" on element "//country" should exist
          And the XML attribute "typo" on element "//country/airports/city:airport" should not exist
          And the XML element "//country/cities" should have 2 elements
          And the XML element "//country/cities/city:city[@id=2]" should have 1 element

    Scenario: Check XML evaluation with namespaces but no default namespace
        Given I am on "/xml/people.xml"
         Then the XML should use the namespace "http://example.org/ns"
          And the XML should not use the namespace "http://example.org/test"
          And the XML element "//people" should exist
          And the XML element "//people/p:person" should exist
          And the XML element "//people/description" should not exist
          And the XML element "//people/p:person[@id=1]/items/item[@id=1]" should be equal to "Rubber Ducky"
          And the XML element "//people" should have 3 elements
          And the XML attribute "name" on element "//people/p:person[@id=1]" should be equal to "Bert"
          And the XML attribute "id" on element "//people/p:person[@id=2]" should not be equal to "4"
          And the XML attribute "name" on element "//people/p:person[@id=3]" should exist
          And the XML attribute "size" on element "//people/p:person[@id=1]/items/item" should not exist

    Scenario: Pretty print xml
       Given I am on "/xml/needsformatting.xml"
         And print last XML response
