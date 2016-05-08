#language: pt
Funcionalidade: Testando o RESTContext

  Cenário: Testando headers
    Quando envio uma requisição GET para "rest/index.php"
    Então o header "Content-Type" deve conter "text"
    E o header "Content-Type" deve ser igual a "text/html; charset=UTF-8"
    E o header "Content-Type" não deve conter "text/json"
    E o header "xxx" não deve existir
    E a resposta deve expirar no futuro
    E a resposta deve estar codificada em "UTF-8"

  Cenário: Testando métodos de requisição
    Quando envio uma requisição GET para "/rest/index.php"
    Então devo ver "You have sent a GET request. "
    E devo ver "No parameter received"

    Quando envio uma requisição GET para "/rest/index.php?first=foo&second=bar"
    Então devo ver "You have sent a GET request. "
    E devo ver "2 parameter(s)"
    E devo ver "first : foo"
    E devo ver "second : bar"

    Quando envio uma requisição POST para "/rest/index.php" com os parâmetros:
      | key     | value      |
      | foo     | bar        |
      | foofile | @lorem.txt |
    Então devo ver "You have sent a POST request. "
    E devo ver "1 parameter(s)"
    E devo ver "1 file(s)"
    E devo ver "foo : bar"
    E devo ver "foofile - name : lorem.txt"
    E devo ver "foofile - error : 0"
    E devo ver "foofile - size : 39"

    Quando envio uma requisição PUT para "/rest/index.php"
    Então devo ver "You have sent a PUT request. "

    Quando envio uma requisição DELETE para "/rest/index.php"
    Então devo ver "You have sent a DELETE request. "

    Quando envio uma requisição POST para "/rest/index.php" com o corpo:
      """
      This is a body.
      """
    Então devo ver "Body : This is a body."

    Quando envio uma requisição PUT para "/rest/index.php" com o corpo:
      """
      {"this is":"some json"}
      """
    Então a resposta deve estar vazia

  Cenário: Adicionar um header
    Quando adiciono o header "xxx" com o valor "yyy"
    E envio uma requisição GET para "/rest/index.php"
    Então devo ver "HTTP_XXX : yyy"

  Cenário: Nome do header case-insensitive
    Como descrito na rfc2614 §4.2
    https://tools.ietf.org/html/rfc2616#section-4.2

    Quando envio uma requisição GET para "rest/index.php"
    Então o header "content-type" deve conter "text"

  Cenário: Debug
    Quando adiciono o header "xxx" com o valor "yyy"
    E envio uma requisição POST para "/rest/index.php" com os parâmetros:
      | key | value |
      | foo | bar   |
    Então exiba os headers da última resposta
    E exiba o comando curl correspondente
