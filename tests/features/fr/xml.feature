#language: fr
Fonctionnalité:

    Contexte:
        Étant donné je suis sur "/xml/feed.xml"

    Scénario:
        Alors le flux XML devrait être valide avec sa DTD

    Scénario:
        Alors le flux XML devrait être valide avec le XSD "tests/fixtures/www/xml/schema.xsd"

    Scénario:
        Alors le flux XML devrait être valide avec cette XSD :
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

    Scénario:
        Alors le flux XML devrait être valide avec le schéma relax NG "tests/fixtures/www/xml/schema.ng"

    Scénario:
        Alors le flux XML devrait être valide avec ce schéma relax NG :
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

    Scénario:
        Étant donné je suis sur "/xml/feed.atom"
        Alors le flux atom devrait être valide

    Scénario:
        Étant donné je suis sur "/xml/feed.rss"
        Alors le flux RSS2 devrait être valide
