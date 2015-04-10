#language: ja
@japanese @json
機能: Testing JSONContext

    シナリオ: Am I a JSON ?
       前提 "/json/imajson.json" を表示している
        ならば レスポンスがJSONであること
        もし "/json/emptyarray.json" を表示している
        ならば レスポンスがJSON形式であること
        もし "/json/emptyobject.json" を表示している
        ならば レスポンスがJSONであること
        もし "/json/imnotajson.json" を表示している
        ならば レスポンスがJSONでないこと

    シナリオ: Count JSON elements
        前提 "/json/imajson.json" を表示している
        ならば JSONのノード"numbers"が4個の要素を持つこと

    シナリオ: Checking JSON evaluation
        前提 "/json/imajson.json" を表示している

        ならば JSONにノード"foo"が存在すること
        かつ JSONにノード"root.foo"が存在すること
        かつ JSONのノード"foo"が"bar"を含むこと
        かつ JSONのノード"foo"が"something else"を含まないこと

        かつ JSONのノード"numbers[0]"が"one"を含むこと
        かつ JSONのノード"numbers[1]"が"two"を含むこと
        かつ JSONのノード"numbers[2]"が"three"を含むこと
        かつ JSONのノード"numbers[3].complexeshizzle"が"true"と等しいこと
        かつ JSONのノード"numbers[3].so[0]"が"very"と等しいこと
        かつ JSONのノード"numbers[3].so[1].complicated"が"indeed"と等しいこと

        かつ JSONにノード"bar"が存在しないこと
#		かつ ブレークポイントを設置する

    シナリオ: Json validation with schema
        前提 "/json/imajson.json" を表示している
        ならば JSONがスキーマファイル"tests/fixtures/www/json/schema.json"に従っていること
#		かつ ブレークポイントを設置する

    シナリオ: Json validation with schema containing ref
        前提 "/json/withref.json" を表示している
        ならば JSONがスキーマファイル"tests/fixtures/www/json/schemaref.json"に従っていること
#		かつ ブレークポイントを設置する

    シナリオ: Json validation
        前提 "/json/imajson.json" を表示している
        ならば JSONが下記のスキーマに従っていること:
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
                        "one": {
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
#		かつ ブレークポイントを設置する

    シナリオ: Json contents validation
        前提 "/json/imajson.json" を表示している
        ならば JSONが下記と一致すること:
            """
            {
                "foo": "bar",
                "numbers": [
                    "one",
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
        かつ 最後のJSONレスポンスを表示する
#	かつ ブレークポイントを設置する

    シナリオ: Check json root node
        前提 "/json/rootarray.json" を表示している
        ならば レスポンスがJSON形式であること
        かつ JSONにノード"root[0].name"が存在すること
        かつ JSONのノード"root"が2個の要素を持つこと
