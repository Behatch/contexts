#language: ja
@japanese @browser
フィーチャ: Browser Feature

    もしテストに失敗した場合は
    セットアップがうまくいっていない可能性があります。
    README.mdをご一読ください

    @javascript
    シナリオ: Testing simple web access
       前提 "/index.html" を表示している
        ならば　画面に "Congratulations, you've correctly set up your apache environment." と表示されていること

    シナリオ: Basic authentication
       前提 "/browser/auth.php" を表示している
        ならば レスポンスコードが 401 であること
        かつ 画面に "NONE SHALL PASS" と表示されていること

        もし Basic認証を"something"と"wrong"で設定する
        かつ "/browser/auth.php" へ移動する
        ならば レスポンスコードが 401 であること
        かつ 画面に "NONE SHALL PASS" と表示されていること

        もし Basic認証を"gabriel"と"30091984"で設定する
        かつ "/browser/auth.php" へ移動する
        ならば レスポンスコードが 200 であること
        かつ 画面に "Successfuly logged in" と表示されていること

        もし "/browser/auth.php?logout" へ移動する
        かつ 画面に "Logged out" と表示されていること

        かつ "/browser/auth.php" へ移動する
        ならば レスポンスコードが 401 であること
        かつ 画面に "NONE SHALL PASS" と表示されていること

    @javascript
    シナリオ: Elements testing
       前提 下記から構成されるURLに遷移する:
            | parameters     |
            | /browser       |
            | /elements.html |
        ならば 1番目の"body"要素が4個の"div"要素を持つこと
        ならば 1番目の"body"要素が6個以下の"div"要素を持つこと
        ならば 1番目の"body"要素が2個以上の"div"要素を持つこと
        かつ セレクトボックス"months_selector"は"january"を含むこと
        かつ セレクトボックス"months_selector"は"december"を含まないこと
        もし 私が 1 番目の "ul li" 要素をクリックする
        ならば 画面に "You clicked First" と表示されていること

    @javascript
    シナリオ: Frames testing
       前提 "/browser/frames.html" を表示している
        もし 私が　"index" iframeにフォーカスする
        ならば 画面に "Visible" と表示されていること

        もし 私が　メインフレームにフォーカスする

        もし 私が　"elements" iframeにフォーカスする
        ならば セレクトボックス"months_selector"は"january"を含むこと

    @javascript
    シナリオ: Wait before seeing
       前提 "/browser/timeout.html" を表示している
        ならば 私が"timeout"を見るまで3秒間待つ
        かつ 私が1秒間待つ
        かつ 私が"#iframe"要素を見るまで待つ
        かつ　私が "#iframe" 要素を見るまで 5 秒間待つ
        かつ　私が "#iframe" 要素を見るまで 5 秒待つ
        かつ　"#iframe" 要素を見るまで 5 秒待つ

    @javascript
    シナリオ: Check element visibility
       前提 "/browser/index.html" を表示している
        ならば 要素"#visible-element"は可視であること
        かつ 要素"#hidden-element"は不可視であること

    @javascript
    シナリオ:
       前提 "/browser/elements.html" を表示している
        ならば 私が"today"に現在の日付を入力する
        かつ 私が"today"に現在の日付を"-1 day"で入力する
