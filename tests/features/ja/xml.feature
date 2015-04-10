#language: ja
@japanese @xml
フィーチャ: Testing XmlContext

    背景:
        前提 "/xml/feed.xml" を表示している

    シナリオ: Am I a XML ?
        ならば レスポンスがXML形式であること
        もし "/xml/feed.atom" を表示している
        ならば レスポンスがXML形式であること
        もし "/xml/feed.rss" を表示している
        ならば レスポンスがXML形式であること
        もし "/xml/book.xml" を表示している
        ならば レスポンスがXML形式であること
        もし "/xml/people.xml" を表示している
        ならば レスポンスがXML形式であること
        もし "/xml/country.xml" を表示している
        ならば レスポンスがXML形式であること
        もし "/xml/needsformatting.xml" を表示している
        ならば レスポンスがXMLであること
        もし "/xml/imnotaxml.xml" を表示している
        ならば レスポンスがXML形式でないこと
        もし "/xml/notfound.xml" を表示している
        ならば レスポンスがXMLでないこと
# ならば ブレークポイントを設置する

    シナリオ: Validation with DTD
        ならば XMLフィードが自身のDTDに従っていること

    シナリオ: Validation with XSD file
        ならば XMLフィードがXSDファイル"tests/fixtures/www/xml/schema.xsd"に従っていること

# ならば ブレークポイントを設置する
    シナリオ: Validation with inline XSD
        ならば XMLフィードが下記のXSDに従っていること:
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

    シナリオ: Validation with relax NG file
        ならば XMLフィードがrelax NG schemaファイル"tests/fixtures/www/xml/schema.ng"に従っていること

# ならば ブレークポイントを設置する
    シナリオ: Validation with inline relax NG
        ならば XMLフィードが下記のrelax NG schemaに従っていること:
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

    シナリオ: Atom feed validation
        前提 "/xml/feed.atom" を表示している
        ならば atomフィードが妥当であること

    シナリオ: RSS feed validation
        前提 "/xml/feed.rss" を表示している
        ならば RSS2フィードが妥当であること

# ならば ブレークポイントを設置する
    シナリオ: Check XML evaluation
        前提 "/xml/book.xml" を表示している
         ならば XMLには "//book/chapter/title" 要素が存在していること
          かつ XMLには "//book/chapter/index" 要素が存在していないこと
          かつ XMLの "//book/chapter/title" 要素は "My books" と一致していること
          かつ XMLの "//book/title" 要素は "My wonderful lists" と一致していないこと
          かつ XMLの "//book/chapter/para/informaltable/tgroup" 要素には "cols" 属性が存在していること
          かつ XMLの "//book/chapter/title" 要素には "color" 属性が存在していないこと
          かつ XMLの "//book/chapter" 要素の "id" 属性は "books" と一致していること
          かつ XMLの "//book" 要素の "id" 属性は "choices" と一致していないこと
          かつ XMLには "//book/chapter/para/informaltable/tgroup/tbody" 要素を 3 個含んでいること
          かつ XMLの "//book/title" 要素は "is" を含んでいること
          かつ XMLの "//book/chapter/title" 要素は "if" を含んでいないこと
# ならば ブレークポイントを設置する

    シナリオ: Check XML evaluation with namespaces and a default namespace
        前提 "/xml/country.xml" を表示している
         ならば XMLは名前空間 "http://example.org/xsd/country" を使っていること
          かつ XMLには "//country/airports" 要素が存在していること
          かつ XMLには "//country/cities/city:city/city:park" 要素が存在していること
          かつ XMLには "//country/treasure" 要素が存在していないこと
          かつ XMLの "//city:city[@id=1]/city:park" 要素の "opened" 属性は "1873" と一致していること
          かつ XMLの "//city:city[@id=2]/city:park" 要素の "attraction" 属性は "Fireworks" と一致していないこと
          かつ XMLの "//country" 要素には "version" 属性が存在していること
          かつ XMLの "//country/airports/city:airport" 要素には "typo" 属性が存在していないこと
          かつ XMLには "//country/cities" 要素を 2 個含んでいること
          かつ XMLには "//country/cities/city:city[@id=2]" 要素を 1 個含んでいること

    シナリオ: Check XML evaluation with namespaces but no default namespace
        前提 "/xml/people.xml" を表示している
         ならば XMLは名前空間 "http://example.org/ns" を使っていること
          かつ XMLは名前空間 "http://example.org/test" を使っていないこと
          かつ XMLには "//people" 要素が存在していること
          かつ XMLには "//people/p:person" 要素が存在していること
          かつ XMLには "//people/description" 要素が存在していないこと
          かつ XMLの "//people/p:person[@id=1]/items/item[@id=1]" 要素は "Rubber Ducky" と一致していること
          かつ XMLには "//people" 要素を 3 個含んでいること
          かつ XMLの "//people/p:person[@id=1]" 要素の "name" 属性は "Bert" と一致していること
          かつ XMLの "//people/p:person[@id=2]" 要素の "id" 属性は "4" と一致していないこと
          かつ XMLの "//people/p:person[@id=3]" 要素には "name" 属性が存在していること
          かつ XMLの "//people/p:person[@id=1]/items/item" 要素には "size" 属性が存在していないこと

    シナリオ: Pretty print xml
       前提 "/xml/needsformatting.xml" を表示している
         かつ 最後のXMLレスポンスを表示する
