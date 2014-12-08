#language: ja
@japanese @rest
フィーチャ: Testing RESTContext

    シナリオ: Testing headers
        もし 私がGETメソッドで"rest/index.php"へリクエストを送る
        かつ "Content-Type"ヘッダが"text"を含むこと
        かつ "Content-Type"ヘッダが"text/html; charset=UTF-8"と一致すること
        かつ "Content-Type"ヘッダが"text/json"を含まないこと
        かつ "xxx"ヘッダが存在しないこと
        かつ レスポンスが将来期限切れになること
        かつ レスポンスが"UTF-8"でエンコードされていること
# ならば ブレークポイントを設置する

    シナリオ: Testing request methods.
       前提 私がGETメソッドで"/rest/index.php"へリクエストを送る
        ならば 画面に "You have sent a GET request. " と表示されていること
        かつ 画面に "No parameter received" と表示されていること

        もし 私がGETメソッドで"/rest/index.php?first=foo&second=bar"へリクエストを送る
        ならば 画面に "You have sent a GET request. " と表示されていること
        かつ 画面に "2 parameter(s)" と表示されていること
        かつ 画面に "first : foo" と表示されていること
        かつ 画面に "second : bar" と表示されていること

#  ならば ブレークポイントを設置する
       もし POSTメソッドで"/rest/index.php"へ下記のパラメーターを伴ったリクエストを送る:
            | key     | value      |
            | foo     | bar        |
            | foofile | @lorem.txt |

        ならば 最後のレスポンスを表示
        ならば 画面に "You have sent a POST request. " と表示されていること
        かつ 画面に "1 parameter(s)" と表示されていること
        かつ 画面に "1 file(s)" と表示されていること
        かつ 画面に "foo : bar" と表示されていること
        かつ 画面に "foofile - name : lorem.txt" と表示されていること
        かつ 画面に "foofile - error : 0" と表示されていること
        かつ 画面に "foofile - size : 39" と表示されていること

        もし 私がPUTメソッドで"rest/index.php"へリクエストを送る
        ならば 画面に "You have sent a PUT request. " と表示されていること

        もし 私がDELETEメソッドで"rest/index.php"へリクエストを送る
        ならば 画面に "You have sent a DELETE request. " と表示されていること

        もし POSTメソッドで"/rest/index.php"へ下記のボディを持ったリクエストを送る:
            """
            This is a body.
            """
        ならば 3秒間待つ
        ならば 最後のレスポンスを表示
        ならば 画面に "Body : This is a body." と表示されていること

        もし PUTメソッドで"/rest/index.php"へ下記のボディを持ったリクエストを送る:
            """
            {"this is":"some json"}
            """
        ならば レスポンスが空であること
#  ならば ブレークポイントを設置する

    シナリオ: Add header
       前提 "xxx"ヘッダに"yyy"を追加する
        もし 私がGETメソッドで"/rest/index.php"へリクエストを送る
        ならば 画面に "HTTP_XXX : yyy" と表示されていること
#  ならば ブレークポイントを設置する

    シナリオ: Case-insensitive header name
        Like describe in the rfc2614 §4.2
        https://tools.ietf.org/html/rfc2616#section-4.2

        もし 私がGETメソッドで"/rest/index.php"へリクエストを送る
        かつ "Content-Type"ヘッダが"text"を含むこと
#  ならば ブレークポイントを設置する

    シナリオ: Debug
       前提 "xxx"ヘッダに"yyy"を追加する
        もし POSTメソッドで"/rest/index.php"へ下記のパラメーターを伴ったリクエストを送る:
            | key | value |
            | foo | bar   |
        ならば 最後のレスポンスヘッダを表示する
        かつ curlコマンドを表示する
