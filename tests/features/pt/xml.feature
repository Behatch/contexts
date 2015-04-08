#language: pt
Funcionalidade: Testando o XmlContext

  Contexto:
    Quando Eu estou em "/xml/feed.xml"

  Cenário: Eu sou um XML?
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/feed.atom"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/feed.rss"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/book.xml"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/people.xml"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/country.xml"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/needsformatting.xml"
    Então a resposta deve estar em XML
    Quando Eu estou em "/xml/imnotaxml.xml"
    Então a resposta não deve estar em XML
    Quando Eu estou em "/xml/notfound.xml"
    Então a resposta não deve estar em XML

  Cenário: Validação com DTD
    Então o XML deve ser válido de acordo com o seu DTD

  Cenário: Validação com um arquivo XSD
    Então o XML deve ser válido de acordo com o XSD "tests/fixtures/www/xml/schema.xsd"

  Cenário: Validação com um XSD inline
    Então o XML deve ser válido de acordo com esse XSD:
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

  Cenário: Validação com um arquivo relax NG
    Então o XML deve ser válido de acordo com o schema relax NG "tests/fixtures/www/xml/schema.ng"

  Cenário: Validação com relax NG inline
    Então o XML deve ser válido de acordo com esse schema relax NG:
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

  Cenário: Validação de feed Atom
    Quando Eu estou em "/xml/feed.atom"
    Então o feed atom deve ser válido

  Cenário: Validação de feed RSS
    Quando Eu estou em "/xml/feed.rss"
    Então o feed RSS2 deve ser válido

  Cenário: Verifica a interpretação do XML
    Quando Eu estou em "/xml/book.xml"
    Então o elemento XML "//book/chapter/title" deve existir
    E o elemento XML "//book/chapter/index" não deve existir
    E o elemento XML "//book/chapter/title" deve ser igual a "My books"
    E o elemento XML "//book/title" não deve ser igual a "My wonderful lists"
    E o elemento "//book/chapter/para/informaltable/tgroup" deve possuir o atributo XML "cols"
    E o elemento "//book/chapter/title" não deve possuir o atributo XML "color"
    E o atributo XML "id" no elemento "//book/chapter" deve ser igual a "books"
    E o atributo XML "id" no elemento "//book" não deve ser igual a "choices"
    E o elemento XML "//book/chapter/para/informaltable/tgroup/tbody" deve ter 3 elementos
    E o elemento XML "//book/title" deve conter "is"
    E o elemento XML "//book/chapter/title" não deve conter "if"

  Cenário: Verifica a interpretação do XML com namespaces e namespace default
    Quando Eu estou em "/xml/country.xml"
    Então o XML deve utilizar o namespace "http://example.org/xsd/country"
    E o elemento XML "//country/airports" deve existir
    E o elemento XML "//country/cities/city:city/city:park" deve existir
    E o elemento XML "//country/treasure" não deve existir
    E o atributo XML "opened" no elemento "//city:city[@id=1]/city:park" deve ser igual a "1873"
    E o atributo XML "attraction" no elemento "//city:city[@id=2]/city:park" não deve ser igual a "Fireworks"
    E o elemento "//country" deve possuir o atributo XML "version"
    E o elemento "//country/airports/city:airport" não deve possuir o atributo XML "typo"
    E o elemento XML "//country/cities" deve ter 2 elementos
    E o elemento XML "//country/cities/city:city[@id=2]" deve ter 1 elemento

  Cenário: Verifica a interpretação do XML com namespaces, mas sem namespace default
    Quando Eu estou em "/xml/people.xml"
    Então o XML deve utilizar o namespace "http://example.org/ns"
    E o XML não deve utilizar o namespace "http://example.org/test"
    E o elemento XML "//people" deve existir
    E o elemento XML "//people/p:person" deve existir
    E o elemento XML "//people/description" não deve existir
    E o elemento XML "//people/p:person[@id=1]/items/item[@id=1]" deve ser igual a "Rubber Ducky"
    E o elemento XML "//people" deve ter 3 elementos
    E o atributo XML "name" no elemento "//people/p:person[@id=1]" deve ser igual a "Bert"
    E o atributo XML "id" no elemento "//people/p:person[@id=2]" não deve ser igual a "4"
    E o elemento "//people/p:person[@id=3]" deve possuir o atributo XML "name"
    E o elemento "//people/p:person[@id=1]/items/item" não deve possuir o atributo XML "size"

  Cenário: Exibe o XML formatado
    Quando Eu estou em "/xml/needsformatting.xml"
    E exiba a última resposta XML
