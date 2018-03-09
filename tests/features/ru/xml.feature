#language: ru
Функционал: Тестирование XmlContext

    Контекст:
        Пусть я на странице "/xml/feed.xml"

    Сценарий: Я XML ?
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/feed.atom"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/feed.rss"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/book.xml"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/people.xml"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/country.xml"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/needsformatting.xml"
        Тогда ответ должен быть в XML
        Когда я на странице "/xml/imnotaxml.xml"
        Тогда ответ не должен быть в XML
        Когда я на странице "/xml/notfound.xml"
        Тогда ответ не должен быть в XML

    Сценарий: Валидация с DTD
        Тогда XML должен соответствовать его DTD

    Сценарий: Валидация с XSD файлом
        Тогда XML должен соответствовать XSD "tests/fixtures/www/xml/schema.xsd"

    Сценарий: Валидация с XSD
        Тогда XML должен соответствовать следующему XSD:
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

    Сценарий: Валидация с relax NG файлом
        Тогда XML должен соответствовать relax NG схеме "tests/fixtures/www/xml/schema.ng"

    Сценарий: Валидация с relax NG
        Тогда XML должен соответствовать следующей relax NG схеме:
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

    Сценарий: Валидация Atom
        Пусть я на странице "/xml/feed.atom"
        Тогда atom должен быть валидным

    Сценарий: Валидация RSS
        Пусть я на странице "/xml/feed.rss"
        Тогда RSS2 должен быть валидным

    Сценарий: Тестирование разбора XML
        Пусть я на странице "/xml/book.xml"
         Тогда XML элемент "//book/chapter/title" должен существовать
          И XML элемент "//book/chapter/index" не должен существовать
          И XML элемент "//book/chapter/title" должен быть равен "My books"
          И XML элемент "//book/title" не должен быть равен "My wonderful lists"
          И XML атрибут "cols" у элемента "//book/chapter/para/informaltable/tgroup" должен существовать
          И XML атрибут "color" у элемента "//book/chapter/title" не должен существовать
          И XML атрибут "id" у элемента "//book/chapter" должен быть равен "books"
          И XML атрибут "id" у элемента "//book" не должен быть равен "choices"
          И XML элемент "//book/chapter/para/informaltable/tgroup/tbody" должен содержать 3 элемента
          И XML элемент "//book/title" должен содержать "is"
          И XML элемент "//book/chapter/title" не должен содержать "if"

    Сценарий: Тестирование разбора XML с пространствами имён и пространством имён по умолчанию
        Пусть я на странице "/xml/country.xml"
         Тогда XML должен использовать пространство имён "http://example.org/xsd/country"
          И XML элемент "//country/airports" должен существовать
          И XML элемент "//country/cities/city:city/city:park" должен существовать
          И XML элемент "//country/treasure" не должен существовать
          И XML атрибут "opened" у элемента "//city:city[@id=1]/city:park" должен быть равен "1873"
          И XML атрибут "attraction" у элемента "//city:city[@id=2]/city:park" не должен быть равен "Fireworks"
          И XML атрибут "version" у элемента "//country" должен существовать
          И XML атрибут "typo" у элемента "//country/airports/city:airport" не должен существовать
          И XML элемент "//country/cities" должен содержать 2 элемента
          И XML элемент "//country/cities/city:city[@id=2]" должен содержать 1 элемент

    Сценарий: Тестирование разбора XML с пространствами имён, но без пространства имён по умолчанию
        Пусть я на странице "/xml/people.xml"
         Тогда XML должен использовать пространство имён "http://example.org/ns"
          И XML не должен использовать пространство имён "http://example.org/test"
          И XML элемент "//people" должен существовать
          И XML элемент "//people/p:person" должен существовать
          И XML элемент "//people/description" не должен существовать
          И XML элемент "//people/p:person[@id=1]/items/item[@id=1]" должен быть равен "Rubber Ducky"
          И XML элемент "//people" должен содержать 3 элемента
          И XML атрибут "name" у элемента "//people/p:person[@id=1]" должен быть равен "Bert"
          И XML атрибут "id" у элемента "//people/p:person[@id=2]" не должен быть равен "4"
          И XML атрибут "name" у элемента "//people/p:person[@id=3]" должен существовать
          И XML атрибут "size" у элемента "//people/p:person[@id=1]/items/item" не должен существовать

    Сценарий: Красивый вывод XML
       Пусть я на странице "/xml/needsformatting.xml"
         И выведи последний XML ответ
