#language: ja
@japanese @debug
機能: Debug Feature

    @user
    シナリオ: Testing a break point
       前提 "/index.html" を表示している
        ならば 私がブレークポイントを設置する
        ならば　画面に "Congratulations, you've correctly set up your apache environment." と表示されていること
        ならば ブレークポイントを設置する

    @javascript
    シナリオ: Taking a screenshot
       前提 "/index.html" を表示している
        ならば スクリーンショットを"./index.png"に保存する
