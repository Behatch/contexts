#language: ru
Функционал: Тестирование JSONContext

    Сценарий: Я JSON ?
        Пусть я на странице "/json/imajson.json"
        Тогда ответ должен быть в JSON
        Когда я на странице "/json/emptyarray.json"
        Тогда ответ должен быть в JSON
        Когда я на странице "/json/emptyobject.json"
        Тогда ответ должен быть в JSON
        Когда я на странице "/json/imnotajson.json"
        Тогда ответ не должен быть в JSON

    Сценарий: Подсчёт элементов JSON
        Пусть я на странице "/json/imajson.json"
        Тогда узел JSON "numbers" должен содержать 4 элемента

    Сценарий: Тестирование разбора JSON
        Пусть я на странице "/json/imajson.json"

        Тогда узел JSON "foo" должен существовать
        И узел JSON "root.foo" должен существовать
        И узел JSON "foo" должен содержать "bar"
        И узел JSON "foo" не должен содержать "something else"

        И узел JSON "numbers[0]" должен содержать "öne"
        И узел JSON "numbers[1]" должен содержать "two"
        И узел JSON "numbers[2]" должен содержать "three"
        И узел JSON "numbers[3].complexeshizzle" должен быть равен "true"
        И узел JSON "numbers[3].so[0]" должен быть равен "very"
        И узел JSON "numbers[3].so[1].complicated" должен быть равен "indeed"
        И узел JSON "numbers[0]" должен соответствовать "/ö.{1}e/"
        И узел JSON "numbers[1]" должен соответствовать "/.{2}o/"
        И узел JSON "numbers[2]" должен соответствовать "/[a-z]{3}e.+/"

        И узлы JSON должны быть равны:
            | foo        | bar   |
            | numbers[0] | öne   |
            | numbers[1] | two   |
            | numbers[2] | three |

        И узлы JSON должны содержать:
            | foo        | bar   |
            | numbers[0] | öne   |
            | numbers[1] | two   |
            | numbers[2] | three |

        И узлы JSON не должны содержать:
            | foo | something else |

        И узел JSON "bar" не должен существовать

    Сценарий: Валидация Json схемой
        Пусть я на странице "/json/imajson.json"
        Тогда JSON должен соответствовать схеме "tests/fixtures/www/json/schema.json"

    Сценарий: Валидация Json схемой со ссылкой (случай невалидного JSON)
        Пусть я на странице "/json/withref-invalid.json"
        Тогда JSON не должен соответствовать схеме "tests/fixtures/www/json/schemaref.json"

    Сценарий: Валидация Json схемой со ссылкой
        Пусть я на странице "/json/withref.json"
        Тогда JSON должен соответствовать схеме "tests/fixtures/www/json/schemaref.json"

    Сценарий: Валидация Json
        Пусть я на странице "/json/imajson.json"
        Тогда JSON должен соответствовать следующей схеме:
            """
            {
                "type": "object",
                "$schema": "http://json-schema.org/draft-03/schema",
                "required":true,
                "properties": {
                    "foo": {
                        "type": "string",
                        "required":true
                    },
                    "numbers": {
                        "type": "array",
                        "required":true,
                        "öne": {
                            "type": "string",
                            "required":true
                        },
                        "two": {
                            "type": "string",
                            "required":true
                        },
                        "three": {
                            "type": "string",
                            "required":true
                        }
                    }
                }
            }
            """

    Сценарий: Глубокая валидация Json
        Пусть я на странице "/json/booking.json"
        Тогда JSON не должен соответствовать следующей схеме:
            """
            {
                "type":"object",
                "$schema": "http://json-schema.org/draft-03/schema",
                "required":false,
                "properties":{
                    "Booking": {
                        "type":"object",
                        "required":false
                    },
                    "Metadata": {
                        "type":"object",
                        "required":false,
                        "properties":{
                            "First": {
                                "type":"object",
                                "required":false,
                                "properties":{
                                    "default_value": {
                                        "type":"boolean",
                                        "required":false
                                    },
                                    "enabled": {
                                        "type":"boolean",
                                        "required":true
                                    }
                                }
                            }
                        }
                    }
                }
            }
            """

    Сценарий: Валидация содержимого Json
        Пусть я на странице "/json/imajson.json"
        Тогда JSON должен быть равен:
            """
            {
                "foo": "bar",
                "numbers": [
                    "öne",
                    "two",
                    "three",
                    {
                        "complexeshizzle": true,
                        "so": [
                            "very",
                            {
                                "complicated": "indeed"
                            }
                        ]
                    }
                ]
            }
            """
        И выведи последний JSON ответ

    Сценарий: Проверка корневого узла JSON
        Пусть я на странице "/json/rootarray.json"
        Тогда ответ должен быть в JSON
        И узел JSON "root[0].name" должен существовать
        И узел JSON "root" должен содержать 2 элемента

    Сценарий: Тестирование сравнения типов
        Пусть я на странице "/json/arraywithtypes.json"
        Тогда ответ должен быть в JSON
        И узел JSON "root[0]" должен быть null
        И узел JSON "root[1]" должен быть истиной
        И узел JSON "root[2]" должен быть ложью
        И узел JSON "root[3]" должен быть равен строке "dunglas.fr"
        И узел JSON "root[4]" должен быть равен числу 1312
        И узел JSON "root[4]" должен быть равен числу 1312.0
        И узел JSON "root[5]" должен быть равен числу 1936.2

    Сценарий: Тестирование не-null значений
        Пусть я на странице "/json/notnullvalues.json"
        Тогда ответ должен быть в JSON
        И узел JSON '' должен содержать 6 элементов
        И узел JSON "one" не должен быть null
        И узел JSON "one" должен быть ложью
        И узел JSON "two" не должен быть null
        И узел JSON "two" должен быть истиной
        И узел JSON "three" не должен быть null
        И узел JSON "three" должен быть равен строке ""
        И узел JSON "four" не должен быть null
        И узел JSON "four" должен быть равен строке "foo"
        И узел JSON "five" не должен быть null
        И узел JSON "five" должен быть равен числу 5
        И узел JSON "six" должен быть равен строке "44000"
