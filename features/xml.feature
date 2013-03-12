Feature: Testing XmlContext

    Background:
        Given I am on "/xml/feed.xml"

    Scenario: Validation with DTD
        Then the XML feed should be valid according to its DTD

    Scenario: Validation with XSD file
        Then the XML feed should be valid according to the XSD "fixtures/www/xml/schema.xsd"

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
        Then the XML feed should be valid according to the relax NG schema "fixtures/www/xml/schema.ng"

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
