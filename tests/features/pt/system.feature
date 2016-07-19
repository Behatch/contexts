#language: pt
Funcionalidade: System

  Cenário: Testando a execução
    Quando Eu executo "true"
    Então o comando deve ser executado com sucesso
    Quando Eu executo "false"
    Então o comando deve falhar

  Cenário: Testando o tempo de execução
    Quando Eu executo "sleep 1"
    Então o comando deve demorar menos que 2 segundos

    Quando Eu executo "sleep 2"
    Então o comando deve demorar mais que 1 segundos

  Cenário: Testando a saída da execução
    Quando Eu executo "echo 'Hello world'"
    Então a saída deve conter "Hello world"
    E a saída deve conter "Hel.*ld"
    E a saída não deve conter "Hello John"
    E a saída não deve conter "Hel.*hn"

  Cenário: Testando a saída da execução com múltiplas linhas
    Quando Eu executo "echo 'Hello world\nHow are you?'"
    Então a saída deve ser:
    """
    Hello world
    How are you?
    """
    E a saída não deve ser:
    """
    Hello John
    How are you?
    """

  Cenário: Testando a execução de comandos a partir da raiz do projeto
    Quando executo "bin/behat --help"

  Cenário: Criação de arquivo
    Quando crio o arquivo "tests/fixtures/test" contendo:
      """
      A new file
      """
    Então exiba o conteúdo do arquivo "tests/fixtures/test"
