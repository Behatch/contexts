#language: ja
@japanese @system
フィーチャ: System feature

    シナリオ: Testing execution
       前提 "ls"を実行する

    シナリオ: Testing execution from the project root
       前提 "bin/behat --help"を実行する

    シナリオ: File creation
        もし "fixtures/test"というファイルを下記のテキストで作成する:
        """
        A new file
        """
        ならば "fixtures/test"というファイルのテキストを表示する
