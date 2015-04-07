#language: ja
@japanese @table
フィーチャ: Table Feature

    シナリオ: Testing access to /table/index.html
        前提 "/table/index.html" を表示している
        ならば 画面に "You are about to test table." と表示されていること

    シナリオ: Testing columns
        前提 "/table/index.html" を表示している

        ならば テーブル"table"が2個のカラムを持つこと

        かつ テーブル"table"のカラムスキーマが下記と一致すること:
            | columns |
            | Lorem   |
            | Ipsum   |
# ならば ブレークポイントを設置する

    シナリオ: Testing rows
        前提 "/table/index.html" を表示している

        ならば テーブル"table"が2行持つこと
        かつ 1番目のテーブル"table"が2行持つこと

        かつ テーブル"table"の1行目のデータが下記と一致すること:
            | col1   | col2   |
            | Lorem  | Ipsum  |

        かつ テーブル"table"の2行目のデータが下記と一致すること:
            | col1   | col2   |
            | Dolor  | Sit    |
# ならば ブレークポイントを設置する

    シナリオ: Partial Testing rows
        前提 "/table/index.html" を表示している

        ならば テーブル"table"が2行持つこと
        かつ 1番目のテーブル"table"が2行持つこと

        かつ テーブル"table"の1行目のデータが下記と一致すること:
            | col2   |
            | Ipsum  |

        かつ テーブル"table"の2行目のデータが下記と一致すること:
            | col1   |
            | Dolor  |
# ならば ブレークポイントを設置する

    シナリオ: Testing cell content
        前提 "/table/index.html" を表示している
        ならば テーブル"table"の1行目1列が"Lorem"を含むこと

        かつ テーブル"table"の1行目2列が"Ipsum"を含むこと
