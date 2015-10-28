#language: pt
Funcionalidade: Browser

    # Se este cenário falhar
    # Seu ambiente não deve estar configurado corretamente
    # Você pode encontrar a ajuda necessária no README.md
  @javascript
  Cenário: Testando um acesso web simples
    Quando estou em "/index.html"
    Então devo ver "Congratulations, you've correctly set up your apache environment."

  Cenário: Basic Authentication
    Quando Eu estou em "/browser/auth.php"
    Então o código de status da resposta deve ser 401
    E devo ver "NONE SHALL PASS"

    Quando eu preencho a autenticação com "something" e "wrong"
    E vou para "/browser/auth.php"
    Então o código de status da resposta deve ser 401
    E devo ver "NONE SHALL PASS"

    Quando eu preencho a autenticação com "gabriel" e "30091984"
    E vou para "/browser/auth.php"
    Então o código de status da resposta deve ser 200
    E devo ver "Successfuly logged in"

    Quando Eu vou para "/browser/auth.php?logout"
    Então devo ver "Logged out"

    Quando Eu vou para "/browser/auth.php"
    Então o código de status da resposta deve ser 401
    E devo ver "NONE SHALL PASS"

  @javascript
  Cenário: Testando elementos
    Quando Eu estou em uma url composta por:
      | parameters     |
      | /browser       |
      | /elements.html |
    Então devo ver 4 "div" no 1º "body"
    E devo ver menos que 6 "div" no 1º "body"
    E devo ver mais que 2 "div" no 1º "body"
    E o select "months_selector" não deve conter "december"
    E o select "months_selector" deve conter "january"
    Quando Eu clico no 1º elemento "ul li"
    Então Eu devo ver "You clicked First"

  @javascript
  Cenário: Testando frames
    Quando Eu estou em "/browser/frames.html"
    E mudo para o iframe "index"
    Então devo ver "Visible"

    Quando eu mudo para o frame principal

    Quando mudo para o iframe "elements"
    Então o select "months_selector" deve conter "january"

  @javascript
  Cenário: Esperar antes de ver
    Quando Eu estou em "/browser/timeout.html"
    Então espero 3 segundos até ver "timeout"
    E espero 1 segundo
    E espero pelo elemento "#iframe"
    E espero 5 segundos pelo elemento "#iframe"

  @javascript
  Cenário: Verificar visibilidade do elemento
    Quando Eu estou em "/browser/index.html"
    Então o elemento "#visible-element" deve estar visível
    E o elemento "#hidden-element" não deve estar visível

  @javascript
  Cenário:
    Quando Eu estou em "/browser/elements.html"
    Então Eu preencho "today" com a data atual
    E Eu preencho "today" com a data atual e o modificador "-1 day"


  Cenário:
    Quando Eu estou em "/browser/elements.html"
    Então Eu salvo o valor de "today" no parâmetro "today"