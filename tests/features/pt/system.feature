#language: pt
Funcionalidade: System

  Cenário: Testando a execução de comandos
    Quando executo "ls"

  Cenário: Testando a execução de comandos a partir da raiz do projeto
    Quando executo "bin/behat --help"

  Cenário: Criação de arquivo
    Quando crio o arquivo "tests/fixtures/test" contendo:
      """
      A new file
      """
    Então exiba o conteúdo do arquivo "tests/fixtures/test"
