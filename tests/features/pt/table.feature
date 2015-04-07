#language: pt
Funcionalidade: Table

  Cenário: Testando o acesso a /table/index.html
    Quando estou em "/table/index.html"
    Então devo ver "You are about to test table."

  Cenário: Testando colunas
    Quando estou em "/table/index.html"

    Então devo ver 2 colunas na tabela "table"

    E as colunas da tabela "table" devem ser:
      | columns |
      | Lorem   |
      | Ipsum   |

  Cenário: Testando linhas
    Quando estou em "/table/index.html"

    Então devo ver 2 linhas na tabela "table"
    E devo ver 2 linhas na 1ª tabela "table"

    E os dados na 1ª linha da tabela "table" devem ser iguais a:
      | col1   | col2   |
      | Lorem  | Ipsum  |

    E os dados na 2ª linha da tabela "table" devem ser iguais a:
      | col1   | col2   |
      | Dolor  | Sit    |

  Cenário: Teste parcial de linhas
    Quando estou em "/table/index.html"

    Então devo ver 2 linhas na tabela "table"
    E devo ver 2 linhas na 1ª tabela "table"

    E os dados na 1ª linha da tabela "table" devem ser iguais a:
      | col2   |
      | Ipsum  |

    E os dados na 2ª linha da tabela "table" devem ser iguais a:
      | col1   |
      | Dolor  |

  Cenário: Testando o conteúdo das células
    Quando estou em "/table/index.html"
    Então a 1ª coluna da 1ª linha da tabela "table" deve conter "Lorem"
    E a 2ª coluna da 1ª linha da tabela "table" deve conter "Ipsum"
